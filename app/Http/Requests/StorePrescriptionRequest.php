<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
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
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:staff,id',
            'medication_id' => 'required|exists:medications,id',
            'dosage' => 'required|string|max:100',
            'frequency' => 'nullable|string|max:100',
            'duration' => 'nullable|string|max:100',
            'instructions' => 'required|string|max:1000',
            'prescribed_date' => 'required|date|before_or_equal:today',
        ];
    }
}
