<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RxMedicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'unit_price',
        'stock_quantity',
        'category',
        'unit',
        'dose_unit',
        'total_per_box',
        'reorder_quantity',
        'expiry_date',
    ];

    public function patches(): BelongsToMany
    {
        return $this->belongsToMany(Patch::class, 'patch_rx_medicine');
    }
}
