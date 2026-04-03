<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@clinic.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Doctor User',
                'email' => 'doctor@clinic.com',
                'role' => 'doctor',
            ],
            [
                'name' => 'Nurse User',
                'email' => 'nurse@clinic.com',
                'role' => 'nurse',
            ],
            [
                'name' => 'Receptionist User',
                'email' => 'receptionist@clinic.com',
                'role' => 'receptionist',
            ],
            [
                'name' => 'Accountant User',
                'email' => 'accountant@clinic.com',
                'role' => 'accountant',
            ],
            [
                'name' => 'Billing User',
                'email' => 'billing@clinic.com',
                'role' => 'billing',
            ],
            [
                'name' => 'Pharmacist User',
                'email' => 'pharmacist@clinic.com',
                'role' => 'pharmacist',
            ],
            [
                'name' => 'Lab User',
                'email' => 'lab@clinic.com',
                'role' => 'lab',
            ],
            [
                'name' => 'Inventory User',
                'email' => 'inventory@clinic.com',
                'role' => 'inventory',
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@clinic.com',
                'role' => 'staff',
            ],
        ];

        foreach ($users as $userData) {
            $user = \App\Models\User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole($userData['role']);
        }
    }
}
