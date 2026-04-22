<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visit;

class VisitPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_visits') || $user->hasRole('admin') || $user->hasRole('doctor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Visit $visit): bool
    {
        // Admin can view all visits
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can view visits
        if ($user->can('view_visits')) {
            return true;
        }

        // Doctors can view visits they are assigned to
        if ($user->hasRole('doctor') && $user->staff) {
            return $visit->staff_id === $user->staff->id;
        }

        // Patients can view their own visits
        if ($user->hasRole('patient') && $user->patient) {
            return $visit->patient_id === $user->patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_visits') || $user->hasRole('doctor') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Visit $visit): bool
    {
        // Admin can update all visits
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can update visits
        if ($user->can('edit_visits')) {
            return true;
        }

        // Doctors can update visits they are assigned to
        if ($user->hasRole('doctor') && $user->staff) {
            return $visit->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Visit $visit): bool
    {
        return $user->can('delete_visits');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Visit $visit): bool
    {
        return false;
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function cancel(User $user, Visit $visit): bool
    {
        return $user->can('cancel_visits') || $user->hasRole('receptionist') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Visit $visit): bool
    {
        return false;
    }
}
