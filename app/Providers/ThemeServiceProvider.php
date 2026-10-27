<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Theme\ThemeManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeManager::class, function () {
            return ThemeManager::getInstance();
        });

        $this->app->alias(ThemeManager::class, 'theme.manager');
    }

    public function boot(): void
    {
        $themeManager = $this->app->make(ThemeManager::class);
        $themes = $themeManager->discover();

        // Dynamically register routes for discovered themes
        foreach ($themes as $slug => $manifest) {
            // inbetween and inbetween_v2 are already loaded in routes/web.php, skip duplicate
            if (in_array($slug, ['inbetween', 'inbetween_v2'], true)) {
                continue;
            }

            $routesFile = $manifest->getRoutesFile();
            $fullPath = null;

            if ($routesFile && file_exists(base_path($routesFile))) {
                $fullPath = base_path($routesFile);
            } elseif (file_exists(base_path("routes/{$slug}.php"))) {
                $fullPath = base_path("routes/{$slug}.php");
            } elseif (file_exists(resource_path("views/themes/{$slug}/routes.php"))) {
                $fullPath = resource_path("views/themes/{$slug}/routes.php");
            }

            if ($fullPath) {
                Route::prefix($slug)
                    ->name("web.{$slug}.")
                    ->middleware(['web', 'project.context'])
                    ->group($fullPath);
            }
        }
    }
}
