<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    /** @use HasFactory<\Database\Factories\VisitFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_AWAITING_ASSIGNMENT = 'awaiting_assignment';

    public const STATUS_ASSIGNED = 'assigned';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_AWAITING_ACCOUNTANT = 'awaiting_accountant';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_AWAITING_ASSIGNMENT,
        self::STATUS_ASSIGNED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_AWAITING_ACCOUNTANT,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'staff_id',
        'visit_date_time',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'visit_date_time' => 'datetime',
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
