<?php

namespace App\Enums;

enum StaffFileTypeEnum: string
{
    case CV = 'cv';
    case CERTIFICATE = 'certificate';
    case LICENSE = 'license';
    case PHOTO = 'photo';
    case CONTRACT = 'contract';

    public function label(): string
    {
        return match ($this) {
            self::CV => 'CV',
            self::CERTIFICATE => 'Certificate',
            self::LICENSE => 'License',
            self::PHOTO => 'Photo',
            self::CONTRACT => 'Contract',
        };
    }

    public static function options(): array
    {
        return [
            self::CV->value => self::CV->label(),
            self::CERTIFICATE->value => self::CERTIFICATE->label(),
            self::LICENSE->value => self::LICENSE->label(),
            self::PHOTO->value => self::PHOTO->label(),
            self::CONTRACT->value => self::CONTRACT->label(),
        ];
    }
}
