<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Deprecated: Use `StaffRole` instead. This class remains for backward compatibility.
 */
class Role extends StaffRole
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // All behavior is inherited from StaffRole
}
