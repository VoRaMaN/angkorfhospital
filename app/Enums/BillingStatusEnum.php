<?php

namespace App\Enums;

enum BillingStatusEnum: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case PARTIAL = 'partial';
    case WRITTEN_OFF = 'written_off';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
            self::PARTIAL => 'Partial',
            self::WRITTEN_OFF => 'Written Off',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::PAID => 'bg-green-100 text-green-800',
            self::OVERDUE => 'bg-red-100 text-red-800',
            self::PARTIAL => 'bg-blue-100 text-blue-800',
            self::WRITTEN_OFF => 'bg-gray-100 text-gray-800',
            self::CANCELLED => 'bg-red-100 text-red-800',
        };
    }

    public static function options(): array
    {
        return [
            self::PENDING->value => self::PENDING->label(),
            self::PAID->value => self::PAID->label(),
            self::OVERDUE->value => self::OVERDUE->label(),
            self::PARTIAL->value => self::PARTIAL->label(),
            self::WRITTEN_OFF->value => self::WRITTEN_OFF->label(),
            self::CANCELLED->value => self::CANCELLED->label(),
        ];
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PENDING, self::PARTIAL]);
    }

    public function isCompleted(): bool
    {
        return in_array($this, [self::PAID, self::OVERDUE, self::WRITTEN_OFF, self::CANCELLED]);
    }
}
