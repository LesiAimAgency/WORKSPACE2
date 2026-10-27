<?php

namespace App\Services;

use App\Core\Theme\ThemeManager;
use App\Models\Project;
use App\Models\ProjectSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsService
{
    private static $instance = null;

    private $settings = [];

    private $loadedForProject = null;

    private function __construct()
    {
        // Don't load in constructor - load on demand
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Resolve project context with standalone/single-project auto detection
     */
    public function resolveCurrentProject()
    {
        $project = request()->attributes->get('project');
        if ($project) {
            return $project;
        }

        if (function_exists('current_project')) {
            $current = current_project();
            if ($current) {
                return $current;
            }
        }

        if (config('app.standalone_mode') || env('STANDALONE_MODE')) {
            try {
                return Project::first();
            } catch (\Throwable $e) {
            }
        }

        try {
            if (Project::count() === 1) {
                return Project::first();
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    /**
     * Check if currently in project context
     */
    private function isProjectContext(): bool
    {
        if ($this->resolveCurrentProject()) {
            return true;
        }

        return config('database.default') === 'project';
    }

    /**
     * Get current project identifier for cache key
     */
    private function getCurrentProjectKey(): string
    {
        $project = $this->resolveCurrentProject();
        if ($project) {
            return 'project_'.($project->id ?? $project->code ?? 'unknown');
        }

        $projId = session('current_project_id');
        if ($projId) {
            return 'project_'.$projId;
        }

        $sess = session('current_project');
        if (is_string($sess)) {
            return 'project_'.$sess;
        }

        if ($this->isProjectContext()) {
            return config('database.connections.project.database') ?? 'project';
        }

        return 'main';
    }

    private function parseSettingsRows($rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $val = null;
            if (! empty($row->payload)) {
                if (is_array($row->payload)) {
                    $val = $row->payload;
                } elseif (is_string($row->payload)) {
                    $decoded = json_decode($row->payload, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $val = $decoded;
                    } else {
                        $val = $row->payload;
                    }
                }
            }
            if ($val === null && isset($row->value) && $row->value !== null) {
                if (is_array($row->value)) {
                    $val = $row->value;
                } elseif (is_string($row->value)) {
                    $decoded = json_decode($row->value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $val = $decoded;
                    } else {
                        $val = $row->value;
                    }
                } else {
                    $val = $row->value;
                }
            }
            $result[$row->key] = $val;
        }

        return $result;
    }

    private function loadSettings()
    {
        $currentProject = $this->getCurrentProjectKey();

        // Skip if already loaded for this project
        if ($this->loadedForProject === $currentProject && ! empty($this->settings)) {
            return;
        }

        $this->settings = [];
        $this->loadedForProject = $currentProject;

        if ($this->isProjectContext()) {
            try {
                // Đọc từ database với project scoping
                $project = $this->resolveCurrentProject();
                if ($project) {
                    $mainConn = config('database.default');
                    // Load global settings (project_id IS NULL) làm fallback
                    $globalRows = DB::connection($mainConn)
                        ->table('settings')
                        ->whereNull('project_id')
                        ->select(['key', 'payload', 'value'])
                        ->get();
                    $globalSettings = $this->parseSettingsRows($globalRows);

                    // Load tenant / project-specific settings (override global)
                    $tenantId = $project->tenant_id ?? session('current_tenant_id') ?? ($project->code === 'viettinmart-eco' ? 3 : (str_contains($project->code ?? '', 'wkcomputer') ? 4 : null));
                    $projectRows = DB::connection($mainConn)
                        ->table('settings')
                        ->where(function ($q) use ($project, $tenantId) {
                            if ($tenantId) {
                                $q->where('tenant_id', $tenantId);
                            }
                            if ($project->id) {
                                $q->orWhere('project_id', $project->id);
                            }
                        })
                        ->select(['key', 'payload', 'value'])
                        ->get();
                    $projectSettings = $this->parseSettingsRows($projectRows);

                    // Merge: project settings override global
                    $this->settings = array_merge($globalSettings, $projectSettings);
                } else {
                    // Fallback: thử đọc từ project database
                    $projectRows = DB::connection('project')
                        ->table('settings')
                        ->select(['key', 'payload', 'value'])
                        ->get();
                    $this->settings = $this->parseSettingsRows($projectRows);
                }
            } catch (\Exception $e) {
                \Log::warning("Failed to load project settings for {$currentProject}: ".$e->getMessage());
                $this->settings = [];
            }
        } else {
            try {
                $cacheKey = 'all_settings_main';
                $this->settings = Cache::rememberForever($cacheKey, function () {
                    try {
                        $rows = Setting::select(['key', 'payload', 'value'])->get();

                        return $this->parseSettingsRows($rows);
                    } catch (\Throwable $e) {
                        return [];
                    }
                }) ?? [];
            } catch (\Throwable $e) {
                $this->settings = [];
            }
        }
    }

    public function get($key, $default = null)
    {
        $this->loadSettings();

        if (! array_key_exists($key, $this->settings)) {
            if (class_exists(ThemeManager::class)) {
                $themeFallback = app(ThemeManager::class)->getDefaultSetting($key, null);
                if ($themeFallback !== null) {
                    return $themeFallback;
                }
            }

            return $default;
        }

        $value = $this->settings[$key];

        // Nếu là string JSON dạng {"value":...} thì tự động unwrap
        if (is_string($value) && str_starts_with(trim($value), '{"value":')) {
            $decoded = json_decode($value, true);
            if (is_array($decoded) && array_key_exists('value', $decoded)) {
                $value = $decoded['value'];
            }
        }

        // Nếu là array và có key 'value', trả về giá trị đó
        if (\is_array($value)) {
            if (array_key_exists('value', $value)) {
                $val = $value['value'];

                if ($val !== null && $val !== '') {
                    return $val;
                }
            } else {
                return $value;
            }
        } elseif ($value !== null && $value !== '') {
            return $value;
        }

        if (class_exists(ThemeManager::class)) {
            $themeFallback = app(ThemeManager::class)->getDefaultSetting($key, null);
            if ($themeFallback !== null) {
                return $themeFallback;
            }
        }

        return $default;
    }

    public function set($key, $value, $group = null, $locked = false)
    {
        $payloadVal = is_array($value) ? $value : ['value' => $value];
        $scalarVal = is_scalar($value) ? (string) $value : (is_null($value) ? null : json_encode($value));

        if ($this->isProjectContext()) {
            $project = request()->attributes->get('project');
            if ($project) {
                $mainConn = config('database.default');
                DB::connection($mainConn)->table('settings')->updateOrInsert(
                    ['key' => $key, 'project_id' => $project->id],
                    [
                        'payload' => json_encode($payloadVal),
                        'value' => $scalarVal,
                        'tenant_id' => $project->tenant_id ?? $project->id,
                        'group' => $group ?? 'general',
                        'updated_at' => now(),
                    ]
                );
            } else {
                ProjectSetting::set($key, $value, $group);
            }
        } else {
            Setting::updateOrCreate(
                ['key' => $key, 'project_id' => null],
                [
                    'payload' => $payloadVal,
                    'value' => $scalarVal,
                    'group' => $group ?? 'general',
                    'locked' => $locked,
                ]
            );
        }

        $this->clearCache();
    }

    public function clearCache()
    {
        Cache::forget('all_settings_main');
        $this->settings = [];
        $this->loadedForProject = null;
    }

    public function forceReload()
    {
        $this->settings = [];
        $this->loadedForProject = null;
        $this->loadSettings();
    }
}
