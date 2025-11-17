<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBillingRequest extends FormRequest
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
        return [
            'appointment_id' => 'nullable|exists:appointments,id|unique:billings,appointment_id',
            'visit_id' => 'nullable|exists:visits,id',
            'medical_order_id' => 'nullable|exists:medical_orders,id',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'status' => 'required|in:pending,paid,overdue,written_off',
            'billing_date' => 'required|date|before_or_equal:today',
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
            'visit_id.exists' => 'The selected visit is invalid.',
            'medical_order_id.exists' => 'The selected medical order is invalid.',
            'billing_date.before_or_equal' => 'Billing date cannot be in the future.',
            'status.in' => 'Status must be one of: pending, paid, overdue, written_off.',
        ];
    }
}
