<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatchRequest extends FormRequest
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
            'package_item_ids' => ['sometimes', 'array'],
            'package_item_ids.*' => ['integer', 'exists:package_items,id'],
            'special_item_ids' => ['sometimes', 'array'],
            'special_item_ids.*' => ['integer', 'exists:special_items,id'],
            'lab_item_ids' => ['sometimes', 'array'],
            'lab_item_ids.*' => ['integer', 'exists:lab_items,id'],
            'medicine_ids' => ['sometimes', 'array'],
            'medicine_ids.*' => ['integer', 'exists:medicines,id'],
            'rx_medicine_ids' => ['sometimes', 'array'],
            'rx_medicine_ids.*' => ['integer', 'exists:rx_medicines,id'],
        ];
    }
}
