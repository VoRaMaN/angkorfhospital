<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Staff::class);

        $query = Staff::with(['role', 'user', 'department']);

        // Apply search filter
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                })
                    ->orWhere('first_name', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%')
                    ->orWhereHas('role', function ($roleQuery) use ($search) {
                        $roleQuery->where('name', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('department', function ($departmentQuery) use ($search) {
                        $departmentQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $staff = $query->paginate(15);

        // Transform staff for the frontend to provide flat data structure
        $transformedStaff = $staff->getCollection()->map(function ($member) {
            return [
                'id' => $member->id,
                'user_id' => $member->user_id,
                'name' => $member->user?->name ?? 'Unknown Staff',
                'email' => $member->user?->email ?? 'No Email',
                'role_name' => $member->role?->name ?? 'No Role',
                'department_name' => $member->department?->name ?? 'No Department',
                'hire_date' => $member->hire_date?->format('Y-m-d'),
                'status' => $member->status ?? 'active',
                'created_at' => $member->created_at,
            ];
        });

        return Inertia::render('Staff/Index', [
            'staff' => $transformedStaff,
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
        $this->authorize('create', Staff::class);

        $departments = \App\Models\Department::all();
        $roles = \Spatie\Permission\Models\Role::all();

        return Inertia::render('Staff/Create', [
            'roles' => $roles,
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $this->authorize('create', Staff::class);
        $validated = $request->validated();

        // Create User associated with Staff
        $user = \App\Models\User::create([
            'name' => $validated['first_name'].' '.$validated['last_name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        // Assign role to user
        $role = \Spatie\Permission\Models\Role::find($validated['role_id']);
        if ($role) {
            $user->assignRole($role);
        }

        // Create Staff with user_id
        $staffData = $validated;
        $staffData['user_id'] = $user->id;
        unset($staffData['email'], $staffData['password']); // Remove user fields

        $staff = Staff::create($staffData);

        return redirect()->route('staff.index')
            ->with('user_success', 'User created successfully.')
            ->with('staff_success', 'Staff member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff): Response
    {
        $this->authorize('view', $staff);

        $staff->load(['role', 'user', 'department', 'staffFiles.file']);

        $transformedStaff = [
            'id' => $staff->id,
            'user_id' => $staff->user_id,
            'name' => $staff->user?->name ?? 'Unknown Staff',
            'email' => $staff->user?->email ?? 'No Email',
            'role_name' => $staff->role?->name ?? 'No Role',
            'department_name' => $staff->department?->name ?? 'No Department',
            'hire_date' => $staff->hire_date ? Carbon::parse($staff->hire_date)->format('Y-m-d') : null,
            'contact_number' => $staff->contact_number,
            'status' => $staff->status ?? 'active',
            'created_at' => $staff->created_at,
            'updated_at' => $staff->updated_at,
            'staffFiles' => $staff->staffFiles,
        ];

        return Inertia::render('Staff/Show', [
            'staff' => $transformedStaff,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff): Response
    {
        $this->authorize('update', $staff);

        $staff->load(['role', 'user', 'department', 'staffFiles.file']);

        $transformedStaff = [
            'id' => $staff->id,
            'user_id' => $staff->user_id,
            'first_name' => $staff->first_name,
            'last_name' => $staff->last_name,
            'name' => $staff->user?->name ?? $staff->first_name.' '.$staff->last_name,
            'email' => $staff->user?->email ?? '',
            'role_id' => $staff->role_id,
            'department_id' => $staff->department_id,
            'contact_number' => $staff->contact_number,
            'staffFiles' => $staff->staffFiles,
        ];

        return Inertia::render('Staff/Edit', [
            'staff' => $transformedStaff,
            'roles' => \Spatie\Permission\Models\Role::all(),
            'departments' => \App\Models\Department::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStaffRequest $request, Staff $staff): RedirectResponse
    {
        $validated = $request->validated();

        // Update user if exists
        if ($staff->user) {
            $userData = [];
            if (isset($validated['email'])) {
                $userData['email'] = $validated['email'];
            }
            if (! empty($userData)) {
                $staff->user->update($userData);
            }
        }

        // Update staff
        $staffData = array_filter([
            'first_name' => $validated['first_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'role_id' => $validated['role_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
        ], fn ($value) => $value !== null);

        $staff->update($staffData);

        // Update user role if role_id changed
        if ($staff->user && isset($validated['role_id'])) {
            $role = \Spatie\Permission\Models\Role::find($validated['role_id']);
            if ($role) {
                $staff->user->syncRoles([$role]);
            }
        }

        // Update user name if first_name or last_name changed
        if ($staff->user && (isset($validated['first_name']) || isset($validated['last_name']))) {
            $newName = ($validated['first_name'] ?? $staff->first_name).' '.($validated['last_name'] ?? $staff->last_name);
            $staff->user->update(['name' => $newName]);
        }

        return redirect()->route('staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff): RedirectResponse
    {
        $this->authorize('delete', $staff);

        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }
}
