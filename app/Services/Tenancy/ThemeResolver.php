<?php

declare(strict_types=1);

namespace App\Services\Tenancy;

use App\Core\Theme\ThemeManager;
use App\Models\Project;

class ThemeResolver
{
    /**
     * Resolve and configure theme views and assets for project
     */
    public function resolve(?Project $project = null): string
    {
        $theme = app(ThemeManager::class)->resolveActiveTheme($project);

        return $theme;
    }
}
