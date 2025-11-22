<?php

namespace App\Policies;

use App\Models\Billing;
use App\Models\User;

class BillingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_billing') || $user->hasRole('admin') || $user->hasRole('doctor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Billing $billing): bool
    {
        // Admin can view all billings
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can view billings
        if ($user->can('view_billing')) {
            return true;
        }

        // Doctors can view billings for their appointments
        if ($user->hasRole('doctor') && $user->staff) {
            return $billing->appointment->staff_id === $user->staff->id;
        }

        // Patients can view their own billings
        if ($user->hasRole('patient') && $user->patient) {
            return $billing->appointment->patient_id === $user->patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_billing') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Billing $billing): bool
    {
        return $user->can('edit_billing') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the billing status.
     */
    public function updateStatus(User $user, Billing $billing): bool
    {
        return $user->can('update_billing_status') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Billing $billing): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Billing $billing): bool
    {
        return false;
    }
}
