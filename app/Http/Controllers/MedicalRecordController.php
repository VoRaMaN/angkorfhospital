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

        $medicalRecords = MedicalRecord::with(['appointment.patient.user', 'appointment.staff.user'])->paginate(15);

        // Transform medical records for the frontend
        $transformedRecords = $medicalRecords->getCollection()->map(function ($record) {
            return [
                'id' => $record->id,
                'patient_id' => $record->appointment?->patient_id,
                'patient_name' => $record->appointment?->patient?->user?->name ?? 'Unknown Patient',
                'doctor_id' => $record->appointment?->staff_id,
                'doctor_name' => $record->appointment?->staff?->user?->name ?? 'Unknown Doctor',
                'diagnosis' => $record->diagnosis,
                'treatment' => $record->treatment,
                'notes' => $record->notes,
                'visit_date' => $record->date_of_service ?? $record->created_at->toDateString(),
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
            ->whereDoesntHave('medicalRecord')
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

        $medicalRecord->load(['appointment.patient.user', 'appointment.staff.user']);

        $transformedRecord = [
            'id' => $medicalRecord->id,
            'patient_id' => $medicalRecord->appointment?->patient_id,
            'patient_name' => $medicalRecord->appointment?->patient?->user?->name ?? 'Unknown Patient',
            'doctor_id' => $medicalRecord->appointment?->staff_id,
            'doctor_name' => $medicalRecord->appointment?->staff?->user?->name ?? 'Unknown Doctor',
            'diagnosis' => $medicalRecord->diagnosis,
            'treatment' => $medicalRecord->treatment,
            'notes' => $medicalRecord->notes,
            'visit_date' => $medicalRecord->date_of_service,
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
            ->where(function ($query) use ($medicalRecord) {
                $query->whereDoesntHave('medicalRecord')
                    ->orWhere('id', $medicalRecord->appointment_id);
            })
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

        $transformedRecord = [
            'id' => $medicalRecord->id,
            'appointment_id' => $medicalRecord->appointment_id,
            'diagnosis' => $medicalRecord->diagnosis,
            'treatment' => $medicalRecord->treatment,
            'notes' => $medicalRecord->notes,
            'visit_date' => $medicalRecord->date_of_service ?? $medicalRecord->created_at->toDateString(),
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
}
