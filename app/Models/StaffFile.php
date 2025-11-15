<?php

namespace App\Models;

use App\Enums\StaffFileTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffFile extends Model
{
    protected $fillable = [
        'staff_id',
        'file_id',
        'type',
    ];

    protected $casts = [
        'type' => StaffFileTypeEnum::class,
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
