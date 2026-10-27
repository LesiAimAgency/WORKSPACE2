<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        // Grant all permissions to superadmins
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // SuperAdmin full access
        Gate::define('manage-employees', function ($user) {
            $employee = $user->employee;

            return $user->level <= 1 || ($employee && (in_array($employee->superadmin_role, ['superadmin', 'director', 'account', 'dev']) || $employee->is_department_manager));
        });

        Gate::define('manage-contracts', function ($user) {
            $employee = $user->employee;

            return $user->level <= 1 || ($employee && in_array($employee->superadmin_role, ['director', 'account']));
        });

        Gate::define('manage-projects', function ($user) {
            $employee = $user->employee;

            return $user->level <= 1 || ($employee && in_array($employee->superadmin_role, ['director', 'dev', 'account']));
        });

        Gate::define('manage-tasks', function ($user) {
            $employee = $user->employee;

            return $user->level <= 1 || ($employee && in_array($employee->superadmin_role, ['director', 'dev', 'account']));
        });
    }
}
