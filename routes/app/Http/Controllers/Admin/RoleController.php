<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with(['permissions', 'users']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $roles = $query->orderBy('level')->paginate(15);

        return view('cms.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('group');

        return view('cms.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'level' => 'required|integer|min:1|max:10',
            'is_default' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $validated['is_default'] = $request->boolean('is_default', false);

        $role = Role::create($validated);

        // Assign permissions
        if (! empty($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        // Log activity (only if not in testing environment)
        if (! app()->environment('testing')) {
            ActivityLog::log(
                'role_created',
                "Created role: {$role->display_name} ({$role->name})",
                null,
                'Role',
                $role->id
            );
        }

        return redirect()->route('cms.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);

        return view('cms.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('group');
        $role->load('permissions');

        return view('cms.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'level' => 'required|integer|min:1|max:10',
            'is_default' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $validated['is_default'] = $request->boolean('is_default', false);

        $role->update($validated);

        // Update permissions
        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        // Log activity (only if not in testing environment)
        if (! app()->environment('testing')) {
            ActivityLog::log(
                'role_updated',
                "Updated role: {$role->display_name} ({$role->name})",
                null,
                'Role',
                $role->id
            );
        }

        return redirect()->route('cms.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting default role
        if ($role->is_default) {
            return redirect()->route('cms.roles.index')
                ->with('error', 'Cannot delete default role.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('cms.roles.index')
                ->with('error', 'Cannot delete role that has assigned users.');
        }

        $roleName = $role->display_name;

        $role->delete();

        // Log activity (only if not in testing environment)
        if (! app()->environment('testing')) {
            ActivityLog::log(
                'role_deleted',
                "Deleted role: {$roleName}",
                null,
                'Role',
                $role->id
            );
        }

        return redirect()->route('cms.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Assign permission to role.
     */
    public function assignPermission(Request $request, Role $role)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $permission = Permission::find($request->permission_id);

        if (! $role->permissions->contains($permission)) {
            $role->permissions()->attach($permission);

            if (! app()->environment('testing')) {
                ActivityLog::log(
                    'permission_assigned',
                    "Assigned permission '{$permission->display_name}' to role: {$role->display_name}",
                    null,
                    'Role',
                    $role->id
                );
            }

            return response()->json(['success' => true, 'message' => 'Permission assigned successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Role already has this permission.']);
    }

    /**
     * Revoke permission from role.
     */
    public function revokePermission(Request $request, Role $role)
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $permission = Permission::find($request->permission_id);

        if ($role->permissions->contains($permission)) {
            $role->permissions()->detach($permission);

            if (! app()->environment('testing')) {
                ActivityLog::log(
                    'permission_revoked',
                    "Revoked permission '{$permission->display_name}' from role: {$role->display_name}",
                    null,
                    'Role',
                    $role->id
                );
            }

            return response()->json(['success' => true, 'message' => 'Permission revoked successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Role does not have this permission.']);
    }

    /**
     * Set role as default.
     */
    public function setDefault(Role $role)
    {
        // Remove default from all other roles
        Role::where('is_default', true)->update(['is_default' => false]);

        // Set this role as default
        $role->update(['is_default' => true]);

        if (! app()->environment('testing')) {
            ActivityLog::log(
                'role_set_default',
                "Set role '{$role->display_name}' as default",
                null,
                'Role',
                $role->id
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->display_name}' set as default successfully.",
        ]);
    }
}
