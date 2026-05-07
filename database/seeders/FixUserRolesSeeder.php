<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\StaffRole;
use Illuminate\Database\Seeder;

class FixUserRolesSeeder extends Seeder
{
    /**
     * Sync Spatie role assignments for all users based on their staff role.
     *
     * Note: Staff model imports Spatie\Permission\Models\Role (spatie_roles table),
     * not App\Models\StaffRole (roles table). We use StaffRole directly to get the
     * correct role name from the custom roles table.
     */
    public function run(): void
    {
        $staffMembers = Staff::with('user')->get();

        foreach ($staffMembers as $staff) {
            if (! $staff->user || ! $staff->role_id) {
                continue;
            }

            $staffRole = StaffRole::find($staff->role_id);

            if (! $staffRole) {
                continue;
            }

            $roleName = $staffRole->name;
            $staff->user->syncRoles([$roleName]);

            $this->command->info("{$staff->user->email} => {$roleName}");
        }

        $this->command->info('Done! Role assignments fixed.');
    }
}
