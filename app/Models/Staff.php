<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Staff extends Model
{
    /** @use HasFactory<\Database\Factories\StaffFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'name',
        'email',
        'role_id',
        'department_id',
        'contact_number',
        'hire_date',
        'status',
    ];

    protected $appends = ['name'];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
        ];
    }

    public function getNameAttribute(): string
    {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasManyThrough(File::class, StaffFile::class);
    }

    public function staffFiles()
    {
        return $this->hasMany(StaffFile::class);
    }
}
