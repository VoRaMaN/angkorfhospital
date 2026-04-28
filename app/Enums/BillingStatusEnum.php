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
    case REVISION = 'revision';
    case REVISED = 'revised';
    case SENT_TO_ACCOUNT = 'sent_to_account';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
            self::PARTIAL => 'Partial',
            self::WRITTEN_OFF => 'Written Off',
            self::CANCELLED => 'Cancelled',
            self::REVISION => 'Revision',
            self::REVISED => 'Revised',
            self::SENT_TO_ACCOUNT => 'Sent to Account',
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
            self::REVISION => 'bg-amber-100 text-amber-800',
            self::REVISED => 'bg-blue-100 text-blue-800',
            self::SENT_TO_ACCOUNT => 'bg-teal-100 text-teal-800',
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
            self::REVISION->value => self::REVISION->label(),
            self::REVISED->value => self::REVISED->label(),
            self::SENT_TO_ACCOUNT->value => self::SENT_TO_ACCOUNT->label(),
        ];
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PENDING, self::PARTIAL, self::REVISION, self::REVISED, self::SENT_TO_ACCOUNT]);
    }

    public function isCompleted(): bool
    {
        return in_array($this, [self::PAID, self::OVERDUE, self::WRITTEN_OFF, self::CANCELLED]);
    }
}
