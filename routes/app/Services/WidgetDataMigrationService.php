<?php

namespace App\Services;

use App\Models\Widget;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WidgetDataMigrationService
{
    /**
     * Migrate legacy widgets to new format
     */
    public function migrateLegacyWidgets(): array
    {
        $results = [
            'migrated' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $legacyWidgets = Widget::whereNull('metadata')
            ->orWhere('variant', '')
            ->orWhereNull('variant')
            ->get();

        foreach ($legacyWidgets as $widget) {
            try {
                $this->migrateSingleWidget($widget);
                $results['migrated']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Widget ID {$widget->id}: ".$e->getMessage();
                Log::error("Widget migration failed for ID {$widget->id}: ".$e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Migrate a single widget
     */
    protected function migrateSingleWidget(Widget $widget): void
    {
        // Get widget metadata from registry
        $metadata = WidgetRegistry::getConfig($widget->type);

        if (! $metadata) {
            throw new \Exception("Widget type '{$widget->type}' not found in registry");
        }

        // Set default variant if not set
        if (empty($widget->variant)) {
            $widget->variant = 'default';
        }

        // Store metadata
        $widget->metadata = $metadata;

        // Validate and clean settings
        $widget->settings = $this->validateAndCleanSettings($widget->settings ?? [], $metadata);

        $widget->save();
    }

    /**
     * Validate and clean widget settings
     */
    protected function validateAndCleanSettings(array $settings, array $metadata): array
    {
        $fields = $metadata['fields'] ?? [];
        $cleanedSettings = [];

        foreach ($fields as $field) {
            $fieldName = $field['name'];
            $fieldType = $field['type'];

            if (isset($settings[$fieldName])) {
                $cleanedSettings[$fieldName] = $this->cleanFieldValue($settings[$fieldName], $fieldType);
            } elseif (isset($field['default'])) {
                $cleanedSettings[$fieldName] = $field['default'];
            }
        }

        return $cleanedSettings;
    }

    /**
     * Clean field value based on type
     */
    protected function cleanFieldValue($value, string $type)
    {
        switch ($type) {
            case 'text':
            case 'textarea':
            case 'url':
            case 'email':
                return is_string($value) ? trim($value) : '';

            case 'number':
            case 'range':
                return is_numeric($value) ? (float) $value : 0;

            case 'checkbox':
                return (bool) $value;

            case 'select':
                return is_string($value) ? $value : '';

            case 'image':
                return is_string($value) ? $value : '';

            case 'gallery':
                return is_array($value) ? $value : [];

            case 'repeatable':
                return is_array($value) ? $value : [];

            case 'date':
                if (is_string($value) && ! empty($value)) {
                    try {
                        return date('Y-m-d', strtotime($value));
                    } catch (\Exception $e) {
                        return '';
                    }
                }

                return '';

            case 'color':
                return is_string($value) && preg_match('/^#[0-9A-Fa-f]{6}$/', $value) ? $value : '#000000';

            default:
                return $value;
        }
    }

    /**
     * Backup widgets before migration
     */
    public function createBackup(?string $filename = null): string
    {
        if (! $filename) {
            $filename = 'widgets_backup_'.date('Y_m_d_H_i_s').'.json';
        }

        $widgets = Widget::all()->toArray();

        $backupData = [
            'created_at' => now()->toISOString(),
            'total_widgets' => count($widgets),
            'widgets' => $widgets,
        ];

        $backupPath = storage_path('app/widget-backups');
        if (! file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $fullPath = $backupPath.'/'.$filename;
        file_put_contents($fullPath, json_encode($backupData, JSON_PRETTY_PRINT));

        return $fullPath;
    }

    /**
     * Restore widgets from backup
     */
    public function restoreFromBackup(string $backupPath): array
    {
        if (! file_exists($backupPath)) {
            throw new \Exception("Backup file not found: {$backupPath}");
        }

        $backupData = json_decode(file_get_contents($backupPath), true);

        if (! $backupData || ! isset($backupData['widgets'])) {
            throw new \Exception('Invalid backup file format');
        }

        $results = [
            'restored' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        DB::beginTransaction();

        try {
            // Clear existing widgets
            Widget::truncate();

            // Restore widgets
            foreach ($backupData['widgets'] as $widgetData) {
                try {
                    Widget::create($widgetData);
                    $results['restored']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = 'Widget restore failed: '.$e->getMessage();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return $results;
    }

    /**
     * Validate all widgets in database
     */
    public function validateAllWidgets(): array
    {
        $results = [
            'valid' => 0,
            'invalid' => 0,
            'errors' => [],
        ];

        $widgets = Widget::all();

        foreach ($widgets as $widget) {
            try {
                if ($widget->validateSettings()) {
                    $results['valid']++;
                } else {
                    $results['invalid']++;
                    $results['errors'][] = "Widget ID {$widget->id} ({$widget->type}): Settings validation failed";
                }
            } catch (\Exception $e) {
                $results['invalid']++;
                $results['errors'][] = "Widget ID {$widget->id} ({$widget->type}): ".$e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Clean orphaned widgets (widgets with non-existent types)
     */
    public function cleanOrphanedWidgets(): array
    {
        $results = [
            'cleaned' => 0,
            'orphaned_types' => [],
        ];

        $widgets = Widget::all();
        $orphanedIds = [];

        foreach ($widgets as $widget) {
            if (! WidgetRegistry::exists($widget->type)) {
                $orphanedIds[] = $widget->id;
                $results['orphaned_types'][] = $widget->type;
            }
        }

        if (! empty($orphanedIds)) {
            $results['cleaned'] = Widget::whereIn('id', $orphanedIds)->delete();
        }

        $results['orphaned_types'] = array_unique($results['orphaned_types']);

        return $results;
    }

    /**
     * Update widget metadata for all widgets
     */
    public function updateAllWidgetMetadata(): array
    {
        $results = [
            'updated' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $widgets = Widget::all();

        foreach ($widgets as $widget) {
            try {
                $metadata = WidgetRegistry::getConfig($widget->type);

                if ($metadata) {
                    $widget->metadata = $metadata;
                    $widget->save();
                    $results['updated']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Widget ID {$widget->id}: No metadata found for type '{$widget->type}'";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Widget ID {$widget->id}: ".$e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Get migration statistics
     */
    public function getMigrationStats(): array
    {
        return [
            'total_widgets' => Widget::count(),
            'widgets_with_metadata' => Widget::whereNotNull('metadata')->count(),
            'widgets_without_metadata' => Widget::whereNull('metadata')->count(),
            'widgets_with_variant' => Widget::where('variant', '!=', '')->whereNotNull('variant')->count(),
            'widgets_without_variant' => Widget::where('variant', '')->orWhereNull('variant')->count(),
            'widget_types' => Widget::distinct('type')->pluck('type')->toArray(),
            'widget_areas' => Widget::distinct('area')->pluck('area')->toArray(),
        ];
    }
}
