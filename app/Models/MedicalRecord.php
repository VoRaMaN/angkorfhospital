<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'visit_id',
        'medical_order_id',
        'diagnosis',
        'treatment',
        'notes',
        'date_of_service',
    ];

    protected function casts(): array
    {
        return [
            'date_of_service' => 'date',
        ];
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function medicalOrder()
    {
        return $this->belongsTo(MedicalOrder::class);
    }
}
