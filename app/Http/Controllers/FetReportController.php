<?php

namespace App\Http\Controllers;

use App\Models\FetReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FetReportController extends Controller
{
    public function show(FetReport $fetReport): Response
    {
        $fetReport->load('medicalOrder.staff.user');
        $staff = $fetReport->medicalOrder?->staff;

        return Inertia::render('LabPanel/FetReport', [
            'report' => array_merge($fetReport->toArray(), [
                'doctor_name' => $staff?->user?->name ?? $fetReport->doctor,
            ]),
        ]);
    }

    public function generatePdf(FetReport $fetReport): \Illuminate\Http\Response
    {
        $fetReport->load('medicalOrder.staff.user');
        $staff = $fetReport->medicalOrder?->staff;

        $report = $fetReport;
        $doctorName = $staff?->user?->name ?? $fetReport->doctor;
        $reportDate = $fetReport->created_at
            ? $fetReport->created_at->format('d/m/Y')
            : now()->format('d/m/Y');

        ini_set('memory_limit', '512M');
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('lab-reports.fet-report', compact('report', 'doctorName', 'reportDate'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'isPhpEnabled' => true,
            ], true);

        return $pdf->stream('fet-report-'.$fetReport->id.'-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Store/refresh the FET report PDF in the patient's files.
     */
    private function syncPatientFile(FetReport $report): void
    {
        $report->loadMissing('medicalOrder.staff.user');
        $staff = $report->medicalOrder?->staff;

        app(\App\Services\LabResultFileService::class)->syncReportPdf(
            $report->medicalOrder,
            'lab-reports.fet-report',
            [
                'report' => $report,
                'doctorName' => $staff?->user?->name ?? $report->doctor,
                'reportDate' => $report->created_at
                    ? $report->created_at->format('d/m/Y')
                    : now()->format('d/m/Y'),
            ],
            'FET Report - Order '.$report->medical_order_id.'.pdf'
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $report = FetReport::updateOrCreate(
            ['medical_order_id' => $data['medical_order_id']],
            $data
        );

        $this->syncPatientFile($report);

        return back()->with('success', 'FET report saved.')->with('report_id', $report->id);
    }

    public function update(Request $request, FetReport $fetReport): RedirectResponse
    {
        $fetReport->update($this->validated($request));

        $this->syncPatientFile($fetReport);

        return back()->with('success', 'FET report updated.');
    }

    public function getByOrder(int $medicalOrderId): JsonResponse
    {
        $report = FetReport::where('medical_order_id', $medicalOrderId)->first();

        return response()->json($report);
    }

    /**
     * Upload one embryo development picture; returns its stored path as JSON
     * so the dialog can keep it in the form and submit paths with the report.
     */
    public function uploadEmbryoImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $path = $request->file('image')->store('fet_embryos');

        return response()->json(['path' => $path]);
    }

    /**
     * Serve an uploaded embryo picture (they live on the private local disk,
     * so the browser can't reach them without this endpoint).
     */
    public function embryoImage(string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        abort_unless(preg_match('/^[\w.\-]+$/', $filename), 404);

        $path = 'fet_embryos/'.$filename;
        abort_unless(\Illuminate\Support\Facades\Storage::exists($path), 404);

        return \Illuminate\Support\Facades\Storage::response($path);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'medical_order_id' => 'sometimes|required|exists:medical_orders,id',
            'female_patient_id' => 'nullable|string',
            'female_patient_name' => 'nullable|string|max:255',
            'female_hn' => 'nullable|string|max:50',
            'female_dob' => 'nullable|string|max:50',
            'male_patient_id' => 'nullable|string',
            'male_patient_name' => 'nullable|string|max:255',
            'male_hn' => 'nullable|string|max:50',
            'male_dob' => 'nullable|string|max:50',
            'procedure' => 'nullable|string|max:50',
            'fet_date' => 'nullable|string|max:50',
            'doctor' => 'nullable|string|max:255',
            'freeze_datetime' => 'nullable|string|max:100',
            'thaw_datetime' => 'nullable|string|max:100',
            'thawing_media' => 'nullable|string|max:100',
            'no_of_freeze' => 'nullable|integer',
            'no_of_thaw' => 'nullable|integer',
            'lot_no' => 'nullable|string|max:50',
            'stage_of_freeze' => 'nullable|string|max:100',
            'no_of_survival' => 'nullable|integer',
            'exp_date' => 'nullable|string|max:50',
            'no_of_remaining' => 'nullable|integer',
            'thawing_by' => 'nullable|string|max:255',
            'day3_datetime' => 'nullable|string|max:100',
            'day3_embryo_1' => 'nullable|string|max:100',
            'day3_embryo_2' => 'nullable|string|max:100',
            'day3_embryo_3' => 'nullable|string|max:100',
            'day3_embryo_4' => 'nullable|string|max:100',
            'day3_embryo_5' => 'nullable|string|max:100',
            'day5_datetime' => 'nullable|string|max:100',
            'day5_embryo_1' => 'nullable|string|max:100',
            'day5_embryo_2' => 'nullable|string|max:100',
            'day5_embryo_3' => 'nullable|string|max:100',
            'day5_embryo_4' => 'nullable|string|max:100',
            'day5_embryo_5' => 'nullable|string|max:100',
            'picture_day' => 'nullable|integer|min:1|max:99',
            'picture_datetime' => 'nullable|string|max:100',
            'embryo_pictures' => 'nullable|array|max:10',
            'embryo_pictures.*.no' => 'nullable|string|max:20',
            'embryo_pictures.*.path' => ['nullable', 'string', 'max:255', 'regex:#^fet_embryos/[\w.\-]+$#'],
            'no_of_et' => 'nullable|integer',
            'et_volume' => 'nullable|string|max:50',
            'number_of_transfer' => 'nullable|integer',
            'et_day' => 'nullable|integer',
            'et_catheter' => 'nullable|string|max:50',
            'number_of_freeze_et' => 'nullable|integer',
            'et_datetime' => 'nullable|string|max:100',
            'et_doctor' => 'nullable|string|max:255',
            'number_of_discard' => 'nullable|integer',
            'assisted_hatching' => 'nullable|string|max:100',
            'et_embryologist' => 'nullable|string|max:255',
            'embryologist_report' => 'nullable|string|max:255',
            'embryologist_approve' => 'nullable|string|max:255',
            'remark' => 'nullable|string',
        ]);
    }
}
