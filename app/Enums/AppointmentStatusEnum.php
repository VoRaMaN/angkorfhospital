<?php

namespace App\Enums;

enum AppointmentStatusEnum: string
{
    case SCHEDULED = 'scheduled';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no-show';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Scheduled',
            self::CONFIRMED => 'Confirmed',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::NO_SHOW => 'No Show',
        };
    }

    public static function options(): array
    {
        return [
            self::SCHEDULED->value => self::SCHEDULED->label(),
            self::CONFIRMED->value => self::CONFIRMED->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
            self::CANCELLED->value => self::CANCELLED->label(),
            self::NO_SHOW->value => self::NO_SHOW->label(),
        ];
    }
}
