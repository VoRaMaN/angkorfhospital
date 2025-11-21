<?php

namespace App\Models;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
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
        'duration_minutes',
        'appointment_type',
        'status',
        'reason_for_visit',
        'notes',
        'is_hormone_test',
        'is_tvs',
        'opu_time',
        'et_fet_time',
        'is_beta_hcg',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date_time' => 'datetime',
            'duration_minutes' => 'integer',
            'appointment_type' => AppointmentTypeEnum::class,
            'status' => AppointmentStatusEnum::class,
            'is_hormone_test' => 'boolean',
            'is_tvs' => 'boolean',
            'is_beta_hcg' => 'boolean',
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

    public function getEndDateTimeAttribute(): \Carbon\Carbon
    {
        return $this->appointment_date_time->copy()->addMinutes($this->duration_minutes ?? 30);
    }

    public function scopeOverlapping($query, $startTime, $endTime, $excludeId = null)
    {
        return $query->where(function ($q) use ($startTime, $endTime) {
            $q->whereBetween('appointment_date_time', [$startTime, $endTime])
                ->orWhere(function ($q2) use ($startTime) {
                    $q2->where('appointment_date_time', '<=', $startTime)
                        ->whereRaw('DATE_ADD(appointment_date_time, INTERVAL duration_minutes MINUTE) > ?', [$startTime]);
                });
        })->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId));
    }

    public function isOverlappingWith(Appointment $other): bool
    {
        $thisStart = $this->appointment_date_time;
        $thisEnd = $this->end_date_time;
        $otherStart = $other->appointment_date_time;
        $otherEnd = $other->end_date_time;

        return $thisStart < $otherEnd && $thisEnd > $otherStart;
    }
}
