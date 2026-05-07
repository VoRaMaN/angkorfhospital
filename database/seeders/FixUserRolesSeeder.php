<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class FixUserRolesSeeder extends Seeder
{
    /**
     * Sync Spatie role assignments for all users based on their staff role.
     */
    public function run(): void
    {
        $staffMembers = Staff::with(['user', 'role'])->get();

        foreach ($staffMembers as $staff) {
            if (! $staff->user || ! $staff->role) {
                continue;
            }

            $roleName = $staff->role->name;
            $staff->user->syncRoles([$roleName]);

            $this->command->info("{$staff->user->email} => {$roleName}");
        }

        $this->command->info('Done! Role assignments fixed.');
    }
}
