<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabPanel extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function labPanelItems(): HasMany
    {
        return $this->hasMany(LabPanelItem::class);
    }

    public function inventoryItems()
    {
        return $this->belongsToMany(Inventory::class, 'lab_panel_items')
            ->withPivot('quantity_required', 'notes')
            ->withTimestamps();
    }
}
