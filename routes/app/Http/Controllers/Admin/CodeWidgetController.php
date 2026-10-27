<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\File;

class CodeWidgetController extends Controller
{
    /**
     * Export a single code-based widget as JSON
     */
    public function export(string $type)
    {
        $widgetClass = WidgetRegistry::get($type);

        if (! $widgetClass) {
            return back()->with('error', "Widget type '{$type}' không tồn tại");
        }

        $config = WidgetRegistry::getConfig($type);

        if (! $config) {
            return back()->with('error', "Không thể load config cho widget '{$type}'");
        }

        // Get view content if exists
        $viewContent = '';
        $cssContent = '';
        $jsContent = '';

        $reflection = new \ReflectionClass($widgetClass);
        $classDir = dirname($reflection->getFileName());
        $phpContent = File::get($reflection->getFileName());

        // Try to find view path from render method
        if (preg_match("/view\(['\"]([^'\"]+)['\"]/", $phpContent, $matches)) {
            $viewName = $matches[1];
            $viewPath = resource_path('views/'.str_replace('.', '/', $viewName).'.blade.php');
            if (File::exists($viewPath)) {
                $viewContent = File::get($viewPath);
            }
        }

        // Check for CSS/JS files
        $viewDir = isset($viewPath) ? dirname($viewPath) : $classDir;
        if (File::exists("{$viewDir}/style.css")) {
            $cssContent = File::get("{$viewDir}/style.css");
        }
        if (File::exists("{$viewDir}/script.js")) {
            $jsContent = File::get("{$viewDir}/script.js");
        }

        $exportData = [
            'type' => $type,
            'name' => $config['name'] ?? $type,
            'description' => $config['description'] ?? '',
            'category' => $config['category'] ?? 'general',
            'version' => $config['version'] ?? '1.0.0',
            'icon' => $config['icon'] ?? 'cube',
            'fields' => $config['fields'] ?? [],
            'is_code_based' => true,
            'class' => $widgetClass,
            'view_template' => $viewContent,
            'css' => $cssContent,
            'js' => $jsContent,
            'exported_at' => now()->toIso8601String(),
        ];

        $filename = "widget_{$type}_".date('Y-m-d_His').'.json';

        return response()->json($exportData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Export all code-based widgets as JSON
     */
    public function exportAll()
    {
        $allWidgets = WidgetRegistry::all();

        // Filter only code-based widgets
        $codeWidgets = collect($allWidgets)->filter(function ($widget) {
            return ! ($widget['metadata']['is_custom'] ?? false) && ! empty($widget['class']);
        });

        $exportData = [
            'widgets' => [],
            'exported_at' => now()->toIso8601String(),
            'total' => $codeWidgets->count(),
        ];

        foreach ($codeWidgets as $widget) {
            $type = $widget['type'];
            $widgetClass = $widget['class'];
            $config = $widget['metadata'];

            // Get view content
            $viewContent = '';
            $cssContent = '';
            $jsContent = '';

            try {
                $reflection = new \ReflectionClass($widgetClass);
                $classDir = dirname($reflection->getFileName());
                $phpContent = File::get($reflection->getFileName());

                if (preg_match("/view\(['\"]([^'\"]+)['\"]/", $phpContent, $matches)) {
                    $viewName = $matches[1];
                    $viewPath = resource_path('views/'.str_replace('.', '/', $viewName).'.blade.php');
                    if (File::exists($viewPath)) {
                        $viewContent = File::get($viewPath);
                    }
                }

                $viewDir = isset($viewPath) ? dirname($viewPath) : $classDir;
                if (File::exists("{$viewDir}/style.css")) {
                    $cssContent = File::get("{$viewDir}/style.css");
                }
                if (File::exists("{$viewDir}/script.js")) {
                    $jsContent = File::get("{$viewDir}/script.js");
                }
            } catch (\Exception $e) {
                // Skip if error
            }

            $exportData['widgets'][] = [
                'type' => $type,
                'name' => $config['name'] ?? $type,
                'description' => $config['description'] ?? '',
                'category' => $config['category'] ?? 'general',
                'version' => $config['version'] ?? '1.0.0',
                'icon' => $config['icon'] ?? 'cube',
                'fields' => $config['fields'] ?? [],
                'is_code_based' => true,
                'class' => $widgetClass,
                'view_template' => $viewContent,
                'css' => $cssContent,
                'js' => $jsContent,
            ];
        }

        $filename = 'code_widgets_all_'.date('Y-m-d_His').'.json';

        return response()->json($exportData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
