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
            ['name' => 'admin', 'description' => 'System Administrator with full access'],
            ['name' => 'doctor', 'description' => 'Medical practitioner'],
            ['name' => 'nurse', 'description' => 'Nursing staff'],
            ['name' => 'receptionist', 'description' => 'Front desk and administrative staff'],
            ['name' => 'accountant', 'description' => 'Handles financial records and transactions'],
            ['name' => 'billing', 'description' => 'Handles billing and payments'],
            ['name' => 'pharmacist', 'description' => 'Manages medications and pharmacy'],
            ['name' => 'lab', 'description' => 'Laboratory technician'],
            ['name' => 'inventory', 'description' => 'Manages inventory and supplies'],
            ['name' => 'staff', 'description' => 'General staff member'],
            ['name' => 'patient', 'description' => 'Patient user'],
        ];

        foreach ($roles as $role) {
            \App\Models\StaffRole::create($role);
        }
    }
}
