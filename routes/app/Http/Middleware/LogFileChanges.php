<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class LogFileChanges
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log POST, PUT, PATCH, DELETE requests that modify data
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->logChange($request, $response);
        }

        return $response;
    }

    private function logChange(Request $request, Response $response): void
    {
        try {
            // Determine project code
            $projectCode = $this->getProjectCode($request);

            // Skip if response is not successful
            if ($response->getStatusCode() >= 400) {
                return;
            }

            // Skip certain routes that shouldn't be logged
            $skipRoutes = [
                'login', 'logout', 'password', 'verification', 'api.bridge',
            ];

            $routeName = $request->route()?->getName();
            if ($routeName && collect($skipRoutes)->contains(fn ($skip) => str_contains($routeName, $skip))) {
                return;
            }

            $logData = [
                'timestamp' => now()->toISOString(),
                'user_id' => Auth::id(),
                'user_name' => Auth::user()?->name ?? 'System',
                'user_email' => Auth::user()?->email ?? 'system@local',
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'route' => $routeName,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'action' => $this->getActionDescription($request),
                'data_summary' => $this->getDataSummary($request),
                'project_code' => $projectCode,
            ];

            $logPath = storage_path('logs/file-changes-'.$projectCode.'.log');

            // Ensure directory exists
            $logDir = dirname($logPath);
            if (! File::exists($logDir)) {
                File::makeDirectory($logDir, 0755, true);
            }

            // Append log entry
            File::append($logPath, json_encode($logData)."\n");

            // Keep only last 1000 entries to prevent file from growing too large
            $this->trimLogFile($logPath);

        } catch (\Exception $e) {
            // Silently fail to avoid breaking the application
            \Log::error('Failed to log file change: '.$e->getMessage());
        }
    }

    private function getProjectCode(Request $request): string
    {
        // Check if this is a project-specific request
        $segments = $request->segments();

        // For project routes like /project-code/admin/...
        if (count($segments) >= 2 && $segments[1] === 'admin') {
            return $segments[0];
        }

        // For superadmin routes
        if (count($segments) >= 1 && $segments[0] === 'superadmin') {
            return 'main';
        }

        // For CMS routes
        if (count($segments) >= 1 && $segments[0] === 'cms') {
            return 'main';
        }

        // Check route parameters
        if ($request->route() && $request->route()->hasParameter('projectCode')) {
            return $request->route()->parameter('projectCode');
        }

        return 'main';
    }

    private function getActionDescription(Request $request): string
    {
        $route = $request->route();
        $routeName = $route?->getName();
        $method = $request->method();

        if (! $routeName) {
            return ucfirst(strtolower($method)).' request';
        }

        // Map common route patterns to descriptions
        $actionMap = [
            'store' => 'Tạo mới',
            'update' => 'Cập nhật',
            'destroy' => 'Xóa',
            'delete' => 'Xóa',
            'create' => 'Tạo',
            'edit' => 'Chỉnh sửa',
            'save' => 'Lưu',
            'upload' => 'Tải lên',
            'import' => 'Import',
            'export' => 'Export',
        ];

        foreach ($actionMap as $pattern => $description) {
            if (str_contains($routeName, $pattern)) {
                return $description;
            }
        }

        // Extract resource name from route
        $parts = explode('.', $routeName);
        if (count($parts) >= 2) {
            $resource = ucfirst($parts[count($parts) - 2]);
            $action = $parts[count($parts) - 1];

            return match ($action) {
                'store' => "Tạo {$resource}",
                'update' => "Cập nhật {$resource}",
                'destroy' => "Xóa {$resource}",
                default => ucfirst($action)." {$resource}"
            };
        }

        return ucfirst(strtolower($method)).' '.$routeName;
    }

    private function getDataSummary(Request $request): array
    {
        $data = $request->except(['_token', '_method', 'password', 'password_confirmation']);

        // Limit data size and remove sensitive information
        $summary = [];
        $maxLength = 100;

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $summary[$key] = strlen($value) > $maxLength
                    ? substr($value, 0, $maxLength).'...'
                    : $value;
            } elseif (is_array($value)) {
                $summary[$key] = '[Array with '.count($value).' items]';
            } elseif (is_object($value)) {
                $summary[$key] = '[Object: '.get_class($value).']';
            } else {
                $summary[$key] = $value;
            }
        }

        return $summary;
    }

    private function trimLogFile(string $logPath): void
    {
        if (! File::exists($logPath)) {
            return;
        }

        $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (count($lines) > 1000) {
            // Keep only the last 1000 lines
            $trimmedLines = array_slice($lines, -1000);
            File::put($logPath, implode("\n", $trimmedLines)."\n");
        }
    }
}
