<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Widget;
use App\Services\FieldTypeService;
use App\Services\WidgetImportExportService;
use App\Services\WidgetPermissionService;
use App\Widgets\WidgetRegistry;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function index()
    {
        // Get user from request attributes (project context) or auth (web context)
        $user = request()->attributes->get('auth_user') ?? auth()->user();

        // Debug log
        \Log::info('Widget index - auth debug', [
            'auth_user_from_request' => request()->attributes->get('auth_user') ? 'YES' : 'NO',
            'auth_user_from_auth' => auth()->user() ? 'YES' : 'NO',
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'user_level' => $user?->level,
            'session_project_user_id' => session('project_user_id'),
            'env' => config('app.env'),
        ]);

        // Skip permission check entirely in local environment
        if (config('app.env') !== 'local') {
            $permissionService = new WidgetPermissionService;

            // Check if user can manage widgets
            if (! $permissionService->canManageWidgets($user)) {
                \Log::error('Widget permission denied', [
                    'user' => $user ? $user->toArray() : null,
                    'can_manage' => $permissionService->canManageWidgets($user),
                ]);
                abort(403, 'You do not have permission to manage widgets');
            }
        }

        // Check if we're in project context
        $projectCode = request()->route('projectCode');
        $currentProject = request()->attributes->get('project');

        if (! $currentProject && $projectCode) {
            $currentProject = Project::where('code', $projectCode)->first();
        }
        if (! $currentProject) {
            $sessionProject = session('current_project');
            if ($sessionProject instanceof Project) {
                $currentProject = $sessionProject;
            } elseif (\is_array($sessionProject) && ! empty($sessionProject['id'])) {
                $currentProject = Project::find($sessionProject['id']);
            } elseif (\is_object($sessionProject) && ! empty($sessionProject->id)) {
                $currentProject = Project::find($sessionProject->id);
            } elseif (session('current_project_id')) {
                $currentProject = Project::find(session('current_project_id'));
            }
        }

        $projId = $currentProject?->id;
        $tenantId = $currentProject?->tenant_id ?? session('current_tenant_id') ?? $projId;

        // Auto-seed or auto-heal widgets if project currently has 0 widgets
        if ($projId || $tenantId) {
            $widgetCount = Widget::withoutGlobalScope('tenant')
                ->where(function ($q) use ($tenantId, $projId) {
                    if ($tenantId) {
                        $q->where('tenant_id', $tenantId);
                    }
                    if ($projId) {
                        $q->orWhere('project_id', $projId);
                    }
                })
                ->count();

            if ($widgetCount === 0) {
                // Auto-heal Viettinmart data if applicable
                if ($currentProject && ($currentProject->code === 'viettinmart-eco' || $currentProject->code === 'viettinmart')) {
                    try {
                        app(ViettinmartDataSyncService::class)->syncProjectId($projId, $tenantId);
                        $widgetCount = Widget::withoutGlobalScope('tenant')
                            ->where(function ($q) use ($tenantId, $projId) {
                                if ($tenantId) {
                                    $q->where('tenant_id', $tenantId);
                                }
                                if ($projId) {
                                    $q->orWhere('project_id', $projId);
                                }
                            })
                            ->count();
                    } catch (\Throwable $e) {
                        \Log::warning('ViettinmartDataSyncService failed in WidgetController: '.$e->getMessage());
                    }
                }
            }

            if ($widgetCount === 0) {
                try {
                    (new \Database\Seeders\WorkspaceWidgetsSeeder)->run($projId, $tenantId);
                } catch (\Throwable $e) {
                    \Log::warning('WorkspaceWidgetsSeeder failed in WidgetController: '.$e->getMessage());
                }
            }
        }

        $query = Widget::withoutGlobalScope('tenant')->orderBy('area')->orderBy('sort_order');
        if ($tenantId || $projId) {
            $query->where(function ($q) use ($tenantId, $projId) {
                if ($tenantId) {
                    $q->where('tenant_id', $tenantId);
                }
                if ($projId) {
                    $q->orWhere('project_id', $projId);
                }
            });
        }

        $existingWidgets = $query->get()
            ->map(function ($w) {
                if ($w->area === 'homepage') {
                    $w->area = 'homepage-main';
                }

                return $w;
            })
            ->groupBy('area')
            ->map(fn ($widgets) => $widgets->map(fn ($w) => [
                'id' => $w->id,
                'type' => $w->type,
                'name' => $w->name,
                'area' => $w->area,
                'sort_order' => $w->sort_order,
                'is_active' => $w->is_active,
                'settings' => $w->settings,
                'variant' => $w->variant ?? 'default',
            ]));

        // Get accessible widgets for current user (including Workspace)
        $permissionService = new WidgetPermissionService;
        $allAccessible = $permissionService->getAccessibleWidgetsByCategory($user);
        $availableWidgets = array_filter($allAccessible, function ($catWidgets, $cat) {
            $slug = \Illuminate\Support\Str::slug($cat);
            return $slug === 'workspace' || str_contains(strtolower($cat), 'workspace') || $slug === 'dai-phuc-68';
        }, ARRAY_FILTER_USE_BOTH);
        if (empty($availableWidgets)) {
            $availableWidgets = $allAccessible;
        }

        $permissionSummary = config('app.env') === 'local' ?
            ['can_manage_widgets' => true, 'can_toggle_widgets' => true, 'accessible_widget_count' => array_sum(array_map('count', $availableWidgets)), 'total_widget_count' => array_sum(array_map('count', $availableWidgets)), 'is_super_admin' => true] :
            $permissionService->getPermissionSummary();

        return view('cms.widgets.builder', compact('existingWidgets', 'availableWidgets', 'currentProject', 'permissionSummary'));
    }

    public function create()
    {
        return view('cms.widgets.create');
    }

    public function store(Request $request)
    {
        $permissionService = new WidgetPermissionService;

        // Check if user can manage widgets
        if (! $permissionService->canManageWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create widgets',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'area' => 'required|string',
            'settings' => 'nullable',
            'config' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'variant' => 'nullable|string',
        ]);

        // Validate widget type exists in registry or custom templates
        if (! WidgetRegistry::exists($validated['type'])) {
            return response()->json([
                'success' => false,
                'message' => "Widget type '{$validated['type']}' not found",
            ], 422);
        }

        // Check if user can access this widget type
        if (! $permissionService->canAccessWidget($validated['type'])) {
            return response()->json([
                'success' => false,
                'message' => "You do not have permission to use '{$validated['type']}' widget",
            ], 403);
        }

        // Check if widget is enabled
        if (! $permissionService->isWidgetEnabled($validated['type'])) {
            return response()->json([
                'success' => false,
                'message' => "Widget type '{$validated['type']}' is currently disabled",
            ], 422);
        }

        // Validate dependencies
        $dependencyErrors = $permissionService->validateDependencies($validated['type']);
        if (! empty($dependencyErrors)) {
            return response()->json([
                'success' => false,
                'message' => 'Widget dependencies not met: '.implode(', ', $dependencyErrors),
            ], 422);
        }

        // Ensure settings is array
        if (\is_string($validated['settings'] ?? null)) {
            $validated['settings'] = json_decode($validated['settings'], true);
        }

        // Nếu có config, merge vào settings
        if (! empty($validated['config'])) {
            $validated['settings'] = [...($validated['settings'] ?? []), 'config' => $validated['config']];
        }
        unset($validated['config']);

        // Check project context
        $projectCode = request()->route('projectCode');
        $currentProject = request()->attributes->get('project');
        if (! $currentProject && $projectCode) {
            $currentProject = Project::where('code', $projectCode)->first();
        }
        if (! $currentProject) {
            $sessionProject = session('current_project');
            if ($sessionProject instanceof Project) {
                $currentProject = $sessionProject;
            } elseif (\is_array($sessionProject) && ! empty($sessionProject['id'])) {
                $currentProject = Project::find($sessionProject['id']);
            } elseif (\is_object($sessionProject) && ! empty($sessionProject->id)) {
                $currentProject = Project::find($sessionProject->id);
            } elseif (session('current_project_id')) {
                $currentProject = Project::find(session('current_project_id'));
            }
        }

        $projId = $currentProject?->id;
        $tenantId = $currentProject?->tenant_id ?? session('current_tenant_id') ?? $projId;

        if ($projId) {
            $validated['project_id'] = $projId;
            $validated['tenant_id'] = $tenantId;
        }

        $newWidget = Widget::create($validated);
        clear_widget_cache($validated['area']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Widget saved successfully', 'widget' => $newWidget]);
        }

        return redirect()->back()->with('success', 'Widget created successfully');
    }

    public function edit($arg1, $arg2 = null)
    {
        $widget = $arg2 ?? $arg1;
        if (! $widget instanceof Widget) {
            $widget = Widget::withoutGlobalScope('tenant')->findOrFail($widget);
        }

        return view('cms.widgets.edit', compact('widget'));
    }

    public function update(Request $request, $arg1, $arg2 = null)
    {
        $widget = $arg2 ?? $arg1;
        if (! $widget instanceof Widget) {
            $widget = Widget::withoutGlobalScope('tenant')->findOrFail($widget);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'area' => 'required|string',
            'settings' => 'nullable',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        while (\is_string($validated['settings'] ?? null) && ! empty($validated['settings'])) {
            $decoded = json_decode($validated['settings'], true);
            if (json_last_error() === JSON_ERROR_NONE && (\is_array($decoded) || \is_string($decoded))) {
                $validated['settings'] = $decoded;
            } else {
                break;
            }
        }
        if (! \is_array($validated['settings'] ?? null)) {
            $validated['settings'] = [];
        }

        if (! empty($validated['config'])) {
            $validated['settings'] = [...($validated['settings'] ?? []), 'config' => $validated['config']];
        }
        unset($validated['config']);

        $widget->update($validated);



        clear_widget_cache($widget->area);
        \Illuminate\Support\Facades\Cache::flush();
        try {
            \Illuminate\Support\Facades\Artisan::call('view:clear');
        } catch (\Throwable $e) {}

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Widget updated successfully', 'widget' => $widget]);
        }

        $projectCode = request()->route('projectCode');
        $route = $projectCode
            ? route('project.admin.widgets.index', $projectCode)
            : route('cms.widgets.index');

        return redirect($route)->with('success', 'Widget updated successfully');
    }

    public function destroy($arg1, $arg2 = null)
    {
        $widget = $arg2 ?? $arg1;
        if (! $widget instanceof Widget) {
            $widget = Widget::withoutGlobalScope('tenant')->findOrFail($widget);
        }

        $area = $widget->area;
        $widget->delete();
        clear_widget_cache($area);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Widget deleted successfully']);
        }

        $projectCode = request()->route('projectCode');
        $route = $projectCode
            ? route('project.admin.widgets.index', $projectCode)
            : route('cms.widgets.index');

        return redirect($route)->with('success', 'Widget deleted successfully');
    }

    /**
     * Save multiple widgets at once
     */
    public function saveWidgets(Request $request)
    {
        try {
            $widgets = $request->input('widgets', []);
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            // Get current project context
            $projectCode = $request->route('projectCode') ?? request()->route('projectCode');
            $currentProject = $request->attributes->get('project') ?? request()->attributes->get('project');
            if (! $currentProject && $projectCode) {
                $currentProject = Project::where('code', $projectCode)->first();
            }
            if (! $currentProject) {
                $sessionProject = session('current_project');
                if ($sessionProject instanceof Project) {
                    $currentProject = $sessionProject;
                } elseif (\is_array($sessionProject) && ! empty($sessionProject['id'])) {
                    $currentProject = Project::find($sessionProject['id']);
                } elseif (\is_object($sessionProject) && ! empty($sessionProject->id)) {
                    $currentProject = Project::find($sessionProject->id);
                } elseif (session('current_project_id')) {
                    $currentProject = Project::find(session('current_project_id'));
                }
            }

            $projId = $currentProject?->id;
            $tenantId = $currentProject?->tenant_id ?? $projId;

            if (! $projId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin dự án để lưu widget.',
                ], 400);
            }

            // Clear existing widgets for the areas being updated (strictly for this project)
            $areas = collect($widgets)->pluck('area')->unique()->values()->all();
            foreach ($areas as $area) {
                $query = Widget::withoutGlobalScope('tenant')->where('project_id', $projId)
                    ->where(function ($q) use ($area) {
                        $q->where('area', $area);
                        if ($area === 'homepage-main') {
                            $q->orWhere('area', 'homepage');
                        }
                    });
                $query->delete();
            }

            // Save new widgets
            foreach ($widgets as $widgetData) {
                try {
                    $validated = $this->validateWidgetData($widgetData);
                    $validated['tenant_id'] = $tenantId;
                    $validated['project_id'] = $projId;

                    Widget::create($validated);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Widget '{$widgetData['name']}': ".$e->getMessage();
                }
            }



            // Clear cache for all affected areas & flush general cache
            foreach ($areas as $area) {
                clear_widget_cache($area);
            }
            \Illuminate\Support\Facades\Cache::flush();
            try {
                \Illuminate\Support\Facades\Artisan::call('view:clear');
            } catch (\Throwable $e) {}

            if ($errorCount === 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully saved {$successCount} widgets",
                    'saved' => $successCount,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Saved {$successCount} widgets, {$errorCount} failed",
                    'saved' => $successCount,
                    'failed' => $errorCount,
                    'errors' => $errors,
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving widgets: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reorder widgets within an area
     */
    public function reorder(Request $request)
    {
        try {
            $widgetIds = $request->input('widget_ids', []);
            $area = $request->input('area');

            foreach ($widgetIds as $index => $id) {
                if ($id) {
                    Widget::withoutGlobalScope('tenant')->where('id', $id)->update(['sort_order' => $index]);
                }
            }

            if ($area) {
                clear_widget_cache($area);
                if ($area === 'homepage-main') {
                    clear_widget_cache('homepage');
                }
            } else {
                clear_widget_cache();
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật vị trí widget thành công',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật vị trí: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear widgets in a specific area
     */
    public function clearArea(Request $request)
    {
        $area = $request->input('area', 'homepage-main');

        // Resolve current project
        $projectCode = $request->route('projectCode') ?? request()->route('projectCode');
        $currentProject = $request->attributes->get('project') ?? request()->attributes->get('project');
        if (! $currentProject && $projectCode) {
            $currentProject = Project::where('code', $projectCode)->first();
        }
        if (! $currentProject) {
            $sessionProject = session('current_project');
            if ($sessionProject instanceof Project) {
                $currentProject = $sessionProject;
            } elseif (\is_array($sessionProject) && ! empty($sessionProject['id'])) {
                $currentProject = Project::find($sessionProject['id']);
            } elseif (\is_object($sessionProject) && ! empty($sessionProject->id)) {
                $currentProject = Project::find($sessionProject->id);
            } elseif (session('current_project_id')) {
                $currentProject = Project::find(session('current_project_id'));
            }
        }

        $projId = $currentProject?->id;
        if (! $projId) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin dự án để xóa widget.',
            ], 400);
        }

        $query = Widget::withoutGlobalScope('tenant')->where('project_id', $projId)->where('area', $area);
        $count = $query->count();
        $query->delete();
        clear_widget_cache($area);

        return response()->json([
            'success' => true,
            'message' => "Cleared {$count} widgets from {$area}",
        ]);
    }

    /**
     * Clear widget cache
     */
    public function clearCache()
    {
        clear_widget_cache();
        WidgetRegistry::clearCache(); // Clear discovery cache

        return response()->json(['success' => true, 'message' => 'Widget cache cleared']);
    }

    /**
     * Validate widget data
     */
    private function validateWidgetData($data)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'area' => 'required|string',
            'settings' => 'nullable',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'variant' => 'nullable|string',
        ];

        $validator = \Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new \Exception('Validation failed: '.implode(', ', $validator->errors()->all()));
        }

        $validated = $validator->validated();

        // Validate widget type exists
        if (! WidgetRegistry::exists($validated['type'])) {
            throw new \Exception("Widget type '{$validated['type']}' not found in registry");
        }

        // Process settings
        if (isset($validated['settings']) && is_string($validated['settings'])) {
            $validated['settings'] = json_decode($validated['settings'], true);
        }

        // Validate settings against widget metadata (only if settings are provided)
        if (! empty($validated['settings'])) {
            try {
                $widgetClass = WidgetRegistry::get($validated['type']);
                if ($widgetClass) {
                    $tempWidget = new $widgetClass($validated['settings'] ?? [], $validated['variant'] ?? 'default');
                    $tempWidget->validateSettings();
                }
            } catch (\Exception $e) {
                // Log warning but don't fail - allow saving with potentially invalid settings
                \Log::warning("Widget settings validation warning for {$validated['type']}: ".$e->getMessage());
                // Only throw if it's a critical error, not just validation
                if (str_contains($e->getMessage(), 'not found') || str_contains($e->getMessage(), 'class')) {
                    throw new \Exception('Widget settings validation failed: '.$e->getMessage());
                }
            }
        }

        // Set defaults
        $validated['is_active'] ??= true;
        $validated['sort_order'] ??= 0;
        $validated['variant'] ??= 'default';

        return $validated;
    }

    /**
     * Get widget preview
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'settings' => 'nullable',
            'variant' => 'nullable|string',
        ]);

        $settings = $validated['settings'] ?? [];
        while (\is_string($settings) && ! empty($settings)) {
            $decoded = json_decode($settings, true);
            if (json_last_error() === JSON_ERROR_NONE && (\is_array($decoded) || \is_string($decoded))) {
                $settings = $decoded;
            } else {
                break;
            }
        }
        if (! \is_array($settings)) {
            $settings = [];
        }

        try {
            $preview = WidgetRegistry::getPreview(
                $validated['type'],
                $settings,
                $validated['variant'] ?? 'default'
            );

            $cleanPreview = mb_convert_encoding($preview, 'UTF-8', 'UTF-8');

            return response()->json([
                'success' => true,
                'preview' => $cleanPreview,
            ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Preview generation failed: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Discover widgets
     */
    public function discover()
    {
        try {
            $discovered = WidgetRegistry::discover();
            $conflicts = WidgetRegistry::validateNamespaces();

            return response()->json([
                'success' => true,
                'discovered' => count($discovered),
                'widgets' => $discovered,
                'conflicts' => $conflicts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Widget discovery failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get widget form fields
     */
    public function getFields(Request $request)
    {
        $id = $request->input('id') ?? $request->input('widget_id');
        $type = $request->input('type') ?? $request->get('type');
        $settings = $request->input('settings') ?? $request->get('settings', []);

        while (\is_string($settings) && ! empty($settings)) {
            $decoded = json_decode($settings, true);
            if (json_last_error() === JSON_ERROR_NONE && (\is_array($decoded) || \is_string($decoded))) {
                $settings = $decoded;
            } else {
                break;
            }
        }
        if (! \is_array($settings)) {
            $settings = [];
        }

        if (! empty($id)) {
            $dbWidget = Widget::withoutGlobalScope('tenant')->find($id);
            if ($dbWidget) {
                $dbSettings = $dbWidget->settings;
                if (\is_array($dbSettings) && ! empty($dbSettings)) {
                    $settings = array_merge($dbSettings, $settings);
                }
            }
        }

        if (! $type || ! WidgetRegistry::exists($type)) {
            return response()->json([
                'success' => false,
                'message' => "Widget type '{$type}' not found",
            ], 404);
        }

        try {
            $config = WidgetRegistry::getConfig($type);
            $fields = $config['fields'] ?? [];

            $fieldTypeService = new FieldTypeService;
            $formHtml = $fieldTypeService->renderForm($fields, $settings);

            $cleanFormHtml = mb_convert_encoding($formHtml, 'UTF-8', 'UTF-8');

            return response()->json([
                'success' => true,
                'fields' => $fields,
                'form_html' => $cleanFormHtml,
                'variants' => $config['variants'] ?? ['default' => 'Default'],
            ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            \Log::error('getFields exception: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get widget fields: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle widget enabled/disabled state
     */
    public function toggleWidget(Request $request)
    {
        $type = $request->get('type');
        $enabled = $request->boolean('enabled');

        $permissionService = new WidgetPermissionService;

        if (! $permissionService->canToggleWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to toggle widgets',
            ], 403);
        }

        if (! WidgetRegistry::exists($type)) {
            return response()->json([
                'success' => false,
                'message' => "Widget type '{$type}' not found",
            ], 404);
        }

        try {
            if ($enabled) {
                $result = $permissionService->enableWidget($type);
                $message = "Widget '{$type}' enabled successfully";
            } else {
                $result = $permissionService->disableWidget($type);
                $message = "Widget '{$type}' disabled successfully";
            }

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to toggle widget state',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling widget: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get widget permissions for current user
     */
    public function getPermissions()
    {
        $permissionService = new WidgetPermissionService;

        return response()->json([
            'success' => true,
            'permissions' => $permissionService->getPermissionSummary(),
        ]);
    }

    /**
     * Export widgets configuration
     */
    public function export(Request $request)
    {
        $permissionService = new WidgetPermissionService;

        if (! $permissionService->canManageWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to export widgets',
            ], 403);
        }

        try {
            $importExportService = new WidgetImportExportService;

            $options = [
                'areas' => $request->get('areas'),
                'types' => $request->get('types'),
                'include_metadata' => $request->boolean('include_metadata', true),
            ];

            $exportData = $importExportService->exportWidgets($options);

            return response()->json([
                'success' => true,
                'data' => $exportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import widgets configuration
     */
    public function import(Request $request)
    {
        $permissionService = new WidgetPermissionService;

        if (! $permissionService->canManageWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to import widgets',
            ], 403);
        }

        $validated = $request->validate([
            'import_data' => 'required|array',
            'overwrite_existing' => 'boolean',
            'validate_only' => 'boolean',
        ]);

        try {
            $importExportService = new WidgetImportExportService;

            $options = [
                'overwrite_existing' => $validated['overwrite_existing'] ?? false,
                'validate_only' => $validated['validate_only'] ?? false,
            ];

            $result = $importExportService->importWidgets($validated['import_data'], $options);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create widget backup
     */
    public function createBackup(Request $request)
    {
        $permissionService = new WidgetPermissionService;

        if (! $permissionService->canManageWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create backups',
            ], 403);
        }

        try {
            $importExportService = new WidgetImportExportService;
            $backupName = $request->get('name');

            $backupPath = $importExportService->createBackup($backupName);

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully',
                'backup_path' => basename($backupPath),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available backups
     */
    public function getBackups()
    {
        $permissionService = new WidgetPermissionService;

        if (! $permissionService->canManageWidgets()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view backups',
            ], 403);
        }

        try {
            $importExportService = new WidgetImportExportService;
            $backups = $importExportService->getAvailableBackups();

            return response()->json([
                'success' => true,
                'backups' => $backups,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get backups: '.$e->getMessage(),
            ], 500);
        }
    }
}
