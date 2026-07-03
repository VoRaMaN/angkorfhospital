<?php

namespace App\Enums;

enum MedicalOrderPriorityEnum: string
{
    case ROUTINE = 'routine';
    case URGENT = 'urgent';
    case STAT = 'stat';

    public function label(): string
    {
        return match ($this) {
            self::ROUTINE => 'Routine',
            self::URGENT => 'Urgent',
            self::STAT => 'STAT',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ROUTINE => 'bg-gray-100 text-gray-800',
            self::URGENT => 'bg-orange-100 text-orange-800',
            self::STAT => 'bg-red-100 text-red-800',
        };
    }

    public static function options(): array
    {
        return [
            self::ROUTINE->value => self::ROUTINE->label(),
            self::URGENT->value => self::URGENT->label(),
            self::STAT->value => self::STAT->label(),
        ];
    }
}
