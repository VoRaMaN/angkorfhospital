<?php

namespace App\Models;

use App\Enums\MedicalOrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalOrderInventory extends Model
{
    protected $table = 'medical_order_inventory';

    protected $fillable = [
        'medical_order_id',
        'inventory_id',
        'item_type',
        'item_name',
        'details',
        'dosage',
        'frequency',
        'route',
        'quantity_required',
        'unit_price',
        'selling_price',
        'status',
        'notes',
        'completed_at',
        'result_value',
        'result_unit',
        'result_notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'quantity_required' => 'integer',
            'completed_at' => 'datetime',
            'status' => MedicalOrderStatusEnum::class,
        ];
    }

    public function medicalOrder(): BelongsTo
    {
        return $this->belongsTo(MedicalOrder::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
