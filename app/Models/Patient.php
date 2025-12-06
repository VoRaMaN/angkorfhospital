<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            $year = date('y'); // 2-digit year, e.g., '25'
            $lastPatient = static::where('id', 'like', $year.'/%')
                ->orderByRaw('CAST(SUBSTRING(id, 4) AS UNSIGNED) DESC')
                ->first();

            $number = $lastPatient ? intval(substr($lastPatient->id, 3)) + 1 : 1;
            $patient->id = $year.'/'.str_pad($number, 10, '0', STR_PAD_LEFT);
        });
    }

    protected $fillable = [
        'user_id',
        'title',
        'name',
        'surname',
        'khmer_china_name',
        'khmer_china_surname',
        'date_of_birth_day',
        'date_of_birth_month',
        'date_of_birth_year',
        'gender',
        'id_card_or_passport',
        'marital_status',
        'nationality',
        'religion',
        'race',

        // Address
        'address',
        'building_village',
        'moo',
        'soi',
        'road',
        'sub_district',
        'district',
        'province',
        'zip_code',

        // Contact
        'home_phone',
        'mobile_phone',
        'email',
        'occupation',
        'company_name',
        'company_phone',

        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_description_other',
        'emergency_contact_address_same_as_patient',
        'emergency_contact_address',
        'emergency_contact_road',
        'emergency_contact_sub_district',
        'emergency_contact_district',
        'emergency_contact_province',
        'emergency_contact_zip_code',
        'emergency_contact_home_phone',
        'emergency_contact_mobile_phone',
        'emergency_contact_email',

        // Payment
        'payment_method',
        'contract_name',
        'insurance_name',
        'staff_id',
        'patient_type',
    ];

    protected $appends = ['full_name'];

    protected function casts(): array
    {
        return [
            //
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->name ?? '').' '.($this->surname ?? ''));
    }

    public function getPhoneNumberAttribute(): ?string
    {
        return $this->mobile_phone ?? $this->home_phone;
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasManyThrough(File::class, PatientFile::class);
    }

    public function patientFiles()
    {
        return $this->hasMany(PatientFile::class);
    }

    public function medicalOrders()
    {
        return $this->hasMany(MedicalOrder::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
