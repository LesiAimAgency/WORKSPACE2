<?php

namespace App\Services;

use App\Widgets\BaseWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class WidgetDiscoveryService
{
    protected string $widgetsPath;

    protected array $discoveredWidgets = [];

    public function __construct()
    {
        $this->widgetsPath = app_path('Widgets');
    }

    /**
     * Discover all widgets in the widgets directory
     */
    public function discoverWidgets(): array
    {
        $cacheKey = 'widget_discovery_'.md5($this->widgetsPath.$this->getDirectoryHash());

        return Cache::remember($cacheKey, 3600, function () {
            return $this->performDiscovery();
        });
    }

    /**
     * Perform the actual widget discovery
     */
    protected function performDiscovery(): array
    {
        $discovered = [];

        if (! File::isDirectory($this->widgetsPath)) {
            return $discovered;
        }

        $this->scanDirectory($this->widgetsPath, $discovered);

        return $discovered;
    }

    /**
     * Recursively scan directory for widgets
     */
    protected function scanDirectory(string $directory, array &$discovered, string $namespace = 'App\\Widgets'): void
    {
        $items = File::glob($directory.'/*');

        foreach ($items as $item) {
            if (File::isDirectory($item)) {
                $dirName = basename($item);

                // Skip certain directories
                if (in_array($dirName, ['.', '..', 'tests', 'assets'])) {
                    continue;
                }

                $this->scanDirectory($item, $discovered, $namespace.'\\'.$dirName);
            } elseif (File::isFile($item) && Str::endsWith($item, 'Widget.php')) {
                $this->processWidgetFile($item, $discovered, $namespace);
            }
        }
    }

    /**
     * Process a widget file
     */
    protected function processWidgetFile(string $filePath, array &$discovered, string $namespace): void
    {
        $fileName = basename($filePath, '.php');
        $className = $namespace.'\\'.$fileName;

        // Skip base classes
        if (in_array($fileName, ['BaseWidget', 'HTMLWidget'])) {
            return;
        }

        // Check if class exists and extends BaseWidget
        if (! class_exists($className) || ! is_subclass_of($className, BaseWidget::class)) {
            return;
        }

        try {
            $widgetType = $this->generateWidgetType($className);
            $metadata = $this->loadWidgetMetadata($className);

            $discovered[$widgetType] = [
                'type' => $widgetType,
                'class' => $className,
                'file' => $filePath,
                'namespace' => $namespace,
                'metadata' => $metadata,
                'category' => $metadata['category'] ?? $this->extractCategoryFromNamespace($namespace),
            ];

        } catch (\Exception $e) {
            \Log::warning("Failed to process widget {$className}: ".$e->getMessage());
        }
    }

    /**
     * Generate widget type from class name
     */
    protected function generateWidgetType(string $className): string
    {
        // Extract widget name from class name
        $parts = explode('\\', $className);
        $widgetName = end($parts);

        // Remove 'Widget' suffix
        $widgetName = Str::replaceLast('Widget', '', $widgetName);

        // Convert to snake_case
        return Str::snake($widgetName);
    }

    /**
     * Extract category from namespace
     */
    protected function extractCategoryFromNamespace(string $namespace): string
    {
        $parts = explode('\\', $namespace);

        // Remove 'App\Widgets' prefix
        $categoryParts = array_slice($parts, 2);

        if (empty($categoryParts)) {
            return 'general';
        }

        return Str::snake(implode('_', $categoryParts));
    }

    /**
     * Load widget metadata
     */
    protected function loadWidgetMetadata(string $className): array
    {
        // Try to load from JSON file first
        $metadataPath = $className::getMetadataPath();

        if (File::exists($metadataPath)) {
            $content = File::get($metadataPath);
            $metadata = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON in widget metadata: '.json_last_error_msg());
            }

            return $this->validateMetadata($metadata);
        }

        // Fallback to getConfig method
        if (method_exists($className, 'getConfig')) {
            return $className::getConfig();
        }

        throw new \RuntimeException("Widget metadata not found for {$className}");
    }

    /**
     * Validate widget metadata
     */
    protected function validateMetadata(array $metadata): array
    {
        $required = ['name', 'description', 'category', 'fields'];

        foreach ($required as $field) {
            if (! isset($metadata[$field])) {
                throw new \InvalidArgumentException("Missing required metadata field: {$field}");
            }
        }

        return $metadata;
    }

    /**
     * Get directory hash for cache invalidation
     */
    protected function getDirectoryHash(): string
    {
        if (! File::isDirectory($this->widgetsPath)) {
            return '';
        }

        $files = File::allFiles($this->widgetsPath);
        $hash = '';

        foreach ($files as $file) {
            if (Str::endsWith($file->getFilename(), ['Widget.php', 'widget.json'])) {
                $hash .= $file->getPathname().$file->getMTime();
            }
        }

        return md5($hash);
    }

    /**
     * Clear discovery cache
     */
    public function clearCache(): void
    {
        $cacheKey = 'widget_discovery_'.md5($this->widgetsPath.$this->getDirectoryHash());
        Cache::forget($cacheKey);
    }

    /**
     * Validate widget naming conventions
     */
    public function validateNamingConventions(array $widgets): array
    {
        $violations = [];

        foreach ($widgets as $widget) {
            $className = $widget['class'];
            $expectedPattern = '/^App\\\\Widgets\\\\[A-Z][a-zA-Z]*\\\\[A-Z][a-zA-Z]*Widget$/';

            if (! preg_match($expectedPattern, $className)) {
                $violations[] = [
                    'class' => $className,
                    'issue' => 'Class name does not follow naming convention',
                    'expected' => 'App\\Widgets\\Category\\NameWidget',
                ];
            }
        }

        return $violations;
    }

    /**
     * Check for namespace conflicts
     */
    public function checkNamespaceConflicts(array $widgets): array
    {
        $conflicts = [];
        $types = [];

        foreach ($widgets as $widget) {
            $type = $widget['type'];

            if (isset($types[$type])) {
                $conflicts[] = [
                    'type' => $type,
                    'classes' => [$types[$type], $widget['class']],
                ];
            } else {
                $types[$type] = $widget['class'];
            }
        }

        return $conflicts;
    }
}
