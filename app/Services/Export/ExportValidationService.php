<?php

declare(strict_types=1);

namespace App\Services\Export;

use App\Core\Theme\ThemeManager;
use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ExportValidationService
{
    public function validateProject(Project $project): array
    {
        $errors = [];
        $warnings = [];

        $themeManager = app(ThemeManager::class);
        $theme = $themeManager->resolveActiveTheme($project);
        $manifest = $themeManager->getManifest($theme);

        // 1. Theme existence check
        if (! $manifest) {
            $errors[] = "Theme '{$theme}' không tìm thấy manifest theme.json hợp lệ.";
        } else {
            if (! $manifest->isValid()) {
                $errors[] = "Theme manifest '{$theme}' thiếu tên hoặc phiên bản.";
            }

            // 2. Database tables check
            $requiredTables = $manifest->getDatabaseTables();
            foreach ($requiredTables as $table) {
                if (! Schema::hasTable($table)) {
                    $errors[] = "Bảng dữ liệu bắt buộc của theme '{$table}' không tồn tại trong database.";
                }
            }

            // 3. Asset directory check
            $assetDir = public_path("themes/{$theme}");
            if (! File::isDirectory($assetDir)) {
                $warnings[] = "Thư mục assets 'public/themes/{$theme}' không tồn tại hoặc bị thiếu.";
            }

            // 4. Views directory check
            $viewDir = resource_path("views/themes/{$theme}");
            $legacyViewDir = resource_path("views/frontend/themes/{$theme}");
            if (! File::isDirectory($viewDir) && ! File::isDirectory($legacyViewDir)) {
                $errors[] = "Thư mục giao diện 'resources/views/themes/{$theme}' không tồn tại.";
            }

            // 5. Routes file check
            if ($routesFile = $manifest->getRoutesFile()) {
                if (! File::exists(base_path($routesFile))) {
                    $warnings[] = "File route khai báo '{$routesFile}' không tìm thấy trên hệ thống.";
                }
            }
        }

        return [
            'is_valid' => empty($errors),
            'theme' => $theme,
            'errors' => $errors,
            'warnings' => $warnings,
            'timestamp' => now()->toISOString(),
        ];
    }

    public function validateTheme(string $theme): array
    {
        $errors = [];
        $warnings = [];

        $themeManager = app(ThemeManager::class);
        $manifest = $themeManager->getManifest($theme);

        if (! $manifest) {
            $errors[] = "Theme '{$theme}' không có theme.json.";
        } elseif (! $manifest->isValid()) {
            $errors[] = "Theme manifest '{$theme}' không hợp lệ.";
        }

        $viewDir = resource_path("views/themes/{$theme}");
        $legacyViewDir = resource_path("views/frontend/themes/{$theme}");
        if (! File::isDirectory($viewDir) && ! File::isDirectory($legacyViewDir)) {
            $errors[] = 'Thư mục giao diện theme không tồn tại.';
        }

        $assetDir = public_path("themes/{$theme}");
        if (! File::isDirectory($assetDir)) {
            $warnings[] = 'Thư mục public assets của theme không tồn tại.';
        }

        return [
            'is_valid' => empty($errors),
            'theme' => $theme,
            'errors' => $errors,
            'warnings' => $warnings,
            'timestamp' => now()->toISOString(),
        ];
    }
}
