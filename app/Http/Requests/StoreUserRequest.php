<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'type' => 'required|in:staff,doctor,patient',
        ];

        // Add type-specific validation rules
        switch ($this->input('type')) {
            case 'staff':
                $rules = array_merge($rules, [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'role_id' => 'required|exists:roles,id',
                    'contact_number' => 'nullable|string|max:20',
                    'hire_date' => 'required|date|before_or_equal:today',
                ]);
                break;

            case 'doctor':
                $rules = array_merge($rules, [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'role_id' => 'required|exists:roles,id',
                    'contact_number' => 'nullable|string|max:20',
                    'hire_date' => 'required|date|before_or_equal:today',
                    'specialization' => 'required|string|max:255',
                    'department_id' => 'required|exists:departments,id',
                    'license_number' => 'required|string|max:50|unique:doctors,license_number',
                ]);
                break;

            case 'patient':
                $rules = array_merge($rules, [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'date_of_birth' => 'required|date|before:today',
                    'gender' => 'required|in:male,female,other',
                    'address' => 'required|string|max:500',
                    'phone_number' => 'required|string|max:20',
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
