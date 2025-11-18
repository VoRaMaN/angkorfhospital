<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Services\VisitFlowService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VisitController extends Controller
{
    public function __construct(
        private VisitFlowService $visitsFlowService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $visits = Visit::with(['patient.user', 'staff.user', 'appointment', 'medicalOrders'])->paginate(15);

        // Transform visits for the frontend
        $transformedVisits = $visits->getCollection()->map(function ($visit) {
            return [
                'id' => $visit->id,
                'patient' => $visit->patient ? [
                    'user' => $visit->patient->user ? [
                        'name' => $visit->patient->user->name ?? trim($visit->patient->first_name.' '.$visit->patient->last_name),
                    ] : ['name' => trim($visit->patient->first_name.' '.$visit->patient->last_name)],
                ] : ['user' => ['name' => 'Unknown Patient']],
                'staff' => $visit->staff ? [
                    'user' => $visit->staff->user ? [
                        'name' => $visit->staff->user->name ?? trim($visit->staff->first_name.' '.$visit->staff->last_name),
                    ] : ['name' => trim($visit->staff->first_name.' '.$visit->staff->last_name)],
                ] : ['user' => ['name' => 'Unassigned']],
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

        $staff = \App\Models\Staff::with('user')->get()->map(function ($staff) {
            return [
                'id' => $staff->id,
                'name' => $staff->user ? $staff->user->name : trim($staff->first_name.' '.$staff->last_name),
            ];
        });

        return Inertia::render('Visits/Index', [
            'visits' => $transformedVisits,
            'staff' => $staff,
            'filters' => [
                'search' => request('search', ''),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $patients = \App\Models\Patient::with('user')->get();
        $staff = \App\Models\Staff::with('user')->get();
        $appointments = \App\Models\Appointment::with(['patient.user', 'staff.user'])
            ->where('status', '!=', 'completed')
            ->get();

        return Inertia::render('Visits/Create', [
            'patients' => $patients,
            'staff' => $staff,
            'appointments' => $appointments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'nullable|exists:appointments,id',
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'nullable|exists:staff,id',
            'visit_date_time' => 'required|date',
            'status' => 'required|in:'.implode(',', Visit::STATUSES),
            'notes' => 'nullable|string',
        ]);

        Visit::create($request->all());

        return redirect()->route('visits.index')
            ->with('success', 'Visit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Visit $visit): Response
    {
        $visit->load(['patient.user', 'staff.user', 'appointment', 'medicalOrders']);

        $staff = \App\Models\Staff::with('user')->get()->map(function ($staff) {
            return [
                'id' => $staff->id,
                'name' => $staff->user ? $staff->user->name : trim($staff->first_name.' '.$staff->last_name),
            ];
        });

        return Inertia::render('Visits/Show', [
            'visit' => $visit,
            'staff' => $staff,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visit $visit): Response
    {
        $patients = \App\Models\Patient::with('user')->get();
        $staff = \App\Models\Staff::with('user')->get();
        $appointments = \App\Models\Appointment::with(['patient.user', 'staff.user'])->get();

        return Inertia::render('Visits/Edit', [
            'visit' => $visit,
            'patients' => $patients,
            'staff' => $staff,
            'appointments' => $appointments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visit $visit)
    {
        // Check if user is trying to cancel the visit
        if ($request->status === 'cancelled' && $visit->status !== 'cancelled') {
            $this->authorize('cancel_visits', $visit);
        } else {
            $this->authorize('update', $visit);
        }

        $request->validate([
            'appointment_id' => 'nullable|exists:appointments,id',
            'patient_id' => 'required|exists:patients,id',
            'staff_id' => 'nullable|exists:staff,id',
            'visit_date_time' => 'required|date',
            'status' => 'required|in:'.implode(',', Visit::STATUSES),
            'notes' => 'nullable|string',
        ]);

        $visit->update($request->all());

        return redirect()->route('visits.index')
            ->with('success', 'Visit updated successfully.');
    }

    /**
     * Assign staff to process the visit.
     */
    public function assignAndProcess(Request $request, Visit $visit)
    {
        $this->authorize('assign_visits', $visit);

        $request->validate([
            'staff_id' => 'required|exists:staff,id',
        ]);

        // Load medical orders relationship
        $visit->load('medicalOrders');

        // Generate medical orders if none exist
        if ($visit->medicalOrders->isEmpty()) {
            $this->visitsFlowService->generateMedicalOrders($visit->id);
            // Reload the visit with the new medical orders
            $visit->load('medicalOrders');
        }

        // Assign staff to the visit
        $visit->update(['staff_id' => $request->staff_id]);

        // Assign staff to associated medical orders
        foreach ($visit->medicalOrders as $order) {
            $order->update(['staff_id' => $request->staff_id]);
        }

        // Update visit status to assigned
        $this->visitsFlowService->updateVisitStatus($visit->id, Visit::STATUS_ASSIGNED);

        return back()->with('success', 'Staff assigned successfully.');
    }

    /**
     * Notify staff that the visit is ready for assignment.
     */
    public function notifyStaff(Visit $visit)
    {
        $this->authorize('notify_visits', $visit);

        $this->visitsFlowService->notifyStaffForAssignment($visit->id);

        return back()->with('success', 'Staff notified successfully.');
    }

    /**
     * Get visits awaiting assignment for the current user.
     */
    public function myVisits(): Response
    {
        $visits = $this->visitsFlowService->getAwaitingToBeAssignVisits();

        // Transform visits for the frontend
        $transformedVisits = $visits->map(function ($visit) {
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

        $staff = \App\Models\Staff::with('user')->get()->map(function ($staff) {
            return [
                'id' => $staff->id,
                'name' => $staff->user ? $staff->user->name : trim($staff->first_name.' '.$staff->last_name),
            ];
        });

        return Inertia::render('Visits/MyVisits', [
            'visits' => $transformedVisits,
            'staff' => $staff,
        ]);
    }

    /**
     * Get visits assigned to the current user that are in progress.
     */
    public function myToBeProcessVisits(): Response
    {
        // Get visits assigned to the current user that are in progress
        $visits = Visit::with(['patient.user', 'staff.user', 'appointment', 'medicalOrders'])
            ->where('staff_id', auth()->user()->staff->id ?? null)
            // ->where('status', Visit::STATUS_ASSIGNED)
            ->get();

        return Inertia::render('Visits/MyVisitProcess', [
            'visits' => $visits,
        ]);
    }
}
