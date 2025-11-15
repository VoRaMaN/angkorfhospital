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
        if ($user->hasRole('Doctor')) {
            return $medicalOrder->staff_id === $user->staff->id;
        }

        // Patients can view their own medical orders
        if ($user->hasRole('Patient')) {
            return $medicalOrder->patient_id === $user->patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_medical_orders') || $user->hasRole('Doctor') || $user->hasRole('admin');
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
        if ($user->hasRole('Doctor')) {
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
}
