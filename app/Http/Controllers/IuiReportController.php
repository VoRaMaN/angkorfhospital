<?php

namespace App\Http\Controllers;

use App\Models\IuiReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IuiReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        IuiReport::updateOrCreate(
            ['medical_order_id' => $data['medical_order_id']],
            $data
        );

        return back()->with('success', 'IUI report saved.');
    }

    public function update(Request $request, IuiReport $iuiReport): RedirectResponse
    {
        $iuiReport->update($this->validated($request));

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
