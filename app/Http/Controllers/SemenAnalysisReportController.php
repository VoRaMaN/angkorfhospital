<?php

namespace App\Http\Controllers;

use App\Models\SemenAnalysisReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SemenAnalysisReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'medical_order_id' => 'required|exists:medical_orders,id',
            'patient_id' => 'nullable|exists:patients,id',
            'wife_name' => 'nullable|string|max:255',
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
        ]);

        SemenAnalysisReport::updateOrCreate(
            ['medical_order_id' => $data['medical_order_id']],
            $data
        );

        return back()->with('success', 'Semen analysis report saved.');
    }

    public function update(Request $request, SemenAnalysisReport $semenAnalysisReport): RedirectResponse
    {
        $data = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'wife_name' => 'nullable|string|max:255',
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
        ]);

        $semenAnalysisReport->update($data);

        return back()->with('success', 'Semen analysis report updated.');
    }

    public function getByOrder(int $medicalOrderId): JsonResponse
    {
        $report = SemenAnalysisReport::where('medical_order_id', $medicalOrderId)->first();

        return response()->json($report);
    }
}
