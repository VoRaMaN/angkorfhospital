<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'custom_price' => ['nullable', 'numeric', 'min:0'],
            'inventory_items' => ['sometimes', 'array'],
            'inventory_items.*.id' => ['required', 'integer', 'exists:inventories,id'],
            'inventory_items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
