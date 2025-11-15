<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatusEnum;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all patients and staff (doctors)
        $patients = Patient::all();
        $doctors = Staff::whereHas('role', function ($query) {
            $query->where('name', 'Doctor');
        })->get();

        // If no doctors exist, create some doctor staff first
        if ($doctors->isEmpty()) {
            // Get doctor role
            $doctorRole = \App\Models\Role::where('name', 'Doctor')->first();

            if ($doctorRole) {
                // Get cardiology department
                $cardiologyDept = \App\Models\Department::where('name', 'Cardiology')->first();

                // Create a doctor user and staff
                $doctorUser = \App\Models\User::create([
                    'name' => 'Dr. James Carter',
                    'email' => 'dr.carter@cynosys.com',
                    'password' => bcrypt('password'),
                ]);
                $doctorUser->assignRole('Doctor');

                $doctor = Staff::create([
                    'user_id' => $doctorUser->id,
                    'first_name' => 'James',
                    'last_name' => 'Carter',
                    'role_id' => $doctorRole->id,
                    'department_id' => $cardiologyDept?->id ?? 1,
                    'contact_number' => '+1-555-0201',
                    'hire_date' => now()->subYears(5),
                ]);

                $doctors = collect([$doctor]);
            }
        }

        // Create appointments for patients
        $appointments = [
            [
                'patient_id' => $patients->first()->id ?? 1,
                'staff_id' => $doctors->first()->id ?? 1,
                'appointment_date_time' => now()->addDays(1)->setTime(9, 0), // Tomorrow at 9 AM
                'status' => AppointmentStatusEnum::SCHEDULED,
                'reason_for_visit' => 'Annual physical examination',
            ],
            [
                'patient_id' => $patients->skip(1)->first()->id ?? 2,
                'staff_id' => $doctors->first()->id ?? 1,
                'appointment_date_time' => now()->addDays(2)->setTime(10, 30), // Day after tomorrow at 10:30 AM
                'status' => AppointmentStatusEnum::SCHEDULED,
                'reason_for_visit' => 'Follow-up for hypertension',
            ],
            [
                'patient_id' => $patients->skip(2)->first()->id ?? 3,
                'staff_id' => $doctors->first()->id ?? 1,
                'appointment_date_time' => now()->addDays(3)->setTime(14, 0), // 3 days from now at 2 PM
                'status' => AppointmentStatusEnum::SCHEDULED,
                'reason_for_visit' => 'Chest pain evaluation',
            ],
            [
                'patient_id' => $patients->skip(3)->first()->id ?? 4,
                'staff_id' => $doctors->first()->id ?? 1,
                'appointment_date_time' => now()->subDays(1)->setTime(11, 0), // Yesterday at 11 AM
                'status' => AppointmentStatusEnum::COMPLETED,
                'reason_for_visit' => 'Routine check-up',
            ],
            [
                'patient_id' => $patients->skip(4)->first()->id ?? 5,
                'staff_id' => $doctors->first()->id ?? 1,
                'appointment_date_time' => now()->subDays(2)->setTime(15, 30), // 2 days ago at 3:30 PM
                'status' => AppointmentStatusEnum::COMPLETED,
                'reason_for_visit' => 'Vaccination',
            ],
            [
                'patient_id' => $patients->first()->id ?? 1,
                'staff_id' => $doctors->first()->id ?? 1,
                'appointment_date_time' => now()->addDays(5)->setTime(8, 30), // 5 days from now at 8:30 AM
                'status' => AppointmentStatusEnum::SCHEDULED,
                'reason_for_visit' => 'Blood work review',
            ],
            [
                'patient_id' => $patients->skip(1)->first()->id ?? 2,
                'staff_id' => $doctors->first()->id ?? 1,
                'appointment_date_time' => now()->addDays(7)->setTime(13, 15), // Next week at 1:15 PM
                'status' => AppointmentStatusEnum::SCHEDULED,
                'reason_for_visit' => 'Diabetes management',
            ],
            [
                'patient_id' => $patients->skip(2)->first()->id ?? 3,
                'staff_id' => $doctors->first()->id ?? 1,
                'appointment_date_time' => now()->subDays(5)->setTime(10, 0), // 5 days ago at 10 AM
                'status' => AppointmentStatusEnum::CANCELLED,
                'reason_for_visit' => 'Consultation',
            ],
        ];

        foreach ($appointments as $appointmentData) {
            Appointment::create($appointmentData);
        }
    }
}
