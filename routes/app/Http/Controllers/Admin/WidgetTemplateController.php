<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\WidgetTemplate;
use App\Widgets\WidgetRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WidgetTemplateController extends Controller
{
    public function index(Request $request, ?string $projectCode = null)
    {
        $currentProject = null;
        if ($projectCode) {
            $currentProject = Project::where('code', $projectCode)->first();
        }

        // Get both code-based widgets and database templates
        $codeWidgets = WidgetRegistry::getByCategory();

        // Database templates are filtered by tenant_id via global scope
        $dbTemplates = WidgetTemplate::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category')
            ->toArray();

        return view('cms.widget-templates.index', compact('codeWidgets', 'dbTemplates', 'currentProject'));
    }

    public function show($type)
    {
        $widget = WidgetRegistry::getConfig($type);
        if (! $widget) {
            // Try database template
            $widget = WidgetTemplate::where('type', $type)->first();
            if (! $widget) {
                abort(404);
            }
        }

        return view('cms.widget-templates.show', compact('widget'));
    }

    public function preview($projectCode, $type, Request $request)
    {
        $settings = $request->all();
        $html = WidgetRegistry::render($type, $settings);

        return response()->json(['html' => $html]);
    }

    public function destroy(Request $request, ?string $projectCode = null, $id = null)
    {
        // Handle both /admin/widget-templates/{id} and /{projectCode}/admin/widget-templates/{id}
        if ($id === null) {
            $id = $projectCode;
            $projectCode = null;
        }

        $template = WidgetTemplate::findOrFail($id);

        // Delete all associated files
        $this->deleteWidgetFiles($template->type);

        $template->delete();

        // Clear widget cache
        WidgetRegistry::clearCache();

        if ($projectCode) {
            return redirect()->route('project.admin.widget-templates.index', $projectCode)
                ->with('success', 'Widget template và tất cả file liên quan đã được xóa!');
        }

        return redirect()->route('cms.widget-templates.index')
            ->with('success', 'Widget template và tất cả file liên quan đã được xóa!');
    }

    /**
     * Delete all widget files (entire widget folder)
     */
    protected function deleteWidgetFiles(string $type): void
    {
        // Delete widget folder (resources/views/widgets/custom/{type}/)
        $widgetDir = resource_path("views/widgets/custom/{$type}");
        if (\File::isDirectory($widgetDir)) {
            \File::deleteDirectory($widgetDir);
        }

        // Cleanup legacy files if exist
        // Old blade file
        $oldBladePath = resource_path("views/widgets/custom/{$type}.blade.php");
        if (\File::exists($oldBladePath)) {
            \File::delete($oldBladePath);
        }

        // Old CSS file
        $oldCssPath = public_path("css/widgets/{$type}.css");
        if (\File::exists($oldCssPath)) {
            \File::delete($oldCssPath);
        }

        // Old JS file
        $oldJsPath = public_path("js/widgets/{$type}.js");
        if (\File::exists($oldJsPath)) {
            \File::delete($oldJsPath);
        }

        // Old PHP class directory
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $type)));
        $oldClassDir = app_path("Widgets/Custom/{$className}");
        if (\File::isDirectory($oldClassDir)) {
            \File::deleteDirectory($oldClassDir);
        }
    }

    /**
     * Export single widget template as JSON
     */
    public function export(?string $projectCode = null, $id = null)
    {
        if ($id === null) {
            $id = $projectCode;
        }

        $template = WidgetTemplate::findOrFail($id);

        $exportData = [
            'version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'template' => [
                'name' => $template->name,
                'type' => $template->type,
                'category' => $template->category,
                'description' => $template->description,
                'icon' => $template->icon,
                'config_schema' => $template->config_schema,
                'default_settings' => $template->default_settings,
                'is_active' => $template->is_active,
                'is_premium' => $template->is_premium,
                'sort_order' => $template->sort_order,
            ],
        ];

        $filename = 'widget-template-'.$template->type.'-'.date('Y-m-d').'.json';

        return response()->json($exportData)
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->header('Content-Type', 'application/json');
    }

    /**
     * Export all widget templates as JSON
     */
    public function exportAll(?string $projectCode = null)
    {
        $templates = WidgetTemplate::orderBy('category')
            ->orderBy('sort_order')
            ->get();

        $exportData = [
            'version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'count' => $templates->count(),
            'templates' => $templates->map(function ($template) {
                return [
                    'name' => $template->name,
                    'type' => $template->type,
                    'category' => $template->category,
                    'description' => $template->description,
                    'icon' => $template->icon,
                    'config_schema' => $template->config_schema,
                    'default_settings' => $template->default_settings,
                    'is_active' => $template->is_active,
                    'is_premium' => $template->is_premium,
                    'sort_order' => $template->sort_order,
                ];
            })->toArray(),
        ];

        $filename = 'widget-templates-all-'.date('Y-m-d').'.json';

        return response()->json($exportData)
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->header('Content-Type', 'application/json');
    }

    /**
     * Import widget templates from JSON
     */
    public function import(Request $request, ?string $projectCode = null)
    {
        $validator = Validator::make($request->all(), [
            'json_file' => 'required|file|mimes:json,txt|max:2048',
        ], [
            'json_file.required' => 'Vui lòng chọn file JSON để import',
            'json_file.mimes' => 'File phải có định dạng JSON',
            'json_file.max' => 'File không được vượt quá 2MB',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $content = file_get_contents($request->file('json_file')->getRealPath());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'File JSON không hợp lệ: '.json_last_error_msg());
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];

            // Handle single template export format
            if (isset($data['template'])) {
                $result = $this->importSingleTemplate($data['template']);
                if ($result['success']) {
                    $imported++;
                } else {
                    $errors[] = $result['error'];
                }
            }
            // Handle multiple templates export format
            elseif (isset($data['templates']) && is_array($data['templates'])) {
                foreach ($data['templates'] as $templateData) {
                    $result = $this->importSingleTemplate($templateData);
                    if ($result['success']) {
                        $imported++;
                    } elseif ($result['skipped']) {
                        $skipped++;
                    } else {
                        $errors[] = $result['error'];
                    }
                }
            } else {
                return back()->with('error', 'Định dạng file JSON không được hỗ trợ');
            }

            $message = "Import thành công {$imported} widget template(s)";
            if ($skipped > 0) {
                $message .= ", bỏ qua {$skipped} (đã tồn tại)";
            }
            if (! empty($errors)) {
                $message .= '. Lỗi: '.implode(', ', array_slice($errors, 0, 3));
            }

            $redirectRoute = $projectCode
                ? route('project.admin.widget-templates.index', $projectCode)
                : route('cms.widget-templates.index');

            return redirect($redirectRoute)->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi import: '.$e->getMessage());
        }
    }

    /**
     * Import a single template from data array
     */
    protected function importSingleTemplate(array $templateData): array
    {
        $requiredFields = ['name', 'type', 'config_schema'];
        foreach ($requiredFields as $field) {
            if (empty($templateData[$field])) {
                return ['success' => false, 'skipped' => false, 'error' => "Thiếu trường bắt buộc: {$field}"];
            }
        }

        // Check if template with same type already exists
        $existing = WidgetTemplate::withoutGlobalScopes()
            ->where('type', $templateData['type'])
            ->where('tenant_id', session('current_tenant_id'))
            ->first();

        if ($existing) {
            return ['success' => false, 'skipped' => true, 'error' => "Template '{$templateData['type']}' đã tồn tại"];
        }

        WidgetTemplate::create([
            'name' => $templateData['name'],
            'type' => $templateData['type'],
            'category' => $templateData['category'] ?? 'general',
            'description' => $templateData['description'] ?? null,
            'icon' => $templateData['icon'] ?? 'cube',
            'config_schema' => $templateData['config_schema'],
            'default_settings' => $templateData['default_settings'] ?? [],
            'is_active' => $templateData['is_active'] ?? true,
            'is_premium' => $templateData['is_premium'] ?? false,
            'sort_order' => $templateData['sort_order'] ?? 0,
        ]);

        return ['success' => true, 'skipped' => false, 'error' => null];
    }
}
