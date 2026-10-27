<?php

namespace App\Services;

use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Cache;

class WidgetPermissionService
{
    /**
     * Check if user can access widget type
     */
    public function canAccessWidget(string $widgetType, $user = null): bool
    {
        // In local environment, allow all widgets
        if (config('app.env') === 'local') {
            return true;
        }

        $user = $user ?? auth()->user();

        if (! $user) {
            return false;
        }

        // Super admin can access everything
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        // Get widget configuration
        $config = WidgetRegistry::getConfig($widgetType);
        if (! $config) {
            return false;
        }

        // Check widget permissions
        $requiredPermissions = $config['settings']['permissions'] ?? [];

        if (empty($requiredPermissions)) {
            return true; // No specific permissions required
        }

        return $this->hasAnyPermission($user, $requiredPermissions);
    }

    /**
     * Check if user can manage widgets (create, edit, delete)
     */
    public function canManageWidgets($user = null): bool
    {
        // Get user from request attributes (project context) or auth (web context)
        $user = $user ?? request()->attributes->get('auth_user') ?? auth()->user();

        if (! $user) {
            return false;
        }

        // Allow in development mode for easier testing
        if (config('app.debug', false) && config('app.env') === 'local') {
            return true;
        }

        // Check development session access
        if (session('widget_dev_access') && config('app.env') === 'local') {
            return true;
        }

        return $this->isSuperAdmin($user) ||
               $this->hasAnyPermission($user, ['admin', 'editor', 'widget_manager', 'cms']);
    }

    /**
     * Check if user can enable/disable widgets
     */
    public function canToggleWidgets($user = null): bool
    {
        // Get user from request attributes (project context) or auth (web context)
        $user = $user ?? request()->attributes->get('auth_user') ?? auth()->user();

        if (! $user) {
            return false;
        }

        // Allow in development mode for easier testing
        if (config('app.debug', false) && config('app.env') === 'local') {
            return true;
        }

        return $this->isSuperAdmin($user) ||
               $this->hasAnyPermission($user, ['admin', 'widget_admin', 'cms']);
    }

    /**
     * Get accessible widgets for user
     */
    public function getAccessibleWidgets($user = null): array
    {
        $user = $user ?? auth()->user();

        // In local environment, return all widgets
        if (config('app.env') === 'local') {
            return WidgetRegistry::all();
        }

        if (! $user) {
            return [];
        }

        $allWidgets = WidgetRegistry::all();
        $accessibleWidgets = [];

        foreach ($allWidgets as $widget) {
            if ($this->canAccessWidget($widget['type'], $user)) {
                $accessibleWidgets[] = $widget;
            }
        }

        return $accessibleWidgets;
    }

    /**
     * Get accessible widgets by category for user
     */
    public function getAccessibleWidgetsByCategory($user = null): array
    {
        $accessibleWidgets = $this->getAccessibleWidgets($user);
        $byCategory = [];

        foreach ($accessibleWidgets as $widget) {
            $category = $widget['metadata']['category'] ?? 'general';
            $byCategory[$category][] = $widget;
        }

        return $byCategory;
    }

    /**
     * Check if widget is enabled
     */
    public function isWidgetEnabled(string $widgetType): bool
    {
        $cacheKey = "widget_enabled_{$widgetType}";

        return Cache::remember($cacheKey, 3600, function () use ($widgetType) {
            // Check if widget is disabled in settings
            $disabledWidgets = $this->getDisabledWidgets();

            return ! in_array($widgetType, $disabledWidgets);
        });
    }

    /**
     * Enable widget
     */
    public function enableWidget(string $widgetType, $user = null): bool
    {
        if (! $this->canToggleWidgets($user)) {
            return false;
        }

        $disabledWidgets = $this->getDisabledWidgets();
        $disabledWidgets = array_diff($disabledWidgets, [$widgetType]);

        $this->setDisabledWidgets($disabledWidgets);
        $this->clearWidgetCache($widgetType);

        return true;
    }

    /**
     * Disable widget
     */
    public function disableWidget(string $widgetType, $user = null): bool
    {
        if (! $this->canToggleWidgets($user)) {
            return false;
        }

        $disabledWidgets = $this->getDisabledWidgets();
        if (! in_array($widgetType, $disabledWidgets)) {
            $disabledWidgets[] = $widgetType;
        }

        $this->setDisabledWidgets($disabledWidgets);
        $this->clearWidgetCache($widgetType);

        return true;
    }

    /**
     * Validate widget dependencies
     */
    public function validateDependencies(string $widgetType): array
    {
        $config = WidgetRegistry::getConfig($widgetType);
        if (! $config) {
            return ['Widget configuration not found'];
        }

        $dependencies = $config['settings']['dependencies'] ?? [];
        $errors = [];

        foreach ($dependencies as $dependency) {
            if (is_string($dependency)) {
                // Check if dependency widget exists and is enabled
                if (! WidgetRegistry::exists($dependency)) {
                    $errors[] = "Required widget '{$dependency}' not found";
                } elseif (! $this->isWidgetEnabled($dependency)) {
                    $errors[] = "Required widget '{$dependency}' is disabled";
                }
            } elseif (is_array($dependency)) {
                // Check complex dependency (e.g., class exists, extension loaded)
                $type = $dependency['type'] ?? '';
                $value = $dependency['value'] ?? '';

                switch ($type) {
                    case 'class':
                        if (! class_exists($value)) {
                            $errors[] = "Required class '{$value}' not found";
                        }
                        break;
                    case 'extension':
                        if (! extension_loaded($value)) {
                            $errors[] = "Required PHP extension '{$value}' not loaded";
                        }
                        break;
                    case 'function':
                        if (! function_exists($value)) {
                            $errors[] = "Required function '{$value}' not available";
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Check if user is super admin
     */
    protected function isSuperAdmin($user): bool
    {
        // Check various common admin indicators
        if (isset($user->level)) {
            if ($user->level === 'superadmin' || ($user->level ?? 0) >= 100) {
                return true;
            }
        }

        if (isset($user->role)) {
            if ($user->role === 'superadmin' || $user->role === 'admin') {
                return true;
            }
        }

        // Check if user has admin email (common pattern)
        if (isset($user->email) && str_contains($user->email, 'admin')) {
            return true;
        }

        // Check if user ID is 1 (common admin pattern)
        if (isset($user->id) && $user->id == 1) {
            return true;
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    protected function hasAnyPermission($user, array $permissions): bool
    {
        // Check user level/role
        $userLevel = $user->level ?? 'user';
        $userRole = $user->role ?? 'user';

        if (in_array($userLevel, $permissions) || in_array($userRole, $permissions)) {
            return true;
        }

        // Check Laravel permissions if available
        if (method_exists($user, 'hasAnyPermission')) {
            return $user->hasAnyPermission($permissions);
        }

        // Check Laravel roles if available
        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole($permissions);
        }

        // Fallback: check if user has admin-like properties
        if (isset($user->email) && str_contains($user->email, 'admin')) {
            return true;
        }

        // If user ID is 1, assume admin
        if (isset($user->id) && $user->id == 1) {
            return true;
        }

        return false;
    }

    /**
     * Get disabled widgets from cache/settings
     */
    protected function getDisabledWidgets(): array
    {
        return Cache::get('disabled_widgets', []);
    }

    /**
     * Set disabled widgets in cache/settings
     */
    protected function setDisabledWidgets(array $disabledWidgets): void
    {
        Cache::put('disabled_widgets', $disabledWidgets, 86400); // 24 hours
    }

    /**
     * Clear widget cache
     */
    protected function clearWidgetCache(string $widgetType): void
    {
        Cache::forget("widget_enabled_{$widgetType}");
        WidgetRegistry::clearCache();
    }

    /**
     * Get widget permission summary
     */
    public function getPermissionSummary($user = null): array
    {
        $user = $user ?? auth()->user();

        return [
            'can_manage_widgets' => $this->canManageWidgets($user),
            'can_toggle_widgets' => $this->canToggleWidgets($user),
            'accessible_widget_count' => count($this->getAccessibleWidgets($user)),
            'total_widget_count' => count(WidgetRegistry::all()),
            'is_super_admin' => $this->isSuperAdmin($user),
        ];
    }
}
