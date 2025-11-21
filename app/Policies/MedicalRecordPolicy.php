<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_medical_records') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->can('view_medical_records') ||
               $user->hasRole('admin') ||
               ($user->hasRole('Patient') && (
                   ($medicalRecord->appointment && $user->patient?->id === $medicalRecord->appointment->patient_id) ||
                   ($medicalRecord->visit && $user->patient?->id === $medicalRecord->visit->patient_id) ||
                   ($medicalRecord->medicalOrder && $user->patient?->id === $medicalRecord->medicalOrder->patient_id)
               )) ||
               ($user->hasRole('Doctor') && (
                   ($medicalRecord->appointment && $user->staff?->id === $medicalRecord->appointment->staff_id) ||
                   ($medicalRecord->visit && $user->staff?->id === $medicalRecord->visit->staff_id) ||
                   ($medicalRecord->medicalOrder && $user->staff?->id === $medicalRecord->medicalOrder->staff_id)
               ));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_medical_records') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->can('edit_medical_records') ||
               $user->hasRole('admin') ||
               ($user->hasRole('Doctor') && (
                   ($medicalRecord->appointment && $user->staff?->id === $medicalRecord->appointment->staff_id) ||
                   ($medicalRecord->visit && $user->staff?->id === $medicalRecord->visit->staff_id) ||
                   ($medicalRecord->medicalOrder && $user->staff?->id === $medicalRecord->medicalOrder->staff_id)
               ));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->can('delete_medical_records') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalRecord $medicalRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalRecord $medicalRecord): bool
    {
        return false;
    }
}
