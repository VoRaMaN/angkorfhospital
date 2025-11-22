<?php

namespace App\Policies;

use App\Models\PatientFile;
use App\Models\User;

class PatientFilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_patient_files') || $user->hasRole('admin') || $user->hasRole('doctor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PatientFile $patientFile): bool
    {
        // Admin can view all patient files
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can view patient files
        if ($user->can('view_patient_files')) {
            return true;
        }

        // Doctors can view files for their patients
        if ($user->hasRole('doctor') && $user->staff) {
            return $patientFile->patient->staff_id === $user->staff->id;
        }

        // Patients can view their own files
        if ($user->hasRole('patient') && $user->patient) {
            return $patientFile->patient_id === $user->patient->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_patient_files') || $user->hasRole('doctor') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PatientFile $patientFile): bool
    {
        // Admin can update all patient files
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can update patient files
        if ($user->can('edit_patient_files')) {
            return true;
        }

        // Doctors can update files for their patients
        if ($user->hasRole('doctor') && $user->staff) {
            return $patientFile->patient->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PatientFile $patientFile): bool
    {
        return $user->can('delete_patient_files');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PatientFile $patientFile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PatientFile $patientFile): bool
    {
        return false;
    }
}
