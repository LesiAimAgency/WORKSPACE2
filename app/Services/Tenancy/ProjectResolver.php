<?php

declare(strict_types=1);

namespace App\Services\Tenancy;

use App\Core\Theme\ThemeManager;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProjectResolver
{
    /**
     * Resolve the active Project from HTTP Request (Domain, Subdomain, Path, Config, Standalone mode)
     */
    public function resolve(Request $request): ?Project
    {
        // 0. If already memoized in request attributes
        if ($request->attributes->has('project')) {
            $cached = $request->attributes->get('project');
            if ($cached instanceof Project) {
                return $cached;
            }
        }

        try {
            // 1. Standalone Mode / Env Project Code detection
            // When deployed independently on any domain/hosting, resolve via config/env or single project DB
            if (config('app.standalone_mode') || env('STANDALONE_MODE')) {
                $envCode = config('app.project_code') ?: env('PROJECT_CODE');
                if ($envCode) {
                    $project = Project::where('code', $envCode)->first();
                    if ($project) {
                        $this->memoize($request, $project);

                        return $project;
                    }
                }

                // In standalone mode, the entire database belongs to this single project
                $singleProject = $this->resolveSingleProject();
                if ($singleProject) {
                    $this->memoize($request, $singleProject);

                    return $singleProject;
                }
            }

            // Do not resolve projects for system-level routes in central multi-tenant mode
            $firstSegment = $request->segment(1);
            if ($firstSegment && in_array($firstSegment, ['superadmin', 'admin', 'login', 'logout', 'up', 'health'])) {
                return null;
            }

            // 2. Resolve by Route Parameter {projectCode}
            $projectCode = $request->route('projectCode');
            if ($projectCode && ! str_contains($projectCode, '{') && ! str_contains($projectCode, '}')) {
                $project = Project::where('code', $projectCode)->first();
                if ($project) {
                    $this->memoize($request, $project);

                    return $project;
                }
            }

            // 3. Resolve by first URI path segment (e.g. /inbetween, /ehenho, /wkcomputer, /viettinmart-eco)
            $firstSegment = $request->segment(1);
            if ($firstSegment && ! in_array($firstSegment, ['superadmin', 'admin', 'api', 'build', 'vendor', 'livewire', 'flux', 'storage', 'install', 'media', 'lang'])) {
                // Exact code match
                $project = Project::where('code', $firstSegment)->first();
                if ($project) {
                    $this->memoize($request, $project);

                    return $project;
                }

                // Dynamic theme slug match (resolves any theme-based route to corresponding project)
                $themeManager = app(ThemeManager::class);
                $availableThemes = $themeManager->discoverThemes();
                $normalizedSegment = strtolower($firstSegment);

                if (in_array($normalizedSegment, $availableThemes) || in_array($normalizedSegment, ['inbetwen', 'inbetween-v2'])) {
                    $matchedTheme = match ($normalizedSegment) {
                        'inbetwen' => 'inbetween',
                        'inbetween-v2' => 'inbetween_v2',
                        default => $normalizedSegment,
                    };

                    $project = Project::where('theme', $matchedTheme)
                        ->orWhere('code', 'like', "%{$matchedTheme}%")
                        ->orWhereJsonContains('features->theme', $matchedTheme)
                        ->first();

                    if ($project) {
                        $this->memoize($request, $project);

                        return $project;
                    }
                }

                // Code prefix or alias match
                $project = Project::where('code', 'like', "{$firstSegment}%")
                    ->orWhere('code', 'like', "%{$firstSegment}%")
                    ->first();
                if ($project) {
                    $this->memoize($request, $project);

                    return $project;
                }
            }

            $host = $request->getHost();

            // 4. Resolve by Domain or External Domain (excluding localhost wildcard)
            if (! in_array($host, ['127.0.0.1', 'localhost'])) {
                $project = Project::where('external_domain', $host)
                    ->orWhere('subdomain', 'like', "%://{$host}%")
                    ->orWhere('subdomain', $host)
                    ->first();

                if ($project) {
                    $this->memoize($request, $project);

                    return $project;
                }

                // 5. Resolve by Subdomain prefix (e.g. ehenho.domain.com -> 'ehenho')
                $subdomainParts = explode('.', $host);
                if (count($subdomainParts) > 2) {
                    $subdomain = $subdomainParts[0];
                    $project = Project::where('code', $subdomain)->first();
                    if ($project) {
                        $this->memoize($request, $project);

                        return $project;
                    }
                }
            } else {
                $project = Project::where('external_domain', $host)->first();
                if ($project) {
                    $this->memoize($request, $project);

                    return $project;
                }
            }

            // 6. Fallback Header (for API / internal calls)
            $headerCode = $request->header('X-Project-Code');
            if ($headerCode) {
                $project = Project::where('code', $headerCode)->first();
                if ($project) {
                    $this->memoize($request, $project);

                    return $project;
                }
            }

            // 7. Single Project Fallback: If DB contains exactly ONE project, it MUST be the active project!
            $singleProject = $this->resolveSingleProject();
            if ($singleProject) {
                $this->memoize($request, $singleProject);

                return $singleProject;
            }

            // 8. Session fallback
            $sess = session('current_project');
            if ($sess instanceof Project) {
                $this->memoize($request, $sess);

                return $sess;
            }
            if (is_string($sess) && $sess !== '') {
                $project = Project::where('code', $sess)->orWhere('id', $sess)->first();
                if ($project) {
                    $this->memoize($request, $project);

                    return $project;
                }
            }
            if (session('current_project_id')) {
                $project = Project::find(session('current_project_id'));
                if ($project) {
                    $this->memoize($request, $project);

                    return $project;
                }
            }
        } catch (\Throwable $e) {
            // Database is offline or not configured yet; prevent 500 error
        }

        return null;
    }

    protected function resolveSingleProject(): ?Project
    {
        try {
            if (Schema::hasTable('projects')) {
                $count = Project::count();
                if ($count === 1) {
                    return Project::first();
                }
            }
        } catch (\Throwable $e) {
            // DB connection not ready yet
        }

        return null;
    }

    protected function memoize(Request $request, Project $project): void
    {
        $request->attributes->set('project', $project);
        if (! app()->bound('current_project_id')) {
            app()->instance('current_project_id', $project->id);
        }
    }
}
