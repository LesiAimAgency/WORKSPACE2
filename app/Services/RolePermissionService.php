<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RolePermissionService
{
    /**
     * Get paginated roles with optional filters.
     */
    public function getPaginatedRoles(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Role::with(['permissions', 'users']);

        // Apply search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply level filter
        if (! empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        // Apply default filter
        if (isset($filters['is_default'])) {
            $query->where('is_default', $filters['is_default']);
        }

        return $query->orderBy('level')->paginate($perPage);
    }

    /**
     * Create a new role with permissions.
     */
    public function createRole(array $roleData): Role
    {
        // Set default values
        $roleData['is_default'] = $roleData['is_default'] ?? false;

        // Create role
        $role = Role::create($roleData);

        // Assign permissions if provided
        if (! empty($roleData['permissions'])) {
            $this->assignPermissionsToRole($role, $roleData['permissions']);
        }

        // Log activity
        $this->logRoleActivity('role_created', "Created role: {$role->display_name} ({$role->name})", $role);

        return $role->fresh(['permissions']);
    }

    /**
     * Update role with permissions.
     */
    public function updateRole(Role $role, array $roleData): Role
    {
        // Update role
        $role->update($roleData);

        // Update permissions if provided
        if (isset($roleData['permissions'])) {
            $this->assignPermissionsToRole($role, $roleData['permissions']);
        }

        // Log activity
        $this->logRoleActivity('role_updated', "Updated role: {$role->display_name} ({$role->name})", $role);

        return $role->fresh(['permissions']);
    }

    /**
     * Delete role with safety checks.
     */
    public function deleteRole(Role $role): bool
    {
        // Safety checks
        if ($role->is_default) {
            throw new \Exception('Cannot delete default role.');
        }

        if ($role->users()->count() > 0) {
            throw new \Exception('Cannot delete role that has assigned users.');
        }

        $roleName = $role->display_name;
        $deleted = $role->delete();

        if ($deleted) {
            // Log activity
            $this->logRoleActivity('role_deleted', "Deleted role: {$roleName}", null, $role->id);
        }

        return $deleted;
    }

    /**
     * Assign permissions to role.
     */
    public function assignPermissionsToRole(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);

        // Log activity for permission changes
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $permissionNames = $permissions->pluck('display_name')->implode(', ');

        $this->logRoleActivity(
            'permissions_assigned',
            "Updated permissions for role '{$role->display_name}': {$permissionNames}",
            $role
        );
    }

    /**
     * Assign single permission to role.
     */
    public function assignPermissionToRole(Role $role, int $permissionId): bool
    {
        $permission = Permission::find($permissionId);

        if (! $permission) {
            return false;
        }

        if ($role->permissions->contains($permission)) {
            return false; // Role already has this permission
        }

        $role->permissions()->attach($permission);

        $this->logRoleActivity(
            'permission_assigned',
            "Assigned permission '{$permission->display_name}' to role: {$role->display_name}",
            $role
        );

        return true;
    }

    /**
     * Revoke permission from role.
     */
    public function revokePermissionFromRole(Role $role, int $permissionId): bool
    {
        $permission = Permission::find($permissionId);

        if (! $permission) {
            return false;
        }

        if (! $role->permissions->contains($permission)) {
            return false; // Role doesn't have this permission
        }

        $role->permissions()->detach($permission);

        $this->logRoleActivity(
            'permission_revoked',
            "Revoked permission '{$permission->display_name}' from role: {$role->display_name}",
            $role
        );

        return true;
    }

    /**
     * Set role as default.
     */
    public function setRoleAsDefault(Role $role): void
    {
        // Remove default from all other roles
        Role::where('is_default', true)->update(['is_default' => false]);

        // Set this role as default
        $role->update(['is_default' => true]);

        $this->logRoleActivity(
            'role_set_default',
            "Set role '{$role->display_name}' as default",
            $role
        );
    }

    /**
     * Get all permissions grouped by group.
     */
    public function getPermissionsGrouped(): Collection
    {
        return Permission::all()->groupBy('group');
    }

    /**
     * Create a new permission.
     */
    public function createPermission(array $permissionData): Permission
    {
        $permission = Permission::create($permissionData);

        $this->logRoleActivity(
            'permission_created',
            "Created permission: {$permission->display_name} ({$permission->name})",
            null,
            null,
            'Permission',
            $permission->id
        );

        return $permission;
    }

    /**
     * Update permission.
     */
    public function updatePermission(Permission $permission, array $permissionData): Permission
    {
        $permission->update($permissionData);

        $this->logRoleActivity(
            'permission_updated',
            "Updated permission: {$permission->display_name} ({$permission->name})",
            null,
            null,
            'Permission',
            $permission->id
        );

        return $permission;
    }

    /**
     * Delete permission with safety checks.
     */
    public function deletePermission(Permission $permission): bool
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            throw new \Exception('Cannot delete permission that is assigned to roles.');
        }

        // Check if permission is assigned to any users directly
        if ($permission->users()->count() > 0) {
            throw new \Exception('Cannot delete permission that is assigned to users.');
        }

        $permissionName = $permission->display_name;
        $deleted = $permission->delete();

        if ($deleted) {
            $this->logRoleActivity(
                'permission_deleted',
                "Deleted permission: {$permissionName}",
                null,
                null,
                'Permission',
                $permission->id
            );
        }

        return $deleted;
    }

    /**
     * Check if user has specific permission.
     */
    public function userHasPermission(User $user, string $permissionName): bool
    {
        return $user->hasPermission($permissionName);
    }

    /**
     * Check if user has any of the specified permissions.
     */
    public function userHasAnyPermission(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the specified permissions.
     */
    public function userHasAllPermissions(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $user->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all permissions for a user (direct + through roles).
     */
    public function getUserPermissions(User $user): Collection
    {
        return $user->getAllPermissions();
    }

    /**
     * Get roles that have a specific permission.
     */
    public function getRolesWithPermission(string $permissionName): Collection
    {
        return Role::whereHas('permissions', function ($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->with('permissions')->get();
    }

    /**
     * Get users that have a specific permission (direct or through roles).
     */
    public function getUsersWithPermission(string $permissionName): Collection
    {
        return User::where(function ($query) use ($permissionName) {
            $query->whereHas('permissions', function ($q) use ($permissionName) {
                $q->where('name', $permissionName);
            })->orWhereHas('roles.permissions', function ($q) use ($permissionName) {
                $q->where('name', $permissionName);
            });
        })->with(['roles', 'permissions'])->get();
    }

    /**
     * Sync permissions for a role (replace all existing permissions).
     */
    public function syncRolePermissions(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);

        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $permissionNames = $permissions->pluck('display_name')->implode(', ');

        $this->logRoleActivity(
            'permissions_synced',
            "Synced permissions for role '{$role->display_name}': {$permissionNames}",
            $role
        );
    }

    /**
     * Get role statistics.
     */
    public function getRoleStatistics(): array
    {
        return [
            'total_roles' => Role::count(),
            'default_roles' => Role::where('is_default', true)->count(),
            'roles_with_users' => Role::has('users')->count(),
            'roles_without_users' => Role::doesntHave('users')->count(),
            'total_permissions' => Permission::count(),
            'permissions_by_group' => Permission::selectRaw('`group`, COUNT(*) as count')
                ->groupBy('group')
                ->pluck('count', 'group'),
            'most_used_roles' => Role::withCount('users')
                ->orderByDesc('users_count')
                ->limit(5)
                ->get()
                ->pluck('users_count', 'display_name'),
        ];
    }

    /**
     * Get permission statistics.
     */
    public function getPermissionStatistics(): array
    {
        return [
            'total_permissions' => Permission::count(),
            'permissions_by_group' => Permission::selectRaw('`group`, COUNT(*) as count')
                ->groupBy('group')
                ->pluck('count', 'group'),
            'most_assigned_permissions' => Permission::withCount(['roles', 'users'])
                ->selectRaw('*, (roles_count + users_count) as total_assignments')
                ->orderByDesc('total_assignments')
                ->limit(10)
                ->get(),
            'unused_permissions' => Permission::doesntHave('roles')
                ->doesntHave('users')
                ->count(),
        ];
    }

    /**
     * Generate menu items based on user permissions.
     */
    public function getMenuItemsForUser(User $user, array $menuConfig): array
    {
        $visibleItems = [];

        foreach ($menuConfig as $item) {
            // If no permission is required, or user has the required permission, show the item
            if (! isset($item['permission']) || empty($item['permission']) || $user->hasPermission($item['permission'])) {
                $visibleItems[] = $item;
            }
        }

        return $visibleItems;
    }

    /**
     * Bulk assign role to multiple users.
     */
    public function bulkAssignRole(array $userIds, int $roleId): int
    {
        $role = Role::findOrFail($roleId);
        $users = User::whereIn('id', $userIds)->get();
        $assignedCount = 0;

        foreach ($users as $user) {
            if (! $user->roles->contains($role)) {
                $user->roles()->attach($role);
                $assignedCount++;

                $this->logRoleActivity(
                    'role_assigned',
                    "Bulk assigned role '{$role->display_name}' to user: {$user->name}",
                    null,
                    null,
                    'User',
                    $user->id
                );
            }
        }

        return $assignedCount;
    }

    /**
     * Bulk revoke role from multiple users.
     */
    public function bulkRevokeRole(array $userIds, int $roleId): int
    {
        $role = Role::findOrFail($roleId);
        $users = User::whereIn('id', $userIds)->get();
        $revokedCount = 0;

        foreach ($users as $user) {
            if ($user->roles->contains($role)) {
                $user->roles()->detach($role);
                $revokedCount++;

                $this->logRoleActivity(
                    'role_revoked',
                    "Bulk revoked role '{$role->display_name}' from user: {$user->name}",
                    null,
                    null,
                    'User',
                    $user->id
                );
            }
        }

        return $revokedCount;
    }

    /**
     * Log role/permission activity.
     */
    private function logRoleActivity(
        string $action,
        string $description,
        ?Role $role = null,
        ?int $roleId = null,
        string $model = 'Role',
        ?int $modelId = null
    ): void {
        if (app()->environment('testing')) {
            return; // Skip logging in tests
        }

        ActivityLog::log(
            $action,
            $description,
            null,
            $model,
            $modelId ?? $role?->id ?? $roleId
        );
    }
}
