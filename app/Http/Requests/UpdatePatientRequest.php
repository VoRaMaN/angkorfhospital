<?php

namespace App\Http\Requests;

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
        $patientId = $this->route('patient')->id ?? null;

        $rules = [
            'create_user_account' => 'boolean',
            'title' => 'nullable|string|max:50',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'native_name' => 'nullable|string|max:255',
            'native_surname' => 'nullable|string|max:255',
            'date_of_birth' => 'sometimes|date|before:today',
            'identification_number' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:100',
            'race' => 'nullable|string|max:100',
            'gender' => 'sometimes|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'address_building_village' => 'nullable|string|max:255',
            'address_moo' => 'nullable|string|max:50',
            'address_soi' => 'nullable|string|max:100',
            'address_road' => 'nullable|string|max:100',
            'address_sub_district' => 'nullable|string|max:100',
            'address_district' => 'nullable|string|max:100',
            'address_province' => 'nullable|string|max:100',
            'address_zip_code' => 'nullable|string|max:20',
            'phone_number' => 'sometimes|string|max:20',
            'home_phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:patients,email,'.$patientId,
            'occupation' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'company_phone_number' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'emergency_contact_description' => 'nullable|string|max:255',
            'emergency_contact_same_address' => 'boolean',
            'emergency_contact_address' => 'nullable|string|max:500',
            'emergency_contact_road' => 'nullable|string|max:100',
            'emergency_contact_sub_district' => 'nullable|string|max:100',
            'emergency_contact_district' => 'nullable|string|max:100',
            'emergency_contact_province' => 'nullable|string|max:100',
            'emergency_contact_zip_code' => 'nullable|string|max:20',
            'emergency_contact_home_phone' => 'nullable|string|max:20',
            'emergency_contact_mobile_phone' => 'nullable|string|max:20',
            'emergency_contact_email' => 'nullable|email|max:255',
            'payment_method' => 'nullable|string|max:50',
            'contract_name' => 'nullable|string|max:255',
            'insurance_name' => 'nullable|string|max:255',
            'insurance_info' => 'nullable|string|max:1000',
            'agent_name' => 'nullable|string|max:255',
            'patient_type' => 'nullable|string|max:50',
        ];

        // Add user account validation only if creating user account for patient without existing user
        if ($this->boolean('create_user_account') && ! $this->route('patient')->user_id) {
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
            'user_id.unique' => 'This user is already registered as another patient.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'gender.in' => 'Gender must be male, female, or other.',
        ];
    }
}
