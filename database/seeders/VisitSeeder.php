<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some patients and staff
        $patients = \App\Models\Patient::all();
        $staff = \App\Models\Staff::all();
        $appointments = \App\Models\Appointment::all();

        if ($patients->isEmpty() || $staff->isEmpty()) {
            return; // Skip if no patients or staff exist
        }

        $visits = [
            [
                'appointment_id' => $appointments->first()?->id,
                'patient_id' => $patients->first()->id,
                'staff_id' => $staff->first()->id,
                'visit_date_time' => now()->subDays(1),
                'status' => 'completed',
                'notes' => 'Regular checkup completed successfully',
            ],
            [
                'appointment_id' => null, // Independent visit
                'patient_id' => $patients->skip(1)->first()?->id ?? $patients->first()->id,
                'staff_id' => $staff->skip(1)->first()?->id ?? $staff->first()->id,
                'visit_date_time' => now()->subHours(2),
                'status' => 'in_progress',
                'notes' => 'Emergency visit for chest pain',
            ],
            [
                'appointment_id' => $appointments->skip(1)->first()?->id,
                'patient_id' => $patients->skip(2)->first()?->id ?? $patients->first()->id,
                'staff_id' => null, // Staff not assigned yet
                'visit_date_time' => now()->addDays(1),
                'status' => 'pending',
                'notes' => 'Follow-up appointment scheduled',
            ],
        ];

        foreach ($visits as $visit) {
            \App\Models\Visit::create($visit);
        }
    }
}
