<?php

namespace App\Policies;

use App\Models\LabPanelItem;
use App\Models\User;

class LabPanelItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_lab_panel_items') || $user->hasRole('admin') || $user->hasRole('doctor');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LabPanelItem $labPanelItem): bool
    {
        return $user->can('view_lab_panel_items') || $user->hasRole('admin') || $user->hasRole('doctor');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_lab_panel_items') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LabPanelItem $labPanelItem): bool
    {
        return $user->can('edit_lab_panel_items') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LabPanelItem $labPanelItem): bool
    {
        return $user->can('delete_lab_panel_items') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LabPanelItem $labPanelItem): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LabPanelItem $labPanelItem): bool
    {
        return false;
    }
}
