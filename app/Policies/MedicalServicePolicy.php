<?php

namespace App\Policies;

use App\Models\MedicalService;
use App\Models\User;

class MedicalServicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_medical_services') || $user->hasRole('admin') || $user->hasRole('Doctor') || $user->hasRole('nurse');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicalService $medicalService): bool
    {
        // Admin can view all medical services
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can view medical services
        if ($user->can('view_medical_services')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_medical_services') || $user->hasRole('admin') || $user->hasRole('Doctor') || $user->hasRole('nurse');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalService $medicalService): bool
    {
        // Admin can update all medical services
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can update medical services
        if ($user->can('edit_medical_services')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalService $medicalService): bool
    {
        return $user->can('delete_medical_services') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalService $medicalService): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalService $medicalService): bool
    {
        return false;
    }
}
