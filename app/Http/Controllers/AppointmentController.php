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

        $query = Appointment::with(['patient.user', 'staff.user']);

        // Apply search filter
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient.user', function ($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', '%'.$search.'%');
                })
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('surname', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('staff.user', function ($staffQuery) use ($search) {
                        $staffQuery->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('staff', function ($staffQuery) use ($search) {
                        $staffQuery->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    })
                    ->orWhere('appointment_type', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhere('reason_for_visit', 'like', '%'.$search.'%');
            });
        }

        $appointments = $query->paginate(15);

        // Transform appointments for the frontend to handle null relationships
        $transformedAppointments = $appointments->getCollection()->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'patient' => $appointment->patient ? [
                    'user' => $appointment->patient->user ? [
                        'name' => $appointment->patient->user->name ?? $appointment->patient->name,
                    ] : ['name' => $appointment->patient->name],
                ] : ['user' => ['name' => 'Unknown Patient']],
                'staff' => $appointment->staff ? [
                    'user' => $appointment->staff->user ? [
                        'name' => $appointment->staff->user->name ?? $appointment->staff->name,
                    ] : ['name' => $appointment->staff->name],
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
                        'name' => $appointment->patient->user->name ?? $appointment->patient->name,
                    ] : ['name' => $appointment->patient->name],
                ] : ['user' => ['name' => 'Unknown Patient']],
                'staff' => $appointment->staff ? [
                    'user' => $appointment->staff->user ? [
                        'name' => $appointment->staff->user->name ?? $appointment->staff->name,
                    ] : ['name' => $appointment->staff->name],
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
            'is_hormone_test' => $appointment->is_hormone_test,
            'is_tvs' => $appointment->is_tvs,
            'opu_time' => $appointment->opu_time,
            'et_fet_time' => $appointment->et_fet_time,
            'is_beta_hcg' => $appointment->is_beta_hcg,
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
            'is_hormone_test' => 'boolean',
            'is_tvs' => 'boolean',
            'opu_time' => 'nullable|date_format:H:i',
            'et_fet_time' => 'nullable|date_format:H:i',
            'is_beta_hcg' => 'boolean',
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
            'is_hormone_test',
            'is_tvs',
            'opu_time',
            'et_fet_time',
            'is_beta_hcg',
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

    /**
     * Generate a comprehensive appointment report as PDF.
     */
    public function generateReport(Appointment $appointment): \Illuminate\Http\Response
    {
        $this->authorize('view', $appointment);

        $appointment->load([
            'patient.user',
            'staff.user',
            'staff.role',
            'visits.staff.user',
            'billings',
            'medicalRecord',
            'visits.medicalOrders.staff.user',
            'visits.medicalOrders.orderItems.inventory',
            'visits.medicalRecord',
        ]);

        // Compile report data
        $report = [
            'appointment_info' => [
                'id' => $appointment->id,
                'appointment_date_time' => $appointment->appointment_date_time,
                'duration_minutes' => $appointment->duration_minutes,
                'reason_for_visit' => $appointment->reason_for_visit,
                'status' => $appointment->status,
                'notes' => $appointment->notes,
                'created_at' => $appointment->created_at,
                'updated_at' => $appointment->updated_at,
            ],
            'patient_info' => [
                'id' => $appointment->patient->id,
                'name' => $appointment->patient->user?->name ?? $appointment->patient->first_name.' '.$appointment->patient->last_name,
                'date_of_birth' => $appointment->patient->date_of_birth,
                'gender' => $appointment->patient->gender,
                'phone_number' => $appointment->patient->phone_number,
                'email' => $appointment->patient->email ?? $appointment->patient->user?->email,
                'address' => $appointment->patient->address,
                'insurance_info' => $appointment->patient->insurance_info,
            ],
            'staff_info' => [
                'id' => $appointment->staff->id,
                'name' => $appointment->staff->user?->name ?? $appointment->staff->first_name.' '.$appointment->staff->last_name,
                'role' => $appointment->staff->role?->name ?? 'Unknown',
            ],
            'visits' => $appointment->visits->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'visit_date_time' => $visit->visit_date_time,
                    'status' => $visit->status,
                    'notes' => $visit->notes,
                    'staff_name' => $visit->staff?->user?->name ?? 'Unassigned',
                    'created_at' => $visit->created_at,
                ];
            }),
            'medical_orders' => $appointment->visits->pluck('medicalOrders')->flatten()->unique('id')->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_type' => $order->order_type,
                    'order_details' => $order->order_details,
                    'status' => $order->status,
                    'priority' => $order->priority,
                    'ordered_at' => $order->ordered_at,
                    'completed_at' => $order->completed_at,
                    'staff_name' => $order->staff?->user?->name ?? 'Unknown',
                ];
            }),
            'medical_records' => collect([$appointment->medicalRecord])->filter()->merge(
                $appointment->visits->pluck('medicalRecord')->filter()
            )->unique('id')->map(function ($record) {
                return [
                    'id' => $record->id,
                    'diagnosis' => $record->diagnosis,
                    'treatment' => $record->treatment,
                    'notes' => $record->notes,
                    'date_of_service' => $record->date_of_service,
                    'created_at' => $record->created_at,
                ];
            }),
            'billings' => $appointment->billings->map(function ($billing) {
                return [
                    'id' => $billing->id,
                    'amount' => $billing->amount,
                    'status' => $billing->status,
                    'billing_date' => $billing->billing_date,
                    'due_date' => $billing->due_date,
                    'paid_at' => $billing->paid_at,
                    'notes' => $billing->notes,
                ];
            }),
            'order_items' => $appointment->visits->pluck('medicalOrders')->flatten()->pluck('orderItems')->flatten()->unique('id')->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_type' => $item->item_type,
                    'item_name' => $item->inventory?->item_name ?? $item->item_name ?? 'Unknown Item',
                    'quantity_required' => $item->quantity_required,
                    'quantity_used' => $item->quantity_used,
                    'status' => $item->status,
                    'completed_at' => $item->completed_at,
                ];
            }),
        ];

        // Calculate totals
        $report['totals'] = [
            'total_visits' => $appointment->visits->count(),
            'total_medical_orders' => $appointment->visits->pluck('medicalOrders')->flatten()->unique('id')->count(),
            'total_medical_records' => collect([$appointment->medicalRecord])->filter()->merge(
                $appointment->visits->pluck('medicalRecord')->filter()
            )->unique('id')->count(),
            'total_billings' => $appointment->billings->count(),
            'total_billed_amount' => $appointment->billings->sum('amount'),
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('appointment-report', compact('report', 'appointment'));

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isPhpEnabled' => true,
        ]);

        $filename = 'appointment-report-'.$appointment->id.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate a simple appointment letter as PDF.
     */
    public function generateLetter(Appointment $appointment): \Illuminate\Http\Response
    {
        $this->authorize('view', $appointment);

        $appointment->load([
            'patient.user',
            'staff.user',
            'staff.role',
        ]);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('appointment-letter', compact('appointment'));

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isPhpEnabled' => true,
        ]);

        $filename = 'appointment-letter-'.$appointment->id.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}
