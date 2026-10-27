<?php

namespace App\Services;

use App\Models\Widget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class WidgetErrorHandlingService
{
    protected array $errorLog = [];

    protected bool $debugMode;

    public function __construct()
    {
        $this->debugMode = config('app.debug', false);
    }

    /**
     * Handle widget rendering error with graceful degradation
     */
    public function handleRenderingError(string $widgetType, array $settings, string $variant, Throwable $error): string
    {
        $this->logError('rendering', $widgetType, $error, [
            'settings' => $settings,
            'variant' => $variant,
        ]);

        // Try fallback rendering
        $fallback = $this->tryFallbackRendering($widgetType, $settings, $variant, $error);
        if ($fallback !== null) {
            return $fallback;
        }

        // Return error placeholder
        return $this->renderErrorPlaceholder($widgetType, $error);
    }

    /**
     * Handle widget validation error
     */
    public function handleValidationError(string $widgetType, array $settings, Throwable $error): array
    {
        $this->logError('validation', $widgetType, $error, [
            'settings' => $settings,
        ]);

        return [
            'success' => false,
            'error' => $this->debugMode ? $error->getMessage() : 'Widget validation failed',
            'error_code' => 'VALIDATION_ERROR',
            'widget_type' => $widgetType,
            'recoverable' => $this->isRecoverableError($error),
        ];
    }

    /**
     * Handle widget discovery error
     */
    public function handleDiscoveryError(string $widgetPath, Throwable $error): void
    {
        $this->logError('discovery', $widgetPath, $error);

        // Mark widget as problematic
        $this->markWidgetAsProblematic($widgetPath, $error);
    }

    /**
     * Handle widget instantiation error
     */
    public function handleInstantiationError(string $widgetClass, Throwable $error): ?object
    {
        $this->logError('instantiation', $widgetClass, $error);

        // Try to create a safe fallback widget
        return $this->createFallbackWidget($widgetClass, $error);
    }

    /**
     * Try fallback rendering strategies
     */
    protected function tryFallbackRendering(string $widgetType, array $settings, string $variant, Throwable $error): ?string
    {
        // Strategy 1: Try with default variant
        if ($variant !== 'default') {
            try {
                return WidgetRegistry::render($widgetType, $settings, 'default');
            } catch (Throwable $e) {
                $this->logError('fallback_variant', $widgetType, $e);
            }
        }

        // Strategy 2: Try with minimal settings
        if (! empty($settings)) {
            try {
                $minimalSettings = $this->getMinimalSettings($widgetType);

                return WidgetRegistry::render($widgetType, $minimalSettings, $variant);
            } catch (Throwable $e) {
                $this->logError('fallback_minimal', $widgetType, $e);
            }
        }

        // Strategy 3: Try cached version
        $cachedVersion = $this->getCachedFallback($widgetType, $settings, $variant);
        if ($cachedVersion) {
            return $cachedVersion;
        }

        return null;
    }

    /**
     * Get minimal settings for a widget type
     */
    protected function getMinimalSettings(string $widgetType): array
    {
        try {
            $config = WidgetRegistry::getConfig($widgetType);
            $fields = $config['fields'] ?? [];
            $minimalSettings = [];

            foreach ($fields as $field) {
                if ($field['required'] ?? false) {
                    $minimalSettings[$field['name']] = $field['default'] ?? $this->getDefaultValueForType($field['type']);
                }
            }

            return $minimalSettings;
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Get default value for field type
     */
    protected function getDefaultValueForType(string $type): mixed
    {
        return match ($type) {
            'text', 'textarea', 'url', 'email' => '',
            'number', 'range' => 0,
            'checkbox' => false,
            'select' => '',
            'image' => '',
            'gallery', 'repeatable' => [],
            'date' => '',
            'color' => '#000000',
            default => ''
        };
    }

    /**
     * Render error placeholder
     */
    protected function renderErrorPlaceholder(string $widgetType, Throwable $error): string
    {
        if (! $this->debugMode) {
            return '<!-- Widget Error: '.htmlspecialchars($widgetType).' -->';
        }

        $errorId = uniqid('widget_error_');
        $errorMessage = htmlspecialchars($error->getMessage());
        $errorFile = htmlspecialchars($error->getFile());
        $errorLine = $error->getLine();

        return "
    <div class=\"widget-error-placeholder\" id=\"{$errorId}\" style=\"
      border: 2px dashed #ef4444;
      background: #fef2f2;
      color: #dc2626;
      padding: 1rem;
      margin: 0.5rem 0;
      border-radius: 0.5rem;
      font-family: monospace;
      font-size: 0.875rem;
    \">
      <div style=\"font-weight: bold; margin-bottom: 0.5rem;\">
        ️ Widget Error: {$widgetType}
      </div>
      <div style=\"margin-bottom: 0.5rem;\">
        <strong>Error:</strong> {$errorMessage}
      </div>
      <div style=\"font-size: 0.75rem; color: #991b1b;\">
        <strong>File:</strong> {$errorFile}:{$errorLine}
      </div>
      <button onclick=\"this.parentElement.style.display='none'\" style=\"
        background: #dc2626;
        color: white;
        border: none;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        cursor: pointer;
        margin-top: 0.5rem;
        font-size: 0.75rem;
      \">Hide Error</button>
    </div>";
    }

    /**
     * Create fallback widget instance
     */
    protected function createFallbackWidget(string $widgetClass, Throwable $error): ?object
    {
        try {
            // Create a minimal widget that just displays an error message
            return new class($widgetClass, $error)
            {
                private string $originalClass;

                private Throwable $error;

                public function __construct(string $originalClass, Throwable $error)
                {
                    $this->originalClass = $originalClass;
                    $this->error = $error;
                }

                public function render(): string
                {
                    return '<div class="widget-fallback">Widget temporarily unavailable</div>';
                }

                public function getPreview(): string
                {
                    return $this->render();
                }

                public function css(): string
                {
                    return '<style>.widget-fallback { padding: 1rem; background: #f3f4f6; color: #6b7280; text-align: center; }</style>';
                }

                public function js(): string
                {
                    return '';
                }
            };
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Log error with context
     */
    protected function logError(string $type, string $context, Throwable $error, array $extra = []): void
    {
        $logData = [
            'error_type' => $type,
            'context' => $context,
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $this->debugMode ? $error->getTraceAsString() : null,
            'extra' => $extra,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
        ];

        Log::error("Widget {$type} error: {$context}", $logData);

        // Store in memory for batch processing
        $this->errorLog[] = $logData;

        // Store critical errors in cache for monitoring
        if ($this->isCriticalError($error)) {
            $this->storeCriticalError($type, $context, $error);
        }
    }

    /**
     * Check if error is recoverable
     */
    protected function isRecoverableError(Throwable $error): bool
    {
        // Recoverable errors are typically validation or configuration issues
        return $error instanceof \InvalidArgumentException ||
            $error instanceof \UnexpectedValueException ||
            str_contains($error->getMessage(), 'validation') ||
            str_contains($error->getMessage(), 'configuration');
    }

    /**
     * Check if error is critical
     */
    protected function isCriticalError(Throwable $error): bool
    {
        return $error instanceof \Error ||
            $error instanceof \ParseError ||
            $error instanceof \TypeError ||
            str_contains($error->getMessage(), 'fatal');
    }

    /**
     * Store critical error for monitoring
     */
    protected function storeCriticalError(string $type, string $context, Throwable $error): void
    {
        $cacheKey = 'widget_critical_errors';
        $errors = Cache::get($cacheKey, []);

        $errors[] = [
            'type' => $type,
            'context' => $context,
            'message' => $error->getMessage(),
            'timestamp' => now()->toISOString(),
        ];

        // Keep only last 50 critical errors
        if (count($errors) > 50) {
            $errors = array_slice($errors, -50);
        }

        Cache::put($cacheKey, $errors, 86400); // 24 hours
    }

    /**
     * Mark widget as problematic
     */
    protected function markWidgetAsProblematic(string $widgetPath, Throwable $error): void
    {
        $cacheKey = 'problematic_widgets';
        $problematic = Cache::get($cacheKey, []);

        $problematic[$widgetPath] = [
            'error' => $error->getMessage(),
            'timestamp' => now()->toISOString(),
            'count' => ($problematic[$widgetPath]['count'] ?? 0) + 1,
        ];

        Cache::put($cacheKey, $problematic, 3600); // 1 hour
    }

    /**
     * Get cached fallback content
     */
    protected function getCachedFallback(string $widgetType, array $settings, string $variant): ?string
    {
        $cacheKey = "widget_fallback_{$widgetType}_{$variant}_".md5(serialize($settings));

        return Cache::get($cacheKey);
    }

    /**
     * Store fallback content in cache
     */
    public function storeFallbackContent(string $widgetType, array $settings, string $variant, string $content): void
    {
        $cacheKey = "widget_fallback_{$widgetType}_{$variant}_".md5(serialize($settings));
        Cache::put($cacheKey, $content, 3600); // 1 hour
    }

    /**
     * Get error statistics
     */
    public function getErrorStatistics(): array
    {
        $criticalErrors = Cache::get('widget_critical_errors', []);
        $problematicWidgets = Cache::get('problematic_widgets', []);

        return [
            'critical_errors_count' => count($criticalErrors),
            'problematic_widgets_count' => count($problematicWidgets),
            'recent_critical_errors' => array_slice($criticalErrors, -10),
            'most_problematic_widgets' => $this->getMostProblematicWidgets($problematicWidgets),
            'error_types' => $this->getErrorTypeDistribution($criticalErrors),
        ];
    }

    /**
     * Get most problematic widgets
     */
    protected function getMostProblematicWidgets(array $problematic): array
    {
        uasort($problematic, function ($a, $b) {
            return $b['count'] - $a['count'];
        });

        return array_slice($problematic, 0, 10, true);
    }

    /**
     * Get error type distribution
     */
    protected function getErrorTypeDistribution(array $errors): array
    {
        $distribution = [];

        foreach ($errors as $error) {
            $type = $error['type'] ?? 'unknown';
            $distribution[$type] = ($distribution[$type] ?? 0) + 1;
        }

        return $distribution;
    }

    /**
     * Clear error logs and caches
     */
    public function clearErrorLogs(): void
    {
        Cache::forget('widget_critical_errors');
        Cache::forget('problematic_widgets');
        $this->errorLog = [];
    }

    /**
     * Perform widget health check
     */
    public function performHealthCheck(): array
    {
        $results = [
            'healthy' => 0,
            'unhealthy' => 0,
            'issues' => [],
        ];

        $widgets = WidgetRegistry::all();

        foreach ($widgets as $widget) {
            try {
                // Test widget instantiation
                $widgetClass = $widget['class'];
                $testWidget = new $widgetClass;

                // Test basic rendering
                $testWidget->getPreview();

                $results['healthy']++;
            } catch (Throwable $e) {
                $results['unhealthy']++;
                $results['issues'][] = [
                    'widget_type' => $widget['type'],
                    'error' => $e->getMessage(),
                    'severity' => $this->isCriticalError($e) ? 'critical' : 'warning',
                ];
            }
        }

        return $results;
    }
}
