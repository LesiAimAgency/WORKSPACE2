<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WkcomputerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $project = function_exists('current_project') ? current_project() : null;
        if (! $project) {
            $project = Project::where('code', 'wkcomputer')
                ->orWhere('code', 'DA009')
                ->orWhere('subdomain', 'wkcomputer')
                ->orWhere('external_domain', 'like', '%wkcomputer%')
                ->first();
        }

        if ($project) {
            view()->share('currentProject', $project);
            $request->attributes->set('project', $project);
            app()->instance('current_project_id', $project->id);
            if (session()) {
                session(['current_project_id' => $project->id]);
                session(['current_project' => $project]);
            }
            $themeViewPath = resource_path('views/frontend/themes/wkcomputerdemo');
            if (is_dir($themeViewPath)) {
                view()->getFinder()->prependLocation($themeViewPath);
            }
            $altThemeViewPath = resource_path('views/themes/wkcomputerdemo');
            if (is_dir($altThemeViewPath)) {
                view()->getFinder()->prependLocation($altThemeViewPath);
            }
        }

        return $next($request);
    }
}
