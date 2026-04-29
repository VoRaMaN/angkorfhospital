<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;

class InventoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_inventory') || $user->hasRole('admin') || $user->hasRole('doctor') || $user->hasRole('pharmacist') || $user->hasRole('inventory');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Inventory $inventory): bool
    {
        return $user->can('view_inventory') || $user->hasRole('admin') || $user->hasRole('doctor') || $user->hasRole('pharmacist') || $user->hasRole('inventory');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_inventory') || $user->hasRole('admin') || $user->hasRole('pharmacist') || $user->hasRole('inventory');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Inventory $inventory): bool
    {
        return $user->can('edit_inventory') || $user->hasRole('admin') || $user->hasRole('pharmacist') || $user->hasRole('inventory');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Inventory $inventory): bool
    {
        return $user->can('delete_inventory') || $user->hasRole('admin') || $user->hasRole('pharmacist') || $user->hasRole('inventory');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Inventory $inventory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Inventory $inventory): bool
    {
        return false;
    }
}
