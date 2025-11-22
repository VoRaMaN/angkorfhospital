<?php

namespace App\Policies;

use App\Models\MedicalOrderInventory;
use App\Models\User;

class MedicalOrderInventoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_medical_order_inventories') || $user->hasRole('admin') || $user->hasRole('doctor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicalOrderInventory $medicalOrderInventory): bool
    {
        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can view
        if ($user->can('view_medical_order_inventories')) {
            return true;
        }

        // Doctors can view for their orders
        if ($user->hasRole('doctor') && $user->staff) {
            return $medicalOrderInventory->medicalOrder->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_medical_order_inventories') || $user->hasRole('doctor') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalOrderInventory $medicalOrderInventory): bool
    {
        // Admin can update all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can update
        if ($user->can('edit_medical_order_inventories')) {
            return true;
        }

        // Doctors can update for their orders
        if ($user->hasRole('doctor') && $user->staff) {
            return $medicalOrderInventory->medicalOrder->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalOrderInventory $medicalOrderInventory): bool
    {
        return $user->can('delete_medical_order_inventories') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalOrderInventory $medicalOrderInventory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalOrderInventory $medicalOrderInventory): bool
    {
        return false;
    }
}
