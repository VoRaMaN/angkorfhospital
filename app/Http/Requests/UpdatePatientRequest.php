<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->staff !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $patient = Patient::find($this->input('patient'));
        $patientId = $patient ? $patient->id : null;

        $rules = [
            'create_user_account' => 'boolean',
            'title' => 'nullable|in:Mr.,Mrs.,Ms.',
            'name' => 'sometimes|string|max:255',
            'surname' => 'sometimes|string|max:255',
            'khmer_china_name' => 'nullable|string|max:255',
            'khmer_china_surname' => 'nullable|string|max:255',
            'date_of_birth_day' => 'sometimes|integer|min:1|max:31',
            'date_of_birth_month' => 'sometimes|integer|min:1|max:12',
            'date_of_birth_year' => 'sometimes|integer|min:1900|max:'.(date('Y') - 1),
            'gender' => 'nullable|in:Male,Female,Other',
            'id_card_or_passport' => 'nullable|string|max:50',
            'marital_status' => 'nullable|in:Single,Married,Divorced,Widowed',
            'nationality' => 'required|string|max:100',
            'religion' => 'nullable|string|max:100',
            'race' => 'nullable|string|max:100',

            // Address
            'address' => 'nullable|string|max:500',
            'building_village' => 'nullable|string|max:255',
            'moo' => 'nullable|string|max:50',
            'soi' => 'nullable|string|max:100',
            'road' => 'nullable|string|max:100',
            'sub_district' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',

            // Contact
            'home_phone' => 'nullable|string|max:20',
            'mobile_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:patients,email,'.$patientId,
            'occupation' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'company_phone' => 'nullable|string|max:20',

            // Emergency Contact
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|in:Spouse,Parent,Sibling,Friend,Other',
            'emergency_contact_description_other' => 'nullable|string|max:255',
            'emergency_contact_address_same_as_patient' => 'nullable|boolean',
            'emergency_contact_address' => 'nullable|string|max:500',
            'emergency_contact_road' => 'nullable|string|max:100',
            'emergency_contact_sub_district' => 'nullable|string|max:100',
            'emergency_contact_district' => 'nullable|string|max:100',
            'emergency_contact_province' => 'nullable|string|max:100',
            'emergency_contact_zip_code' => 'nullable|string|max:20',
            'emergency_contact_home_phone' => 'nullable|string|max:20',
            'emergency_contact_mobile_phone' => 'nullable|string|max:20',
            'emergency_contact_email' => 'nullable|email',

            // Payment
            'payment_method' => 'nullable|in:Corporate Contract,Self-Pay,Insurance',
            'contract_name' => 'nullable|string|max:255',
            'insurance_name' => 'nullable|string|max:255',
            'staff_id' => 'nullable|exists:staff,id',
            'patient_type' => 'nullable|in:Patient,Customer,Dependent',
        ];

        // Add user account validation only if creating user account for patient without existing user
        if ($this->boolean('create_user_account') && $patient && ! $patient->user_id) {
            $rules = array_merge($rules, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
            ]);
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered for another patient.',
            'date_of_birth_year.min' => 'Year of birth must be at least 1900.',
            'date_of_birth_year.max' => 'Year of birth must be in the past.',
        ];
    }
}
