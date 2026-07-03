<?php

namespace App\Models;

use App\Enums\PatientFileTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientFile extends Model
{
    protected $fillable = [
        'patient_id',
        'file_id',
        'type',
        'medical_order_id',
    ];

    protected $casts = [
        'type' => PatientFileTypeEnum::class,
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function medicalOrder(): BelongsTo
    {
        return $this->belongsTo(MedicalOrder::class);
    }
}
