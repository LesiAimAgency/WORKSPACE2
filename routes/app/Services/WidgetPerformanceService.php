<?php

namespace App\Services;

use App\Models\Widget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WidgetPerformanceService
{
    protected array $performanceMetrics = [];

    protected bool $monitoringEnabled;

    public function __construct()
    {
        $this->monitoringEnabled = config('app.debug', false);
    }

    /**
     * Optimize widget caching strategies
     */
    public function optimizeCaching(): array
    {
        $results = [
            'cache_hits' => 0,
            'cache_misses' => 0,
            'optimized_widgets' => 0,
            'cache_size_before' => $this->getCacheSize(),
        ];

        // Preload frequently used widgets
        $frequentWidgets = $this->getFrequentlyUsedWidgets();
        foreach ($frequentWidgets as $widget) {
            $this->preloadWidget($widget);
            $results['optimized_widgets']++;
        }

        // Optimize cache keys and durations
        $this->optimizeCacheKeys();

        // Clean up stale cache entries
        $this->cleanupStaleCache();

        $results['cache_size_after'] = $this->getCacheSize();

        return $results;
    }

    /**
     * Get frequently used widgets
     */
    protected function getFrequentlyUsedWidgets(int $limit = 20): array
    {
        return Widget::select('type', DB::raw('COUNT(*) as usage_count'))
            ->where('is_active', true)
            ->groupBy('type')
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Preload widget for caching
     */
    protected function preloadWidget(array $widgetData): void
    {
        try {
            $type = $widgetData['type'];
            $widgetClass = WidgetRegistry::get($type);

            if ($widgetClass) {
                // Preload class
                class_exists($widgetClass);

                // Preload metadata
                $config = WidgetRegistry::getConfig($type);
                if ($config) {
                    $cacheKey = "widget_config_{$type}";
                    Cache::put($cacheKey, $config, 7200); // 2 hours
                }

                // Preload common variants
                $this->preloadCommonVariants($type, $widgetClass);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to preload widget {$widgetData['type']}: ".$e->getMessage());
        }
    }

    /**
     * Preload common widget variants
     */
    protected function preloadCommonVariants(string $type, string $widgetClass): void
    {
        try {
            $widget = new $widgetClass;
            $variants = $widget->getVariants();

            foreach (array_keys($variants) as $variant) {
                if (in_array($variant, ['default', 'compact', 'minimal'])) {
                    $cacheKey = "widget_variant_{$type}_{$variant}";
                    Cache::remember($cacheKey, 3600, function () use ($widgetClass, $variant) {
                        return new $widgetClass([], $variant);
                    });
                }
            }
        } catch (\Exception $e) {
            // Ignore preload errors
        }
    }

    /**
     * Optimize cache keys for better performance
     */
    protected function optimizeCacheKeys(): void
    {
        // Implement cache key optimization strategies
        $this->compactCacheKeys();
        $this->groupRelatedCacheEntries();
    }

    /**
     * Compact cache keys to reduce memory usage
     */
    protected function compactCacheKeys(): void
    {
        // Use shorter, more efficient cache keys
        $longKeys = Cache::get('widget_long_keys', []);

        foreach ($longKeys as $longKey => $shortKey) {
            $value = Cache::get($longKey);
            if ($value !== null) {
                Cache::put($shortKey, $value, 3600);
                Cache::forget($longKey);
            }
        }
    }

    /**
     * Group related cache entries for better locality
     */
    protected function groupRelatedCacheEntries(): void
    {
        // Group widget-related cache entries together
        $widgetTypes = WidgetRegistry::getTypes();

        foreach ($widgetTypes as $type) {
            $groupKey = "widget_group_{$type}";
            $relatedData = [
                'config' => Cache::get("widget_config_{$type}"),
                'metadata' => Cache::get("widget_metadata_{$type}"),
                'variants' => Cache::get("widget_variants_{$type}"),
            ];

            Cache::put($groupKey, $relatedData, 7200);
        }
    }

    /**
     * Clean up stale cache entries
     */
    protected function cleanupStaleCache(): void
    {
        // Remove cache entries for non-existent widgets
        $existingTypes = WidgetRegistry::getTypes();
        $cacheKeys = $this->getWidgetCacheKeys();

        foreach ($cacheKeys as $key) {
            if (preg_match('/widget_(\w+)_(.+)/', $key, $matches)) {
                $type = $matches[1];
                if (! in_array($type, $existingTypes)) {
                    Cache::forget($key);
                }
            }
        }
    }

    /**
     * Get all widget-related cache keys
     */
    protected function getWidgetCacheKeys(): array
    {
        // This is a simplified implementation
        // In production, you might need a more sophisticated approach
        return Cache::get('widget_cache_keys', []);
    }

    /**
     * Monitor widget rendering performance
     */
    public function startPerformanceMonitoring(string $widgetType): void
    {
        if (! $this->monitoringEnabled) {
            return;
        }

        $this->performanceMetrics[$widgetType] = [
            'start_time' => microtime(true),
            'start_memory' => memory_get_usage(true),
        ];
    }

    /**
     * End performance monitoring and log results
     */
    public function endPerformanceMonitoring(string $widgetType): array
    {
        if (! $this->monitoringEnabled || ! isset($this->performanceMetrics[$widgetType])) {
            return [];
        }

        $metrics = $this->performanceMetrics[$widgetType];
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $results = [
            'widget_type' => $widgetType,
            'execution_time' => round(($endTime - $metrics['start_time']) * 1000, 2), // ms
            'memory_usage' => $endMemory - $metrics['start_memory'], // bytes
            'peak_memory' => memory_get_peak_usage(true),
            'timestamp' => now()->toISOString(),
        ];

        // Store performance data
        $this->storePerformanceData($results);

        // Clean up
        unset($this->performanceMetrics[$widgetType]);

        return $results;
    }

    /**
     * Store performance data for analysis
     */
    protected function storePerformanceData(array $data): void
    {
        $cacheKey = 'widget_performance_data';
        $performanceData = Cache::get($cacheKey, []);

        $performanceData[] = $data;

        // Keep only last 1000 entries
        if (count($performanceData) > 1000) {
            $performanceData = array_slice($performanceData, -1000);
        }

        Cache::put($cacheKey, $performanceData, 86400); // 24 hours
    }

    /**
     * Get performance statistics
     */
    public function getPerformanceStatistics(): array
    {
        $performanceData = Cache::get('widget_performance_data', []);

        if (empty($performanceData)) {
            return [
                'total_measurements' => 0,
                'average_execution_time' => 0,
                'average_memory_usage' => 0,
                'slowest_widgets' => [],
                'memory_intensive_widgets' => [],
            ];
        }

        $totalTime = 0;
        $totalMemory = 0;
        $widgetStats = [];

        foreach ($performanceData as $data) {
            $type = $data['widget_type'];
            $time = $data['execution_time'];
            $memory = $data['memory_usage'];

            $totalTime += $time;
            $totalMemory += $memory;

            if (! isset($widgetStats[$type])) {
                $widgetStats[$type] = [
                    'count' => 0,
                    'total_time' => 0,
                    'total_memory' => 0,
                    'max_time' => 0,
                    'max_memory' => 0,
                ];
            }

            $widgetStats[$type]['count']++;
            $widgetStats[$type]['total_time'] += $time;
            $widgetStats[$type]['total_memory'] += $memory;
            $widgetStats[$type]['max_time'] = max($widgetStats[$type]['max_time'], $time);
            $widgetStats[$type]['max_memory'] = max($widgetStats[$type]['max_memory'], $memory);
        }

        // Calculate averages and sort
        foreach ($widgetStats as $type => &$stats) {
            $stats['avg_time'] = $stats['total_time'] / $stats['count'];
            $stats['avg_memory'] = $stats['total_memory'] / $stats['count'];
        }

        $slowestWidgets = $widgetStats;
        uasort($slowestWidgets, fn ($a, $b) => $b['avg_time'] <=> $a['avg_time']);

        $memoryIntensiveWidgets = $widgetStats;
        uasort($memoryIntensiveWidgets, fn ($a, $b) => $b['avg_memory'] <=> $a['avg_memory']);

        return [
            'total_measurements' => count($performanceData),
            'average_execution_time' => round($totalTime / count($performanceData), 2),
            'average_memory_usage' => round($totalMemory / count($performanceData)),
            'slowest_widgets' => array_slice($slowestWidgets, 0, 10, true),
            'memory_intensive_widgets' => array_slice($memoryIntensiveWidgets, 0, 10, true),
            'widget_stats' => $widgetStats,
        ];
    }

    /**
     * Optimize metadata loading
     */
    public function optimizeMetadataLoading(): array
    {
        $results = [
            'cached_metadata' => 0,
            'optimized_files' => 0,
        ];

        $widgets = WidgetRegistry::all();

        foreach ($widgets as $widget) {
            try {
                $type = $widget['type'];
                $metadata = $widget['metadata'];

                // Cache metadata with optimized structure
                $optimizedMetadata = $this->optimizeMetadataStructure($metadata);
                $cacheKey = "optimized_metadata_{$type}";
                Cache::put($cacheKey, $optimizedMetadata, 7200);

                $results['cached_metadata']++;

                // Precompile field validation rules
                $this->precompileValidationRules($type, $metadata);
                $results['optimized_files']++;

            } catch (\Exception $e) {
                Log::warning("Failed to optimize metadata for {$widget['type']}: ".$e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Optimize metadata structure for faster access
     */
    protected function optimizeMetadataStructure(array $metadata): array
    {
        // Create indexed structures for faster lookups
        $optimized = $metadata;

        // Index fields by name
        if (isset($metadata['fields'])) {
            $optimized['fields_by_name'] = [];
            foreach ($metadata['fields'] as $field) {
                $optimized['fields_by_name'][$field['name']] = $field;
            }
        }

        // Index variants
        if (isset($metadata['variants'])) {
            $optimized['variant_keys'] = array_keys($metadata['variants']);
        }

        return $optimized;
    }

    /**
     * Precompile validation rules for faster validation
     */
    protected function precompileValidationRules(string $type, array $metadata): void
    {
        $fields = $metadata['fields'] ?? [];
        $compiledRules = [];

        foreach ($fields as $field) {
            $fieldName = $field['name'];
            $rules = [];

            if ($field['required'] ?? false) {
                $rules[] = 'required';
            }

            if (isset($field['validation'])) {
                $rules[] = $field['validation'];
            }

            $compiledRules[$fieldName] = implode('|', $rules);
        }

        Cache::put("validation_rules_{$type}", $compiledRules, 7200);
    }

    /**
     * Create cache invalidation mechanisms
     */
    public function setupCacheInvalidation(): void
    {
        // Set up cache tags for better invalidation
        $this->setupCacheTags();

        // Set up automatic cache warming
        $this->setupCacheWarming();
    }

    /**
     * Setup cache tags for organized invalidation
     */
    protected function setupCacheTags(): void
    {
        $widgetTypes = WidgetRegistry::getTypes();

        foreach ($widgetTypes as $type) {
            $tags = ["widget:{$type}", 'widgets:all'];

            // Tag all related cache entries
            $relatedKeys = [
                "widget_config_{$type}",
                "widget_metadata_{$type}",
                "optimized_metadata_{$type}",
                "validation_rules_{$type}",
            ];

            foreach ($relatedKeys as $key) {
                Cache::tags($tags)->put($key, Cache::get($key), 7200);
            }
        }
    }

    /**
     * Setup automatic cache warming
     */
    protected function setupCacheWarming(): void
    {
        // Warm cache for most used widgets
        $frequentWidgets = $this->getFrequentlyUsedWidgets(10);

        foreach ($frequentWidgets as $widget) {
            $this->warmWidgetCache($widget['type']);
        }
    }

    /**
     * Warm cache for specific widget type
     */
    protected function warmWidgetCache(string $type): void
    {
        try {
            // Warm metadata cache
            WidgetRegistry::getConfig($type);

            // Warm class cache
            $widgetClass = WidgetRegistry::get($type);
            if ($widgetClass) {
                class_exists($widgetClass);
            }

        } catch (\Exception $e) {
            Log::warning("Failed to warm cache for widget {$type}: ".$e->getMessage());
        }
    }

    /**
     * Get cache size estimation
     */
    protected function getCacheSize(): int
    {
        // This is a simplified estimation
        // In production, you might need more sophisticated cache size calculation
        return strlen(serialize(Cache::get('widget_cache_keys', [])));
    }

    /**
     * Clear performance data
     */
    public function clearPerformanceData(): void
    {
        Cache::forget('widget_performance_data');
        $this->performanceMetrics = [];
    }
}
