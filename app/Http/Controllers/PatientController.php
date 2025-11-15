<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Models\Role;
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

        $patient->load(['user', 'appointments.doctor.staff']);

        return Inertia::render('Patients/Show', [
            'patient' => $patient,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient): Response
    {
        $this->authorize('update', $patient);

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
            $patientRole = Role::where('name', 'patient')->first();
            if ($patientRole) {
                $user->assignRole($patientRole);
            }

            $patient->user_id = $user->id;

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
}
