<?php

namespace App\Http\Requests;

use App\Enums\MedicalOrderPriorityEnum;
use App\Enums\MedicalOrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalOrderRequest extends FormRequest
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
            'patient_id' => 'nullable|exists:patients,id',
            'staff_id' => 'nullable|exists:staff,id',
            'order_details' => 'required|string|max:1000',
            'priority' => 'required|in:'.implode(',', array_column(MedicalOrderPriorityEnum::cases(), 'value')),
            'notes' => 'nullable|string|max:1000',
            'ordered_at' => 'required|date|before_or_equal:now',
            'order_items' => 'nullable|array|min:1',
            'order_items.*.inventory_id' => 'nullable|exists:inventories,id',
            'order_items.*.item_type' => 'required_with:order_items|string|in:lab,rx_medicine,procedure,imaging,consultation,therapy,supply',
            'order_items.*.item_name' => 'required_with:order_items|string|max:255',
            'order_items.*.details' => 'nullable|string|max:1000',
            'order_items.*.dosage' => 'nullable|string|max:100',
            'order_items.*.frequency' => 'nullable|string|max:100',
            'order_items.*.route' => 'nullable|string|max:100',
            'order_items.*.quantity_required' => 'nullable|integer|min:1',
            'order_items.*.notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => MedicalOrderStatusEnum::PENDING->value,
        ]);
    }
}
