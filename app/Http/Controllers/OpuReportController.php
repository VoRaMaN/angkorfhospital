<?php

namespace App\Http\Controllers;

use App\Models\OpuReport;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OpuReportController extends Controller
{
    /**
     * Show full OPU report view.
     */
    public function show(int $medicalOrderId): Response
    {
        $report = OpuReport::with(['femalePatient', 'malePatient', 'doctor.user', 'medicalOrder.patient'])
            ->where('medical_order_id', $medicalOrderId)
            ->firstOrFail();

        return Inertia::render('LabPanel/OPUReport', [
            'report' => $this->formatReport($report),
        ]);
    }

    public function generatePdf(int $medicalOrderId): \Illuminate\Http\Response
    {
        $opuReport = OpuReport::with(['femalePatient', 'malePatient', 'doctor.user', 'medicalOrder.patient'])
            ->where('medical_order_id', $medicalOrderId)
            ->firstOrFail();

        $report = $this->formatReport($opuReport);
        $reportDate = $opuReport->created_at
            ? $opuReport->created_at->format('d/m/Y')
            : now()->format('d/m/Y');

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('lab-reports.opu-report', compact('report', 'reportDate'))
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'dpi' => 96,
                'isPhpEnabled' => true,
            ]);

        return $pdf->stream('opu-report-'.$medicalOrderId.'-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Return OPU report data as JSON for the edit dialog.
     */
    public function getByOrder(int $medicalOrderId): JsonResponse
    {
        $report = OpuReport::with(['femalePatient', 'malePatient', 'doctor.user', 'medicalOrder.patient'])
            ->where('medical_order_id', $medicalOrderId)
            ->first();

        if (! $report) {
            return response()->json(null);
        }

        return response()->json($this->formatReport($report));
    }

    /**
     * Store a new OPU report.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'medical_order_id' => 'required|exists:medical_orders,id',
            'female_patient_id' => 'nullable|exists:patients,id',
            'male_patient_id' => 'nullable|exists:patients,id',
            'procedure' => 'nullable|string|max:255',
            'doctor_id' => 'nullable|exists:staff,id',
            'opu_datetime' => 'nullable|date',
            'no_of_opu_right' => 'nullable|integer',
            'no_of_opu_left' => 'nullable|integer',
            'maturation_datetime' => 'nullable|date',
            'm_ii' => 'nullable|integer',
            'm_i' => 'nullable|integer',
            'gv' => 'nullable|integer',
            'post_mature' => 'nullable|integer',
            'maturation_abnormal' => 'nullable|integer',
            'maturation_dead' => 'nullable|integer',
            'fertilization_datetime' => 'nullable|date',
            'pn2' => 'nullable|integer',
            'pn1' => 'nullable|integer',
            'pn3' => 'nullable|integer',
            'pn4' => 'nullable|integer',
            'no_pn' => 'nullable|integer',
            'fert_dead' => 'nullable|integer',
            'sperm_prep_datetime' => 'nullable|date',
            'sperm_volume_ml' => 'nullable|numeric',
            'sperm_count_per_ml' => 'nullable|numeric',
            'sperm_total_count' => 'nullable|numeric',
            'sperm_motile_per_ml' => 'nullable|numeric',
            'sperm_total_motile' => 'nullable|numeric',
            'sperm_motility_pct' => 'nullable|numeric',
            'sperm_type' => 'nullable|in:fresh,frozen',
            'embryo_freeze_datetime' => 'nullable|date',
            'freeze_day' => 'nullable|string',
            'freeze_stage' => 'nullable|string',
            'no_of_embryo' => 'nullable|integer',
            'no_of_straw' => 'nullable|integer',
            'freeze_position' => 'nullable|string',
            'freeze_method' => 'nullable|string',
            'freeze_media' => 'nullable|string',
            'day3_datetime' => 'nullable|date',
            'day3_checked_by' => 'nullable|string',
            'day3_embryos' => 'nullable|array',
            'day3_embryos.*' => 'nullable|string',
            'day5_datetime' => 'nullable|date',
            'day5_checked_by' => 'nullable|string',
            'day5_embryos' => 'nullable|array',
            'day5_embryos.*' => 'nullable|string',
            'et_no' => 'nullable|integer',
            'et_day' => 'nullable|string',
            'et_datetime' => 'nullable|date',
            'assisted_hatching' => 'nullable|boolean',
            'et_volume' => 'nullable|string',
            'et_catheter' => 'nullable|string',
            'et_doctor' => 'nullable|string',
            'et_embryologist' => 'nullable|string',
            'number_of_transfer' => 'nullable|integer',
            'number_of_freeze' => 'nullable|integer',
            'number_of_discard' => 'nullable|integer',
            'remark' => 'nullable|string',
            'embryologist_report' => 'nullable|string',
            'embryologist_approve' => 'nullable|string',
        ]);

        $report = OpuReport::updateOrCreate(
            ['medical_order_id' => $data['medical_order_id']],
            $data
        );

        return back()->with('success', 'OPU Report saved successfully.')->with('report_id', $report->id);
    }

    /**
     * Update existing OPU report.
     */
    public function update(Request $request, OpuReport $opuReport): RedirectResponse
    {
        $data = $request->validate([
            'female_patient_id' => 'nullable|exists:patients,id',
            'male_patient_id' => 'nullable|exists:patients,id',
            'procedure' => 'nullable|string|max:255',
            'doctor_id' => 'nullable|exists:staff,id',
            'opu_datetime' => 'nullable|date',
            'no_of_opu_right' => 'nullable|integer',
            'no_of_opu_left' => 'nullable|integer',
            'maturation_datetime' => 'nullable|date',
            'm_ii' => 'nullable|integer',
            'm_i' => 'nullable|integer',
            'gv' => 'nullable|integer',
            'post_mature' => 'nullable|integer',
            'maturation_abnormal' => 'nullable|integer',
            'maturation_dead' => 'nullable|integer',
            'fertilization_datetime' => 'nullable|date',
            'pn2' => 'nullable|integer',
            'pn1' => 'nullable|integer',
            'pn3' => 'nullable|integer',
            'pn4' => 'nullable|integer',
            'no_pn' => 'nullable|integer',
            'fert_dead' => 'nullable|integer',
            'sperm_prep_datetime' => 'nullable|date',
            'sperm_volume_ml' => 'nullable|numeric',
            'sperm_count_per_ml' => 'nullable|numeric',
            'sperm_total_count' => 'nullable|numeric',
            'sperm_motile_per_ml' => 'nullable|numeric',
            'sperm_total_motile' => 'nullable|numeric',
            'sperm_motility_pct' => 'nullable|numeric',
            'sperm_type' => 'nullable|in:fresh,frozen',
            'embryo_freeze_datetime' => 'nullable|date',
            'freeze_day' => 'nullable|string',
            'freeze_stage' => 'nullable|string',
            'no_of_embryo' => 'nullable|integer',
            'no_of_straw' => 'nullable|integer',
            'freeze_position' => 'nullable|string',
            'freeze_method' => 'nullable|string',
            'freeze_media' => 'nullable|string',
            'day3_datetime' => 'nullable|date',
            'day3_checked_by' => 'nullable|string',
            'day3_embryos' => 'nullable|array',
            'day3_embryos.*' => 'nullable|string',
            'day5_datetime' => 'nullable|date',
            'day5_checked_by' => 'nullable|string',
            'day5_embryos' => 'nullable|array',
            'day5_embryos.*' => 'nullable|string',
            'et_no' => 'nullable|integer',
            'et_day' => 'nullable|string',
            'et_datetime' => 'nullable|date',
            'assisted_hatching' => 'nullable|boolean',
            'et_volume' => 'nullable|string',
            'et_catheter' => 'nullable|string',
            'et_doctor' => 'nullable|string',
            'et_embryologist' => 'nullable|string',
            'number_of_transfer' => 'nullable|integer',
            'number_of_freeze' => 'nullable|integer',
            'number_of_discard' => 'nullable|integer',
            'remark' => 'nullable|string',
            'embryologist_report' => 'nullable|string',
            'embryologist_approve' => 'nullable|string',
        ]);

        $opuReport->update($data);

        return back()->with('success', 'OPU Report updated successfully.');
    }

    /**
     * Search patients (JSON) for partner selection.
     */
    public function searchPatients(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $pattern = '%'.implode('%', mb_str_split(mb_strtolower($q))).'%';

        $patients = Patient::where(function ($query) use ($q, $pattern) {
            $sub = '%'.mb_strtolower($q).'%';
            $query->where('id', 'like', $sub)
                ->orWhereRaw("LOWER(CONCAT(COALESCE(name,''),' ',COALESCE(surname,''))) LIKE ?", [$sub])
                ->orWhereRaw("LOWER(CONCAT(COALESCE(name,''),' ',COALESCE(surname,''))) LIKE ?", [$pattern])
                ->orWhere('mobile_phone', 'like', $sub)
                ->orWhere('id_card_or_passport', 'like', $sub);
        })
            ->limit(10)
            ->get(['id', 'title', 'name', 'surname', 'date_of_birth_day', 'date_of_birth_month', 'date_of_birth_year', 'mobile_phone', 'id_card_or_passport', 'gender'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => trim(($p->title ? $p->title.' ' : '').($p->name ?? '').($p->surname ? ' '.$p->surname : '')),
                'dob' => $p->date_of_birth_day ? str_pad($p->date_of_birth_day, 2, '0', STR_PAD_LEFT).'/'.str_pad($p->date_of_birth_month, 2, '0', STR_PAD_LEFT).'/'.$p->date_of_birth_year : null,
                'phone' => $p->mobile_phone,
                'id_card' => $p->id_card_or_passport,
                'gender' => $p->gender,
            ]);

        return response()->json($patients);
    }

    private function formatReport(OpuReport $report): array
    {
        // Fall back to the medical order's patient when no explicit female_patient_id is set
        $fp = $report->femalePatient ?? $report->medicalOrder?->patient;
        $mp = $report->malePatient;

        return [
            'id' => $report->id,
            'medical_order_id' => $report->medical_order_id,
            'female_patient_id' => $fp?->id ?? $report->female_patient_id,
            'female_patient_name' => $fp ? trim(($fp->title ? $fp->title.' ' : '').($fp->name ?? '').($fp->surname ? ' '.$fp->surname : '')) : null,
            'female_hn' => $fp?->id,
            'female_dob' => $fp && $fp->date_of_birth_day ? str_pad($fp->date_of_birth_day, 2, '0', STR_PAD_LEFT).'/'.str_pad($fp->date_of_birth_month, 2, '0', STR_PAD_LEFT).'/'.$fp->date_of_birth_year : null,
            'male_patient_id' => $report->male_patient_id,
            'male_patient_name' => $mp ? trim(($mp->title ? $mp->title.' ' : '').($mp->name ?? '').($mp->surname ? ' '.$mp->surname : '')) : null,
            'male_hn' => $mp?->id,
            'male_dob' => $mp && $mp->date_of_birth_day ? str_pad($mp->date_of_birth_day, 2, '0', STR_PAD_LEFT).'/'.str_pad($mp->date_of_birth_month, 2, '0', STR_PAD_LEFT).'/'.$mp->date_of_birth_year : null,
            'procedure' => $report->procedure,
            'doctor_id' => $report->doctor_id,
            'doctor_name' => $report->doctor?->user?->name,
            'opu_datetime' => $report->opu_datetime?->format('Y-m-d\TH:i'),
            'no_of_opu_right' => $report->no_of_opu_right,
            'no_of_opu_left' => $report->no_of_opu_left,
            'maturation_datetime' => $report->maturation_datetime?->format('Y-m-d\TH:i'),
            'm_ii' => $report->m_ii,
            'm_i' => $report->m_i,
            'gv' => $report->gv,
            'post_mature' => $report->post_mature,
            'maturation_abnormal' => $report->maturation_abnormal,
            'maturation_dead' => $report->maturation_dead,
            'fertilization_datetime' => $report->fertilization_datetime?->format('Y-m-d\TH:i'),
            'pn2' => $report->pn2,
            'pn1' => $report->pn1,
            'pn3' => $report->pn3,
            'pn4' => $report->pn4,
            'no_pn' => $report->no_pn,
            'fert_dead' => $report->fert_dead,
            'sperm_prep_datetime' => $report->sperm_prep_datetime?->format('Y-m-d\TH:i'),
            'sperm_volume_ml' => $report->sperm_volume_ml,
            'sperm_count_per_ml' => $report->sperm_count_per_ml,
            'sperm_total_count' => $report->sperm_total_count,
            'sperm_motile_per_ml' => $report->sperm_motile_per_ml,
            'sperm_total_motile' => $report->sperm_total_motile,
            'sperm_motility_pct' => $report->sperm_motility_pct,
            'sperm_type' => $report->sperm_type,
            'embryo_freeze_datetime' => $report->embryo_freeze_datetime?->format('Y-m-d\TH:i'),
            'freeze_day' => $report->freeze_day,
            'freeze_stage' => $report->freeze_stage,
            'no_of_embryo' => $report->no_of_embryo,
            'no_of_straw' => $report->no_of_straw,
            'freeze_position' => $report->freeze_position,
            'freeze_method' => $report->freeze_method,
            'freeze_media' => $report->freeze_media,
            'day3_datetime' => $report->day3_datetime?->format('Y-m-d\TH:i'),
            'day3_checked_by' => $report->day3_checked_by,
            'day3_embryos' => $report->day3_embryos ?? array_fill(0, 20, null),
            'day5_datetime' => $report->day5_datetime?->format('Y-m-d\TH:i'),
            'day5_checked_by' => $report->day5_checked_by,
            'day5_embryos' => $report->day5_embryos ?? array_fill(0, 20, null),
            'et_no' => $report->et_no,
            'et_day' => $report->et_day,
            'et_datetime' => $report->et_datetime?->format('Y-m-d\TH:i'),
            'assisted_hatching' => $report->assisted_hatching,
            'et_volume' => $report->et_volume ?? '15µl',
            'et_catheter' => $report->et_catheter,
            'et_doctor' => $report->et_doctor,
            'et_embryologist' => $report->et_embryologist,
            'number_of_transfer' => $report->number_of_transfer,
            'number_of_freeze' => $report->number_of_freeze,
            'number_of_discard' => $report->number_of_discard,
            'remark' => $report->remark,
            'embryologist_report' => $report->embryologist_report,
            'embryologist_approve' => $report->embryologist_approve,
        ];
    }
}
