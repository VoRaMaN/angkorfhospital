<?php

namespace App\Models;

use App\Enums\VisitStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    /** @use HasFactory<\Database\Factories\VisitFactory> */
    use HasFactory;

    public const STATUS_PENDING = VisitStatusEnum::PENDING->value;

    public const STATUS_AWAITING_ASSIGNMENT = VisitStatusEnum::AWAITING_ASSIGNMENT->value;

    public const STATUS_ASSIGNED = VisitStatusEnum::ASSIGNED->value;

    public const STATUS_IN_PROGRESS = VisitStatusEnum::IN_PROGRESS->value;

    public const STATUS_SENT_BACK = VisitStatusEnum::SENT_BACK->value;

    public const STATUS_AWAITING_ACCOUNTANT = VisitStatusEnum::AWAITING_ACCOUNTANT->value;

    public const STATUS_COMPLETED = VisitStatusEnum::COMPLETED->value;

    public const STATUS_CANCELLED = VisitStatusEnum::CANCELLED->value;

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_AWAITING_ASSIGNMENT,
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_SENT_BACK,
        self::STATUS_AWAITING_ACCOUNTANT,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'staff_id',
        'doctor_id',
        'visit_date_time',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'visit_date_time' => 'datetime',
            'status' => VisitStatusEnum::class,
        ];
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }

    public function medicalOrders()
    {
        return $this->hasMany(MedicalOrder::class);
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
