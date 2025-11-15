<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'description' => 'System Administrator with full access'],
            ['name' => 'Doctor', 'description' => 'Medical practitioner'],
            ['name' => 'Nurse', 'description' => 'Nursing staff'],
            ['name' => 'Receptionist', 'description' => 'Front desk and administrative staff'],
            ['name' => 'Pharmacist', 'description' => 'Pharmacy staff'],
        ];

        foreach ($roles as $role) {
            \App\Models\StaffRole::create($role);
        }
    }
}
