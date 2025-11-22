<?php

namespace App\Policies;

use App\Models\StaffFile;
use App\Models\User;

class StaffFilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_staff_files') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StaffFile $staffFile): bool
    {
        // Admin can view all staff files
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can view staff files
        if ($user->can('view_staff_files')) {
            return true;
        }

        // Staff can view their own files
        if ($user->hasRole('staff') && $user->staff) {
            return $staffFile->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_staff_files') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StaffFile $staffFile): bool
    {
        // Admin can update all staff files
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can update staff files
        if ($user->can('edit_staff_files')) {
            return true;
        }

        // Staff can update their own files
        if ($user->hasRole('staff') && $user->staff) {
            return $staffFile->staff_id === $user->staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StaffFile $staffFile): bool
    {
        return $user->can('delete_staff_files');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StaffFile $staffFile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StaffFile $staffFile): bool
    {
        return false;
    }
}
