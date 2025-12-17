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
    ];

    public function getTotalUnitPriceAttribute(): float
    {
        return (float) $this->inventories->sum('unit_price');
    }

    public function inventories(): BelongsToMany
    {
        return $this->belongsToMany(Inventory::class, 'inventory_patch');
    }
}
