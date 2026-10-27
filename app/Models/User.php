<?php

// MODIFIED: 2025-01-25 - Added Multi-Tenant Support

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';

    const ROLE_MANAGER = 'manager';

    const ROLE_STORE_MANAGER = 'store_manager';

    const ROLE_WEB_ADMIN = 'web_admin';

    const ROLE_MULTI_TENANCY = 'multi_tenancy';

    const ROLE_SUPER_ADMIN = 'super_admin';

    const ROLE_PROJECT_MANAGER = 'project_manager';

    const ROLE_WEB_DESIGNER = 'web_designer';

    const ROLE_DESIGNER = 'designer';

    const ROLE_EMPLOYEE = 'employee';

    public const INTERNAL_ROLES = [
        'super_admin',
        'superadmin',
        'admin',
        'project_manager',
        'manager',
        'web_designer',
        'designer',
        'employee',
        'dev',
        'account',
    ];

    public const MULTI_TENANCY_ROLES = [
        'multi_tenancy',
        'multi_tenancy_control_center',
        'cms',
    ];

    /**
     * Get the database connection for the model.
     */
    public function getConnectionName()
    {
        // If we're in a project context (project database is set), use project connection
        if (config('database.default') === 'project') {
            return 'project';
        }

        return parent::getConnectionName();
    }

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'avatar',
        'phone',
        'address',
        'status',
        'role',
        'department',
        'gold',
        'level',
        'project_ids',
        'tenant_id',
        'last_login_at',
        'preferences',
        'employee_code',
        'dob',
        'identity_card',
        'identity_date',
        'identity_place',
        'joining_date',
        'contract_type',
        'base_salary',
        'manager_id',
        'bank_account',
        'bank_name',
        'province_code',
        'district_code',
        'ward_code',
        'street_address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'role' => 'visitor',
        'level' => 2,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'project_ids' => 'array',
            'preferences' => 'array',
            'last_login_at' => 'datetime',
            'status' => 'boolean',
            'gold' => 'integer',
            'dob' => 'date',
            'identity_date' => 'date',
            'joining_date' => 'date',
            'base_salary' => 'decimal:2',
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            if ($user->email === 'admin@example.com') {
                throw new \Exception('Không thể xóa tài khoản Super Admin gốc (admin@example.com).');
            }
        });
    }

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // public function tenant()
    // {
    //     return $this->belongsTo(Tenant::class, 'tenant_id');
    // }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // public function briefs()
    // {
    //     return $this->hasMany(Brief::class, 'account_id');
    // }

    // public function tasks()
    // {
    //     return $this->hasMany(Task::class, 'dev_id');
    // }

    // public function orders()
    // {
    //     return $this->hasMany(Order::class, 'customer_email', 'email');
    // }

    // public function addresses()
    // {
    //     return $this->hasMany(UserAddress::class);
    // }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    // Methods
    public function hasRole(string|array $roles): bool
    {
        if (\is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    public function hasPermission(string $permission): bool
    {
        // Super admin có tất cả permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Check direct permissions
        if ($this->permissions()->where('name', $permission)->exists()) {
            return true;
        }

        // Check permissions through roles
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Give permission directly to user.
     */
    public function givePermissionTo(string|Permission $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission && ! $this->permissions->contains($permission)) {
            $this->permissions()->attach($permission);
        }
    }

    /**
     * Revoke permission from user.
     */
    public function revokePermissionTo(string|Permission $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->detach($permission);
        }
    }

    /**
     * Get all permissions for user (direct + through roles).
     */
    public function getAllPermissions()
    {
        $directPermissions = $this->permissions;
        $rolePermissions = $this->roles->flatMap->permissions;

        return $directPermissions->merge($rolePermissions)->unique('id');
    }

    public function assignRole(string $role): void
    {
        $roleModel = Role::where('name', $role)->first();
        if ($roleModel && ! $this->roles->contains($roleModel)) {
            $this->roles()->attach($roleModel);
        }
    }

    // Accessors
    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole(['admin', 'editor', 'support']);
    }

    public function isSuperAdmin(): bool
    {
        return (isset($this->level) && $this->level === 0)
            || $this->role === 'superadmin'
            || $this->role === 'super_admin'
            || $this->hasRole('superadmin')
            || $this->hasRole('super_admin')
            || $this->email === 'admin@example.com'
            || $this->isManager();
    }

    public function isAdministrator(): bool
    {
        return isset($this->level) && $this->level === 1;
    }

    public function isManager(): bool
    {
        return $this->role === 'manager'
            || $this->role === 'project_manager'
            || $this->hasRole('manager')
            || $this->hasRole('project_manager')
            || (isset($this->level) && $this->level === 1 && ! $this->isMultiTenancy());
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee'
            || in_array($this->role, ['web_designer', 'designer', 'dev', 'account'])
            || $this->hasRole(['employee', 'web_designer', 'designer', 'dev', 'account'])
            || (isset($this->level) && $this->level === 2 && ! $this->isMultiTenancy());
    }

    public function isVisitor(): bool
    {
        return $this->role === 'visitor' || $this->hasRole('visitor');
    }

    public function isMultiTenancy(): bool
    {
        return in_array($this->role, self::MULTI_TENANCY_ROLES)
            || $this->hasRole(self::MULTI_TENANCY_ROLES)
            || (! empty($this->tenant_id))
            || (! empty($this->project_ids));
    }

    public function isInternal(): bool
    {
        if ($this->isMultiTenancy()) {
            return false;
        }

        $nonInternal = array_merge(self::MULTI_TENANCY_ROLES, ['visitor', 'customer', 'user']);

        if (in_array($this->role, $nonInternal) || $this->hasRole($nonInternal)) {
            return false;
        }

        return true;
    }

    public function canAccessSuperAdmin(): bool
    {
        return $this->isInternal();
    }

    public function canAccessEhenhoCms(): bool
    {
        if (in_array($this->role, ['user', 'employee', 'visitor'], true)) {
            return false;
        }

        $adminRoles = [
            'cms',
            'admin',
            'dev',
            'super_admin',
            'superadmin',
            'manager',
            'web_admin',
            'store_manager',
            'multi_tenancy',
        ];

        return in_array($this->role, $adminRoles, true)
            || (isset($this->level) && (int) $this->level <= 1);
    }

    public function scopeInternal($query)
    {
        $nonInternal = array_merge(self::MULTI_TENANCY_ROLES, ['visitor', 'customer', 'user']);

        return $query->where(function ($q) use ($nonInternal) {
            $q->whereNotIn('role', $nonInternal)
                ->orWhereNull('role');
        })
            ->whereDoesntHave('roles', function ($rq) use ($nonInternal) {
                $rq->whereIn('name', $nonInternal);
            })
            ->whereNull('tenant_id')
            ->where(function ($sq) {
                $sq->whereNull('project_ids')
                    ->orWhere('project_ids', '[]')
                    ->orWhere('project_ids', 'null')
                    ->orWhere('project_ids', '""');
            });
    }

    public function scopeSuperAdminPersonnel($query)
    {
        return $query->where(function ($q) {
            // 1. Super Admin (level 0, role super_admin / superadmin, hoac email admin goc)
            $q->where('level', 0)
                ->orWhereIn('role', ['super_admin', 'superadmin'])
                ->orWhere('email', 'admin@example.com')
                ->orWhereHas('roles', function ($rq) {
                    $rq->whereIn('name', ['super_admin', 'superadmin']);
                })
                // 2. Nhan su & Quan tri noi bo (Internal)
                ->orWhere(function ($sub) {
                    $nonInternal = array_merge(self::MULTI_TENANCY_ROLES, ['visitor', 'customer', 'user', 'admin', 'store_manager', 'web_admin']);
                    $sub->where(function ($roleQ) use ($nonInternal) {
                        $roleQ->whereNotIn('role', $nonInternal)
                            ->orWhereNull('role');
                    })
                        ->whereDoesntHave('roles', function ($rq) use ($nonInternal) {
                            $rq->whereIn('name', $nonInternal);
                        })
                        ->whereNull('tenant_id')
                        ->where(function ($sq) {
                            $sq->whereNull('project_ids')
                                ->orWhere('project_ids', '[]')
                                ->orWhere('project_ids', 'null')
                                ->orWhere('project_ids', '""');
                        });
                });
        });
    }

    public function scopeSuperAdminOnly($query)
    {
        return $query->where(function ($q) {
            $q->where('level', 0)
                ->orWhereIn('role', ['super_admin', 'superadmin'])
                ->orWhere('email', 'admin@example.com')
                ->orWhereHas('roles', function ($rq) {
                    $rq->whereIn('name', ['super_admin', 'superadmin']);
                });
        });
    }

    public function scopeMultiTenancy($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('role', self::MULTI_TENANCY_ROLES)
                ->orWhereHas('roles', function ($rq) {
                    $rq->whereIn('name', self::MULTI_TENANCY_ROLES);
                })
                ->orWhereNotNull('tenant_id')
                ->orWhere(function ($sq) {
                    $sq->whereNotNull('project_ids')
                        ->where('project_ids', '!=', '[]')
                        ->where('project_ids', '!=', 'null')
                        ->where('project_ids', '!=', '""');
                });
        });
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN
            || $this->role === 'superadmin'
            || $this->role === 'super_admin'
            || $this->isSuperAdmin()
            || $this->getIsAdminAttribute();
    }

    public function isStoreManager(): bool
    {
        return $this->role === self::ROLE_STORE_MANAGER;
    }

    public function isWebAdmin(): bool
    {
        return $this->role === self::ROLE_WEB_ADMIN;
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_MANAGER,
            self::ROLE_STORE_MANAGER,
            self::ROLE_WEB_ADMIN,
            'superadmin',
            'super_admin',
        ]) || $this->isSuperAdmin();
    }

    public function canAccess(string $feature): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $restrictedForManager = ['spam', 'modules', 'seo', 'logs', 'settings'];

        $restrictedForStoreManager = [
            'spam', 'modules', 'seo', 'logs', 'settings', 'translations', 'languages',
            'users', 'appearance', 'posts', 'pages', 'form-templates', 'modal-forms',
            'flash-sales', 'coupons', 'reviews', 'agents',
        ];

        $restrictedForWebAdmin = [
            'spam', 'modules', 'seo', 'logs', 'settings', 'translations', 'languages',
            'users', 'agents', 'flash-sales', 'coupons',
        ];

        if ($this->isManager()) {
            return ! in_array($feature, $restrictedForManager);
        }

        if ($this->isStoreManager()) {
            return ! in_array($feature, $restrictedForStoreManager);
        }

        if ($this->isWebAdmin()) {
            return ! in_array($feature, $restrictedForWebAdmin);
        }

        return false;
    }

    public function getRoleNameAttribute(): string
    {
        return [
            self::ROLE_ADMIN => 'Quản trị viên',
            self::ROLE_MANAGER => 'Quản lý',
            self::ROLE_STORE_MANAGER => 'Quản lý cửa hàng',
            self::ROLE_WEB_ADMIN => 'Quản trị Website',
            'superadmin' => 'Super Admin',
            'super_admin' => 'Super Admin',
        ][$this->role] ?? 'Thành viên';
    }

    // public function agent()
    // {
    //     return $this->hasOne(Agent::class, 'user_id');
    // }

    // public function getManagedAgentId()
    // {
    //     return $this->agent?->id;
    // }

    public function hasAccessToProject(int $projectId): bool
    {
        return $this->project_ids && \in_array($projectId, $this->project_ids);
    }

    public function assignToProject(int $projectId): void
    {
        $projects = $this->project_ids ?? [];
        if (! \in_array($projectId, $projects)) {
            $projects[] = $projectId;
            $this->update(['project_ids' => $projects]);
        }
    }

    // public function profile(): HasOne
    // {
    //     return $this->hasOne(Profile::class, 'user_id');
    // }
}
