<?php

namespace App\Models;

use App\Enums\PrescriptionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    /** @use HasFactory<\Database\Factories\PrescriptionFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'medication_name',
        'dosage',
        'frequency',
        'duration',
        'instructions',
        'prescribed_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'prescribed_date' => 'date',
            'status' => PrescriptionStatusEnum::class,
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }
}
