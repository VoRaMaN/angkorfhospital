<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(): Response
    {
        $this->authorize('view_roles');

        $roles = Role::all()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'permissions_count' => 0, // Custom roles don't have direct permissions
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
            ];
        });

        return Inertia::render('settings/Roles/Index', [
            'roles' => $roles,
            'filters' => [
                'search' => request('search', ''),
            ],
        ]);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): Response
    {
        $this->authorize('view_roles');

        return Inertia::render('settings/Roles/Show', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'permissions' => [], // Custom roles don't have direct permissions
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
            ],
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): Response
    {
        $this->authorize('create_roles');

        return Inertia::render('settings/Roles/Create');
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $this->authorize('create_roles');

        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            Role::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            DB::commit();

            return redirect()->route('settings.roles.index')->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to create role: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): Response
    {
        $this->authorize('edit_roles');

        return Inertia::render('settings/Roles/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'permissions' => [], // Custom roles don't have direct permissions
            ],
        ]);
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('edit_roles');

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:roles,name,'.$role->id,
            'description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            DB::commit();

            return redirect()->route('settings.roles.index')->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to update role: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete_roles');

        // Prevent deletion of system roles
        if (in_array(strtolower($role->name), ['admin', 'doctor', 'nurse', 'receptionist', 'pharmacist'])) {
            return redirect()->back()->with('error', 'Cannot delete system roles');
        }

        DB::beginTransaction();
        try {
            // Check if role is being used by any staff
            if ($role->staff()->exists()) {
                return redirect()->back()->with('error', 'Cannot delete role that is assigned to staff members');
            }

            $role->delete();

            DB::commit();

            return redirect()->route('settings.roles.index')->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to delete role: '.$e->getMessage());
        }
    }
}
