<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Inertia\Inertia;
use Inertia\Response;

class DoctorController extends Controller
{
    /**
     * Show doctor's appointments.
     */
    public function myAppointments(): Response
    {
        $currentUser = auth()->user();

        $appointments = Appointment::with(['patient.user', 'staff.user'])
            ->whereHas('staff', function ($query) use ($currentUser) {
                $query->where('user_id', $currentUser->id);
            })
            ->orderBy('appointment_date_time', 'desc')
            ->paginate(15);

        // Transform appointments for the frontend
        $transformedAppointments = $appointments->getCollection()->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'patient' => [
                    'id' => $appointment->patient->id,
                    'name' => trim($appointment->patient->first_name.' '.$appointment->patient->last_name) ?? 'Unknown Patient',
                    'date_of_birth' => $appointment->patient->date_of_birth,
                ],
                'appointment_date' => $appointment->appointment_date_time->format('Y-m-d'),
                'appointment_time' => $appointment->appointment_date_time->format('H:i'),
                'appointment_type' => $appointment->appointment_type ?? 'consultation',
                'duration_minutes' => $appointment->duration_minutes ?? 30,
                'status' => $appointment->status,
                'notes' => $appointment->reason_for_visit,
            ];
        });

        return Inertia::render('Doctors/MyAppointments', [
            'appointments' => $transformedAppointments,
        ]);
    }

    /**
     * Show doctor's patients.
     */
    public function myPatients(): Response
    {
        $currentUser = auth()->user();

        // Get unique patients for this doctor with their appointment statistics
        $patients = \App\Models\Patient::with([
            'user',
            'appointments' => function ($query) use ($currentUser) {
                $query->whereHas('staff', function ($subQuery) use ($currentUser) {
                    $subQuery->where('user_id', $currentUser->id);
                });
            },
        ])
            ->whereHas('appointments', function ($query) use ($currentUser) {
                $query->whereHas('staff', function ($subQuery) use ($currentUser) {
                    $subQuery->where('user_id', $currentUser->id);
                });
            })
            ->get()
            ->map(function ($patient) use ($currentUser) {
                $doctorAppointments = $patient->appointments->filter(function ($appointment) use ($currentUser) {
                    return $appointment->staff->user_id === $currentUser->id;
                });

                $lastAppointment = $doctorAppointments->sortByDesc('appointment_date_time')->first();

                return [
                    'id' => $patient->id,
                    'name' => trim($patient->first_name.' '.$patient->last_name) ?? 'Unknown Patient',
                    'email' => $patient->user ? $patient->user->email : $patient->email,
                    'phone' => $patient->phone_number,
                    'date_of_birth' => $patient->date_of_birth,
                    'last_visit' => $lastAppointment ? $lastAppointment->appointment_date_time->format('Y-m-d') : null,
                    'total_appointments' => $doctorAppointments->count(),
                ];
            })
            ->unique('id')
            ->values();

        return Inertia::render('Doctors/MyPatients', [
            'patients' => $patients,
        ]);
    }

    /**
     * Show doctor's visits.
     */
    public function myVisits(): Response
    {
        $currentUser = auth()->user();

        $visits = \App\Models\Visit::with(['patient.user', 'appointment', 'medicalOrders'])
            ->whereHas('staff', function ($query) use ($currentUser) {
                $query->where('user_id', $currentUser->id);
            })
            ->orderBy('visit_date_time', 'desc')
            ->paginate(15);

        // Transform visits for the frontend
        $transformedVisits = $visits->getCollection()->map(function ($visit) {
            return [
                'id' => $visit->id,
                'patient' => $visit->patient ? [
                    'user' => $visit->patient->user ? [
                        'name' => $visit->patient->user->name ?? trim($visit->patient->first_name.' '.$visit->patient->last_name),
                    ] : ['name' => trim($visit->patient->first_name.' '.$visit->patient->last_name)],
                ] : ['user' => ['name' => 'Unknown Patient']],
                'appointment' => $visit->appointment,
                'visit_date_time' => $visit->visit_date_time,
                'status' => $visit->status,
                'notes' => $visit->notes,
                'created_at' => $visit->created_at,
                'medical_orders' => $visit->medicalOrders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'status' => $order->status->value,
                    ];
                }),
            ];
        });

        return Inertia::render('Doctors/MyVisits', [
            'visits' => $transformedVisits,
        ]);
    }
}
