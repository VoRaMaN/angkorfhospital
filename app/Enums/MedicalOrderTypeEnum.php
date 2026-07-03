<?php

namespace App\Enums;

enum MedicalOrderTypeEnum: string
{
    case LAB = 'lab';
    case PROCEDURE = 'procedure';
    case REFERRAL = 'referral';
    case THERAPY = 'therapy';
    case IMAGING = 'imaging';
    case CONSULTATION = 'consultation';

    public function label(): string
    {
        return match ($this) {
            self::LAB => 'Lab Order',
            self::PROCEDURE => 'Procedure',
            self::REFERRAL => 'Referral',
            self::THERAPY => 'Therapy',
            self::IMAGING => 'Imaging',
            self::CONSULTATION => 'Consultation',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LAB => 'bg-blue-100 text-blue-800',
            self::PROCEDURE => 'bg-green-100 text-green-800',
            self::REFERRAL => 'bg-purple-100 text-purple-800',
            self::THERAPY => 'bg-orange-100 text-orange-800',
            self::IMAGING => 'bg-cyan-100 text-cyan-800',
            self::CONSULTATION => 'bg-indigo-100 text-indigo-800',
        };
    }

    public static function options(): array
    {
        return [
            self::LAB->value => self::LAB->label(),
            self::PROCEDURE->value => self::PROCEDURE->label(),
            self::REFERRAL->value => self::REFERRAL->label(),
            self::THERAPY->value => self::THERAPY->label(),
            self::IMAGING->value => self::IMAGING->label(),
            self::CONSULTATION->value => self::CONSULTATION->label(),
        ];
    }
}
