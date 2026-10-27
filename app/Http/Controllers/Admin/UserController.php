<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Get the index route based on current context.
     */
    protected function getIndexRoute()
    {
        return route('admin.users.index');
    }

    // Middleware is now handled in routes or via attributes in Laravel 12

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $isSuperAdminRoute = request()->routeIs('superadmin.*');
        $type = $request->get('type', 'all'); // 'all', 'super_admin', 'internal', 'multi_tenancy'

        if ($isSuperAdminRoute) {
            // Khi o SuperAdmin: mac dinh chi hien thi tai khoan SuperAdmin & Nhan su noi bo (10 tai khoan he thong)
            $totalCount = User::superAdminPersonnel()->count();
            $superAdminCount = User::superAdminOnly()->count();
            $internalCount = User::internal()->count();
            $multiTenancyCount = User::multiTenancy()->count();

            $query = User::superAdminPersonnel()->with(['roles', 'tenant', 'activityLogs' => function ($q) {
                $q->latest()->limit(5);
            }]);

            // Filter by tab type
            if ($type === 'super_admin') {
                $query = User::superAdminOnly()->with(['roles', 'tenant', 'activityLogs' => function ($q) {
                    $q->latest()->limit(5);
                }]);
            } elseif ($type === 'internal') {
                $query = User::internal()->with(['roles', 'tenant', 'activityLogs' => function ($q) {
                    $q->latest()->limit(5);
                }]);
            } elseif ($type === 'multi_tenancy') {
                $query = User::multiTenancy()->with(['roles', 'tenant', 'activityLogs' => function ($q) {
                    $q->latest()->limit(5);
                }]);
            }

            $roles = Role::whereNotIn('name', ['visitor', 'multi_tenancy'])->get();
        } else {
            // Context ngoai superadmin (trong CMS du an)
            $projectCode = request()->route('projectCode');
            $currentProject = request()->attributes->get('project') ?? ($projectCode ? Project::where('code', $projectCode)->first() : null);

            $projectUserScope = null;
            if ($currentProject) {
                $pId = $currentProject->id;
                $tId = $currentProject->tenant_id;

                $projectUserScope = function ($q) use ($pId, $tId) {
                    $q->where(function ($sub) use ($pId, $tId) {
                        if ($tId) {
                            $sub->where('tenant_id', $tId);
                        }
                        $sub->orWhereJsonContains('project_ids', $pId)
                            ->orWhereJsonContains('project_ids', (string) $pId);
                    });
                };
            }

            if ($projectUserScope) {
                $totalCount = User::where($projectUserScope)->count();
                $multiTenancyCount = User::multiTenancy()->where($projectUserScope)->count();
                $internalCount = User::internal()->where($projectUserScope)->count();
                $superAdminCount = User::superAdminOnly()->where($projectUserScope)->count();

                $query = User::where($projectUserScope)->with(['roles', 'tenant', 'activityLogs' => function ($q) {
                    $q->latest()->limit(5);
                }]);
            } else {
                $totalCount = User::count();
                $multiTenancyCount = User::multiTenancy()->count();
                $internalCount = User::internal()->count();
                $superAdminCount = User::superAdminOnly()->count();

                $query = User::with(['roles', 'tenant', 'activityLogs' => function ($q) {
                    $q->latest()->limit(5);
                }]);
            }

            if ($type === 'multi_tenancy') {
                $query->multiTenancy();
            } elseif ($type === 'internal') {
                $query->internal();
            } elseif ($type === 'super_admin') {
                $query->superAdminOnly();
            }

            $roles = Role::where('name', '!=', 'visitor')->get();
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $roleParam = $request->role;
            $query->where(function ($q) use ($roleParam) {
                $q->where('role', $roleParam)
                    ->orWhereHas('roles', function ($rq) use ($roleParam) {
                        $rq->where('name', $roleParam);
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $projects = Project::select('id', 'name', 'code')->get()->keyBy('id');

        return view('cms.users.index', compact(
            'users',
            'roles',
            'type',
            'totalCount',
            'superAdminCount',
            'internalCount',
            'multiTenancyCount',
            'projects'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $isSuperAdminRoute = request()->routeIs('superadmin.*');
        $roles = $isSuperAdminRoute
            ? Role::whereNotIn('name', ['visitor', 'multi_tenancy'])->get()
            : Role::where('name', '!=', 'visitor')->get();

        $managers = User::where('status', true)
            ->where(function ($q) {
                $q->where('role', 'manager')
                    ->orWhereHas('roles', function ($q2) {
                        $q2->where('name', 'manager');
                    });
            })->get();
        $departments = Department::where('status', 'active')->pluck('name');
        $projects = $isSuperAdminRoute ? collect() : Project::select('id', 'name', 'code')->orderBy('name')->get();

        return view('cms.users.create', compact('roles', 'managers', 'departments', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'status' => 'boolean',
            'employee_code' => 'nullable|string|max:50|unique:users',
            'dob' => 'nullable|date',
            'identity_card' => 'nullable|string|max:50',
            'identity_date' => 'nullable|date',
            'identity_place' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'contract_type' => 'nullable|string|max:50',
            'base_salary' => 'nullable|numeric|min:0',
            'manager_id' => 'nullable|exists:users,id',
            'bank_account' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'province_code' => 'nullable|string|max:50',
            'district_code' => 'nullable|string|max:50',
            'ward_code' => 'nullable|string|max:50',
            'street_address' => 'nullable|string|max:500',
            'full_address' => 'nullable|string|max:1000',
            'project_ids' => 'nullable|array',
            'project_ids.*' => 'exists:projects,id',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $request->boolean('status', true);
        if ($request->has('full_address')) {
            $validated['address'] = $request->full_address;
        }

        if ($request->filled('project_ids')) {
            $validated['project_ids'] = array_values(array_map('intval', (array) $request->project_ids));
            $firstProj = Project::find($validated['project_ids'][0] ?? null);
            if ($firstProj && $firstProj->tenant_id) {
                $validated['tenant_id'] = $firstProj->tenant_id;
            }
        }

        $user = User::create($validated);

        // Assign roles
        if (! empty($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
            $firstRole = Role::find($validated['roles'][0]);
            if ($firstRole) {
                $user->update([
                    'role' => $firstRole->name,
                    'level' => ($firstRole->name === 'multi_tenancy' ? 2 : ($firstRole->level ?? 2)),
                ]);
            }
        }

        // Log activity (only if not in testing environment)
        if (! app()->environment('testing')) {
            ActivityLog::log(
                'user_created',
                "Created user: {$user->name} ({$user->email})",
                null,
                'User',
                $user->id
            );
        }

        // Send welcome email (optional)
        if ($request->boolean('send_welcome_email')) {
            // Mail::to($user)->send(new WelcomeEmail($user));
        }

        return redirect($this->getIndexRoute())
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'permissions', 'activityLogs' => function ($q) {
            $q->latest()->limit(20);
        }]);

        return view('cms.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $isSuperAdminRoute = request()->routeIs('superadmin.*');
        $roles = $isSuperAdminRoute
            ? Role::whereNotIn('name', ['visitor', 'multi_tenancy'])->get()
            : Role::where('name', '!=', 'visitor')->get();

        $managers = User::where('status', true)
            ->where('id', '!=', $user->id)
            ->where(function ($q) {
                $q->where('role', 'manager')
                    ->orWhereHas('roles', function ($q2) {
                        $q2->where('name', 'manager');
                    });
            })->get();
        $departments = Department::where('status', 'active')->pluck('name');
        $projects = $isSuperAdminRoute ? collect() : Project::select('id', 'name', 'code')->orderBy('name')->get();
        $user->load('roles');

        return view('cms.users.edit', compact('user', 'roles', 'managers', 'departments', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'status' => 'boolean',
            'preferences' => 'nullable|array',
            'employee_code' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'dob' => 'nullable|date',
            'identity_card' => 'nullable|string|max:50',
            'identity_date' => 'nullable|date',
            'identity_place' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'contract_type' => 'nullable|string|max:50',
            'base_salary' => 'nullable|numeric|min:0',
            'manager_id' => 'nullable|exists:users,id',
            'bank_account' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'province_code' => 'nullable|string|max:50',
            'district_code' => 'nullable|string|max:50',
            'ward_code' => 'nullable|string|max:50',
            'street_address' => 'nullable|string|max:500',
            'full_address' => 'nullable|string|max:1000',
            'project_ids' => 'nullable|array',
            'project_ids.*' => 'exists:projects,id',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Only update password if provided
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['status'] = $request->boolean('status', true);
        if ($request->has('full_address')) {
            $validated['address'] = $request->full_address;
        }

        if ($request->has('project_ids')) {
            $validated['project_ids'] = $request->filled('project_ids')
                ? array_values(array_map('intval', (array) $request->project_ids))
                : null;
            if (! empty($validated['project_ids'])) {
                $firstProj = Project::find($validated['project_ids'][0] ?? null);
                if ($firstProj && $firstProj->tenant_id) {
                    $validated['tenant_id'] = $firstProj->tenant_id;
                }
            }
        }

        $user->update($validated);

        // Update roles
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
            if (count($validated['roles']) > 0) {
                $firstRole = Role::find($validated['roles'][0]);
                if ($firstRole) {
                    $user->update([
                        'role' => $firstRole->name,
                        'level' => ($firstRole->name === 'multi_tenancy' ? 2 : ($firstRole->level ?? $user->level)),
                    ]);
                }
            }
        }

        // Log activity (only if not in testing environment)
        if (! app()->environment('testing')) {
            ActivityLog::log(
                'user_updated',
                "Updated user: {$user->name} ({$user->email})",
                null,
                'User',
                $user->id
            );
        }

        return back()->with('success', 'Cập nhật tài khoản thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting super admin
        if ($user->isSuperAdmin()) {
            return redirect($this->getIndexRoute())
                ->with('error', 'Cannot delete super administrator.');
        }

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect($this->getIndexRoute())
                ->with('error', 'Cannot delete your own account.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        $user->delete();

        // Log activity (only if not in testing environment)
        if (! app()->environment('testing')) {
            ActivityLog::log(
                'user_deleted',
                "Deleted user: {$userName} ({$userEmail})",
                null,
                'User',
                $user->id
            );
        }

        return redirect($this->getIndexRoute())
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Assign role to user.
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);

        if (! $user->roles->contains($role)) {
            $user->roles()->attach($role);

            if (! app()->environment('testing')) {
                ActivityLog::log(
                    'role_assigned',
                    "Assigned role '{$role->display_name}' to user: {$user->name}",
                    null,
                    'User',
                    $user->id
                );
            }

            return response()->json(['success' => true, 'message' => 'Role assigned successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'User already has this role.']);
    }

    /**
     * Revoke role from user.
     */
    public function revokeRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);

        if ($user->roles->contains($role)) {
            $user->roles()->detach($role);

            if (! app()->environment('testing')) {
                ActivityLog::log(
                    'role_revoked',
                    "Revoked role '{$role->display_name}' from user: {$user->name}",
                    null,
                    'User',
                    $user->id
                );
            }

            return response()->json(['success' => true, 'message' => 'Role revoked successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'User does not have this role.']);
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user)
    {
        // Prevent disabling super admin
        if ($user->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Cannot disable super administrator.']);
        }

        // Prevent self-disabling
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Cannot disable your own account.']);
        }

        $user->update(['status' => ! $user->status]);

        $action = $user->status ? 'activated' : 'deactivated';

        if (! app()->environment('testing')) {
            ActivityLog::log(
                'user_status_changed',
                "User {$action}: {$user->name} ({$user->email})",
                null,
                'User',
                $user->id
            );
        }

        return response()->json([
            'success' => true,
            'message' => "User {$action} successfully.",
            'status' => $user->status,
        ]);
    }
}
