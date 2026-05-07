<?php

namespace App\Models;

use App\Enums\MedicalOrderPriorityEnum;
use App\Enums\MedicalOrderStatusEnum;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalOrder extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalOrderFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'visit_id',
        'patient_id',
        'staff_id',
        'order_details',
        'status',
        'priority',
        'notes',
        'ordered_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'completed_at' => 'datetime',
            'status' => MedicalOrderStatusEnum::class,
            'priority' => MedicalOrderPriorityEnum::class,
        ];
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function orderItems()
    {
        return $this->hasMany(MedicalOrderInventory::class);
    }

    public function inventoryItems()
    {
        return $this->belongsToMany(Inventory::class, 'medical_order_inventory')
            ->withPivot('item_type', 'item_name', 'details', 'dosage', 'frequency', 'route', 'quantity_required', 'status', 'notes', 'completed_at')
            ->withTimestamps();
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    public function latestBilling()
    {
        return $this->hasOne(Billing::class)->latestOfMany();
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function opuReport()
    {
        return $this->hasOne(OpuReport::class);
    }

    public function semenAnalysisReport()
    {
        return $this->hasOne(SemenAnalysisReport::class);
    }

    public function iuiReport()
    {
        return $this->hasOne(IuiReport::class);
    }

    public function fetReport()
    {
        return $this->hasOne(FetReport::class);
    }

    public function saReport()
    {
        return $this->hasOne(SaReport::class);
    }

    public function spermFreezingReport()
    {
        return $this->hasOne(SpermFreezingReport::class);
    }
}
