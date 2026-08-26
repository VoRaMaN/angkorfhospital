<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    /** @use HasFactory<\Database\Factories\BillingFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'bill_no',
        'patient_id',
        'appointment_id',
        'visit_id',
        'medical_order_id',
        'doctor_id',
        'amount',
        'discount_amount',
        'status',
        'payment_method',
        'billing_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
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
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Staff::class, 'doctor_id');
    }

    /**
     * Generate bill number in format YYMM-XXXX
     * e.g., 2512-0001 for December 2025
     */
    public static function generateBillNo($billingDate = null): string
    {
        $date = $billingDate ? \Carbon\Carbon::parse($billingDate) : now();
        $yearMonth = $date->format('ym'); // e.g., 2512 for Dec 2025

        // Get the last bill number for this month
        $lastBill = self::where('bill_no', 'like', $yearMonth.'-%')
            ->orderBy('bill_no', 'desc')
            ->first();

        if ($lastBill) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastBill->bill_no, -4);
            $sequence = $lastSequence + 1;
        } else {
            // First bill of the month
            $sequence = 1;
        }

        return $yearMonth.'-'.str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
