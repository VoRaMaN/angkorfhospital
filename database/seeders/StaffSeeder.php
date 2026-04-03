<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();

        $roleMap = [
            'admin' => 1,
            'doctor' => 2,
            'nurse' => 3,
            'receptionist' => 4,
            'accountant' => 5,
            'billing' => 6,
            'pharmacist' => 7,
            'lab' => 8,
            'inventory' => 9,
            'staff' => 10,
        ];

        foreach ($users as $index => $user) {
            $roleName = $user->roles->first()?->name;

            if (! $roleName || $roleName === 'patient') {
                continue;
            }

            $roleId = $roleMap[$roleName] ?? 1;

            $names = explode(' ', $user->name);

            \App\Models\Staff::create([
                'user_id' => $user->id,
                'first_name' => $names[0],
                'last_name' => $names[1] ?? '',
                'role_id' => $roleId,
                'contact_number' => '+123456789'.($user->id),
                'hire_date' => now(),
            ]);
        }
    }
}
