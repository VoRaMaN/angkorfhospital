<?php

namespace App\Http\Controllers;

use App\Models\StaffRole as Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Models\Role as SpatieRole;

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
                'permissions_count' => SpatieRole::where('name', $role->name)->first()?->permissions()->count() ?? 0,
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

    protected function getRolePermissions(Role $role): array
    {
        $spatieRole = SpatieRole::where('name', $role->name)->first();

        if (! $spatieRole) {
            return [];
        }

        return $spatieRole->permissions->map(function ($p) {
            $nameParts = explode('_', $p->name, 2);
            $group = count($nameParts) > 1 ? $nameParts[1] : $p->name;

            return [
                'id' => $p->id,
                'name' => $p->name,
                'group' => $group,
            ];
        })->toArray();
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
                'permissions' => $this->getRolePermissions($role),
                'created_at' => $role->created_at,
                'updated_at' => $role->updated_at,
            ],
            // Also provide all available permissions grouped for parity with create/edit
            'available_permissions' => SpatiePermission::all()->map(function ($p) {
                $nameParts = explode('_', $p->name, 2);
                $group = count($nameParts) > 1 ? $nameParts[1] : $p->name;

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'group' => $group,
                ];
            }),
        ]);
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): Response
    {
        $this->authorize('create_roles');

        $permissions = SpatiePermission::all()->map(function ($p) {
            $nameParts = explode('_', $p->name, 2);
            $group = count($nameParts) > 1 ? $nameParts[1] : $p->name;

            return [
                'id' => $p->id,
                'name' => $p->name,
                'group' => $group,
            ];
        });

        return Inertia::render('settings/Roles/Create', [
            'available_permissions' => $permissions,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $this->authorize('create_roles');

        $permissionTable = config('permission.table_names.permissions', 'spatie_permissions');

        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => "exists:{$permissionTable},id",
        ]);

        DB::beginTransaction();
        try {
            Role::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Create matching Spatie role and assign permissions
            $spatieRole = SpatieRole::firstOrCreate([
                'name' => $request->name,
            ], ['guard_name' => config('auth.defaults.guard', 'web')]);

            if ($request->has('permissions')) {
                $spatieRole->syncPermissions($request->permissions);
            }

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
                // RoleForm expects initial permissions as an array of IDs
                'permissions' => SpatieRole::where('name', $role->name)->first()?->permissions()->pluck('id')->toArray() ?? [],
            ],
            'available_permissions' => SpatiePermission::all()->map(function ($p) {
                $nameParts = explode('_', $p->name, 2);
                $group = count($nameParts) > 1 ? $nameParts[1] : $p->name;

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'group' => $group,
                ];
            }),
        ]);
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('edit_roles');

        $permissionTable = config('permission.table_names.permissions', 'spatie_permissions');

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:roles,name,'.$role->id,
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => "exists:{$permissionTable},id",
        ]);

        DB::beginTransaction();
        try {
            $oldName = $role->name;

            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // Keep Spatie role in sync (rename if necessary and sync permissions)
            $spatieRole = SpatieRole::where('name', $oldName)->first();
            if ($spatieRole && $oldName !== $role->name) {
                $spatieRole->name = $role->name;
                $spatieRole->save();
            }

            $spatieRole = SpatieRole::firstOrCreate([
                'name' => $role->name,
            ], ['guard_name' => config('auth.defaults.guard', 'web')]);

            if ($request->has('permissions')) {
                $spatieRole->syncPermissions($request->permissions);
            }

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

            // Remove spatie role if it exists and is not assigned to any user
            $spatieRole = SpatieRole::where('name', $role->name)->first();
            if ($spatieRole) {
                $assignedCount = DB::table(config('permission.table_names.model_has_roles'))->where('role_id', $spatieRole->id)->count();
                if ($assignedCount === 0) {
                    $spatieRole->delete();
                }
            }

            DB::commit();

            return redirect()->route('settings.roles.index')->with('success', 'Role deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to delete role: '.$e->getMessage());
        }
    }
}
