<?php

namespace App\Http\Controllers;

use App\Models\SaReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SaReportController extends Controller
{
    public function show(SaReport $saReport): Response
    {
        $saReport->load(['patient', 'medicalOrder.patient', 'medicalOrder.staff.user']);

        $patient = $saReport->patient ?? $saReport->medicalOrder?->patient;
        $staff = $saReport->medicalOrder?->staff;

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

        return Inertia::render('LabPanel/SaReport', [
            'report' => array_merge($saReport->toArray(), [
                'patient_name' => $patientName,
                'patient_hn' => $patient?->id,
                'patient_dob' => $dob,
                'patient_age' => $age,
                'doctor_name' => $staff?->user?->name,
            ]),
        ]);
    }

    public function generatePdf(SaReport $saReport): \Illuminate\Http\Response
    {
        $saReport->load(['patient', 'medicalOrder.patient', 'medicalOrder.staff.user']);

        $patient = $saReport->patient ?? $saReport->medicalOrder?->patient;
        $staff = $saReport->medicalOrder?->staff;

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

        $report = (object) array_merge($saReport->toArray(), [
            'patient_name' => $patientName,
            'patient_hn' => $patient?->id,
            'patient_dob' => $dob,
            'patient_age' => $age,
            'doctor_name' => $staff?->user?->name,
        ]);

        $reportDate = now()->format('d/m/Y');

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('lab-reports.sa-report', compact('report', 'reportDate'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'isPhpEnabled' => true,
            ]);

        return $pdf->stream('sa-report-'.$saReport->id.'-'.now()->format('Y-m-d').'.pdf');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'medical_order_id' => 'required|exists:medical_orders,id',
            'patient_id' => 'nullable|exists:patients,id',
            'wife_name' => 'nullable|string|max:255',
            'wife_hn' => 'nullable|string|max:255',
            'abstinence_days' => 'nullable|integer',
            'appearance' => 'nullable|string|max:255',
            'liquefaction' => 'nullable|string|max:255',
            'viscosity' => 'nullable|string|max:255',
            'ph' => 'nullable|numeric',
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
            'wbc' => 'nullable|string|max:255',
            'morphology_normal' => 'nullable|numeric',
            'morphology_abnormal' => 'nullable|numeric',
            'head_defect' => 'nullable|numeric',
            'neck_defect' => 'nullable|numeric',
            'tail_defect' => 'nullable|numeric',
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

        $report = SaReport::updateOrCreate(
            ['medical_order_id' => $data['medical_order_id']],
            $data
        );

        return back()->with('success', 'SA report saved.')->with('report_id', $report->id);
    }

    public function update(Request $request, SaReport $saReport): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'wife_name' => 'nullable|string|max:255',
            'wife_hn' => 'nullable|string|max:255',
            'abstinence_days' => 'nullable|integer',
            'appearance' => 'nullable|string|max:255',
            'liquefaction' => 'nullable|string|max:255',
            'viscosity' => 'nullable|string|max:255',
            'ph' => 'nullable|numeric',
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
            'wbc' => 'nullable|string|max:255',
            'morphology_normal' => 'nullable|numeric',
            'morphology_abnormal' => 'nullable|numeric',
            'head_defect' => 'nullable|numeric',
            'neck_defect' => 'nullable|numeric',
            'tail_defect' => 'nullable|numeric',
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

        $saReport->update($data);

        return back()->with('success', 'SA report updated.');
    }

    public function getByOrder(int $medicalOrderId): JsonResponse
    {
        $report = SaReport::where('medical_order_id', $medicalOrderId)->first();

        return response()->json($report);
    }
}
