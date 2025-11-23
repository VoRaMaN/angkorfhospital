<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Requests\UpdateMedicalRecordRequest;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', MedicalRecord::class);

        $query = MedicalRecord::with(['appointment.patient.user', 'appointment.staff.user', 'visit.patient.user', 'visit.staff.user', 'medicalOrder.patient.user', 'medicalOrder.staff.user']);

        // Apply search filter
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                // Search in patient names
                $q->whereHas('appointment.patient.user', function ($patientQuery) use ($search) {
                    $patientQuery->where('name', 'like', '%'.$search.'%');
                })
                    ->orWhereHas('appointment.patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('surname', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('visit.patient.user', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('visit.patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('surname', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('medicalOrder.patient.user', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('medicalOrder.patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('surname', 'like', '%'.$search.'%');
                    })
                // Search in staff/doctor names
                    ->orWhereHas('appointment.staff.user', function ($staffQuery) use ($search) {
                        $staffQuery->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('appointment.staff', function ($staffQuery) use ($search) {
                        $staffQuery->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('visit.staff.user', function ($staffQuery) use ($search) {
                        $staffQuery->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('visit.staff', function ($staffQuery) use ($search) {
                        $staffQuery->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('medicalOrder.staff.user', function ($staffQuery) use ($search) {
                        $staffQuery->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('medicalOrder.staff', function ($staffQuery) use ($search) {
                        $staffQuery->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%');
                    })
                // Search in diagnosis and treatment
                    ->orWhere('diagnosis', 'like', '%'.$search.'%')
                    ->orWhere('treatment', 'like', '%'.$search.'%')
                    ->orWhere('notes', 'like', '%'.$search.'%');
            });
        }

        $medicalRecords = $query->paginate(15);

        // Transform medical records for the frontend
        $transformedRecords = $medicalRecords->getCollection()->map(function ($record) {
            // Try to get patient from appointment first, then from visit, then from medical order
            $patient = $record->appointment?->patient ?? $record->visit?->patient ?? $record->medicalOrder?->patient;
            $patientName = $patient?->user?->name ?? 'Unknown Patient';

            // Try to get staff/doctor from appointment first, then from visit, then from medical order
            $staff = $record->appointment?->staff ?? $record->visit?->staff ?? $record->medicalOrder?->staff;
            $doctorName = $staff?->user?->name ?? 'Unknown Doctor';

            return [
                'id' => $record->id,
                'patient_id' => $patient?->id,
                'patient_name' => $patientName,
                'doctor_id' => $staff?->id,
                'doctor_name' => $doctorName,
                'diagnosis' => $record->diagnosis,
                'treatment' => $record->treatment,
                'notes' => $record->notes,
                'date_of_service' => $record->date_of_service?->format('Y-m-d'),
                'created_at' => $record->created_at,
            ];
        });

        return Inertia::render('MedicalRecords/Index', [
            'medicalRecords' => $transformedRecords,
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
        $this->authorize('create', MedicalRecord::class);

        $appointments = Appointment::with(['patient.user', 'staff.user'])
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'patient_name' => $appointment->patient?->user?->name ?? 'Unknown Patient',
                    'doctor_name' => $appointment->staff?->user?->name ?? 'Unknown Doctor',
                    'date' => $appointment->appointment_date_time->toDateString(),
                    'time' => $appointment->appointment_date_time->toTimeString(),
                ];
            });

        // Add "No Appointment" option
        $appointments->prepend([
            'id' => null,
            'patient_name' => 'No Appointment',
            'doctor_name' => '',
            'date' => '',
            'time' => '',
        ]);

        return Inertia::render('MedicalRecords/Create', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicalRecordRequest $request): RedirectResponse
    {
        $medicalRecord = MedicalRecord::create($request->validated());

        return redirect()->route('medical-records.index')
            ->with('success', 'Medical record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalRecord $medicalRecord): Response
    {
        $this->authorize('view', $medicalRecord);

        $medicalRecord->load(['appointment.patient.user', 'appointment.staff.user', 'visit.patient.user', 'visit.staff.user', 'medicalOrder.patient.user', 'medicalOrder.staff.user']);

        // Try to get patient from appointment first, then from visit, then from medical order
        $patient = $medicalRecord->appointment?->patient ?? $medicalRecord->visit?->patient ?? $medicalRecord->medicalOrder?->patient;
        $patientName = $patient?->user?->name ?? 'Unknown Patient';

        // Try to get staff/doctor from appointment first, then from visit, then from medical order
        $staff = $medicalRecord->appointment?->staff ?? $medicalRecord->visit?->staff ?? $medicalRecord->medicalOrder?->staff;
        $doctorName = $staff?->user?->name ?? 'Unknown Doctor';

        $transformedRecord = [
            'id' => $medicalRecord->id,
            'patient_id' => $patient?->id,
            'patient_name' => $patientName,
            'doctor_id' => $staff?->id,
            'doctor_name' => $doctorName,
            'diagnosis' => $medicalRecord->diagnosis,
            'treatment' => $medicalRecord->treatment,
            'notes' => $medicalRecord->notes,
            'date_of_service' => $medicalRecord->date_of_service?->format('Y-m-d'),
            'created_at' => $medicalRecord->created_at,
            'updated_at' => $medicalRecord->updated_at,
        ];

        return Inertia::render('MedicalRecords/Show', [
            'medicalRecord' => $transformedRecord,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalRecord $medicalRecord): Response
    {
        $this->authorize('update', $medicalRecord);

        $appointments = Appointment::with(['patient.user', 'staff.user'])
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'patient_name' => $appointment->patient?->user?->name ?? 'Unknown Patient',
                    'doctor_name' => $appointment->staff?->user?->name ?? 'Unknown Doctor',
                    'date' => $appointment->appointment_date_time->toDateString(),
                    'time' => $appointment->appointment_date_time->toTimeString(),
                ];
            });

        // Add "No Appointment" option
        $appointments->prepend([
            'id' => null,
            'patient_name' => 'No Appointment',
            'doctor_name' => '',
            'date' => '',
            'time' => '',
        ]);

        $transformedRecord = [
            'id' => $medicalRecord->id,
            'appointment_id' => $medicalRecord->appointment_id,
            'diagnosis' => $medicalRecord->diagnosis,
            'treatment' => $medicalRecord->treatment,
            'notes' => $medicalRecord->notes,
            'date_of_service' => $medicalRecord->date_of_service?->format('Y-m-d'),
        ];

        return Inertia::render('MedicalRecords/Edit', [
            'record' => $transformedRecord,
            'appointments' => $appointments,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicalRecordRequest $request, MedicalRecord $medicalRecord): RedirectResponse
    {
        $medicalRecord->update($request->validated());

        return redirect()->route('medical-records.index')
            ->with('success', 'Medical record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalRecord $medicalRecord): RedirectResponse
    {
        $this->authorize('delete', $medicalRecord);

        $medicalRecord->delete();

        return redirect()->route('medical-records.index')
            ->with('success', 'Medical record deleted successfully.');
    }

    /**
     * Generate a comprehensive medical record report as PDF.
     */
    public function generateReport(MedicalRecord $medicalRecord): \Illuminate\Http\Response
    {
        $this->authorize('view', $medicalRecord);

        $medicalRecord->load([
            'appointment.patient.user',
            'appointment.staff.user',
            'visit.patient.user',
            'visit.staff.user',
            'visit.appointment',
            'medicalOrder.patient.user',
            'medicalOrder.staff.user',
            'medicalOrder.orderItems.inventory',
        ]);

        // Try to get patient from appointment first, then from visit, then from medical order
        $patient = $medicalRecord->appointment?->patient ??
                  $medicalRecord->visit?->patient ??
                  $medicalRecord->medicalOrder?->patient;

        $patientName = $patient?->user?->name ?? 'Unknown Patient';

        // Try to get staff/doctor from appointment first, then from visit, then from medical order
        $staff = $medicalRecord->appointment?->staff ??
                $medicalRecord->visit?->staff ??
                $medicalRecord->medicalOrder?->staff;

        $doctorName = $staff?->user?->name ?? 'Unknown Doctor';

        // Compile report data
        $report = [
            'record_info' => [
                'id' => $medicalRecord->id,
                'diagnosis' => $medicalRecord->diagnosis,
                'treatment' => $medicalRecord->treatment,
                'notes' => $medicalRecord->notes,
                'date_of_service' => $medicalRecord->date_of_service,
                'created_at' => $medicalRecord->created_at,
                'updated_at' => $medicalRecord->updated_at,
            ],
            'patient_info' => $patient ? [
                'id' => $patient->id,
                'name' => $patientName,
                // Normalize date_of_birth to a Y-m-d string or null
                'date_of_birth' => (function () use ($patient) {
                    if (empty($patient->date_of_birth)) {
                        return null;
                    }
                    try {
                        return \Carbon\Carbon::parse($patient->date_of_birth)->toDateString();
                    } catch (\Exception $e) {
                        return null;
                    }
                })(),
                'gender' => $patient->gender,
                'phone_number' => $patient->phone_number,
                'email' => $patient->email ?? $patient->user?->email,
                'address' => $patient->address,
            ] : null,
            'staff_info' => $staff ? [
                'id' => $staff->id,
                'name' => $doctorName,
                'role' => $staff->role?->name ?? 'Unknown',
            ] : null,
            'appointment_info' => $medicalRecord->appointment ? [
                'id' => $medicalRecord->appointment->id,
                'appointment_date_time' => $medicalRecord->appointment->appointment_date_time,
                'duration_minutes' => $medicalRecord->appointment->duration_minutes,
                'reason_for_visit' => $medicalRecord->appointment->reason_for_visit,
                'status' => $medicalRecord->appointment->status,
                'notes' => $medicalRecord->appointment->notes,
            ] : null,
            'visit_info' => $medicalRecord->visit ? [
                'id' => $medicalRecord->visit->id,
                'visit_date_time' => $medicalRecord->visit->visit_date_time,
                'status' => $medicalRecord->visit->status,
                'notes' => $medicalRecord->visit->notes,
            ] : null,
            'medical_order_info' => $medicalRecord->medicalOrder ? [
                'id' => $medicalRecord->medicalOrder->id,
                'order_type' => $medicalRecord->medicalOrder->order_type,
                'order_details' => $medicalRecord->medicalOrder->order_details,
                'status' => $medicalRecord->medicalOrder->status,
                'priority' => $medicalRecord->medicalOrder->priority,
                'ordered_at' => $medicalRecord->medicalOrder->ordered_at,
                'completed_at' => $medicalRecord->medicalOrder->completed_at,
            ] : null,
            'medical_orders' => $medicalRecord->medicalOrder ? collect([$medicalRecord->medicalOrder])->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_type' => $order->order_type,
                    'order_details' => $order->order_details,
                    'status' => $order->status,
                    'priority' => $order->priority,
                    'ordered_at' => $order->ordered_at,
                    'completed_at' => $order->completed_at,
                ];
            }) : collect(),
            'medical_services' => collect(), // Empty for now, can be populated if needed
            'related_tests' => $medicalRecord->medicalOrder?->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_type' => $item->item_type,
                    'item_name' => $item->inventory?->item_name ?? $item->item_name ?? 'Unknown Test',
                    'quantity_required' => $item->quantity_required,
                    'quantity_used' => $item->quantity_used,
                    'status' => $item->status,
                    'completed_at' => $item->completed_at,
                    'notes' => $item->notes,
                ];
            }) ?? collect(),
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('medical-record-report', compact('report', 'medicalRecord'));

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isPhpEnabled' => true,
        ]);

        $filename = 'medical-record-report-'.$medicalRecord->id.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}
