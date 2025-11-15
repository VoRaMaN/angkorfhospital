<?php

namespace App\Observers;

use App\Models\Staff;
use Spatie\Permission\Models\Role as SpatieRole;

class StaffObserver
{
    public function created(Staff $staff): void
    {
        $this->syncRole($staff);
    }

    public function updated(Staff $staff): void
    {
        // If the role_id changed, sync the Spatie role
        if ($staff->wasChanged('role_id')) {
            $this->syncRole($staff);
        }
    }

    /**
     * Sync the user's Spatie role with the domain staff role name.
     */
    protected function syncRole(Staff $staff): void
    {
        $user = $staff->user;

        if (! $user) {
            return; // No user associated yet
        }

        // Domain Role model name -> use as Spatie role name
        $role = $staff->role;

        if (! $role || ! $role->name) {
            // If there's no domain role, remove all roles from the user
            $user->syncRoles([]);

            return;
        }

        // Ensure the Spatie role exists (create if missing), then sync
        $spatieRole = SpatieRole::firstOrCreate([
            'name' => $role->name,
            'guard_name' => config('auth.defaults.guard', 'web'),
        ]);

        // Sync roles so the user has exactly the spatie role aligned to the domain role.
        $user->syncRoles([$spatieRole->name]);
    }

    public function deleted(Staff $staff): void
    {
        $user = $staff->user;

        if (! $user) {
            return; // Nothing to do
        }

        $role = $staff->role;
        if (! $role || ! $role->name) {
            return;
        }

        // Remove the role from the user if they have it.
        if ($user->hasRole($role->name)) {
            $user->removeRole($role->name);
        }

        // If no models are assigned to this spatie role, delete it to keep things clean.
        $spatieRole = SpatieRole::where('name', $role->name)
            ->where('guard_name', config('auth.defaults.guard', 'web'))
            ->first();

        if ($spatieRole) {
            $table = config('permission.table_names.model_has_roles');
            $assignedCount = \DB::table($table)->where('role_id', $spatieRole->id)->count();

            if ($assignedCount === 0) {
                $spatieRole->delete();
            }
        }
    }
}
