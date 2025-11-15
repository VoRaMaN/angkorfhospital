<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_appointments') || $user->hasRole('admin') || $user->hasRole('Doctor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        // Admin can view all appointments
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can view appointments
        if ($user->can('view_appointments')) {
            return true;
        }

        // Doctors can view appointments they are assigned to
        if ($user->hasRole('Doctor') && $user->staff) {
            return $appointment->staff_id === $user->staff->id;
        }

        // Patients can view their own appointments
        if ($user->hasRole('Patient') && $user->patient) {
            return $appointment->patient_id === $user->patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_appointments') || $user->hasRole('Doctor') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        // Admin can update all appointments
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can update appointments
        if ($user->can('edit_appointments')) {
            return true;
        }

        // Doctors can update appointments they are assigned to
        if ($user->hasRole('Doctor') && $user->staff) {
            return $appointment->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the status of the model.
     */
    public function updateStatus(User $user, Appointment $appointment): bool
    {
        return $this->update($user, $appointment);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->can('delete_appointments');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Appointment $appointment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Appointment $appointment): bool
    {
        return false;
    }
}
