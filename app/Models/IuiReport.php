<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IuiReport extends Model
{
    protected $fillable = [
        'medical_order_id', 'patient_id',
        'wife_name', 'wife_hn',
        'owner_sperm', 'donor_sperm', 'fresh_sperm', 'frozen_sperm', 'frozen_vial',
        'abstinence_days', 'appearance', 'liquefaction', 'viscosity',
        'pre_volume', 'pre_count', 'pre_total_count', 'pre_motile', 'pre_total_motile',
        'pre_motility', 'pre_motility_4_rapid', 'pre_motility_3_medium',
        'pre_motility_2_slow', 'pre_motility_1_static',
        'post_volume', 'post_count', 'post_total_count', 'post_motile', 'post_total_motile',
        'post_motility', 'post_motility_4_rapid', 'post_motility_3_medium',
        'post_motility_2_slow', 'post_motility_1_static',
        'ejaculation_time', 'examination_time', 'receive_time', 'finish_time',
        'remark', 'reported_by', 'reported_date', 'reported_time',
        'approved_by', 'approved_date', 'approved_time',
    ];

    protected function casts(): array
    {
        return [
            'owner_sperm' => 'boolean',
            'donor_sperm' => 'boolean',
            'fresh_sperm' => 'boolean',
            'frozen_sperm' => 'boolean',
        ];
    }

    public function medicalOrder(): BelongsTo
    {
        return $this->belongsTo(MedicalOrder::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
