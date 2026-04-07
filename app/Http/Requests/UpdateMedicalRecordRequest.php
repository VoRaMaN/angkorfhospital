<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $medicalRecord = $this->route('medical_record');
        $user = $this->user();

        // Admin users can update any medical record
        if ($user->hasRole('admin')) {
            return true;
        }

        // Only doctors can update medical records
        if (! $user->hasRole('doctor')) {
            return false;
        }

        // Only the doctor assigned to the appointment can update the medical record
        return $medicalRecord->appointment->staff_id === $user->staff->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $medicalRecordId = $this->route('medical_record')->id ?? null;

        return [
            'appointment_id' => 'sometimes|nullable|exists:appointments,id|unique:medical_records,appointment_id,'.$medicalRecordId.',id',
            'diagnosis' => 'sometimes|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'treatment' => 'nullable|string|max:1000',
            'date_of_service' => 'sometimes|date|before_or_equal:today',
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
