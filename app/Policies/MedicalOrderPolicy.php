<?php

namespace App\Policies;

use App\Models\MedicalOrder;
use App\Models\User;

class MedicalOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_medical_orders') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicalOrder $medicalOrder): bool
    {
        // Admin can view all medical orders
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can view medical orders
        if ($user->can('view_medical_orders')) {
            return true;
        }

        // Doctors can view medical orders they created
        if ($user->hasRole('doctor')) {
            return $medicalOrder->staff_id === $user->staff->id;
        }

        // Patients can view their own medical orders
        if ($user->hasRole('patient')) {
            return $medicalOrder->patient_id === $user->patient?->id ||
                   ($medicalOrder->visit && $medicalOrder->visit->patient_id === $user->patient?->id);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_medical_orders') || $user->hasRole('doctor') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalOrder $medicalOrder): bool
    {
        // Admin can update all medical orders
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can update medical orders
        if ($user->can('edit_medical_orders')) {
            return true;
        }

        // Only the ordering staff can update
        if ($user->hasRole('doctor')) {
            return $medicalOrder->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalOrder $medicalOrder): bool
    {
        return $user->can('delete_medical_orders') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalOrder $medicalOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalOrder $medicalOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can process the medical order.
     */
    public function process(User $user, MedicalOrder $medicalOrder): bool
    {
        // Admin can process all medical orders
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can process medical orders
        if ($user->can('process_medical_orders')) {
            return true;
        }

        // Only the ordering staff can process
        if ($user->hasRole('doctor')) {
            return $medicalOrder->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can process and bill the medical order.
     */
    public function processAndBill(User $user, MedicalOrder $medicalOrder): bool
    {
        // Admin can process and bill all medical orders
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can process and bill medical orders
        if ($user->can('process_and_bill_medical_orders')) {
            return true;
        }

        // Only the ordering staff can process and bill
        if ($user->hasRole('doctor')) {
            return $medicalOrder->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can confirm processed medical order.
     */
    public function confirmProcessed(User $user, MedicalOrder $medicalOrder): bool
    {
        // Admin can confirm all medical orders
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can confirm processed medical orders
        if ($user->can('confirm_processed_medical_orders')) {
            return true;
        }

        // Only the ordering staff can confirm
        if ($user->hasRole('doctor')) {
            return $medicalOrder->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can complete the medical order.
     */
    public function complete(User $user, MedicalOrder $medicalOrder): bool
    {
        // Admin can complete all medical orders
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can complete medical orders
        if ($user->can('complete_medical_orders')) {
            return true;
        }

        // Only the ordering staff can complete
        if ($user->hasRole('doctor')) {
            return $medicalOrder->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can complete individual order items.
     */
    public function completeItem(User $user, MedicalOrder $medicalOrder): bool
    {
        // Admin can complete all order items
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can complete order items
        if ($user->can('complete_medical_order_items')) {
            return true;
        }

        // Only the ordering staff can complete items
        if ($user->hasRole('doctor')) {
            return $medicalOrder->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can send back the medical order for revision.
     */
    public function sendBack(User $user, MedicalOrder $medicalOrder): bool
    {
        // Admin can send back all medical orders
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can send back medical orders
        if ($user->can('send_back_medical_orders')) {
            return true;
        }

        // Only the ordering staff can send back
        if ($user->hasRole('doctor')) {
            return $medicalOrder->staff_id === $user->staff->id;
        }

        return false;
    }
}
