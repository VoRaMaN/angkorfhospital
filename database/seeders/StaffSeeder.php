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
        // Create admin staff user
        \App\Models\Staff::create([
            'user_id' => 1, // Will be created by UserSeeder
            'first_name' => 'Admin',
            'last_name' => 'User',
            'role_id' => 1, // Admin role
            'contact_number' => '+1234567890',
            'hire_date' => now(),
        ]);
    }
}
