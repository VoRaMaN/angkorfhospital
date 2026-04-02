<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineGroupItem extends Model
{
    protected $fillable = [
        'medicine_group_id',
        'inventory_id',
        'quantity',
        'dosage',
        'frequency',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function medicineGroup(): BelongsTo
    {
        return $this->belongsTo(MedicineGroup::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
