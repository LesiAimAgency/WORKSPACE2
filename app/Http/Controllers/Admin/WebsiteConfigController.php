<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Project;
use App\Models\ProjectSettingModel;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class WebsiteConfigController extends Controller
{
    public function index(Request $request)
    {
        $project = $request->attributes->get('project');
        if (! $project && $request->route('projectCode')) {
            $project = Project::where('code', $request->route('projectCode'))->first();
            if ($project) {
                $request->attributes->set('project', $project);
            }
        }

        $sections = config('website_sections');
        $activeTab = $request->get('tab', 'general');

        $settings = [];
        foreach ($sections as $section) {
            foreach ($section['fields'] as $fieldKey => $field) {
                $settings[$fieldKey] = setting($fieldKey, $field['default'] ?? '');
            }
        }

        if ($project) {
            $tenantId = $project->tenant_id ?? session('current_tenant_id') ?? (app()->bound('current_tenant_id') ? app('current_tenant_id') : 3);
            $menus = Menu::withoutGlobalScopes()
                ->where(function ($q) use ($project, $tenantId) {
                    $q->where('tenant_id', $tenantId)
                        ->orWhere('project_id', $project->id);
                    if ($tenantId == 3) {
                        $q->orWhere('project_id', 10);
                    }
                })
                ->get();
            if ($menus->isEmpty()) {
                $menus = Menu::withoutGlobalScopes()->whereNull('project_id')->get();
            }
        } else {
            $menus = Menu::all();
        }

        return view('cms.website-config.index', compact('sections', 'activeTab', 'settings', 'menus'));
    }

    public function save(Request $request)
    {
        try {
            $sections = config('website_sections');
            $activeTab = $request->get('tab', 'general');

            $project = $request->attributes->get('project');
            if (! $project && $request->route('projectCode')) {
                $project = Project::where('code', $request->route('projectCode'))->first();
                if ($project) {
                    $request->attributes->set('project', $project);
                }
            }

            if (isset($sections[$activeTab])) {
                foreach ($sections[$activeTab]['fields'] as $fieldKey => $field) {
                    if ($request->hasFile($fieldKey)) {
                        $file = $request->file($fieldKey);
                        $path = $file->store('website-config', 'public');
                        $value = '/storage/'.$path;
                    } elseif ($field['type'] === 'checkbox') {
                        $value = $request->input($fieldKey, 0);
                    } else {
                        $value = $request->input($fieldKey, '');
                    }

                    // Sử dụng model phù hợp dựa trên context
                    if ($project) {
                        ProjectSettingModel::set($fieldKey, $value, $activeTab);
                    } else {
                        SettingsService::getInstance()->set($fieldKey, $value, $activeTab);
                    }
                }
            }

            $tenantId = session('current_tenant_id') ?? $project?->tenant_id ?? $project?->id;
            if ($tenantId) {
                \Cache::forget('all_settings_'.$tenantId);
            }
            SettingsService::getInstance()->clearCache();

            return back()->with('alert', [
                'type' => 'success',
                'message' => 'Lưu cấu hình thành công!',
            ]);
        } catch (\Exception $e) {
            \Log::error('Website config save error: '.$e->getMessage());

            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi: '.$e->getMessage(),
            ]);
        }
    }

    public function preview()
    {
        return view('cms.website-config.preview');
    }
}
