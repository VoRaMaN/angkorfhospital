<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PrescriptionController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Prescription::class);
        $prescriptions = Prescription::with(['patient.user', 'doctor.user', 'medication'])->paginate(15);

        // Transform prescriptions for the frontend
        $transformedPrescriptions = $prescriptions->getCollection()->map(function ($prescription) {
            return [
                'id' => $prescription->id,
                'patient_id' => $prescription->patient_id,
                'patient_name' => $prescription->patient?->user?->name ?? 'Unknown Patient',
                'doctor_id' => $prescription->doctor_id,
                'doctor_name' => $prescription->doctor?->user?->name ?? 'Unknown Doctor',
                'medication_id' => $prescription->medication_id,
                'medication_name' => $prescription->medication?->name ?? 'Unknown Medication',
                'dosage' => $prescription->dosage,
                'frequency' => $prescription->frequency,
                'duration' => $prescription->duration,
                'instructions' => $prescription->instructions,
                'status' => $prescription->status->value,
                'prescribed_date' => $prescription->prescribed_date->toDateString(),
                'created_at' => $prescription->created_at,
            ];
        });

        return Inertia::render('Prescriptions/Index', [
            'prescriptions' => $transformedPrescriptions,
            'filters' => [
                'search' => request('search', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Prescription::class);

        $patients = \App\Models\Patient::with('user')->get()->map(function ($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->user?->name ?? 'Unknown Patient',
            ];
        });

        $doctors = \App\Models\Staff::whereHas('role', function ($query) {
            $query->where('name', 'Doctor');
        })->with('user')->get()->map(function ($doctor) {
            return [
                'id' => $doctor->id,
                'name' => $doctor->user?->name ?? 'Unknown Doctor',
            ];
        });

        $medications = \App\Models\Medication::all()->map(function ($medication) {
            return [
                'id' => $medication->id,
                'name' => $medication->name,
            ];
        });

        return Inertia::render('Prescriptions/Create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'medications' => $medications,
        ]);
    }

    public function store(StorePrescriptionRequest $request): RedirectResponse
    {
        $prescription = Prescription::create($request->validated());

        return redirect()->route('prescriptions.index')->with('success', 'Prescription created successfully.');
    }

    public function show(Prescription $prescription): Response
    {
        $this->authorize('view', $prescription);
        $prescription->load(['patient.user', 'doctor.user', 'medication']);

        $transformedPrescription = [
            'id' => $prescription->id,
            'patient_id' => $prescription->patient_id,
            'patient_name' => $prescription->patient?->user?->name ?? 'Unknown Patient',
            'doctor_id' => $prescription->doctor_id,
            'doctor_name' => $prescription->doctor?->user?->name ?? 'Unknown Doctor',
            'medication_id' => $prescription->medication_id,
            'medication_name' => $prescription->medication?->name ?? 'Unknown Medication',
            'dosage' => $prescription->dosage,
            'frequency' => $prescription->frequency,
            'duration' => $prescription->duration,
            'instructions' => $prescription->instructions,
            'status' => $prescription->status->value,
            'prescribed_date' => $prescription->prescribed_date ? \Carbon\Carbon::parse($prescription->prescribed_date)->toDateString() : null,
            'created_at' => $prescription->created_at,
            'updated_at' => $prescription->updated_at,
        ];

        return Inertia::render('Prescriptions/Show', [
            'prescription' => $transformedPrescription,
        ]);
    }

    public function edit(Prescription $prescription): Response
    {
        $this->authorize('update', $prescription);

        $prescription->load(['patient.user', 'doctor.user', 'medication']);

        $patients = \App\Models\Patient::with('user')->get()->map(function ($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->user?->name ?? 'Unknown Patient',
            ];
        });

        $doctors = \App\Models\Staff::whereHas('role', function ($query) {
            $query->where('name', 'Doctor');
        })->with('user')->get()->map(function ($doctor) {
            return [
                'id' => $doctor->id,
                'name' => $doctor->user?->name ?? 'Unknown Doctor',
            ];
        });

        $medications = \App\Models\Medication::all()->map(function ($medication) {
            return [
                'id' => $medication->id,
                'name' => $medication->name,
            ];
        });

        $transformedPrescription = [
            'id' => $prescription->id,
            'patient_id' => $prescription->patient_id,
            'doctor_id' => $prescription->doctor_id,
            'medication_id' => $prescription->medication_id,
            'dosage' => $prescription->dosage,
            'frequency' => $prescription->frequency,
            'duration' => $prescription->duration,
            'instructions' => $prescription->instructions,
            'status' => $prescription->status->value,
            'prescribed_date' => $prescription->prescribed_date ? \Carbon\Carbon::parse($prescription->prescribed_date)->toDateString() : null,
        ];

        return Inertia::render('Prescriptions/Edit', [
            'prescription' => $transformedPrescription,
            'patients' => $patients,
            'doctors' => $doctors,
            'medications' => $medications,
        ]);
    }

    public function update(UpdatePrescriptionRequest $request, Prescription $prescription): RedirectResponse
    {
        $prescription->update($request->validated());

        return redirect()->route('prescriptions.index')->with('success', 'Prescription updated successfully.');
    }

    public function destroy(Prescription $prescription): RedirectResponse
    {
        $this->authorize('delete', $prescription);
        $prescription->delete();

        return redirect()->route('prescriptions.index')->with('success', 'Prescription deleted successfully.');
    }
}
