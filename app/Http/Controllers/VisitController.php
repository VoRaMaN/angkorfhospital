<?php

namespace App\Http\Controllers;

use App\Exports\VisitsExport;
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
        // Auto-archive: mark yesterday's (and older) active visits as completed
        Visit::whereIn('status', [
            Visit::STATUS_PENDING,
            Visit::STATUS_AWAITING_ASSIGNMENT,
            Visit::STATUS_ASSIGNED,
            Visit::STATUS_IN_PROGRESS,
            Visit::STATUS_SENT_BACK,
            Visit::STATUS_AWAITING_ACCOUNTANT,
        ])
            ->whereDate('visit_date_time', '<', today())
            ->update(['status' => Visit::STATUS_COMPLETED]);

        $query = Visit::with(['patient.user', 'staff.user', 'doctor.user', 'appointment', 'medicalOrders']);

        // Patient filter - if patient ID is provided, show all visits for that patient
        $patientFilter = request('patient');
        if ($patientFilter) {
            $query->where('patient_id', $patientFilter);
            // Don't exclude completed visits when viewing patient history
        } else {
            // Exclude completed and cancelled visits by default (show only active visits)
            $query->whereNotIn('status', [Visit::STATUS_COMPLETED, Visit::STATUS_CANCELLED]);
        }

        // Date filter
        $dateFilter = request('date');
        $fromFilter = request('from');
        $toFilter = request('to');

        if ($fromFilter || $toFilter) {
            // Date range filter
            if ($fromFilter) {
                $query->whereDate('visit_date_time', '>=', $fromFilter);
            }
            if ($toFilter) {
                $query->whereDate('visit_date_time', '<=', $toFilter);
            }
        } elseif ($dateFilter) {
            // User selected a specific date
            $query->whereDate('visit_date_time', $dateFilter);
        } elseif (! $patientFilter) {
            // Default: show only today's visits (unless viewing patient history)
            $query->whereDate('visit_date_time', today());
        }

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
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhere('notes', 'like', '%'.$search.'%');
            });
        }

        $visits = $query->paginate(15);

        // Transform visits for the frontend
        $transformedVisits = $visits->getCollection()->map(function ($visit) {
            $patientName = $visit->patient?->full_name ?: ($visit->patient ? 'Patient #'.$visit->patient->id : 'Unknown Patient');

            return [
                'id' => $visit->id,
                'patient' => $visit->patient ? [
                    'user' => [
                        'name' => $patientName,
                    ],
                ] : ['user' => ['name' => 'Unknown Patient']],
                'staff' => $visit->staff ? [
                    'user' => $visit->staff->user ? [
                        'name' => $visit->staff->user->name ?? $visit->staff->name,
                    ] : ['name' => $visit->staff->name],
                ] : ['user' => ['name' => 'Unassigned']],
                'doctor' => $visit->doctor ? [
                    'id' => $visit->doctor->id,
                    'user' => $visit->doctor->user ? [
                        'name' => $visit->doctor->user->name ?? $visit->doctor->name,
                    ] : ['name' => $visit->doctor->name],
                ] : null,
                'appointment' => $visit->appointment,
                'visit_date_time' => $visit->visit_date_time->format('d/m/y H:i'),
                'status' => $visit->status,
                'notes' => $visit->notes,
                'created_at' => $visit->created_at->format('d/m/y H:i'),
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

        // Get only doctors (role_id = 2)
        $doctors = \App\Models\Staff::with('user')->where('role_id', 2)->get()->map(function ($doctor) {
            return [
                'id' => $doctor->id,
                'name' => $doctor->user ? $doctor->user->name : $doctor->name,
            ];
        });

        // Get patient name if filtering by patient
        $patientName = null;
        if ($patientFilter) {
            $patient = \App\Models\Patient::find($patientFilter);
            if ($patient) {
                $patientName = $patient->full_name ?: 'Patient #'.$patient->id;
            }
        }

        return Inertia::render('Visits/Index', [
            'visits' => $transformedVisits,
            'staff' => $staff,
            'doctors' => $doctors,
            'filters' => [
                'search' => request('search', ''),
                'date' => request('date', ''),
                'from' => request('from', ''),
                'to' => request('to', ''),
                'patient' => $patientFilter,
            ],
            'patientName' => $patientName,
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
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'patient' => [
                        'name' => $appointment->patient?->full_name ?: 'Unknown Patient',
                    ],
                    'appointment_date_time' => $appointment->appointment_date_time->format('d/m/y H:i'),
                ];
            });

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

        // Format dates
        $visitData = [
            'id' => $visit->id,
            'patient' => $visit->patient ? [
                'user' => [
                    'name' => $visit->patient->full_name ?: 'Patient #'.$visit->patient->id,
                ],
            ] : null,
            'staff' => $visit->staff ? [
                'user' => [
                    'name' => $visit->staff->user->name ?? $visit->staff->name,
                ],
            ] : null,
            'appointment' => $visit->appointment ? [
                'id' => $visit->appointment->id,
                'appointment_date_time' => $visit->appointment->appointment_date_time->format('d/m/y H:i'),
            ] : null,
            'visit_date_time' => $visit->visit_date_time->format('d/m/y H:i'),
            'status' => $visit->status,
            'notes' => $visit->notes,
            'created_at' => $visit->created_at->format('d/m/y H:i'),
            'medicalOrders' => $visit->medicalOrders,
        ];

        $staff = \App\Models\Staff::with('user')->get()->map(function ($staff) {
            return [
                'id' => $staff->id,
                'name' => $staff->user ? $staff->user->name : $staff->name,
            ];
        });

        return Inertia::render('Visits/Show', [
            'visit' => $visitData,
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
        $appointments = \App\Models\Appointment::with(['patient.user', 'staff.user'])
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'patient' => [
                        'name' => $appointment->patient?->full_name ?: 'Unknown Patient',
                    ],
                    'appointment_date_time' => $appointment->appointment_date_time->format('d/m/y H:i'),
                ];
            });

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

        // Prevent duplicate visits for the same patient and date
        $visitDate = date('Y-m-d', strtotime($request->visit_date_time));
        $duplicate = Visit::where('patient_id', $request->patient_id)
            ->whereDate('visit_date_time', $visitDate)
            ->where('id', '!=', $visit->id)
            ->exists();
        if ($duplicate) {
            return back()->withErrors(['visit_date_time' => 'A visit for this patient already exists on this date.']);
        }

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
     * Assign doctor to the visit for treatment.
     */
    public function assignDoctor(Request $request, Visit $visit)
    {
        $this->authorize('assign_visits', $visit);

        $request->validate([
            'doctor_id' => 'required|exists:staff,id',
        ]);

        // Assign doctor to the visit
        $visit->update(['doctor_id' => $request->doctor_id]);

        // Update billings to track which doctor gets paid
        foreach ($visit->billings as $billing) {
            $billing->update(['doctor_id' => $request->doctor_id]);
        }

        return back()->with('success', 'Doctor assigned successfully.');
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
            $patientName = $visit->patient?->full_name ?: ($visit->patient ? 'Patient #'.$visit->patient->id : 'Unknown Patient');

            return [
                'id' => $visit->id,
                'patient' => $visit->patient ? [
                    'user' => [
                        'name' => $patientName,
                    ],
                ] : ['user' => ['name' => 'Unknown Patient']],
                'appointment' => $visit->appointment,
                'visit_date_time' => $visit->visit_date_time->format('d/m/y H:i'),
                'status' => $visit->status,
                'notes' => $visit->notes,
                'created_at' => $visit->created_at->format('d/m/y H:i'),
                'medical_orders' => $visit->medicalOrders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'status' => $order->status->value,
                    ];
                }),
            ];
        });

        $staff = \App\Models\Staff::with('user')
            ->whereHas('role', function ($query) {
                $query->where('name', 'Doctor');
            })
            ->get()
            ->map(function ($staff) {
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
        // Auto-archive: mark expired active visits as completed
        Visit::whereIn('status', [
            Visit::STATUS_PENDING,
            Visit::STATUS_AWAITING_ASSIGNMENT,
            Visit::STATUS_ASSIGNED,
            Visit::STATUS_IN_PROGRESS,
            Visit::STATUS_SENT_BACK,
            Visit::STATUS_AWAITING_ACCOUNTANT,
        ])
            ->whereDate('visit_date_time', '<', today())
            ->update(['status' => Visit::STATUS_COMPLETED]);

        $query = Visit::with(['patient.user', 'staff.user', 'appointment', 'medicalOrders'])
            ->whereNotIn('status', [Visit::STATUS_COMPLETED, Visit::STATUS_CANCELLED]);

        // Date filter - defaults to today
        $dateFilter = request('date');
        if ($dateFilter) {
            $query->whereDate('visit_date_time', $dateFilter);
        } else {
            $query->whereDate('visit_date_time', today());
        }

        // Search filter
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient.user', function ($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', '%'.$search.'%');
                })
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('surname', 'like', '%'.$search.'%');
                    });
            });
        }

        $visits = $query->get();

        // Transform visits for the frontend
        $transformedVisits = $visits->map(function ($visit) {
            $patientName = $visit->patient?->full_name ?: ($visit->patient ? 'Patient #'.$visit->patient->id : 'Unknown Patient');

            return [
                'id' => $visit->id,
                'patient' => $visit->patient ? [
                    'user' => [
                        'name' => $patientName,
                    ],
                    'name' => $visit->patient->name,
                    'surname' => $visit->patient->surname,
                ] : ['user' => ['name' => 'Unknown Patient'], 'name' => '', 'surname' => ''],
                'appointment' => $visit->appointment,
                'visit_date_time' => $visit->visit_date_time->format('d/m/y H:i'),
                'status' => $visit->status,
                'notes' => $visit->notes,
                'created_at' => $visit->created_at->format('d/m/y H:i'),
                'medical_orders' => $visit->medicalOrders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'status' => $order->status->value,
                    ];
                }),
            ];
        });

        return Inertia::render('Visits/MyVisitProcess', [
            'visits' => $transformedVisits,
            'filters' => [
                'search' => request('search', ''),
                'date' => request('date', today()->toDateString()),
            ],
        ]);
    }

    public function export()
    {
        $this->authorize('viewAny', Visit::class);

        $filters = [
            'search' => request('search', ''),
            'date' => request('date', ''),
            'patient' => request('patient', ''),
            'from' => request('from', ''),
            'to' => request('to', ''),
        ];

        return (new VisitsExport($filters))->download();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
        $this->authorize('delete', $visit);

        $visit->delete();

        return redirect()->route('visits.index')
            ->with('success', 'Visit deleted successfully.');
    }
}
