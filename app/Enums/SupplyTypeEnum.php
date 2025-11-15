<?php

namespace App\Enums;

enum SupplyTypeEnum: string
{
    case MEDICATION = 'medication';
    case RX_MEDICINE = 'rx_medicine';
    case LAB_SUPPLY = 'lab_supply';
    case MEDICAL_EQUIPMENT = 'medical_equipment';
    case OFFICE_SUPPLY = 'office_supply';
    case CLEANING_SUPPLY = 'cleaning_supply';

    public function label(): string
    {
        return match ($this) {
            self::MEDICATION => 'Medication',
            self::RX_MEDICINE => 'Rx Medicine',
            self::LAB_SUPPLY => 'Lab Supply',
            self::MEDICAL_EQUIPMENT => 'Medical Equipment',
            self::OFFICE_SUPPLY => 'Office Supply',
            self::CLEANING_SUPPLY => 'Cleaning Supply',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [
            $case->value => $case->label(),
        ])->toArray();
    }
}
