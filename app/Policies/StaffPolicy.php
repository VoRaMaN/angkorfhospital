<?php

namespace App\Policies;

use App\Models\Staff;
use App\Models\User;

class StaffPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_staff') || $user->hasRole('admin') || $user->hasRole('doctor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Staff $staff): bool
    {
        // Admin can view all staff
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can view staff
        if ($user->can('view_staff')) {
            return true;
        }

        // Staff can view their own record
        if ($user->staff) {
            return $user->staff->id === $staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_staff') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Staff $staff): bool
    {
        // Admin can update all staff
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users with permission can update staff
        if ($user->can('edit_staff')) {
            return true;
        }

        // Staff can update their own record
        if ($user->staff) {
            return $user->staff->id === $staff->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Staff $staff): bool
    {
        return $user->can('delete_staff') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Staff $staff): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Staff $staff): bool
    {
        return false;
    }
}
