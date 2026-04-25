<?php

namespace App\Http\Requests;

use App\Enums\MedicalOrderPriorityEnum;
use App\Enums\MedicalOrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $medicalOrder = $this->route('medical_order');
        $user = $this->user();

        // Admin users can update any medical order
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with process permission (e.g. nurses) can update during processing
        if ($user->can('process_medical_orders')) {
            return true;
        }

        // Only doctors can update medical orders
        if (! $user->hasRole('doctor')) {
            return false;
        }

        // Only the staff who ordered it can update the medical order
        return $medicalOrder->staff_id === $user->staff->id;
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
            'visit_id' => 'sometimes|nullable|exists:visits,id',
            'staff_id' => 'sometimes|exists:staff,id',
            'order_details' => 'sometimes|string|max:1000',
            'status' => 'sometimes|in:'.implode(',', array_column(MedicalOrderStatusEnum::cases(), 'value')),
            'priority' => 'sometimes|in:'.implode(',', array_column(MedicalOrderPriorityEnum::cases(), 'value')),
            'notes' => 'nullable|string|max:1000',
            'ordered_at' => 'sometimes|date',
            'completed_at' => 'nullable|date',
            'order_items' => 'nullable|array',
            'order_items.*.id' => 'nullable|exists:medical_order_inventory,id',
            'order_items.*.inventory_id' => 'nullable|exists:inventories,id',
            'order_items.*.item_type' => 'required_with:order_items|string|in:lab,rx_medicine,procedure,imaging,consultation,therapy,supply,special_item',
            'order_items.*.item_name' => 'required_with:order_items|string|max:255',
            'order_items.*.details' => 'nullable|string|max:1000',
            'order_items.*.dosage' => 'nullable|string|max:100',
            'order_items.*.frequency' => 'nullable|string|max:100',
            'order_items.*.route' => 'nullable|string|max:100',
            'order_items.*.quantity_required' => 'nullable|integer|min:1',
            'order_items.*.status' => 'nullable|in:'.implode(',', array_column(MedicalOrderStatusEnum::cases(), 'value')),
            'order_items.*.notes' => 'nullable|string|max:500',
        ];
    }
}
