<?php

namespace App\Http\Controllers;

use App\Models\SpermFreezingReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SpermFreezingReportController extends Controller
{
    private function buildPatientData(SpermFreezingReport $report): array
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
            }
            $birth = $dob ? new \DateTime("{$y}-{$m}-{$d}") : null;
            $age = $birth ? (new \DateTime)->diff($birth)->y : null;
        }

        $patientName = $patient
            ? trim(($patient->title ? $patient->title.' ' : '').($patient->name ?? '').($patient->surname ? ' '.$patient->surname : ''))
            : null;

        return [
            'patient_name' => $patientName,
            'patient_hn' => $patient?->id,
            'patient_dob' => $dob,
            'patient_age' => $age,
            'doctor_name' => $staff?->user?->name,
        ];
    }

    private function validationRules(bool $requireOrder = true): array
    {
        $rules = [
            'patient_id' => 'nullable|exists:patients,id',
            'wife_name' => 'nullable|string|max:255',
            'wife_hn' => 'nullable|string|max:255',
            'abstinence_days' => 'nullable|integer',
            'appearance' => 'nullable|string|max:255',
            'liquefaction' => 'nullable|string|max:255',
            'viscosity' => 'nullable|string|max:255',
            'viability' => 'nullable|numeric',
            'volume' => 'nullable|numeric',
            'count_per_ml' => 'nullable|numeric',
            'total_count' => 'nullable|numeric',
            'motile' => 'nullable|numeric',
            'total_motile' => 'nullable|numeric',
            'motility' => 'nullable|numeric',
            'motility_4_rapid' => 'nullable|numeric',
            'motility_3_medium' => 'nullable|numeric',
            'motility_2_slow' => 'nullable|numeric',
            'motility_1_static' => 'nullable|numeric',
            'no_of_vial' => 'nullable|integer',
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
        ];

        if ($requireOrder) {
            $rules['medical_order_id'] = 'required|exists:medical_orders,id';
        }

        return $rules;
    }

    /**
     * Store/refresh the sperm freezing report PDF in the patient's files.
     */
    private function syncPatientFile(SpermFreezingReport $report): void
    {
        $patientData = $this->buildPatientData($report);

        app(\App\Services\LabResultFileService::class)->syncReportPdf(
            $report->medicalOrder,
            'lab-reports.sperm-freezing-report',
            [
                'report' => (object) array_merge($report->toArray(), $patientData),
                'reportDate' => now()->format('d/m/Y'),
            ],
            'Sperm Freezing Report - Order '.$report->medical_order_id.'.pdf'
        );
    }

    public function generatePdf(SpermFreezingReport $spermFreezingReport): \Illuminate\Http\Response
    {
        $extra = $this->buildPatientData($spermFreezingReport);
        $report = (object) array_merge($spermFreezingReport->toArray(), $extra);
        $reportDate = now()->format('d/m/Y');

        ini_set('memory_limit', '512M');
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('lab-reports.sperm-freezing-report', compact('report', 'reportDate'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'isPhpEnabled' => true,
            ], true);

        return $pdf->stream('sperm-freezing-report-'.$spermFreezingReport->id.'-'.now()->format('Y-m-d').'.pdf');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->validationRules());

        $report = SpermFreezingReport::updateOrCreate(
            ['medical_order_id' => $data['medical_order_id']],
            $data
        );

        $this->syncPatientFile($report);

        return back()->with('success', 'Sperm freezing report saved.')->with('report_id', $report->id);
    }

    public function update(Request $request, SpermFreezingReport $spermFreezingReport): RedirectResponse
    {
        $data = $request->validate($this->validationRules(false));
        $spermFreezingReport->update($data);

        $this->syncPatientFile($spermFreezingReport);

        return back()->with('success', 'Sperm freezing report updated.');
    }

    public function getByOrder(int $medicalOrderId): JsonResponse
    {
        $report = SpermFreezingReport::where('medical_order_id', $medicalOrderId)->first();

        return response()->json($report);
    }
}
