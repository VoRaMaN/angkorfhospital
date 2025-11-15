<?php

namespace App\Models;

use App\Enums\AppointmentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'staff_id',
        'appointment_date_time',
        'status',
        'reason_for_visit',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date_time' => 'datetime',
            'status' => AppointmentStatusEnum::class,
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function medicalRecord()
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }
}
