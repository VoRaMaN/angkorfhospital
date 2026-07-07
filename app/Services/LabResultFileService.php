<?php

namespace App\Services;

use App\Enums\PatientFileTypeEnum;
use App\Models\File;
use App\Models\MedicalOrder;
use App\Models\PatientFile;
use Illuminate\Support\Facades\Storage;

class LabResultFileService
{
    private const PDF_OPTIONS = [
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'defaultFont' => 'DejaVu Sans',
        'dpi' => 96,
        'isPhpEnabled' => true,
    ];

    /**
     * Build a dompdf instance with the shared PDF options.
     *
     * setOptions(..., true) merges with config/dompdf.php's defaults instead
     * of replacing them outright — without this, dompdf's own Options class
     * falls back to its internal default chroot (its own vendor directory),
     * silently blocking every local image (logos, embryo grading references)
     * from embedding. Bumping memory_limit here because dompdf/GD decode
     * embedded PNGs into full uncompressed RGBA buffers (and doubles that for
     * alpha-channel compositing), which can exceed the default 128M on
     * larger source images.
     */
    public function makePdf(string $view, array $data)
    {
        ini_set('memory_limit', '512M');

        $pdf = app('dompdf.wrapper');
        $pdf->loadView($view, $data)->setOptions(self::PDF_OPTIONS, true);

        return $pdf;
    }

    /**
     * Regenerate the lab results PDF for a medical order and store it in the
     * patient's files (Files tab, type "lab_result"). Called every time a lab
     * result value is saved, so it upserts a single file per order.
     */
    public function syncOrderResults(MedicalOrder $order): ?PatientFile
    {
        if (! $order->patient_id) {
            return null;
        }

        $items = $order->orderItems()->with('inventory')->where('item_type', 'lab')->get();

        if ($items->isEmpty()) {
            return null;
        }

        $order->loadMissing('patient', 'staff.user');

        $patient = $order->patient;
        if (! $patient) {
            return null;
        }
        $dob = null;
        $age = null;
        if ($patient->date_of_birth_day && $patient->date_of_birth_month && $patient->date_of_birth_year) {
            $dob = str_pad($patient->date_of_birth_day, 2, '0', STR_PAD_LEFT)
                .'/'.str_pad($patient->date_of_birth_month, 2, '0', STR_PAD_LEFT)
                .'/'.$patient->date_of_birth_year;
            $birth = new \DateTime("{$patient->date_of_birth_year}-{$patient->date_of_birth_month}-{$patient->date_of_birth_day}");
            $age = (new \DateTime)->diff($birth)->y;
        }

        $pdf = $this->makePdf('lab-reports.lab-results', [
            'order' => $order,
            'items' => $items,
            'patientInfo' => [
                'name' => trim(($patient->title ? $patient->title.' ' : '').($patient->name ?? '').($patient->surname ? ' '.$patient->surname : '')),
                'hn' => $patient->id,
                'dob' => $dob,
                'age' => $age,
                'sex' => $patient->gender,
                'doctor_name' => $order->staff?->user?->name,
            ],
        ]);

        return $this->storePdf($order, $pdf->output(), 'Lab Results - Order '.$order->id.'.pdf');
    }

    /**
     * Store (or refresh) a generated lab PDF as a patient file linked to the
     * medical order. One file is kept per (order, file name): regenerating
     * replaces the stored document instead of piling up duplicates.
     */
    public function storePdf(MedicalOrder $order, string $content, string $fileName): ?PatientFile
    {
        if (! $order->patient_id) {
            return null;
        }

        $existing = PatientFile::where('patient_id', $order->patient_id)
            ->where('medical_order_id', $order->id)
            ->where('type', PatientFileTypeEnum::LAB_RESULT)
            ->whereHas('file', fn ($q) => $q->where('name', $fileName))
            ->first();

        $path = 'patient_files/'.time().'_'.$fileName;
        Storage::put($path, $content);

        if ($existing) {
            $oldPath = $existing->file->path;
            $existing->file->update([
                'path' => $path,
                'mime_type' => 'application/pdf',
                'size' => strlen($content),
            ]);
            if ($oldPath !== $path) {
                Storage::delete($oldPath);
            }
            $existing->touch();

            return $existing;
        }

        $file = File::create([
            'name' => $fileName,
            'path' => $path,
            'mime_type' => 'application/pdf',
            'size' => strlen($content),
        ]);

        return PatientFile::create([
            'patient_id' => $order->patient_id,
            'file_id' => $file->id,
            'type' => PatientFileTypeEnum::LAB_RESULT,
            'medical_order_id' => $order->id,
        ]);
    }

    /**
     * Render a specialized lab report view and store it as a patient file.
     * Failures are reported but never interrupt the caller's request.
     */
    public function syncReportPdf(?MedicalOrder $order, string $view, array $data, string $fileName): void
    {
        if (! $order || ! $order->patient_id) {
            return;
        }

        try {
            $pdf = $this->makePdf($view, $data);
            $this->storePdf($order, $pdf->output(), $fileName);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
