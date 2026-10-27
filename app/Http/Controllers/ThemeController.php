<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectSetting;
use App\Services\SettingsService;

class ThemeController extends Controller
{
    public function dynamicCss()
    {
        $settings = SettingsService::getInstance();

        $primaryColor = $settings->get('primary_color', ['color' => '#3490dc']);
        $secondaryColor = $settings->get('secondary_color', ['color' => '#ffed4e']);

        $css = ":root {
            --primary-color: {$primaryColor['color']};
            --secondary-color: {$secondaryColor['color']};
        }
        
        .btn-primary {
            background-color: var(--primary-color);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
        }";

        return response($css)
            ->header('Content-Type', 'text/css')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function projectCustomCss($projectCode)
    {
        $project = Project::where('code', $projectCode)->first();

        if (! $project) {
            return response('/* Project not found */', 404)
                ->header('Content-Type', 'text/css');
        }

        // Lấy custom CSS từ settings của project
        $customCss = ProjectSetting::where('project_id', $project->id)
            ->where('key', 'custom_css')
            ->value('value') ?? '';

        // Lấy theme colors từ settings
        $primaryColor = ProjectSetting::where('project_id', $project->id)
            ->where('key', 'primary_color')
            ->value('value') ?? '#98191F';

        $secondaryColor = ProjectSetting::where('project_id', $project->id)
            ->where('key', 'secondary_color')
            ->value('value') ?? '#1a1a1a';

        $css = "/* Custom CSS for Project: {$projectCode} */\n\n";
        $css .= ":root {\n";
        $css .= "    --project-primary: {$primaryColor};\n";
        $css .= "    --project-secondary: {$secondaryColor};\n";
        $css .= "}\n\n";
        $css .= $customCss;

        return response($css)
            ->header('Content-Type', 'text/css')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function projectCustomJs($projectCode)
    {
        $project = Project::where('code', $projectCode)->first();

        if (! $project) {
            return response('// Project not found', 200)
                ->header('Content-Type', 'application/javascript');
        }

        $customJs = ProjectSetting::where('project_id', $project->id)
            ->where('key', 'custom_js')
            ->value('value') ?? '';

        $js = "// Custom JavaScript for Project: {$projectCode}\n\n";
        $js .= $customJs;

        return response($js)
            ->header('Content-Type', 'application/javascript')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
