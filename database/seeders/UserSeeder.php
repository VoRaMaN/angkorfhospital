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
        // Create admin user
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@clinic.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Assign admin role
        $admin->assignRole('admin');

        // Create test user
        $testUser = \App\Models\User::create([
            'name' => 'Doctor User',
            'email' => 'doctor@clinic.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Assign staff role to test user
        $testUser->assignRole('Doctor');
    }
}
