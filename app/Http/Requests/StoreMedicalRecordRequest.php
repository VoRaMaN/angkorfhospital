<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('Doctor') || $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appointment_id' => 'required|exists:appointments,id|unique:medical_records,appointment_id',
            'diagnosis' => 'required|string|max:1000',
            'treatment' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'date_of_service' => 'required|date|before_or_equal:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'appointment_id.unique' => 'A medical record already exists for this appointment.',
            'date_of_service.before_or_equal' => 'Date of service cannot be in the future.',
        ];
    }
}
