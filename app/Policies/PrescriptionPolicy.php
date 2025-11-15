<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_prescriptions') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Prescription $prescription): bool
    {
        // Admin can view all prescriptions
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can view prescriptions
        if ($user->can('view_prescriptions')) {
            return true;
        }

        // Doctors can view prescriptions they created
        if ($user->hasRole('Doctor')) {
            return $prescription->doctor_id === $user->staff->id;
        }

        // Patients can view their own prescriptions
        if ($user->hasRole('Patient')) {
            return $prescription->patient_id === $user->patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_prescriptions') || $user->hasRole('Doctor') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Prescription $prescription): bool
    {
        // Admin can update all prescriptions
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can update prescriptions
        if ($user->can('edit_prescriptions')) {
            return true;
        }

        // Only the prescribing doctor can update
        if ($user->hasRole('Doctor')) {
            return $prescription->doctor_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Prescription $prescription): bool
    {
        return $user->can('delete_prescriptions') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Prescription $prescription): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Prescription $prescription): bool
    {
        return false;
    }
}
