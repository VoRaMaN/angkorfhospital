<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpuReport extends Model
{
    protected $fillable = [
        'medical_order_id', 'female_patient_id', 'male_patient_id',
        'procedure', 'doctor_id', 'opu_datetime',
        'no_of_opu_right', 'no_of_opu_left', 'maturation_datetime',
        'm_ii', 'm_i', 'gv', 'post_mature', 'maturation_abnormal', 'maturation_dead',
        'fertilization_datetime', 'pn2', 'pn1', 'pn3', 'pn4', 'no_pn', 'fert_dead',
        'sperm_prep_datetime', 'sperm_volume_ml', 'sperm_count_per_ml',
        'sperm_total_count', 'sperm_motile_per_ml', 'sperm_total_motile',
        'sperm_motility_pct', 'sperm_type',
        'embryo_freeze_datetime', 'freeze_day', 'freeze_stage', 'no_of_embryo',
        'no_of_straw', 'freeze_position', 'freeze_method', 'freeze_media',
        'day3_datetime', 'day3_checked_by', 'day3_embryos',
        'day5_datetime', 'day5_checked_by', 'day5_embryos',
        'embryo_developments',
        'et_no', 'et_day', 'et_datetime', 'assisted_hatching', 'et_volume',
        'et_catheter', 'et_doctor', 'et_embryologist',
        'number_of_transfer', 'number_of_freeze', 'number_of_discard',
        'remark', 'embryologist_report', 'embryologist_approve',
    ];

    protected function casts(): array
    {
        return [
            'opu_datetime' => 'datetime',
            'maturation_datetime' => 'datetime',
            'fertilization_datetime' => 'datetime',
            'sperm_prep_datetime' => 'datetime',
            'embryo_freeze_datetime' => 'datetime',
            'day3_datetime' => 'datetime',
            'day5_datetime' => 'datetime',
            'et_datetime' => 'datetime',
            'day3_embryos' => 'array',
            'day5_embryos' => 'array',
            'embryo_developments' => 'array',
            'assisted_hatching' => 'boolean',
        ];
    }

    public function medicalOrder(): BelongsTo
    {
        return $this->belongsTo(MedicalOrder::class);
    }

    public function femalePatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'female_patient_id');
    }

    public function malePatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'male_patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }
}
