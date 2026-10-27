<?php

namespace App\Http\Middleware;

use App\Models\Project;
use App\Services\Tenancy\ProjectContext;
use App\Services\Tenancy\ProjectResolver;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        return static::resolveLoginUrl($request);
    }

    /**
     * Resolve the appropriate login URL based on project context, route, or URL prefix.
     */
    public static function resolveLoginUrl(Request $request): string
    {
        $routeName = $request->route()?->getName() ?? '';

        // 0. Standalone mode, SuperAdmin or Admin routes should always go to central /login
        if (config('app.standalone_mode') || env('STANDALONE_MODE') || str_starts_with($routeName, 'superadmin.') || $request->is('superadmin') || $request->is('superadmin/*') || str_starts_with($routeName, 'admin.') || $request->is('admin') || $request->is('admin/*')) {
            return Route::has('login') ? route('login') : url('/login');
        }

        // 1. Ehenho theme / project
        if (str_starts_with($routeName, 'ehenho.domain.') || ($request->getHost() === 'ehenho.local' && Route::has('ehenho.domain.login'))) {
            return route('ehenho.domain.login');
        }

        if (str_starts_with($routeName, 'ehenho.') || $request->is('ehenho/*') || $request->is('ehenho')) {
            if (Route::has('ehenho.login')) {
                return route('ehenho.login');
            }

            return url('/ehenho/dang-nhap');
        }

        // 2. Wkcomputer project
        if (str_starts_with($routeName, 'wkcomputer.') || $request->is('wkcomputer/*') || $request->is('wkcomputer')) {
            if (Route::has('customer.login')) {
                return route('customer.login');
            }
            if (Route::has('wkcomputer.login')) {
                return route('wkcomputer.login');
            }

            return url('/wkcomputer/dang-nhap');
        }

        // 3. Route parameter {projectCode}
        $projectCode = $request->route('projectCode');
        if ($projectCode && is_string($projectCode)) {
            if (Route::has("{$projectCode}.login")) {
                return route("{$projectCode}.login");
            }
            if (Route::has('project.login')) {
                return route('project.login', ['projectCode' => $projectCode]);
            }

            return url("/{$projectCode}/login");
        }

        // 4. Resolved project from Request attributes, ProjectContext, or ProjectResolver
        /** @var Project|null $project */
        $project = $request->attributes->get('project')
            ?? (app()->has(ProjectContext::class) ? app(ProjectContext::class)->getProject() : null);

        if (! $project && app()->has(ProjectResolver::class)) {
            try {
                $project = app(ProjectResolver::class)->resolve($request);
            } catch (\Throwable $e) {
                $project = null;
            }
        }

        if ($project) {
            $code = $project->code;

            if ($code === 'ehenho') {
                if (($request->getHost() === 'ehenho.local' || str_starts_with($routeName, 'ehenho.domain.')) && Route::has('ehenho.domain.login')) {
                    return route('ehenho.domain.login');
                }
                if (Route::has('ehenho.login')) {
                    return route('ehenho.login');
                }

                return url('/ehenho/dang-nhap');
            }

            if ($code === 'wkcomputer') {
                if (Route::has('customer.login')) {
                    return route('customer.login');
                }

                return url('/wkcomputer/dang-nhap');
            }

            if (Route::has("{$code}.login")) {
                return route("{$code}.login");
            }

            if (Route::has('project.login')) {
                return route('project.login', ['projectCode' => $code]);
            }

            return url("/{$code}/login");
        }

        // 5. First URI segment matching a project code fallback
        $firstSegment = $request->segment(1);
        if ($firstSegment && ! in_array($firstSegment, ['admin', 'superadmin', 'login', 'logout', 'api', 'livewire', 'build', 'vendor', 'storage', 'flux'])) {
            if (Route::has("{$firstSegment}.login")) {
                return route("{$firstSegment}.login");
            }
            if (Route::has('project.login')) {
                return route('project.login', ['projectCode' => $firstSegment]);
            }
        }

        // 6. Default fallback to main system login
        return Route::has('login') ? route('login') : url('/login');
    }

    /**
     * Get the guards that should be used for authentication.
     */
    protected function authenticate($request, array $guards): void
    {
        // If no guards specified, determine based on route
        if (empty($guards)) {
            $projectCode = $request->route('projectCode');
            $guards = $projectCode ? ['project'] : ['web'];
        }

        parent::authenticate($request, $guards);
    }
}
