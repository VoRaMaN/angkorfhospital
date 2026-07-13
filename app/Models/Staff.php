<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    /** @use HasFactory<\Database\Factories\StaffFactory> */
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'role_id',
        'department_id',
        'contact_number',
        'hire_date',
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

    /**
     * Name to print on reports: the staff record's own first/last name (the
     * editable, full version), falling back to the login account's name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name !== '' ? $this->name : (string) ($this->user?->name ?? '');
    }

    public function role()
    {
        // role_id references the custom roles table (StaffRole), not the Spatie
        // roles table — their ids collide, so resolving via Spatie here would
        // return the wrong role. StaffObserver syncs the Spatie role by name.
        return $this->belongsTo(StaffRole::class, 'role_id');
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
