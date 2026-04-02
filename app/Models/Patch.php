<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Patch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'custom_price',
    ];

    public function getTotalUnitPriceAttribute(): float
    {
        // If custom price is set, use it; otherwise sum inventory items
        if ($this->custom_price !== null) {
            return (float) $this->custom_price;
        }

        return (float) $this->inventories->sum(function ($inventory) {
            return $inventory->unit_price * ($inventory->pivot->quantity ?? 1);
        });
    }

    public function inventories(): BelongsToMany
    {
        return $this->belongsToMany(Inventory::class, 'inventory_patch')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
