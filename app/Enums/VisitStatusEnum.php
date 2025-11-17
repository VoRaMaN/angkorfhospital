<?php

namespace App\Enums;

enum VisitStatusEnum: string
{
    case PENDING = 'pending';
    case AWAITING_ASSIGNMENT = 'awaiting_assignment';
    case ASSIGNED = 'assigned';
    case IN_PROGRESS = 'in_progress';
    case SENT_BACK = 'sent_back';
    case AWAITING_ACCOUNTANT = 'awaiting_accountant';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::AWAITING_ASSIGNMENT => 'Awaiting Assignment',
            self::ASSIGNED => 'Assigned',
            self::IN_PROGRESS => 'In Progress',
            self::SENT_BACK => 'Sent Back',
            self::AWAITING_ACCOUNTANT => 'Awaiting Accountant',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::AWAITING_ASSIGNMENT => 'bg-orange-100 text-orange-800',
            self::ASSIGNED => 'bg-purple-100 text-purple-800',
            self::IN_PROGRESS => 'bg-blue-100 text-blue-800',
            self::SENT_BACK => 'bg-red-100 text-red-800',
            self::AWAITING_ACCOUNTANT => 'bg-cyan-100 text-cyan-800',
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::CANCELLED => 'bg-red-100 text-red-800',
        };
    }
}
