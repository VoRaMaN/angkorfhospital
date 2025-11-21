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
                        'name' => $visit->patient->user->name ?? $visit->patient->name,
                    ] : ['name' => $visit->patient->name],
                ] : ['user' => ['name' => 'Unknown Patient']],
                'staff' => $visit->staff ? [
                    'user' => $visit->staff->user ? [
                        'name' => $visit->staff->user->name ?? $visit->staff->name,
                    ] : ['name' => $visit->staff->name],
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
                'name' => $staff->user ? $staff->user->name : $staff->name,
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
                'name' => $staff->user ? $staff->user->name : $staff->name,
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
        $visit->update([
            'staff_id' => $request->staff_id,
            'status' => 'in_progress',
        ]);

        // Generate medical orders for this visit if none exist
        if ($visit->medicalOrders()->count() === 0) {
            $flowService = app(\App\Services\FlowService::class);
            $flowService->generateMedicalOrders($visit->id);
        }

        // Process any pending medical orders that have items
        $pendingOrders = $visit->medicalOrders()->where('status', 'pending')->get();
        $processedCount = 0;

        foreach ($pendingOrders as $order) {
            $flowService = app(\App\Services\FlowService::class);
            $flowService->assignStaffToOrder($order->id, $request->staff_id);

            // Only process orders that have items
            if ($order->orderItems()->count() > 0) {
                $flowService->processMedicalOrder($order->id);
                $processedCount++;
            }
        }

        $message = 'Staff assigned successfully.';
        if ($processedCount > 0) {
            $message .= " {$processedCount} medical order(s) with items have been processed.";
        }
        if (($pendingOrders->count() - $processedCount) > 0) {
            $message .= ' '.($pendingOrders->count() - $processedCount).' medical order(s) are waiting for items to be added.';
        }

        return redirect()->route('visits.index')
            ->with('success', $message);
    }
}
