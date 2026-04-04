<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()->staff?->role?->name, ['admin', 'inventory']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'type_of_supply' => 'sometimes|in:'.implode(',', array_keys(\App\Enums\SupplyTypeEnum::options())),
            'quantity' => 'sometimes|integer|min:0',
            'unit' => 'sometimes|string|max:50',
            'dose_unit' => 'nullable|string|max:50',
            'total_per_box' => 'nullable|integer|min:0',
            'minimum_stock' => 'sometimes|integer|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'alert_days' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'expiry_date.after' => 'Expiry date must be in the future.',
        ];
    }
}
