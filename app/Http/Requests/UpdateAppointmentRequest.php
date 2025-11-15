<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $appointment = $this->route('appointment');
        $user = $this->user();

        // Admins can update any appointment
        if ($user->staff?->role?->name === 'Admin') {
            return true;
        }

        // Staff can update appointments they are assigned to
        if ($user->staff && $appointment->staff_id === $user->staff->id) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => 'sometimes|exists:patients,id',
            'staff_id' => 'sometimes|exists:staff,id',
            'appointment_date_time' => 'sometimes|date|after:now',
            'status' => 'sometimes|in:scheduled,confirmed,completed,cancelled',
            'reason_for_visit' => 'sometimes|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'appointment_date_time.after' => 'Appointment date and time must be in the future.',
            'status.in' => 'Status must be one of: scheduled, confirmed, completed, cancelled.',
        ];
    }
}
