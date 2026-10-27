<?php

namespace App\Http\Middleware;

use App\Core\Theme\ThemeManager;
use App\Models\Category;
use App\Models\Project;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Wkcomputer\WkProduct;
use App\Services\ViettinmartDataSyncService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class ProjectSubdomainMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $projectCode = $request->route('projectCode') ?: $request->segment(1);

        // Block placeholder URLs
        if ($projectCode && (str_contains($projectCode, '{') || str_contains($projectCode, '}'))) {
            abort(404, 'Invalid project code format');
        }

        $isStandalone = config('app.standalone_mode') || env('STANDALONE_MODE') || in_array($projectCode, ['admin', 'login', 'logout']);

        if ($isStandalone) {
            try {
                $project = Project::where('code', env('PROJECT_CODE', 'DA005'))->first() ?: Project::first();
            } catch (\Throwable $e) {
                $project = null;
            }

            if (! $project) {
                if (! file_exists(storage_path('installed.lock')) || env('DB_DATABASE') === '__DB_DATABASE__') {
                    return redirect('/install');
                }

                $project = new Project([
                    'id' => 1,
                    'code' => env('PROJECT_CODE', 'DA005'),
                    'name' => env('PROJECT_NAME', 'Website'),
                    'tenant_id' => 1,
                    'features' => ['theme' => env('THEME', 'inbetween_v2')],
                ]);
            }
        }

        if (! $project && $projectCode) {
            $project = Project::where('code', $projectCode)
                ->orWhere('external_domain', $projectCode)
                ->first();

            // Dynamic match by code or subdomain contains project code / theme slug
            if (! $project) {
                $project = Project::where('code', 'like', "%{$projectCode}%")
                    ->orWhere('subdomain', 'like', "%{$projectCode}%")
                    ->first();
            }

            // Fallback for DA010 / ehenho aliases for backward compatibility
            if (! $project && in_array(strtoupper($projectCode), ['EHENHO', 'DA010', 'DA010-EHENHO-DATING-SOCIAL-NETWORK'])) {
                $project = Project::where('code', 'DA010')
                    ->orWhere('code', 'DA010-EHENHO-DATING-SOCIAL-NETWORK')
                    ->orWhere('code', 'ehenho')
                    ->orWhere('external_domain', 'ehenho.local')
                    ->first();
            }

            // Fallback for inbetween / inbetween_v2 aliases -> always resolve canonical DA005
            if (! $project && in_array(strtolower($projectCode), ['inbetween', 'inbetween_v2', 'inbetween-v2', 'inbetwen', 'da005'])) {
                $project = Project::where('code', 'DA005')
                    ->orWhere('code', 'inbetween_v2')
                    ->orWhereJsonContains('features->theme', 'inbetween_v2')
                    ->orWhere('name', 'like', '%INBETWEEN%')
                    ->first();
            }

            if (! $project) {
                try {
                    if (Project::count() === 1) {
                        $project = Project::first();
                    }
                } catch (\Throwable $e) {
                }
            }
        } elseif (! $project) {
            // For direct domain access
            try {
                $project = Project::first();
            } catch (\Throwable $e) {
                $project = null;
            }
        }

        if (! $project) {
            if ($projectCode && ! in_array($projectCode, ['admin', 'login', 'logout'])) {
                $isWkProduct = WkProduct::where('slug', $projectCode)->exists();
                $isWkCategory = Category::where('slug', $projectCode)->exists();
                if ($isWkProduct || $isWkCategory) {
                    $queryString = $request->getQueryString();
                    $target = '/wkcomputer/'.$projectCode.($queryString ? '?'.$queryString : '');

                    return redirect($target, 301);
                }
            }

            if (! file_exists(storage_path('installed.lock')) || env('DB_DATABASE') === '__DB_DATABASE__') {
                return redirect('/install');
            }

            abort(404, 'Project not found'.($projectCode ? ': '.$projectCode : ''));
        }

        $hasTenantCol = false;
        try {
            $hasTenantCol = Schema::hasColumn('projects', 'tenant_id');
        } catch (\Throwable $e) {
            $hasTenantCol = false;
        }

        $tenantId = $hasTenantCol ? $project->tenant_id : null;
        if ($project->code === 'viettinmart-eco' || str_contains($project->code, 'viettinmart')) {
            $tenantId = 3;
        } elseif (str_contains($project->code, 'wkcomputer')) {
            $tenantId = 4;
        } elseif (! $tenantId) {
            $matchedTenant = Tenant::where('code', $project->code)
                ->orWhere('code', str_replace(['-eco', '-ecommerce', '-demo'], '', $project->code))
                ->first();
            $tenantId = $matchedTenant?->id ?? $project->id;
        }

        if ($tenantId && $hasTenantCol && $project->tenant_id !== $tenantId) {
            try {
                $project->update(['tenant_id' => $tenantId]);
            } catch (\Throwable $e) {
                // Fallback gracefully if database table is not migrated yet
            }
        }

        view()->share('currentProject', $project);
        $request->attributes->set('project', $project);
        app()->instance('current_project_id', $project->id);
        app()->instance('current_tenant_id', $tenantId);
        config(['app.current_tenant_id' => $tenantId]);
        config(['app.default_tenant_id' => $tenantId]);

        if (session()) {
            session(['current_project_id' => $project->id]);
            session(['current_project' => $project]);
            session(['current_tenant_id' => $tenantId]);
        }

        // Auto-heal check for Viettinmart project data (100% Tenant Isolation)
        if ($project->code === 'viettinmart-eco' || str_contains($project->code, 'viettinmart')) {
            try {
                app(ViettinmartDataSyncService::class)->syncProjectId($project->id, 3);
            } catch (\Throwable $e) {
                \Log::warning('Viettinmart auto-heal check failed: '.$e->getMessage());
            }
        }

        // Prepend active project's theme view path so project theme templates/layouts take precedence
        $themeManager = app(ThemeManager::class);
        $theme = $themeManager->resolveActiveTheme($project);
        if ($theme) {
            $themeViewPath = resource_path("views/frontend/themes/{$theme}");
            if (is_dir($themeViewPath)) {
                view()->getFinder()->prependLocation($themeViewPath);
            }
        }

        // Ensure active locale is set for the project
        $sessionLocale = session('locale');
        if ($sessionLocale) {
            app()->setLocale(trim($sessionLocale));
        } else {
            $languages = setting('languages', []);
            if (is_string($languages)) {
                $languages = json_decode($languages, true) ?: [];
            }
            $defaultLocale = collect($languages)->firstWhere('is_default', true)['code'] ?? config('app.locale', 'vi');
            app()->setLocale(trim($defaultLocale) ?: 'vi');
        }

        return $next($request);
    }

    private function ensureProjectHasCmsUser($project)
    {
        // Check if project already has a CMS user
        $existingUser = User::where('username', $project->code)
            ->where('role', 'cms')
            ->first();

        if (! $existingUser) {
            try {
                // Create CMS user for existing project
                $user = User::create([
                    'name' => 'Admin - '.$project->code,
                    'username' => $project->code,
                    'email' => strtolower($project->code).'@project.local',
                    'password' => bcrypt($project->project_admin_password ?? 'admin123'),
                    'role' => 'cms',
                    'level' => 2,
                    'email_verified_at' => now(),
                ]);

                \Log::info('Created CMS user for existing project: '.$project->code);

                return $user;
            } catch (\Exception $e) {
                \Log::error('Failed to create CMS user for project '.$project->code.': '.$e->getMessage());
            }
        }

        return $existingUser;
    }
}
