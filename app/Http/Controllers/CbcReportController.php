<?php

namespace App\Http\Controllers;

use App\Models\CbcReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CbcReportController extends Controller
{
    private function buildPatientData(CbcReport $report): array
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
            'patient_phone' => $patient?->mobile_phone ?? $patient?->home_phone,
            'doctor_name' => $staff?->user?->name,
        ];
    }

    /**
     * Full-page printable report. No PDF: dompdf's DejaVu Sans stack can't
     * shape Khmer script (below-base COENG conjuncts render broken), so this
     * is a browser-rendered Inertia page using a real Khmer web font and
     * window.print() instead — see CbcReport.vue.
     */
    public function show(CbcReport $cbcReport): Response
    {
        $patientData = $this->buildPatientData($cbcReport);

        return Inertia::render('LabPanel/CbcReport', [
            'report' => array_merge($cbcReport->toArray(), $patientData),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'medical_order_id' => 'required|exists:medical_orders,id',
            'patient_id' => 'nullable|exists:patients,id',
            'lab_id' => 'nullable|string|max:100',
            'requested_by' => 'nullable|string|max:255',
            'requested_date' => 'nullable|string|max:50',
            'analysis_date' => 'nullable|string|max:50',
            'wbc' => 'nullable|numeric',
            'rbc' => 'nullable|numeric',
            'hemoglobin' => 'nullable|numeric',
            'hematocrit' => 'nullable|numeric',
            'mcv' => 'nullable|numeric',
            'mch' => 'nullable|numeric',
            'mchc' => 'nullable|numeric',
            'platelets' => 'nullable|numeric',
            'rdw' => 'nullable|numeric',
            'neutrophils' => 'nullable|numeric',
            'lymphocytes' => 'nullable|numeric',
            'monocytes' => 'nullable|numeric',
            'eosinophils' => 'nullable|numeric',
            'basophils' => 'nullable|numeric',
            'remark' => 'nullable|string|max:1000',
            'reported_by' => 'nullable|string|max:255',
            'reported_date' => 'nullable|string|max:50',
        ]);

        $report = CbcReport::updateOrCreate(
            ['medical_order_id' => $data['medical_order_id']],
            $data,
        );

        return back()
            ->with('success', 'CBC report saved.')
            ->with('report_id', $report->id);
    }

    public function update(Request $request, CbcReport $cbcReport): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'lab_id' => 'nullable|string|max:100',
            'requested_by' => 'nullable|string|max:255',
            'requested_date' => 'nullable|string|max:50',
            'analysis_date' => 'nullable|string|max:50',
            'wbc' => 'nullable|numeric',
            'rbc' => 'nullable|numeric',
            'hemoglobin' => 'nullable|numeric',
            'hematocrit' => 'nullable|numeric',
            'mcv' => 'nullable|numeric',
            'mch' => 'nullable|numeric',
            'mchc' => 'nullable|numeric',
            'platelets' => 'nullable|numeric',
            'rdw' => 'nullable|numeric',
            'neutrophils' => 'nullable|numeric',
            'lymphocytes' => 'nullable|numeric',
            'monocytes' => 'nullable|numeric',
            'eosinophils' => 'nullable|numeric',
            'basophils' => 'nullable|numeric',
            'remark' => 'nullable|string|max:1000',
            'reported_by' => 'nullable|string|max:255',
            'reported_date' => 'nullable|string|max:50',
        ]);

        $cbcReport->update($data);

        return back()
            ->with('success', 'CBC report updated.')
            ->with('report_id', $cbcReport->id);
    }

    public function getByOrder(int $medicalOrderId): JsonResponse
    {
        $report = CbcReport::where('medical_order_id', $medicalOrderId)->first();

        if (! $report) {
            return response()->json(null);
        }

        return response()->json($report);
    }
}
