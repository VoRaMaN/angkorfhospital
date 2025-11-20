<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'first_name',
        'last_name',
        'native_name',
        'native_surname',
        'date_of_birth',
        'identification_number',
        'marital_status',
        'nationality',
        'religion',
        'race',
        'gender',
        'address',
        'address_building_village',
        'address_moo',
        'address_soi',
        'address_road',
        'address_sub_district',
        'address_district',
        'address_province',
        'address_zip_code',
        'phone_number',
        'home_phone_number',
        'email',
        'occupation',
        'company_name',
        'company_phone_number',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_description',
        'emergency_contact_same_address',
        'emergency_contact_address',
        'emergency_contact_road',
        'emergency_contact_sub_district',
        'emergency_contact_district',
        'emergency_contact_province',
        'emergency_contact_zip_code',
        'emergency_contact_home_phone',
        'emergency_contact_mobile_phone',
        'emergency_contact_email',
        'payment_method',
        'contract_name',
        'insurance_name',
        'insurance_info',
        'agent_name',
        'patient_type',
    ];

    protected $appends = ['name'];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function getNameAttribute(): string
    {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
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

    public function medicalRecords()
    {
        return $this->hasManyThrough(MedicalRecord::class, Appointment::class, 'patient_id', 'appointment_id');
    }
}
