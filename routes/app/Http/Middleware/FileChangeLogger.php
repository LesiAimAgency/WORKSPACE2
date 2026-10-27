<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FileChangeLogger
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Log file changes for POST/PUT/PATCH requests
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->logFileChange($request);
        }

        return $response;
    }

    private function logFileChange(Request $request)
    {
        // Detect project from URL
        $project = $this->detectProject($request);

        $logData = [
            'timestamp' => now()->timezone('Asia/Ho_Chi_Minh')->toIso8601String(),
            'project_id' => $project['id'] ?? null,
            'project_code' => $project['code'] ?? 'main',
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Unknown',
            'ip_address' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => $request->route()?->getName(),
            'user_agent' => $request->userAgent(),
            'data' => $request->except(['password', '_token']),
        ];

        // Log to project-specific file
        $logPath = storage_path('logs/file-changes-'.($project['code'] ?? 'main').'.log');
        File::append($logPath, json_encode($logData)."\n");
    }

    private function detectProject(Request $request)
    {
        $url = $request->getPathInfo();

        // Check if URL contains project code (e.g., /SiVGT/admin)
        if (preg_match('/^\/([A-Za-z0-9\-_.]+)\/admin/', $url, $matches)) {
            $projectCode = $matches[1];

            // Get project info from database
            $project = \DB::table('projects')->where('code', $projectCode)->first();

            if ($project) {
                return [
                    'id' => $project->id,
                    'code' => $project->code,
                    'name' => $project->name,
                ];
            }
        }

        return ['code' => 'main', 'id' => null, 'name' => 'Main System'];
    }
}
