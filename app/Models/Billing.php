<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    /** @use HasFactory<\Database\Factories\BillingFactory> */
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'visit_id',
        'medical_order_id',
        'amount',
        'status',
        'billing_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'billing_date' => 'date',
            'status' => \App\Enums\BillingStatusEnum::class,
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

    public function patient()
    {
        return $this->hasOneThrough(Patient::class, Appointment::class, 'id', 'id', 'appointment_id', 'patient_id');
    }
}
