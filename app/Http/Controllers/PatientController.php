<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
// Note: the application's domain `Role` model is separate from Spatie's Role model.
// Spatie Role is not imported here; we simply use the role name when assigning using Spatie's trait
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Patient::class);

        $patients = Patient::with('user')->paginate(15);

        // Transform patients for the frontend to handle null relationships
        $transformedPatients = $patients->getCollection()->map(function ($patient) {
            return [
                'id' => $patient->id,
                'user' => $patient->user ? [
                    'name' => $patient->user->name ?? 'Unknown Patient',
                    'email' => $patient->user->email ?? 'No Email',
                ] : [
                    'name' => trim($patient->first_name.' '.$patient->last_name),
                    'email' => $patient->email ?? 'No Email',
                ],
                'date_of_birth' => $patient->date_of_birth,
                'gender' => $patient->gender,
                'phone_number' => $patient->phone_number,
                'created_at' => $patient->created_at,
            ];
        });

        return Inertia::render('Patients/Index', [
            'patients' => $transformedPatients,
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
        $this->authorize('create', Patient::class);

        return Inertia::render('Patients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Create patient record without user account
        $patient = Patient::create($data);

        return redirect()->route('patients.index')
            ->with('success', 'Patient created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient): Response
    {
        $this->authorize('view', $patient);

        $patient->load([
            'user',
            'appointments.staff',
            'patientFiles.file',
            'medicalOrders',
            'appointments.visits.medicalRecord',
            'medicalOrders.medicalRecords',
        ]);

        // Collect all medical records from different sources
        $medicalRecords = collect();

        // Records linked to appointments through visits
        foreach ($patient->appointments as $appointment) {
            if ($appointment->visits) {
                foreach ($appointment->visits as $visit) {
                    if ($visit->medicalRecord) {
                        $medicalRecords->push($visit->medicalRecord);
                    }
                }
            }
        }

        // Records linked directly to medical orders
        foreach ($patient->medicalOrders as $medicalOrder) {
            $medicalRecords = $medicalRecords->merge($medicalOrder->medicalRecords ?? []);
        }

        // Remove duplicates based on ID and sort by date
        $uniqueRecords = $medicalRecords->unique('id')->sortByDesc('date_of_service')->values();

        // Also collect medical orders for display
        $medicalOrdersData = $patient->medicalOrders->map(function ($order) {
            return [
                'id' => $order->id,
                'type' => 'medical_order',
                'order_details' => $order->order_details,
                'status' => $order->status->label(),
                'priority' => $order->priority->label(),
                'ordered_at' => $order->ordered_at,
                'completed_at' => $order->completed_at,
                'staff_name' => $order->staff ? $order->staff->first_name.' '.$order->staff->last_name : null,
                'notes' => $order->notes,
            ];
        })->sortByDesc('ordered_at')->values();

        return Inertia::render('Patients/Show', [
            'patient' => array_merge($patient->toArray(), [
                'medical_records' => $uniqueRecords->toArray(),
                'medical_orders_data' => $medicalOrdersData->toArray(),
            ]),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient): Response
    {
        $this->authorize('update', $patient);

        $patient->load(['user', 'patientFiles.file']);

        return Inertia::render('Patients/Edit', [
            'patient' => $patient,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $data = $request->validated();

        // Handle user account creation (only for patients without existing user accounts)
        if ($request->boolean('create_user_account') && ! $patient->user_id) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt('password'), // Default password, should be changed by user
            ]);

            // Assign patient role
            // `App\Models\Role` is a domain model (staff roles). Spatie uses its
            // own Role model for permission checks. When creating a user account
            // for a patient we must assign the Spatie role (not the domain Role
            // model). Use the role name string so `assignRole` will work whether
            // the Spatie role exists as a model or only as a string.
            $user->assignRole('Patient');

            $patient->user_id = $user->id;
            $patient->save();

            // Remove user account fields from patient data
            unset($data['create_user_account'], $data['name'], $data['email']);
        }

        $patient->update($data);

        return redirect()->route('patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient): RedirectResponse
    {
        $this->authorize('delete', $patient);

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully.');
    }

    /**
     * Generate a comprehensive patient report as PDF.
     */
    public function generateReport(Patient $patient): \Illuminate\Http\Response
    {
        $this->authorize('view', $patient);

        $patient->load([
            'user',
            'appointments.staff.user',
            'patientFiles.file',
            'medicalOrders.staff.user',
            'appointments.visits.staff.user',
            'appointments.visits.medicalRecord',
            'medicalOrders.medicalRecords',
        ]);

        // Compile medical records from all sources
        $medicalRecords = collect();

        // Records from appointments/visits
        foreach ($patient->appointments as $appointment) {
            if ($appointment->visits) {
                foreach ($appointment->visits as $visit) {
                    if ($visit->medicalRecord) {
                        $medicalRecords->push($visit->medicalRecord);
                    }
                }
            }
        }

        // Records directly linked to medical orders
        foreach ($patient->medicalOrders as $medicalOrder) {
            $medicalRecords = $medicalRecords->merge($medicalOrder->medicalRecords ?? []);
        }

        // Remove duplicates and sort by date
        $uniqueRecords = $medicalRecords->unique('id')->sortByDesc('date_of_service');

        // Compile report data
        $report = [
            'patient_info' => [
                'id' => $patient->id,
                'name' => $patient->user?->name ?? $patient->first_name.' '.$patient->last_name,
                'title' => $patient->title,
                'first_name' => $patient->first_name,
                'last_name' => $patient->last_name,
                'native_name' => $patient->native_name,
                'date_of_birth' => $patient->date_of_birth,
                'gender' => $patient->gender,
                
                // Contact
                'email' => $patient->email ?? $patient->user?->email,
                'phone_number' => $patient->phone_number,
                'home_phone' => $patient->home_phone,
                'work_phone' => $patient->work_phone,
                'mobile_phone' => $patient->mobile_phone,
                'contact_method' => $patient->contact_method,

                // Address
                'address' => $patient->address,
                'city' => $patient->city,
                'province' => $patient->province,
                'postal_code' => $patient->postal_code,
                'country' => $patient->country,

                // Employment
                'occupation' => $patient->occupation,
                'employer' => $patient->employer,
                'employer_address' => $patient->employer_address,
                'employer_phone' => $patient->employer_phone,

                // Emergency Contact
                'emergency_contact_name' => $patient->emergency_contact_name,
                'emergency_contact_relationship' => $patient->emergency_contact_relationship,
                'emergency_contact_phone' => $patient->emergency_contact_phone,
                'emergency_contact_address' => $patient->emergency_contact_address,

                // Payment
                'payment_method' => $patient->payment_method,
                'card_holder' => $patient->card_holder,
                'card_number' => $patient->card_number ? '**** **** **** '.substr($patient->card_number, -4) : null,
                'card_expiry' => $patient->card_expiry,

                // Insurance
                'insurance_info' => $patient->insurance_info, // Keep for backward compatibility if needed
                'insurance_provider' => $patient->insurance_provider,
                'insurance_policy_number' => $patient->insurance_policy_number,
                'insurance_group_number' => $patient->insurance_group_number,
                'insurance_plan_name' => $patient->insurance_plan_name,
                'insurance_expiry' => $patient->insurance_expiry,
                'insurance_holder' => $patient->insurance_holder,
                'insurance_relationship' => $patient->insurance_relationship,

                'created_at' => $patient->created_at,
                'updated_at' => $patient->updated_at,
            ],
            'appointments' => $patient->appointments->unique('id')->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'appointment_date_time' => $appointment->appointment_date_time,
                    'duration_minutes' => $appointment->duration_minutes,
                    'reason_for_visit' => $appointment->reason_for_visit,
                    'status' => $appointment->status,
                    'staff_name' => $appointment->staff->user->name ?? 'Unknown',
                    'created_at' => $appointment->created_at,
                ];
            }),
            'visits' => $patient->appointments->pluck('visits')->flatten()->unique('id')->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'visit_date_time' => $visit->visit_date_time,
                    'status' => $visit->status,
                    'notes' => $visit->notes,
                    'staff_name' => $visit->staff?->user->name ?? null,
                    'created_at' => $visit->created_at,
                ];
            }),
            'medical_orders' => $patient->medicalOrders->unique('id')->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_type' => $order->order_type,
                    'order_details' => $order->order_details,
                    'status' => $order->status,
                    'priority' => $order->priority,
                    'ordered_at' => $order->ordered_at,
                    'completed_at' => $order->completed_at,
                    'staff_name' => $order->staff?->user->name ?? null,
                    'notes' => $order->notes,
                ];
            }),
            'medical_records' => $uniqueRecords->map(function ($record) {
                return [
                    'id' => $record->id,
                    'diagnosis' => $record->diagnosis,
                    'treatment' => $record->treatment,
                    'notes' => $record->notes,
                    'date_of_service' => $record->date_of_service,
                    'created_at' => $record->created_at,
                ];
            }),
            'patient_files' => $patient->patientFiles->unique('id')->map(function ($patientFile) {
                return [
                    'id' => $patientFile->id,
                    'file_type' => $patientFile->file_type,
                    'filename' => $patientFile->file->filename ?? 'Unknown',
                    'uploaded_at' => $patientFile->file->uploaded_at ?? null,
                    'notes' => $patientFile->notes,
                ];
            }),
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('patient-report', compact('report', 'patient'));
        
        // Set PDF options to ensure single page
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
            'isPhpEnabled' => true,
        ]);

        $filename = 'patient-report-'.$patient->id.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}
