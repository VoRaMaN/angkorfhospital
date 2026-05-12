<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLabPackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin', 'lab']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|in:'.implode(',', \App\Models\LabPackage::getCategories()),
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'is_active' => 'boolean',
            'inventory_items' => 'sometimes|required|array|min:1',
            'inventory_items.*.inventory_id' => 'required|exists:inventories,id',
            'inventory_items.*.quantity_required' => 'required|integer|min:1',
            'inventory_items.*.notes' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'inventory_items.required' => 'At least one inventory item is required for the lab package.',
            'inventory_items.min' => 'At least one inventory item is required for the lab package.',
            'inventory_items.*.inventory_id.required' => 'Inventory item is required.',
            'inventory_items.*.inventory_id.exists' => 'Selected inventory item does not exist.',
            'inventory_items.*.quantity_required.required' => 'Quantity required is required.',
            'inventory_items.*.quantity_required.min' => 'Quantity required must be at least 1.',
        ];
    }
}
