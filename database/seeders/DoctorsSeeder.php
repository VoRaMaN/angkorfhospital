<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'email' => 'dr.sokong@clinic.com',
                'name' => 'Dr. Sokong',
                'first_name' => 'Sokong',
                'last_name' => '',
            ],
            [
                'email' => 'dr.so@clinic.com',
                'name' => 'Dr. So',
                'first_name' => 'So',
                'last_name' => '',
            ],
        ];

        foreach ($doctors as $doctorData) {
            // Create user account
            $user = User::create([
                'name' => $doctorData['name'],
                'email' => $doctorData['email'],
                'password' => Hash::make('password'),
            ]);

            // Assign doctor role
            $user->assignRole('doctor');

            // Create staff record
            Staff::create([
                'user_id' => $user->id,
                'first_name' => $doctorData['first_name'],
                'last_name' => $doctorData['last_name'],
                'role_id' => 2, // Doctor role ID
                'contact_number' => '+855'.rand(10000000, 99999999),
                'hire_date' => now(),
            ]);
        }
    }
}
