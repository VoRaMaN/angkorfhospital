<?php

namespace App\Http\Controllers;

use App\Models\IuiReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IuiReportController extends Controller
{
    private function buildPatientData(IuiReport $report): array
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

        $patientName = $patient ? trim(($patient->title ? $patient->title.' ' : '').($patient->name ?? '').($patient->surname ? ' '.$patient->surname : '')) : null;

        return [
            'patient_name' => $patientName,
            'patient_hn' => $patient?->id,
            'patient_dob' => $dob,
            'patient_age' => $age,
            'doctor_name' => $staff?->user?->name,
        ];
    }

    /**
     * Store/refresh the IUI report PDF in the patient's files.
     */
    private function syncPatientFile(IuiReport $report): void
    {
        $patientData = $this->buildPatientData($report);

        app(\App\Services\LabResultFileService::class)->syncReportPdf(
            $report->medicalOrder,
            'lab-reports.iui-report',
            [
                'report' => (object) array_merge($report->toArray(), $patientData),
                'reportDate' => now()->format('d/m/Y'),
            ],
            'IUI Report - Order '.$report->medical_order_id.'.pdf'
        );
    }

    public function show(IuiReport $iuiReport): Response
    {
        return Inertia::render('LabPanel/IuiReport', [
            'report' => array_merge($iuiReport->toArray(), $this->buildPatientData($iuiReport)),
        ]);
    }

    public function generatePdf(IuiReport $iuiReport): \Illuminate\Http\Response
    {
        $report = (object) array_merge($iuiReport->toArray(), $this->buildPatientData($iuiReport));

        $reportDate = now()->format('d/m/Y');

        ini_set('memory_limit', '512M');
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('lab-reports.iui-report', compact('report', 'reportDate'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'isPhpEnabled' => true,
            ], true);

        return $pdf->stream('iui-report-'.$iuiReport->id.'-'.now()->format('Y-m-d').'.pdf');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $report = IuiReport::updateOrCreate(
            ['medical_order_id' => $data['medical_order_id']],
            $data
        );

        $this->syncPatientFile($report);

        return back()->with('success', 'IUI report saved.')->with('report_id', $report->id);
    }

    public function update(Request $request, IuiReport $iuiReport): RedirectResponse
    {
        $iuiReport->update($this->validated($request));

        $this->syncPatientFile($iuiReport);

        return back()->with('success', 'IUI report updated.');
    }

    public function getByOrder(int $medicalOrderId): JsonResponse
    {
        $report = IuiReport::where('medical_order_id', $medicalOrderId)->first();

        return response()->json($report);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'medical_order_id' => 'sometimes|required|exists:medical_orders,id',
            'patient_id' => 'nullable|exists:patients,id',
            'wife_name' => 'nullable|string|max:255',
            'wife_hn' => 'nullable|string|max:50',
            'owner_sperm' => 'nullable|boolean',
            'donor_sperm' => 'nullable|boolean',
            'fresh_sperm' => 'nullable|boolean',
            'frozen_sperm' => 'nullable|boolean',
            'frozen_vial' => 'nullable|integer',
            'abstinence_days' => 'nullable|integer',
            'appearance' => 'nullable|string|max:255',
            'liquefaction' => 'nullable|string|max:255',
            'viscosity' => 'nullable|string|max:255',
            'pre_volume' => 'nullable|numeric',
            'pre_count' => 'nullable|numeric',
            'pre_total_count' => 'nullable|numeric',
            'pre_motile' => 'nullable|numeric',
            'pre_total_motile' => 'nullable|numeric',
            'pre_motility' => 'nullable|numeric',
            'pre_motility_4_rapid' => 'nullable|numeric',
            'pre_motility_3_medium' => 'nullable|numeric',
            'pre_motility_2_slow' => 'nullable|numeric',
            'pre_motility_1_static' => 'nullable|numeric',
            'post_volume' => 'nullable|numeric',
            'post_count' => 'nullable|numeric',
            'post_total_count' => 'nullable|numeric',
            'post_motile' => 'nullable|numeric',
            'post_total_motile' => 'nullable|numeric',
            'post_motility' => 'nullable|numeric',
            'post_motility_4_rapid' => 'nullable|numeric',
            'post_motility_3_medium' => 'nullable|numeric',
            'post_motility_2_slow' => 'nullable|numeric',
            'post_motility_1_static' => 'nullable|numeric',
            'ejaculation_time' => 'nullable|string|max:50',
            'examination_time' => 'nullable|string|max:50',
            'receive_time' => 'nullable|string|max:50',
            'finish_time' => 'nullable|string|max:50',
            'remark' => 'nullable|string',
            'reported_by' => 'nullable|string|max:255',
            'reported_date' => 'nullable|date',
            'reported_time' => 'nullable|string|max:50',
            'approved_by' => 'nullable|string|max:255',
            'approved_date' => 'nullable|date',
            'approved_time' => 'nullable|string|max:50',
        ]);
    }
}
