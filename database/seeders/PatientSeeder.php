<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = [
            [
                'user' => [
                    'name' => 'John Smith',
                    'email' => 'john.smith@example.com',
                    'password' => Hash::make('password'),
                ],
                'patient' => [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'date_of_birth' => '1985-03-15',
                    'gender' => 'Male',
                    'address' => '123 Main St, Springfield, IL 62701',
                    'phone_number' => '+1-555-0101',
                    'email' => 'john.smith@example.com',
                    'insurance_info' => 'Blue Cross Blue Shield - Policy #123456789',
                ],
            ],
            [
                'user' => [
                    'name' => 'Sarah Johnson',
                    'email' => 'sarah.johnson@example.com',
                    'password' => Hash::make('password'),
                ],
                'patient' => [
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'date_of_birth' => '1992-07-22',
                    'gender' => 'Female',
                    'address' => '456 Oak Ave, Springfield, IL 62702',
                    'phone_number' => '+1-555-0102',
                    'email' => 'sarah.johnson@example.com',
                    'insurance_info' => 'Aetna - Policy #987654321',
                ],
            ],
            [
                'user' => [
                    'name' => 'Michael Brown',
                    'email' => 'michael.brown@example.com',
                    'password' => Hash::make('password'),
                ],
                'patient' => [
                    'first_name' => 'Michael',
                    'last_name' => 'Brown',
                    'date_of_birth' => '1978-11-08',
                    'gender' => 'Male',
                    'address' => '789 Pine Rd, Springfield, IL 62703',
                    'phone_number' => '+1-555-0103',
                    'email' => 'michael.brown@example.com',
                    'insurance_info' => 'United Healthcare - Policy #456789123',
                ],
            ],
            [
                'user' => [
                    'name' => 'Emily Davis',
                    'email' => 'emily.davis@example.com',
                    'password' => Hash::make('password'),
                ],
                'patient' => [
                    'first_name' => 'Emily',
                    'last_name' => 'Davis',
                    'date_of_birth' => '1988-05-30',
                    'gender' => 'Female',
                    'address' => '321 Elm St, Springfield, IL 62704',
                    'phone_number' => '+1-555-0104',
                    'email' => 'emily.davis@example.com',
                    'insurance_info' => 'Cigna - Policy #789123456',
                ],
            ],
            [
                'user' => [
                    'name' => 'David Wilson',
                    'email' => 'david.wilson@example.com',
                    'password' => Hash::make('password'),
                ],
                'patient' => [
                    'first_name' => 'David',
                    'last_name' => 'Wilson',
                    'date_of_birth' => '1995-09-12',
                    'gender' => 'Male',
                    'address' => '654 Maple Dr, Springfield, IL 62705',
                    'phone_number' => '+1-555-0105',
                    'email' => 'david.wilson@example.com',
                    'insurance_info' => 'Humana - Policy #321654987',
                ],
            ],
        ];

        foreach ($patients as $patientData) {
            // Check if user already exists
            $user = User::where('email', $patientData['user']['email'])->first();

            if (! $user) {
                // Create user if doesn't exist
                $user = User::create($patientData['user']);

                // Assign patient role to user
                $user->assignRole('Patient');
            }

            // Check if patient record already exists
            $existingPatient = Patient::where('user_id', $user->id)->first();

            if (! $existingPatient) {
                // Create patient record
                $patientData['patient']['user_id'] = $user->id;
                Patient::create($patientData['patient']);
            }
        }
    }
}
