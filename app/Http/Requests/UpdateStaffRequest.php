<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->staff?->role?->name === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $staffId = $this->route('staff')->id ?? null;

        return [
            'user_id' => 'sometimes|exists:users,id|unique:staff,user_id,'.$staffId,
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$this->route('staff')->user_id,
            'role_id' => 'sometimes|exists:roles,id',
            'department_id' => 'sometimes|nullable|exists:departments,id',
            'contact_number' => 'nullable|string|max:20',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.unique' => 'This user is already assigned to another staff member.',
        ];
    }
}
