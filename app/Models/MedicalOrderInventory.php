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
        'is_package_included',
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
            'is_package_included' => 'boolean',
            'quantity_required' => 'integer',
            'completed_at' => 'datetime',
            'status' => MedicalOrderStatusEnum::class,
        ];
    }

    /**
     * Whether this item's price is covered by a package. Falls back to the
     * notes sentinel written by the Process screen so rows saved by clients
     * that didn't send the flag (stale bundles, pre-flag data) still count.
     */
    public function isPackageIncluded(): bool
    {
        return $this->is_package_included
            || str_contains((string) $this->notes, 'Include Package - Not counted in billing');
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
