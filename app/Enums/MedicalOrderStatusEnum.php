<?php

namespace App\Enums;

enum MedicalOrderStatusEnum: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case COMPLETED = 'complete';
    case CANCEL = 'cancel';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::PROCESSED => 'Processed',
            self::COMPLETED => 'Completed',
            self::CANCEL => 'Cancel',
            self::REJECTED => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::PROCESSING => 'bg-blue-100 text-blue-800',
            self::PROCESSED => 'bg-orange-100 text-orange-800',
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::CANCEL => 'bg-gray-100 text-gray-800',
            self::REJECTED => 'bg-red-100 text-red-800',
        };
    }
}
