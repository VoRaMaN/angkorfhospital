<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['staff.role', 'doctor.department', 'patient'])->get();

        // Transform users for the frontend
        $transformedUsers = $users->map(function ($user) {
            $type = 'patient';
            $roleName = null;
            $departmentName = null;

            if ($user->staff) {
                $type = 'staff';
                $roleName = $user->staff->role->name ?? null;
            }

            if ($user->doctor) {
                $type = 'doctor';
                $departmentName = $user->doctor->department->name ?? null;
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type' => $type,
                'role_name' => $roleName,
                'department_name' => $departmentName,
                'status' => $user->email_verified_at ? 'active' : 'inactive',
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
            ];
        });

        return Inertia::render('settings/UserManager', [
            'users' => $transformedUsers,
            'filters' => [
                'search' => request('search', ''),
                'type' => request('type', ''),
                'status' => request('status', ''),
            ],
            'stats' => [
                'total_users' => $users->count(),
                'active_users' => $users->whereNotNull('email_verified_at')->count(),
                'staff_count' => $users->whereNotNull('staff')->count(),
                'doctor_count' => $users->whereNotNull('doctor')->count(),
                'patient_count' => $users->whereNotNull('patient')->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Settings/UserManagement/Create', [
            'roles' => \App\Models\Role::all(),
            'departments' => \App\Models\Department::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        // Create the specific type
        switch ($validated['type']) {
            case 'staff':
                Staff::create([
                    'user_id' => $user->id,
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'role_id' => $validated['role_id'],
                    'contact_number' => $validated['contact_number'],
                    'hire_date' => $validated['hire_date'],
                ]);
                break;

            case 'doctor':
                // First create staff record for the doctor
                $staff = Staff::create([
                    'user_id' => $user->id,
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'role_id' => 2, // Assuming doctor role exists
                    'contact_number' => $validated['contact_number'] ?? '',
                    'hire_date' => now(),
                ]);

                Doctor::create([
                    'user_id' => $user->id,
                    'staff_id' => $staff->id,
                    'specialization' => $validated['specialization'],
                    'department_id' => $validated['department_id'],
                    'license_number' => $validated['license_number'],
                ]);
                break;

            case 'patient':
                Patient::create([
                    'user_id' => $user->id,
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'date_of_birth' => $validated['date_of_birth'],
                    'gender' => $validated['gender'],
                    'address' => $validated['address'],
                    'phone_number' => $validated['phone_number'],
                    'insurance_info' => $validated['insurance_info'],
                ]);
                break;
        }

        return redirect()->route('settings.user-management.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['staff.role', 'doctor.department', 'patient']);

        return Inertia::render('Settings/UserManagement/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load(['staff.role', 'doctor.department', 'patient']);

        return Inertia::render('Settings/UserManagement/Edit', [
            'user' => $user,
            'roles' => \App\Models\Role::all(),
            'departments' => \App\Models\Department::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (isset($validated['password'])) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        // Update specific type
        if ($user->staff) {
            $user->staff->update([
                'first_name' => $validated['first_name'] ?? $user->staff->first_name,
                'last_name' => $validated['last_name'] ?? $user->staff->last_name,
                'role_id' => $validated['role_id'] ?? $user->staff->role_id,
                'contact_number' => $validated['contact_number'] ?? $user->staff->contact_number,
                'hire_date' => $validated['hire_date'] ?? $user->staff->hire_date,
            ]);
        }

        if ($user->doctor) {
            $user->doctor->update([
                'specialization' => $validated['specialization'] ?? $user->doctor->specialization,
                'department_id' => $validated['department_id'] ?? $user->doctor->department_id,
                'license_number' => $validated['license_number'] ?? $user->doctor->license_number,
            ]);
            $user->doctor->staff->update([
                'first_name' => $validated['first_name'] ?? $user->doctor->staff->first_name,
                'last_name' => $validated['last_name'] ?? $user->doctor->staff->last_name,
                'contact_number' => $validated['contact_number'] ?? $user->doctor->staff->contact_number,
            ]);
        }

        if ($user->patient) {
            $user->patient->update([
                'first_name' => $validated['first_name'] ?? $user->patient->first_name,
                'last_name' => $validated['last_name'] ?? $user->patient->last_name,
                'date_of_birth' => $validated['date_of_birth'] ?? $user->patient->date_of_birth,
                'gender' => $validated['gender'] ?? $user->patient->gender,
                'address' => $validated['address'] ?? $user->patient->address,
                'phone_number' => $validated['phone_number'] ?? $user->patient->phone_number,
                'insurance_info' => $validated['insurance_info'] ?? $user->patient->insurance_info,
            ]);
        }

        return redirect()->route('settings.user-management.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Delete related records first
        if ($user->staff) {
            $user->staff->delete();
        }
        if ($user->doctor) {
            $user->doctor->delete();
        }
        if ($user->patient) {
            $user->patient->delete();
        }

        $user->delete();

        return redirect()->route('settings.user-management.index')
            ->with('success', 'User deleted successfully.');
    }
}
