<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;

class ProjectMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect(Authenticate::resolveLoginUrl($request));
        }

        $projectCode = $request->route('projectCode');

        if ($projectCode) {
            $project = Project::where('code', $projectCode)->first();

            if (! $project) {
                abort(404, 'Project not found');
            }

            $request->attributes->set('project', $project);
        }

        return $next($request);
    }
}
