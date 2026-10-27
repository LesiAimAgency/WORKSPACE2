<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * Get paginated users with optional filters.
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with(['roles', 'activityLogs' => function ($q) {
            $q->latest()->limit(5);
        }]);

        // Apply search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if (! empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        // Apply status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status'] === 'active');
        }

        // Apply level filter
        if (! empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        // Apply project filter (multi-tenant)
        if (! empty($filters['project_id'])) {
            $query->whereJsonContains('project_ids', (int) $filters['project_id']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Create a new user with complete information.
     */
    public function createUser(array $userData): User
    {
        // Handle avatar upload
        if (isset($userData['avatar']) && $userData['avatar'] instanceof UploadedFile) {
            $userData['avatar'] = $this->handleAvatarUpload($userData['avatar']);
        }

        // Hash password
        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        }

        // Set default values
        $userData['status'] = $userData['status'] ?? true;
        $userData['level'] = $userData['level'] ?? 2; // Default user level

        // Create user
        $user = User::create($userData);

        // Assign roles if provided
        if (! empty($userData['roles'])) {
            $this->assignRolesToUser($user, $userData['roles']);
        }

        // Log activity
        $this->logUserActivity('user_created', "Created user: {$user->name} ({$user->email})", $user);

        return $user->fresh(['roles', 'permissions']);
    }

    /**
     * Update user with complete information.
     */
    public function updateUser(User $user, array $userData): User
    {
        // Handle avatar upload
        if (isset($userData['avatar']) && $userData['avatar'] instanceof UploadedFile) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $userData['avatar'] = $this->handleAvatarUpload($userData['avatar']);
        }

        // Hash password if provided
        if (! empty($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        } else {
            unset($userData['password']);
        }

        // Update user
        $user->update($userData);

        // Update roles if provided
        if (isset($userData['roles'])) {
            $this->assignRolesToUser($user, $userData['roles']);
        }

        // Log activity
        $this->logUserActivity('user_updated', "Updated user: {$user->name} ({$user->email})", $user);

        return $user->fresh(['roles', 'permissions']);
    }

    /**
     * Delete user with safety checks.
     */
    public function deleteUser(User $user): bool
    {
        // Safety checks
        if ($user->isSuperAdmin()) {
            throw new \Exception('Cannot delete super administrator.');
        }

        if ($user->id === auth()->id()) {
            throw new \Exception('Cannot delete your own account.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Delete user
        $deleted = $user->delete();

        if ($deleted) {
            // Log activity
            $this->logUserActivity('user_deleted', "Deleted user: {$userName} ({$userEmail})", null, $user->id);
        }

        return $deleted;
    }

    /**
     * Assign roles to user.
     */
    public function assignRolesToUser(User $user, array $roleIds): void
    {
        $user->roles()->sync($roleIds);

        // Log activity for each role assignment
        $roles = Role::whereIn('id', $roleIds)->get();
        foreach ($roles as $role) {
            $this->logUserActivity(
                'role_assigned',
                "Assigned role '{$role->display_name}' to user: {$user->name}",
                $user
            );
        }
    }

    /**
     * Assign single role to user.
     */
    public function assignRoleToUser(User $user, int $roleId): bool
    {
        $role = Role::find($roleId);

        if (! $role) {
            return false;
        }

        if ($user->roles->contains($role)) {
            return false; // User already has this role
        }

        $user->roles()->attach($role);

        $this->logUserActivity(
            'role_assigned',
            "Assigned role '{$role->display_name}' to user: {$user->name}",
            $user
        );

        return true;
    }

    /**
     * Revoke role from user.
     */
    public function revokeRoleFromUser(User $user, int $roleId): bool
    {
        $role = Role::find($roleId);

        if (! $role) {
            return false;
        }

        if (! $user->roles->contains($role)) {
            return false; // User doesn't have this role
        }

        $user->roles()->detach($role);

        $this->logUserActivity(
            'role_revoked',
            "Revoked role '{$role->display_name}' from user: {$user->name}",
            $user
        );

        return true;
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleUserStatus(User $user): bool
    {
        // Safety checks
        if ($user->isSuperAdmin()) {
            throw new \Exception('Cannot disable super administrator.');
        }

        if ($user->id === auth()->id()) {
            throw new \Exception('Cannot disable your own account.');
        }

        $newStatus = ! $user->status;
        $user->update(['status' => $newStatus]);

        $action = $newStatus ? 'activated' : 'deactivated';
        $this->logUserActivity(
            'user_status_changed',
            "User {$action}: {$user->name} ({$user->email})",
            $user
        );

        return $newStatus;
    }

    /**
     * Get users by role.
     */
    public function getUsersByRole(string $roleName): Collection
    {
        return User::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        })->with('roles')->get();
    }

    /**
     * Get users with specific permission.
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
     * Assign user to project (multi-tenant support).
     */
    public function assignUserToProject(User $user, int $projectId): void
    {
        $projects = $user->project_ids ?? [];
        if (! in_array($projectId, $projects)) {
            $projects[] = $projectId;
            $user->update(['project_ids' => $projects]);

            $this->logUserActivity(
                'user_assigned_to_project',
                "Assigned user {$user->name} to project ID: {$projectId}",
                $user
            );
        }
    }

    /**
     * Remove user from project.
     */
    public function removeUserFromProject(User $user, int $projectId): void
    {
        $projects = $user->project_ids ?? [];
        $projects = array_filter($projects, fn ($id) => $id !== $projectId);
        $user->update(['project_ids' => array_values($projects)]);

        $this->logUserActivity(
            'user_removed_from_project',
            "Removed user {$user->name} from project ID: {$projectId}",
            $user
        );
    }

    /**
     * Update user preferences.
     */
    public function updateUserPreferences(User $user, array $preferences): void
    {
        $currentPreferences = $user->preferences ?? [];
        $newPreferences = array_merge($currentPreferences, $preferences);

        $user->update(['preferences' => $newPreferences]);

        $this->logUserActivity(
            'user_preferences_updated',
            "Updated preferences for user: {$user->name}",
            $user
        );
    }

    /**
     * Handle avatar file upload.
     */
    private function handleAvatarUpload(UploadedFile $file): string
    {
        return $file->store('avatars', 'public');
    }

    /**
     * Log user activity.
     */
    private function logUserActivity(string $action, string $description, ?User $user = null, ?int $userId = null): void
    {
        if (app()->environment('testing')) {
            return; // Skip logging in tests
        }

        ActivityLog::log(
            $action,
            $description,
            null,
            'User',
            $user?->id ?? $userId
        );
    }

    /**
     * Get user statistics.
     */
    public function getUserStatistics(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('status', true)->count(),
            'inactive_users' => User::where('status', false)->count(),
            'super_admins' => User::where('level', 0)->count(),
            'administrators' => User::where('level', 1)->count(),
            'regular_users' => User::where('level', '>', 1)->count(),
            'users_by_role' => Role::withCount('users')->get()->pluck('users_count', 'display_name'),
            'recent_registrations' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];
    }

    /**
     * Search users with advanced filters.
     */
    public function searchUsers(array $criteria): Collection
    {
        $query = User::with(['roles', 'permissions']);

        if (! empty($criteria['name'])) {
            $query->where('name', 'like', "%{$criteria['name']}%");
        }

        if (! empty($criteria['email'])) {
            $query->where('email', 'like', "%{$criteria['email']}%");
        }

        if (! empty($criteria['username'])) {
            $query->where('username', 'like', "%{$criteria['username']}%");
        }

        if (isset($criteria['status'])) {
            $query->where('status', $criteria['status']);
        }

        if (! empty($criteria['level'])) {
            $query->where('level', $criteria['level']);
        }

        if (! empty($criteria['role'])) {
            $query->whereHas('roles', function ($q) use ($criteria) {
                $q->where('name', $criteria['role']);
            });
        }

        if (! empty($criteria['permission'])) {
            $query->where(function ($q) use ($criteria) {
                $q->whereHas('permissions', function ($subQ) use ($criteria) {
                    $subQ->where('name', $criteria['permission']);
                })->orWhereHas('roles.permissions', function ($subQ) use ($criteria) {
                    $subQ->where('name', $criteria['permission']);
                });
            });
        }

        if (! empty($criteria['created_from'])) {
            $query->whereDate('created_at', '>=', $criteria['created_from']);
        }

        if (! empty($criteria['created_to'])) {
            $query->whereDate('created_at', '<=', $criteria['created_to']);
        }

        return $query->get();
    }
}
