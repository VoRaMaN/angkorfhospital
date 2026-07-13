<?php

namespace App\Http\Controllers;

use App\Models\HormoneReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HormoneReportController extends Controller
{
    private function buildPatientData(HormoneReport $report): array
    {
        $report->load(['patient', 'medicalOrder.patient', 'medicalOrder.staff.user']);

        $patient = $report->patient ?? $report->medicalOrder?->patient;
        $staff = $report->medicalOrder?->staff;

        $dob = null;
        $age = null;
        if ($patient) {
            $d = $patient->date_of_birth_day;
            $m = $patient->date_of_birth_month;
            $y = $patient->date_of_birth_year;
            if ($d && $m && $y) {
                $dob = str_pad($d, 2, '0', STR_PAD_LEFT).'/'.str_pad($m, 2, '0', STR_PAD_LEFT).'/'.$y;
                $birth = new \DateTime("{$y}-{$m}-{$d}");
                $age = (new \DateTime)->diff($birth)->y;
            }
        }

        $patientName = $patient
            ? trim(($patient->title ? $patient->title.' ' : '').($patient->name ?? '').($patient->surname ? ' '.$patient->surname : ''))
            : null;

        $sex = null;
        if ($patient) {
            $name = strtolower($patientName ?? '');
            $sex = preg_match('/^(mrs\.?\s|ms\.?\s|miss\s)/', $name) ? 'FEMALE' : 'MALE';
        }

        return [
            'patient_name' => $patientName,
            'patient_hn' => $patient?->id,
            'patient_dob' => $dob,
            'patient_age' => $age,
            'patient_sex' => $sex,
            'doctor_name' => $staff?->display_name,
        ];
    }

    /**
     * Store/refresh the hormone report PDF in the patient's files.
     */
    private function syncPatientFile(HormoneReport $report): void
    {
        $patientData = $this->buildPatientData($report);

        app(\App\Services\LabResultFileService::class)->syncReportPdf(
            $report->medicalOrder,
            'lab-reports.hormone-report',
            ['report' => (object) array_merge($report->toArray(), $patientData)],
            'Hormone Report - Order '.$report->medical_order_id.'.pdf'
        );
    }

    public function generatePdf(HormoneReport $hormoneReport): \Illuminate\Http\Response
    {
        $patientData = $this->buildPatientData($hormoneReport);

        $report = (object) array_merge($hormoneReport->toArray(), $patientData);

        ini_set('memory_limit', '512M');
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('lab-reports.hormone-report', compact('report'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'isPhpEnabled' => true,
            ], true);

        return $pdf->stream('hormone-report-'.$hormoneReport->id.'-'.now()->format('Y-m-d').'.pdf');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'medical_order_id' => 'required|exists:medical_orders,id',
            'patient_id' => 'nullable|exists:patients,id',
            'specimen' => 'nullable|string|max:100',
            'collection_date' => 'nullable|string|max:50',
            'collection_time' => 'nullable|string|max:50',
            'received_date' => 'nullable|string|max:50',
            'received_time' => 'nullable|string|max:50',
            'lh' => 'nullable|numeric',
            'fsh' => 'nullable|numeric',
            'prolactin' => 'nullable|numeric',
            'estradiol' => 'nullable|numeric',
            'progesterone' => 'nullable|numeric',
            'testosterone' => 'nullable|numeric',
            'tsh' => 'nullable|numeric',
            't3' => 'nullable|numeric',
            't4' => 'nullable|numeric',
            'amh' => 'nullable|numeric',
            'beta_hcg' => 'nullable|numeric',
            'remark' => 'nullable|string|max:500',
            'reported_by' => 'nullable|string|max:255',
            'reported_date' => 'nullable|string|max:50',
            'reported_time' => 'nullable|string|max:50',
            'approved_by' => 'nullable|string|max:255',
            'approved_date' => 'nullable|string|max:50',
            'approved_time' => 'nullable|string|max:50',
        ]);

        $report = HormoneReport::updateOrCreate(
            ['medical_order_id' => $data['medical_order_id']],
            $data,
        );

        $this->syncPatientFile($report);

        return back()
            ->with('success', 'Hormone report saved.')
            ->with('report_id', $report->id);
    }

    public function update(Request $request, HormoneReport $hormoneReport): RedirectResponse
    {
        $data = $request->validate([
            'specimen' => 'nullable|string|max:100',
            'collection_date' => 'nullable|string|max:50',
            'collection_time' => 'nullable|string|max:50',
            'received_date' => 'nullable|string|max:50',
            'received_time' => 'nullable|string|max:50',
            'lh' => 'nullable|numeric',
            'fsh' => 'nullable|numeric',
            'prolactin' => 'nullable|numeric',
            'estradiol' => 'nullable|numeric',
            'progesterone' => 'nullable|numeric',
            'testosterone' => 'nullable|numeric',
            'tsh' => 'nullable|numeric',
            't3' => 'nullable|numeric',
            't4' => 'nullable|numeric',
            'amh' => 'nullable|numeric',
            'beta_hcg' => 'nullable|numeric',
            'remark' => 'nullable|string|max:500',
            'reported_by' => 'nullable|string|max:255',
            'reported_date' => 'nullable|string|max:50',
            'reported_time' => 'nullable|string|max:50',
            'approved_by' => 'nullable|string|max:255',
            'approved_date' => 'nullable|string|max:50',
            'approved_time' => 'nullable|string|max:50',
        ]);

        $hormoneReport->update($data);

        $this->syncPatientFile($hormoneReport);

        return back()
            ->with('success', 'Hormone report updated.')
            ->with('report_id', $hormoneReport->id);
    }

    public function getByOrder(int $medicalOrderId): JsonResponse
    {
        $report = HormoneReport::where('medical_order_id', $medicalOrderId)->first();

        if (! $report) {
            return response()->json(null);
        }

        return response()->json($report);
    }
}
