<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicineGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'custom_price',
        'is_active',
    ];

    protected $casts = [
        'custom_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MedicineGroupItem::class);
    }

    public function getTotalPriceAttribute()
    {
        if ($this->custom_price) {
            return $this->custom_price;
        }

        return $this->items->sum(function ($item) {
            return $item->inventory->selling_price * $item->quantity;
        });
    }
}
