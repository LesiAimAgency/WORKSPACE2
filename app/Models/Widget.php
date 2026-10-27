<?php

namespace App\Models;

use App\Widgets\WidgetRegistry;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{

    protected $fillable = ['project_id', 'name', 'type', 'area', 'settings', 'sort_order', 'is_active', 'variant', 'metadata', 'tenant_id', 'widget_code', 'rules', 'data', 'is_lazy_loaded'];

    protected $casts = [
        'settings' => 'array',
        'metadata' => 'array',
        'rules' => 'array',
        'data' => 'array',
        'is_active' => 'boolean',
        'is_lazy_loaded' => 'boolean',
    ];

    public function getSettingsAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && ! empty($value)) {
            while (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_string($decoded))) {
                    $value = $decoded;
                } else {
                    break;
                }
            }
            if (is_array($value)) {
                return $value;
            }
        }

        return [];
    }

    /**
     * Get rendered content for this widget
     */
    public function getRenderedContent(): string
    {
        return WidgetRegistry::render(
            $this->type,
            $this->settings ?? [],
            $this->variant ?? 'default'
        );
    }

    /**
     * Validate widget settings against metadata
     */
    public function validateSettings(): bool
    {
        try {
            $widgetClass = WidgetRegistry::get($this->type);
            if ($widgetClass) {
                $widget = new $widgetClass($this->settings ?? [], $this->variant ?? 'default');

                return $widget->validateSettings();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get preview HTML for admin interface
     */
    public function getPreview(): string
    {
        return WidgetRegistry::getPreview(
            $this->type,
            $this->settings ?? [],
            $this->variant ?? 'default'
        );
    }

    /**
     * Get widget metadata
     */
    public function getWidgetMetadata(): ?array
    {
        return WidgetRegistry::getConfig($this->type);
    }

    /**
     * Get menu items for a specific area/slug.
     */
    public static function getMenu(string $area): array
    {
        $currentLocale = strtolower(app()->getLocale() ?: 'vi');

        $project = function_exists('current_project') ? current_project() : null;
        if (! $project && app()->bound('current_project_id')) {
            $project = Project::find(app('current_project_id'));
        }
        if (! $project && session('current_project_id')) {
            $project = Project::find(session('current_project_id'));
        }
        if (! $project && request()->route('projectCode')) {
            $project = Project::where('code', request()->route('projectCode'))->first();
        }

        $projectId = $project?->id;
        $projectCode = $project?->code ?? request()->route('projectCode');

        // 1. Primary Source: Check dynamic Menu model for this project
        if ($projectId && class_exists('\App\Models\Menu')) {
            $dbMenu = Menu::withoutGlobalScopes()
                ->where('project_id', $projectId)
                ->where(function ($q) use ($area) {
                    $q->where('slug', $area)
                        ->orWhere('slug', str_replace('-menu', '', $area))
                        ->orWhere('location', str_contains($area, 'header') ? 'header' : (str_contains($area, 'footer') ? 'footer' : $area));
                })
                ->where('is_active', true)
                ->with(['items' => function ($q) {
                    $q->withoutGlobalScopes()->whereNull('parent_id')->with(['children' => function ($cq) {
                        $cq->withoutGlobalScopes()->orderBy('order');
                    }])->orderBy('order');
                }])
                ->first();

            if ($dbMenu && $dbMenu->items && $dbMenu->items->isNotEmpty()) {
                $formatItem = function ($item) use (&$formatItem) {
                    return [
                        'id' => $item->id,
                        'label' => $item->title,
                        'url' => $item->url,
                        'target' => $item->target ?? '_self',
                        'icon' => $item->icon ?? null,
                        'image' => $item->image ?? null,
                        'badge' => $item->badge ?? null,
                        'badge_color' => $item->badge_color ?? null,
                        'children' => $item->children && $item->children->isNotEmpty() ? $item->children->map($formatItem)->toArray() : [],
                    ];
                };

                $items = $dbMenu->items->map($formatItem)->toArray();

                if ($projectCode) {
                    $items = static::prefixMenuItemsUrl($items, $projectCode);
                }

                return static::deduplicateMenuItems($items);
            }
        }

        // 2. Secondary Source: Try finding widget with area and matching locale
        $query = static::where('area', $area)->where('is_active', true);
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        $widget = (clone $query)->where('settings->locale', $currentLocale)->first();

        // 3. Fallback: Any active widget for this area in this project
        if (! $widget) {
            $widget = $query->first();
        }

        // 4. Fallback: Global widgets (project_id IS NULL)
        if (! $widget && $projectId) {
            $widget = static::where('area', $area)
                ->whereNull('project_id')
                ->where('is_active', true)
                ->where('settings->locale', $currentLocale)
                ->first()
                ?? static::where('area', $area)->whereNull('project_id')->where('is_active', true)->first();
        }

        if (! $widget) {
            return [];
        }

        $settings = $widget->settings ?? [];
        $items = $settings['items'] ?? $widget->config['items'] ?? [];

        if (! is_array($items)) {
            return [];
        }

        // Format relative URLs with project code if present
        if ($projectCode) {
            $items = static::prefixMenuItemsUrl($items, $projectCode);
        }

        return static::deduplicateMenuItems($items);
    }

    protected static function deduplicateMenuItems(array $items): array
    {
        return array_values(collect($items)->unique(function ($i) {
            return ($i['label'] ?? '').'|'.($i['url'] ?? '');
        })->map(function ($i) {
            if (! empty($i['children']) && is_array($i['children'])) {
                $i['children'] = static::deduplicateMenuItems($i['children']);
            }

            return $i;
        })->toArray());
    }

    protected static function prefixMenuItemsUrl(array $items, string $projectCode = ''): array
    {
        return $items;
    }

    public function scopeForProject($query, ?int $projectId = null)
    {
        return $query;
    }

    /**
     * Relationship back to the project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
