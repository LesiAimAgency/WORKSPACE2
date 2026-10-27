<?php

namespace App\Services;

use App\Core\Theme\ThemeManager;
use App\Models\Project;
use App\Models\Widget;
use App\Widgets\BaseWidget;
use App\Widgets\WidgetRegistry;
use Database\Seeders\WkcomputerWidgetsSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\View\Factory;

class WidgetRenderingService
{
    protected array $renderingContext = [];

    protected bool $cacheEnabled = false;

    protected int $defaultCacheDuration = 3600; // 1 hour

    public function __construct()
    {
        $this->cacheEnabled = (bool) env('WIDGET_CACHE', false);
    }

    /**
     * Render a widget with caching and error handling
     */
    public function render(string $type, array $settings = [], string $variant = 'default', array $context = []): string
    {
        $viewFactory = app('view');
        $savedStack = null;
        $savedSections = null;
        $stackProp = null;
        $sectionsProp = null;

        try {
            if ($viewFactory instanceof Factory) {
                $ref = new \ReflectionClass($viewFactory);
                if ($ref->hasProperty('sectionStack')) {
                    $stackProp = $ref->getProperty('sectionStack');
                    $stackProp->setAccessible(true);
                    $savedStack = $stackProp->getValue($viewFactory);
                }
                if ($ref->hasProperty('sections')) {
                    $sectionsProp = $ref->getProperty('sections');
                    $sectionsProp->setAccessible(true);
                    $savedSections = $sectionsProp->getValue($viewFactory);
                }
            }

            $this->renderingContext = $context;

            // Get widget class
            $widgetClass = WidgetRegistry::get($type);
            if (! $widgetClass) {
                return $this->renderError("Widget type '{$type}' not found");
            }

            if (! class_exists($widgetClass)) {
                return $this->renderError("Widget class '{$widgetClass}' for type '{$type}' not found");
            }

            // Create widget instance
            $widget = new $widgetClass($settings, $variant);
            if (isset($settings['id'])) {
                $widget->id = $settings['id'];
            }

            // Check if widget should be cached
            if ($this->shouldCache($widget)) {
                return $this->renderWithCache($widget, $type, $settings, $variant, $context);
            }

            return $this->renderWidget($widget);

        } catch (\Throwable $e) {
            Log::error('Widget rendering failed: '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine());

            // If an inner view exception caused Laravel to flush the outer section stack, restore it
            if ($stackProp && ! empty($savedStack) && empty($stackProp->getValue($viewFactory))) {
                $stackProp->setValue($viewFactory, $savedStack);
                if ($sectionsProp && ! empty($savedSections)) {
                    $sectionsProp->setValue($viewFactory, $savedSections);
                }
            }

            // Use error handling service for graceful degradation
            $errorHandler = new WidgetErrorHandlingService;

            return $errorHandler->handleRenderingError($type, $settings, $variant, $e);
        }
    }

    /**
     * Render widget with caching
     */
    protected function renderWithCache(BaseWidget $widget, string $type, array $settings, string $variant, array $context): string
    {
        $cacheKey = $this->generateCacheKey($type, $settings, $variant, $context);
        $cacheDuration = $this->getCacheDuration($widget);

        return Cache::remember($cacheKey, $cacheDuration, function () use ($widget) {
            return $this->renderWidget($widget);
        });
    }

    /**
     * Actually render the widget
     */
    protected function renderWidget(BaseWidget $widget): string
    {
        $html = '';

        // Add CSS if available
        $css = $widget->css();
        if (! empty($css)) {
            $html .= "<style>{$css}</style>";
        }

        // Render main content
        $html .= $widget->render();

        // Add JavaScript if available
        $js = $widget->js();
        if (! empty($js)) {
            $html .= "<script>{$js}</script>";
        }

        return $html;
    }

    /**
     * Render multiple widgets in a container
     */
    public function renderContainer(array $widgets, string $containerClass = '', array $containerAttributes = []): string
    {
        if (empty($widgets)) {
            return '';
        }

        $renderedWidgets = [];

        foreach ($widgets as $widget) {
            $type = $widget['type'] ?? '';
            $settings = $widget['settings'] ?? [];
            $variant = $widget['variant'] ?? 'default';

            if (empty($type)) {
                continue;
            }

            $rendered = $this->render($type, $settings, $variant, $this->renderingContext);
            if (! empty($rendered)) {
                $renderedWidgets[] = $rendered;
            }
        }

        if (empty($renderedWidgets)) {
            return '';
        }

        // Build container attributes
        $attributes = '';
        if (! empty($containerClass)) {
            $attributes .= " class=\"{$containerClass}\"";
        }

        foreach ($containerAttributes as $key => $value) {
            $attributes .= " {$key}=\"".htmlspecialchars($value).'"';
        }

        return "<div{$attributes}>".implode("\n", $renderedWidgets).'</div>';
    }

    public function renderArea(string $area, array $context = []): string
    {
        $this->renderingContext = $context;
        $html = '';

        try {
            $widgets = Widget::where('area', $area)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            foreach ($widgets as $widget) {
                $settings = $widget->settings ?? [];
                if (! isset($settings['id'])) {
                    $settings['id'] = $widget->id;
                }

                $html .= $this->render(
                    $widget->type,
                    $settings,
                    $widget->variant ?? 'default',
                    $context
                );
            }

            return $html;
        } catch (\Throwable $e) {
            Log::error('Widget area rendering failed: '.$e->getMessage());

            return '';
        }
    }

    /**
     * Render widget with template variant support
     */
    public function renderWithTemplate(string $type, array $settings = [], string $variant = 'default', ?string $template = null): string
    {
        try {
            $widgetClass = WidgetRegistry::get($type);
            if (! $widgetClass) {
                return $this->renderError("Widget type '{$type}' not found");
            }

            if (! class_exists($widgetClass)) {
                return $this->renderError("Widget class '{$widgetClass}' for type '{$type}' not found");
            }

            $widget = new $widgetClass($settings, $variant);

            // If custom template is specified, try to use it
            if ($template && View::exists($template)) {
                return View::make($template, [
                    'widget' => $widget,
                    'settings' => $settings,
                    'variant' => $variant,
                    'context' => $this->renderingContext,
                ])->render();
            }

            return $this->renderWidget($widget);

        } catch (\Throwable $e) {
            Log::error('Widget template rendering failed: '.$e->getMessage());

            return $this->renderError('Template rendering failed');
        }
    }

    /**
     * Check if widget should be cached
     */
    protected function shouldCache(BaseWidget $widget): bool
    {
        if (! $this->cacheEnabled || config('app.debug', false) || env('WIDGET_CACHE', false) === false) {
            return false;
        }

        $metadata = $widget->getMetadata();
        $settings = $metadata['settings'] ?? [];

        return $settings['cacheable'] ?? true;
    }

    /**
     * Get cache duration for widget
     */
    protected function getCacheDuration(BaseWidget $widget): int
    {
        $metadata = $widget->getMetadata();
        $settings = $metadata['settings'] ?? [];

        return $settings['cache_duration'] ?? $this->defaultCacheDuration;
    }

    /**
     * Generate cache key for widget
     */
    protected function generateCacheKey(string $type, array $settings, string $variant, array $context): string
    {
        $keyData = [
            'type' => $type,
            'settings' => $settings,
            'variant' => $variant,
            'context' => $context,
            'user_id' => auth()->id(),
            'locale' => app()->getLocale(),
        ];

        return 'widget_render_'.md5(serialize($keyData));
    }

    /**
     * Render error message
     */
    protected function renderError(string $message): string
    {
        if (app()->environment('production')) {
            return '<!-- Widget Error: '.htmlspecialchars($message).' -->';
        }

        return '<div class="widget-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">'
             .'<strong>Widget Error:</strong> '.htmlspecialchars($message)
             .'</div>';
    }

    /**
     * Set rendering context
     */
    public function setContext(array $context): self
    {
        $this->renderingContext = $context;

        return $this;
    }

    /**
     * Get rendering context
     */
    public function getContext(): array
    {
        return $this->renderingContext;
    }

    /**
     * Enable/disable caching
     */
    public function setCacheEnabled(bool $enabled): self
    {
        $this->cacheEnabled = $enabled;

        return $this;
    }

    /**
     * Set default cache duration
     */
    public function setDefaultCacheDuration(int $duration): self
    {
        $this->defaultCacheDuration = $duration;

        return $this;
    }

    /**
     * Clear widget cache
     */
    public function clearCache(?string $type = null): void
    {
        if ($type) {
            // Clear cache for specific widget type
            $pattern = "widget_render_*{$type}*";
            // Note: Laravel doesn't support pattern-based cache clearing by default
            // This would need a custom cache implementation or use cache tags
            Cache::flush(); // For now, clear all cache
        } else {
            // Clear all widget cache
            Cache::flush();
        }
    }

    /**
     * Render widget in isolation (for testing/preview)
     */
    public function renderIsolated(string $type, array $settings = [], string $variant = 'default'): array
    {
        try {
            $widgetClass = WidgetRegistry::get($type);
            if (! $widgetClass) {
                return [
                    'success' => false,
                    'error' => "Widget type '{$type}' not found",
                ];
            }

            $widget = new $widgetClass($settings, $variant);

            return [
                'success' => true,
                'html' => $widget->render(),
                'css' => $widget->css(),
                'js' => $widget->js(),
                'metadata' => $widget->getMetadata(),
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => app()->environment('local') ? $e->getTraceAsString() : null,
            ];
        }
    }

    /**
     * Batch render multiple widgets
     */
    public function batchRender(array $widgetConfigs): array
    {
        $results = [];

        foreach ($widgetConfigs as $config) {
            $type = $config['type'] ?? '';
            $settings = $config['settings'] ?? [];
            $variant = $config['variant'] ?? 'default';
            $id = $config['id'] ?? uniqid();

            $results[$id] = $this->renderIsolated($type, $settings, $variant);
        }

        return $results;
    }

    /**
     * Optimize widget rendering performance
     */
    public function optimizeRendering(): self
    {
        // Preload commonly used widgets
        $commonWidgets = ['hero', 'features', 'cta', 'newsletter'];

        foreach ($commonWidgets as $type) {
            if (WidgetRegistry::exists($type)) {
                try {
                    $widgetClass = WidgetRegistry::get($type);
                    // Preload class to improve performance
                    class_exists($widgetClass);
                } catch (\Exception $e) {
                    // Ignore preload errors
                }
            }
        }

        return $this;
    }
}
