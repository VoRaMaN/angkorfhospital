<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
        return [
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'nullable|exists:staff,id',
            'appointment_date_time' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'appointment_type' => 'required|in:consultation,emergency,follow_up,procedure,checkup,telemedicine,screening,therapy',
            'reason_for_visit' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'appointment_date_time.after' => 'Appointment date and time must be in the future.',
            'duration_minutes.min' => 'Duration must be at least 15 minutes.',
            'duration_minutes.max' => 'Duration cannot exceed 8 hours (480 minutes).',
            'appointment_type.in' => 'Please select a valid appointment type.',
            'notes.max' => 'Notes cannot exceed 2000 characters.',
        ];
    }
}
