<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalService extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
}
