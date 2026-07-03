<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PackageItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'unit_price',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
        ];
    }

    public function patches(): BelongsToMany
    {
        return $this->belongsToMany(Patch::class, 'patch_package_item');
    }
}
