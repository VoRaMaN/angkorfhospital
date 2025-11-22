<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_patients') || $user->hasRole('admin') || $user->hasRole('doctor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Patient $patient): bool
    {
        // Admin can view all patients
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can view patients
        if ($user->can('view_patients')) {
            return true;
        }

        // Doctors can view patients they have appointments with
        if ($user->hasRole('doctor') && $user->staff) {
            return $patient->appointments()->where('staff_id', $user->staff->id)->exists();
        }

        // Patients can view their own record
        if ($user->hasRole('patient') && $user->patient) {
            return $user->patient->id === $patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_patients') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Patient $patient): bool
    {
        // Admin can update all patients
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can update patients
        if ($user->can('edit_patients')) {
            return true;
        }

        // Patients can update their own record
        if ($user->hasRole('patient') && $user->patient) {
            return $user->patient->id === $patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Patient $patient): bool
    {
        return $user->can('delete_patients') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Patient $patient): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Patient $patient): bool
    {
        return false;
    }
}
