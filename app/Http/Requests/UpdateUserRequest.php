<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->staff?->role?->name === 'Admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;

        $rules = [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$userId,
            'password' => 'nullable|string|min:8|confirmed',
            'type' => 'sometimes|in:staff,doctor,patient',
        ];

        // Add type-specific validation rules
        switch ($this->input('type')) {
            case 'staff':
                $rules = array_merge($rules, [
                    'first_name' => 'sometimes|string|max:255',
                    'last_name' => 'sometimes|string|max:255',
                    'role_id' => 'sometimes|exists:roles,id',
                    'contact_number' => 'nullable|string|max:20',
                    'hire_date' => 'sometimes|date|before_or_equal:today',
                ]);
                break;

            case 'doctor':
                $rules = array_merge($rules, [
                    'first_name' => 'sometimes|string|max:255',
                    'last_name' => 'sometimes|string|max:255',
                    'role_id' => 'sometimes|exists:roles,id',
                    'contact_number' => 'nullable|string|max:20',
                    'hire_date' => 'sometimes|date|before_or_equal:today',
                    'specialization' => 'sometimes|string|max:255',
                    'department_id' => 'sometimes|exists:departments,id',
                    'license_number' => 'sometimes|string|max:50|unique:doctors,license_number,'.($this->route('user')->doctor?->id ?? 'null'),
                ]);
                break;

            case 'patient':
                $rules = array_merge($rules, [
                    'first_name' => 'sometimes|string|max:255',
                    'last_name' => 'sometimes|string|max:255',
                    'date_of_birth' => 'sometimes|date|before:today',
                    'gender' => 'sometimes|in:male,female,other',
                    'address' => 'sometimes|string|max:500',
                    'phone_number' => 'sometimes|string|max:20',
                    'insurance_info' => 'nullable|string|max:1000',
                ]);
                break;
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'type.in' => 'User type must be staff, doctor, or patient.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'gender.in' => 'Gender must be male, female, or other.',
            'license_number.unique' => 'This license number is already registered.',
        ];
    }
}
