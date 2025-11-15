<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffRole extends Model
{
    /**
     * Use the `roles` table for compatibility with existing migrations
     * and code that expects the `roles` table.
     */
    protected $table = 'roles';

    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}
