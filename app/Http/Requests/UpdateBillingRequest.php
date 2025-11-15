<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBillingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()->staff?->role?->name, ['Admin', 'Billing']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $billingId = $this->route('billing')->id ?? null;

        return [
            'appointment_id' => 'sometimes|exists:appointments,id|unique:billings,appointment_id,'.$billingId,
            'amount' => 'sometimes|numeric|min:0|max:999999.99',
            'status' => 'sometimes|in:pending,paid,overdue,written_off',
            'billing_date' => 'sometimes|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'appointment_id.unique' => 'A billing record already exists for this appointment.',
            'billing_date.before_or_equal' => 'Billing date cannot be in the future.',
            'status.in' => 'Status must be one of: pending, paid, overdue, written_off.',
        ];
    }
}
