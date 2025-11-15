<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabPanelItem extends Model
{
    protected $fillable = [
        'lab_panel_id',
        'inventory_id',
        'quantity_required',
        'notes',
    ];

    protected $casts = [
        'quantity_required' => 'integer',
    ];

    public function labPanel(): BelongsTo
    {
        return $this->belongsTo(LabPanel::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
