<?php

namespace App\Services;

use App\Models\Widget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class WidgetImportExportService
{
    /**
     * Export widgets configuration.
     *
     * @param  array  $options  Accepts 'project_id' to scope export to a specific project.
     */
    public function exportWidgets(array $options = []): array
    {
        $includeAreas = $options['areas'] ?? null;
        $includeTypes = $options['types'] ?? null;
        $includeMetadata = $options['include_metadata'] ?? true;
        $projectId = $options['project_id'] ?? null;

        $query = Widget::query()->orderBy('area')->orderBy('sort_order');

        if ($projectId) {
            $query->forProject($projectId);
        }

        if ($includeAreas) {
            $query->whereIn('area', $includeAreas);
        }

        if ($includeTypes) {
            $query->whereIn('type', $includeTypes);
        }

        $widgets = $query->get();

        $exportData = [
            'version' => '1.0',
            'exported_at' => now()->toISOString(),
            'exported_by' => auth()->user()->username ?? 'system',
            'project_id' => $projectId,
            'widget_count' => $widgets->count(),
            'widgets' => [],
        ];

        foreach ($widgets as $widget) {
            $widgetData = [
                'project_id' => $widget->project_id,
                'name' => $widget->name,
                'type' => $widget->type,
                'area' => $widget->area,
                'settings' => $widget->settings,
                'variant' => $widget->variant ?? 'default',
                'sort_order' => $widget->sort_order,
                'is_active' => $widget->is_active,
            ];

            if ($includeMetadata) {
                $widgetData['metadata'] = $widget->getWidgetMetadata();
            }

            $exportData['widgets'][] = $widgetData;
        }

        return $exportData;
    }

    /**
     * Export widgets to JSON file
     */
    public function exportToFile(?string $filename = null, array $options = []): string
    {
        $filename = $filename ?? 'widgets_export_'.date('Y-m-d_H-i-s').'.json';
        $exportData = $this->exportWidgets($options);

        $exportPath = storage_path('app/exports');
        if (! File::isDirectory($exportPath)) {
            File::makeDirectory($exportPath, 0755, true);
        }

        $filePath = $exportPath.'/'.$filename;
        File::put($filePath, json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $filePath;
    }

    /**
     * Import widgets from configuration array
     */
    public function importWidgets(array $importData, array $options = []): array
    {
        $overwriteExisting = $options['overwrite_existing'] ?? false;
        $validateOnly = $options['validate_only'] ?? false;
        $targetAreas = $options['target_areas'] ?? null;

        $results = [
            'success' => true,
            'imported' => 0,
            'skipped' => 0,
            'errors' => [],
            'warnings' => [],
        ];

        // Validate import data structure
        $validation = $this->validateImportData($importData);
        if (! $validation['valid']) {
            $results['success'] = false;
            $results['errors'] = $validation['errors'];

            return $results;
        }

        $widgets = $importData['widgets'] ?? [];

        foreach ($widgets as $index => $widgetData) {
            try {
                $importResult = $this->importSingleWidget($widgetData, [
                    'overwrite_existing' => $overwriteExisting,
                    'validate_only' => $validateOnly,
                    'target_areas' => $targetAreas,
                    'index' => $index,
                ]);

                if ($importResult['success']) {
                    $results['imported']++;
                } else {
                    $results['skipped']++;
                    if (! empty($importResult['error'])) {
                        $results['errors'][] = "Widget {$index}: ".$importResult['error'];
                    }
                }

                if (! empty($importResult['warnings'])) {
                    $results['warnings'] = array_merge($results['warnings'], $importResult['warnings']);
                }

            } catch (\Exception $e) {
                $results['errors'][] = "Widget {$index}: ".$e->getMessage();
                $results['skipped']++;
            }
        }

        if (! empty($results['errors'])) {
            $results['success'] = false;
        }

        return $results;
    }

    /**
     * Import widgets from JSON file
     */
    public function importFromFile(string $filePath, array $options = []): array
    {
        if (! File::exists($filePath)) {
            return [
                'success' => false,
                'errors' => ['Import file not found: '.$filePath],
            ];
        }

        try {
            $content = File::get($filePath);
            $importData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success' => false,
                    'errors' => ['Invalid JSON file: '.json_last_error_msg()],
                ];
            }

            return $this->importWidgets($importData, $options);

        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => ['Error reading import file: '.$e->getMessage()],
            ];
        }
    }

    /**
     * Validate import data structure
     */
    protected function validateImportData(array $data): array
    {
        $validator = Validator::make($data, [
            'version' => 'required|string',
            'widgets' => 'required|array',
            'widgets.*.name' => 'required|string',
            'widgets.*.type' => 'required|string',
            'widgets.*.area' => 'required|string',
            'widgets.*.settings' => 'nullable|array',
            'widgets.*.variant' => 'nullable|string',
            'widgets.*.sort_order' => 'nullable|integer',
            'widgets.*.is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->all(),
            ];
        }

        // Additional validation
        $errors = [];
        $widgets = $data['widgets'] ?? [];

        foreach ($widgets as $index => $widget) {
            // Check if widget type exists
            if (! WidgetRegistry::exists($widget['type'])) {
                $errors[] = "Widget {$index}: Unknown widget type '{$widget['type']}'";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Import single widget
     */
    protected function importSingleWidget(array $widgetData, array $options = []): array
    {
        $overwriteExisting = $options['overwrite_existing'] ?? false;
        $validateOnly = $options['validate_only'] ?? false;
        $targetAreas = $options['target_areas'] ?? null;
        $index = $options['index'] ?? 0;

        $result = [
            'success' => false,
            'error' => null,
            'warnings' => [],
        ];

        // Map target area if specified
        if ($targetAreas && isset($targetAreas[$widgetData['area']])) {
            $widgetData['area'] = $targetAreas[$widgetData['area']];
        }

        // Check if widget type exists
        if (! WidgetRegistry::exists($widgetData['type'])) {
            $result['error'] = "Widget type '{$widgetData['type']}' not found";

            return $result;
        }

        // Validate widget settings
        try {
            $widgetClass = WidgetRegistry::get($widgetData['type']);
            if ($widgetClass) {
                $tempWidget = new $widgetClass(
                    $widgetData['settings'] ?? [],
                    $widgetData['variant'] ?? 'default'
                );
                $tempWidget->validateSettings();
            }
        } catch (\Exception $e) {
            $result['error'] = 'Settings validation failed: '.$e->getMessage();

            return $result;
        }

        if ($validateOnly) {
            $result['success'] = true;

            return $result;
        }

        // Check for existing widget
        $existingWidget = Widget::where('area', $widgetData['area'])
            ->where('type', $widgetData['type'])
            ->where('name', $widgetData['name'])
            ->first();

        if ($existingWidget && ! $overwriteExisting) {
            $result['error'] = 'Widget already exists and overwrite is disabled';

            return $result;
        }

        // Prepare widget data for creation/update
        $createData = [
            'name' => $widgetData['name'],
            'type' => $widgetData['type'],
            'area' => $widgetData['area'],
            'settings' => $widgetData['settings'] ?? [],
            'variant' => $widgetData['variant'] ?? 'default',
            'sort_order' => $widgetData['sort_order'] ?? 0,
            'is_active' => $widgetData['is_active'] ?? true,
        ];

        // Prefer explicit project_id from the import data, then fall back to session context.
        if (! empty($widgetData['project_id'])) {
            $createData['project_id'] = $widgetData['project_id'];
            $createData['tenant_id'] = $widgetData['project_id']; // keep legacy field in sync
        } elseif (session('current_project_id')) {
            $createData['project_id'] = session('current_project_id');
            $createData['tenant_id'] = session('current_project_id');
        } elseif (session('current_tenant_id')) {
            $createData['tenant_id'] = session('current_tenant_id');
        }

        try {
            if ($existingWidget) {
                $existingWidget->update($createData);
                $result['warnings'][] = "Updated existing widget: {$widgetData['name']}";
            } else {
                Widget::create($createData);
            }

            // Clear cache for the area
            clear_widget_cache($widgetData['area']);

            $result['success'] = true;

        } catch (\Exception $e) {
            $result['error'] = 'Database error: '.$e->getMessage();
        }

        return $result;
    }

    /**
     * Export widget templates (metadata only)
     */
    public function exportTemplates(array $widgetTypes = []): array
    {
        $allWidgets = WidgetRegistry::all();

        if (! empty($widgetTypes)) {
            $allWidgets = array_filter($allWidgets, function ($widget) use ($widgetTypes) {
                return in_array($widget['type'], $widgetTypes);
            });
        }

        $templates = [
            'version' => '1.0',
            'type' => 'templates',
            'exported_at' => now()->toISOString(),
            'template_count' => count($allWidgets),
            'templates' => [],
        ];

        foreach ($allWidgets as $widget) {
            $templates['templates'][] = [
                'type' => $widget['type'],
                'metadata' => $widget['metadata'],
                'class' => $widget['class'],
            ];
        }

        return $templates;
    }

    /**
     * Create widget configuration backup
     */
    public function createBackup(?string $backupName = null): string
    {
        $backupName = $backupName ?? 'widget_backup_'.date('Y-m-d_H-i-s');

        $backup = [
            'backup_name' => $backupName,
            'created_at' => now()->toISOString(),
            'created_by' => auth()->user()->username ?? 'system',
            'widgets' => $this->exportWidgets(['include_metadata' => true]),
            'registry_info' => [
                'total_types' => count(WidgetRegistry::getTypes()),
                'categories' => array_keys(WidgetRegistry::getByCategory()),
            ],
        ];

        $backupPath = storage_path('app/backups');
        if (! File::isDirectory($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $filePath = $backupPath.'/'.$backupName.'.json';
        File::put($filePath, json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $filePath;
    }

    /**
     * Restore from backup
     */
    public function restoreBackup(string $backupPath, array $options = []): array
    {
        $clearExisting = $options['clear_existing'] ?? false;

        if (! File::exists($backupPath)) {
            return [
                'success' => false,
                'errors' => ['Backup file not found'],
            ];
        }

        try {
            $backup = json_decode(File::get($backupPath), true);

            if ($clearExisting) {
                Widget::truncate();
                clear_widget_cache();
            }

            return $this->importWidgets($backup['widgets'], [
                'overwrite_existing' => true,
            ]);

        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => ['Restore failed: '.$e->getMessage()],
            ];
        }
    }

    /**
     * Get available backups
     */
    public function getAvailableBackups(): array
    {
        $backupPath = storage_path('app/backups');

        if (! File::isDirectory($backupPath)) {
            return [];
        }

        $backups = [];
        $files = File::files($backupPath);

        foreach ($files as $file) {
            if ($file->getExtension() === 'json') {
                $backups[] = [
                    'name' => $file->getFilenameWithoutExtension(),
                    'path' => $file->getPathname(),
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                ];
            }
        }

        // Sort by modification time (newest first)
        usort($backups, function ($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $backups;
    }
}
