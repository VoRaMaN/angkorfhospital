<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::with(['patient.user', 'staff.user'])->paginate(15);

        // Transform appointments for the frontend to handle null relationships
        $transformedAppointments = $appointments->getCollection()->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'patient' => $appointment->patient ? [
                    'user' => $appointment->patient->user ? [
                        'name' => $appointment->patient->user->name ?? trim($appointment->patient->first_name.' '.$appointment->patient->last_name),
                    ] : ['name' => trim($appointment->patient->first_name.' '.$appointment->patient->last_name)],
                ] : ['user' => ['name' => 'Unknown Patient']],
                'staff' => $appointment->staff ? [
                    'user' => $appointment->staff->user ? [
                        'name' => $appointment->staff->user->name ?? trim($appointment->staff->first_name.' '.$appointment->staff->last_name),
                    ] : ['name' => trim($appointment->staff->first_name.' '.$appointment->staff->last_name)],
                ] : ['user' => ['name' => 'Unknown Staff']],
                'appointment_date_time' => $appointment->appointment_date_time,
                'duration_minutes' => $appointment->duration_minutes ?? 30,
                'appointment_type' => $appointment->appointment_type ?? 'consultation',
                'status' => $appointment->status,
                'created_at' => $appointment->created_at,
            ];
        });

        return Inertia::render('Appointments/Index', [
            'appointments' => $transformedAppointments,
            'filters' => [
                'search' => request('search', ''),
            ],
        ]);
    }

    /**
     * Display a calendar view of appointments.
     */
    public function calendar(): Response
    {
        $this->authorize('viewAny', Appointment::class);

        $currentDate = request('date', now()->toDateString());
        $startDate = \Carbon\Carbon::parse($currentDate)->startOfMonth()->startOfWeek();
        $endDate = \Carbon\Carbon::parse($currentDate)->endOfMonth()->endOfWeek();

        $query = Appointment::with(['patient.user', 'staff.user'])
            ->whereBetween('appointment_date_time', [$startDate, $endDate]);

        // Filter appointments based on user role
        $user = auth()->user();
        if ($user->hasRole('Doctor') && $user->staff && ! $user->hasRole('admin') && ! $user->can('view_appointments')) {
            // Doctors can only see their own appointments unless they have broader permissions
            $query->where('staff_id', $user->staff->id);
        }

        $appointments = $query->get();

        // Transform appointments for the frontend
        $transformedAppointments = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'patient' => $appointment->patient ? [
                    'user' => $appointment->patient->user ? [
                        'name' => $appointment->patient->user->name ?? trim($appointment->patient->first_name.' '.$appointment->patient->last_name),
                    ] : ['name' => trim($appointment->patient->first_name.' '.$appointment->patient->last_name)],
                ] : ['user' => ['name' => 'Unknown Patient']],
                'staff' => $appointment->staff ? [
                    'user' => $appointment->staff->user ? [
                        'name' => $appointment->staff->user->name ?? trim($appointment->staff->first_name.' '.$appointment->staff->last_name),
                    ] : ['name' => trim($appointment->staff->first_name.' '.$appointment->staff->last_name)],
                ] : ['user' => ['name' => 'Unknown Staff']],
                'appointment_date_time' => $appointment->appointment_date_time,
                'duration_minutes' => $appointment->duration_minutes ?? 30,
                'appointment_type' => $appointment->appointment_type ?? 'consultation',
                'status' => $appointment->status,
                'reason_for_visit' => $appointment->reason_for_visit,
            ];
        });

        return Inertia::render('Appointments/Calendar', [
            'appointments' => $transformedAppointments,
            'currentDate' => $currentDate,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Appointment::class);

        $patients = \App\Models\Patient::with('user')->get();
        $staff = \App\Models\Staff::with('user')->get();

        return Inertia::render('Appointments/Create', [
            'patients' => $patients,
            'staff' => $staff,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = \App\Enums\AppointmentStatusEnum::SCHEDULED;

        $appointment = Appointment::create($data);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment): Response
    {
        $this->authorize('view', $appointment);

        $appointment->load(['patient.user', 'staff.user', 'staff.role', 'medicalRecord', 'billings']);

        return Inertia::render('Appointments/Show', [
            'appointment' => $appointment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment): Response
    {
        $this->authorize('update', $appointment);

        $patients = \App\Models\Patient::with('user')->get();
        $staff = \App\Models\Staff::with('user')->get();

        // Format the appointment data for the form
        $appointmentData = [
            'id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'staff_id' => $appointment->staff_id,
            'appointment_date_time' => $appointment->appointment_date_time->format('Y-m-d\TH:i'),
            'duration_minutes' => $appointment->duration_minutes,
            'appointment_type' => $appointment->appointment_type,
            'status' => $appointment->status,
            'reason_for_visit' => $appointment->reason_for_visit,
            'notes' => $appointment->notes,
        ];

        return Inertia::render('Appointments/Edit', [
            'appointment' => $appointmentData,
            'patients' => $patients,
            'staff' => $staff,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'required|exists:staff,id',
            'appointment_date_time' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'appointment_type' => 'required|in:consultation,emergency,follow_up,procedure,checkup,telemedicine,screening,therapy',
            'status' => 'required|in:scheduled,confirmed,arrived,in_progress,completed,cancelled,no_show,rescheduled',
            'reason_for_visit' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ]);

        $appointment->update($request->only([
            'patient_id',
            'staff_id',
            'appointment_date_time',
            'duration_minutes',
            'appointment_type',
            'status',
            'reason_for_visit',
            'notes',
        ]));

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Update the status of the specified resource in storage.
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);

        $request->validate([
            'status' => 'required|in:scheduled,confirmed,arrived,in_progress,completed,cancelled,no_show,rescheduled',
        ]);

        $appointment->update([
            'status' => $request->status,
        ]);

        return redirect()->back()
            ->with('success', 'Appointment status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment): RedirectResponse
    {
        $this->authorize('delete', $appointment);

        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }
}
