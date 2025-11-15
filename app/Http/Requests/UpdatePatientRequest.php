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
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'date_of_birth' => 'sometimes|date|before:today',
            'gender' => 'sometimes|in:male,female,other',
            'address' => 'sometimes|string|max:500',
            'phone_number' => 'sometimes|string|max:20',
            'email' => 'nullable|email|unique:patients,email,'.$patientId,
            'insurance_info' => 'nullable|string|max:1000',
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
