<?php

namespace App\Providers;

use App\Helpers\SuperAdminLogHelper;
use App\Services\DynamicWidgetRenderer;
use App\Services\ProjectPasswordService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ProjectPasswordService::class);
        $this->app->singleton(DynamicWidgetRenderer::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::addLocation(resource_path('views/frontend/themes/viettinmartdemo'));
        $wkThemePath = resource_path('views/frontend/themes/wkcomputerdemo');
        if (is_dir($wkThemePath)) {
            View::addLocation($wkThemePath);
        }
        $wkComponents = resource_path('views/frontend/themes/wkcomputerdemo/components');
        if (is_dir($wkComponents)) {
            Blade::anonymousComponentPath($wkComponents);
        }
        if (! app()->runningInConsole() && ! headers_sent()) {
            @header('X-Powered-By: VGTCRM');
        }

        // Configure Livewire to use project.web middleware
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle)
                ->middleware('project.web');
        });

        // Register Blade directives for widgets
        Blade::directive('widgetArea', function ($expression) {
            return "<?php echo app(\App\Services\DynamicWidgetRenderer::class)->renderArea($expression); ?>";
        });

        Blade::directive('widget', function ($expression) {
            return "<?php echo app(\App\Services\DynamicWidgetRenderer::class)->renderById($expression); ?>";
        });

        // Log logins
        Event::listen(function (Login $event) {
            $user = $event->user;
            $ip = request()->ip();
            $userAgent = request()->userAgent();
            SuperAdminLogHelper::logActivity('User Logged In', [
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);
        });

        // Log logouts
        Event::listen(function (Logout $event) {
            $user = $event->user;
            $ip = request()->ip();
            SuperAdminLogHelper::logActivity('User Logged Out', [
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'ip_address' => $ip,
            ]);
        });

        // Log all model changes
        Event::listen('eloquent.*', function ($eventName, array $data) {
            // Only log saved/created/updated/deleted events
            if (! preg_match('/^eloquent\.(created|updated|deleted): (.*)$/', $eventName, $matches)) {
                return;
            }

            $action = $matches[1]; // created, updated, deleted
            $modelClass = $matches[2];
            $model = $data[0] ?? null;

            // Ignore models that shouldn't be logged to avoid spam
            $ignoredModels = [
                'App\Models\Task', // Đã được log tay bên trong MyTaskController
            ];

            // Only log if we have a valid model, not ignored, and user is logged in
            if (! $model || in_array($modelClass, $ignoredModels) || ! auth()->check()) {
                return;
            }

            // Only log if the user has access to superadmin (SuperAdmin, Manager, Dev, etc)
            $user = auth()->user();
            if (! $user->isSuperAdmin() && ! $user->isManager() && $user->role !== 'dev' && ! $user->hasRole('dev')) {
                return;
            }

            // In case of updated, check if there are actual changes
            if ($action === 'updated' && empty($model->getChanges())) {
                return;
            }

            $changes = [];
            if ($action === 'updated') {
                $changes = $model->getChanges();
                // Bỏ qua nếu chỉ là thay đổi updated_at
                if (count($changes) === 1 && isset($changes['updated_at'])) {
                    return;
                }
            } elseif ($action === 'created' || $action === 'deleted') {
                $changes = $model->toArray();
            }

            $modelName = class_basename($modelClass);
            $recordId = $model->id ?? 'unknown';

            $actionVi = [
                'created' => 'Tạo mới',
                'updated' => 'Cập nhật',
                'deleted' => 'Xóa',
            ][$action];

            SuperAdminLogHelper::logActivity("{$actionVi} {$modelName} (ID: {$recordId})", $changes);
        });
    }
}
