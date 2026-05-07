<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpermFreezingReport extends Model
{
    protected $fillable = [
        'medical_order_id', 'patient_id',
        'wife_name', 'wife_hn', 'abstinence_days', 'appearance', 'liquefaction', 'viscosity',
        'viability', 'volume', 'count_per_ml', 'total_count', 'motile', 'total_motile', 'motility',
        'motility_4_rapid', 'motility_3_medium', 'motility_2_slow', 'motility_1_static',
        'no_of_vial', 'ejaculation_time', 'examination_time', 'receive_time', 'finish_time',
        'remark', 'reported_by', 'reported_date', 'reported_time',
        'approved_by', 'approved_date', 'approved_time',
    ];

    public function medicalOrder(): BelongsTo
    {
        return $this->belongsTo(MedicalOrder::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
