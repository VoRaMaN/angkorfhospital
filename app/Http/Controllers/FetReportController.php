<?php

namespace App\Http\Controllers;

use App\Models\FetReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FetReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        FetReport::updateOrCreate(
            ['medical_order_id' => $data['medical_order_id']],
            $data
        );

        return back()->with('success', 'FET report saved.');
    }

    public function update(Request $request, FetReport $fetReport): RedirectResponse
    {
        $fetReport->update($this->validated($request));

        return back()->with('success', 'FET report updated.');
    }

    public function getByOrder(int $medicalOrderId): JsonResponse
    {
        $report = FetReport::where('medical_order_id', $medicalOrderId)->first();

        return response()->json($report);
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
