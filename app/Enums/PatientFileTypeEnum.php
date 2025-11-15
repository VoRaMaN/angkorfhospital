<?php

namespace App\Enums;

enum PatientFileTypeEnum: string
{
    case MEDICAL_RECORD = 'medical_record';
    case LAB_RESULT = 'lab_result';
    case INSURANCE = 'insurance';
    case IDENTIFICATION = 'identification';
    case CONSENT_FORM = 'consent_form';
    case DISCHARGE_SUMMARY = 'discharge_summary';

    public function label(): string
    {
        return match ($this) {
            self::MEDICAL_RECORD => 'Medical Record',
            self::LAB_RESULT => 'Lab Result',
            self::INSURANCE => 'Insurance Document',
            self::IDENTIFICATION => 'Identification',
            self::CONSENT_FORM => 'Consent Form',
            self::DISCHARGE_SUMMARY => 'Discharge Summary',
        };
    }

    public static function options(): array
    {
        return [
            self::MEDICAL_RECORD->value => self::MEDICAL_RECORD->label(),
            self::LAB_RESULT->value => self::LAB_RESULT->label(),
            self::INSURANCE->value => self::INSURANCE->label(),
            self::IDENTIFICATION->value => self::IDENTIFICATION->label(),
            self::CONSENT_FORM->value => self::CONSENT_FORM->label(),
            self::DISCHARGE_SUMMARY->value => self::DISCHARGE_SUMMARY->label(),
        ];
    }
}
