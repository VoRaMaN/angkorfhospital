<?php

namespace App\Enums;

enum AppointmentTypeEnum: string
{
    case CONSULTATION = 'consultation';
    case EMERGENCY = 'emergency';
    case FOLLOW_UP = 'follow_up';
    case PROCEDURE = 'procedure';
    case CHECKUP = 'checkup';
    case TELEMEDICINE = 'telemedicine';
    case SCREENING = 'screening';
    case THERAPY = 'therapy';

    public function label(): string
    {
        return match ($this) {
            self::CONSULTATION => 'Consultation',
            self::EMERGENCY => 'Emergency',
            self::FOLLOW_UP => 'Follow-up',
            self::PROCEDURE => 'Procedure',
            self::CHECKUP => 'Check-up',
            self::TELEMEDICINE => 'Telemedicine',
            self::SCREENING => 'Screening',
            self::THERAPY => 'Therapy',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CONSULTATION => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            self::EMERGENCY => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            self::FOLLOW_UP => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::PROCEDURE => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
            self::CHECKUP => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            self::TELEMEDICINE => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
            self::SCREENING => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
            self::THERAPY => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
        };
    }

    public function isUrgent(): bool
    {
        return match ($this) {
            self::EMERGENCY => true,
            default => false,
        };
    }

    public function requiresPreparation(): bool
    {
        return match ($this) {
            self::PROCEDURE, self::SCREENING => true,
            default => false,
        };
    }
}
