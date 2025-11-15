<?php

namespace App\Enums;

enum AppointmentStatusEnum: string
{
    case SCHEDULED = 'scheduled';
    case CONFIRMED = 'confirmed';
    case ARRIVED = 'arrived';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no-show';
    case RESCHEDULED = 'rescheduled';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Scheduled',
            self::CONFIRMED => 'Confirmed',
            self::ARRIVED => 'Arrived',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
            self::NO_SHOW => 'No Show',
            self::RESCHEDULED => 'Rescheduled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SCHEDULED => 'bg-blue-100 text-blue-800',
            self::CONFIRMED => 'bg-green-100 text-green-800',
            self::ARRIVED => 'bg-purple-100 text-purple-800',
            self::IN_PROGRESS => 'bg-orange-100 text-orange-800',
            self::COMPLETED => 'bg-emerald-100 text-emerald-800',
            self::CANCELLED => 'bg-red-100 text-red-800',
            self::NO_SHOW => 'bg-gray-100 text-gray-800',
            self::RESCHEDULED => 'bg-yellow-100 text-yellow-800',
        };
    }

    public static function options(): array
    {
        return [
            self::SCHEDULED->value => self::SCHEDULED->label(),
            self::CONFIRMED->value => self::CONFIRMED->label(),
            self::ARRIVED->value => self::ARRIVED->label(),
            self::IN_PROGRESS->value => self::IN_PROGRESS->label(),
            self::COMPLETED->value => self::COMPLETED->label(),
            self::CANCELLED->value => self::CANCELLED->label(),
            self::NO_SHOW->value => self::NO_SHOW->label(),
            self::RESCHEDULED->value => self::RESCHEDULED->label(),
        ];
    }

    public function isActive(): bool
    {
        return in_array($this, [self::SCHEDULED, self::CONFIRMED, self::ARRIVED, self::IN_PROGRESS]);
    }

    public function isCompleted(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED, self::NO_SHOW]);
    }
}
