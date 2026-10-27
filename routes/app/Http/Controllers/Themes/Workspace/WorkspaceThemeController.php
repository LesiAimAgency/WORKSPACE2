<?php

declare(strict_types=1);

namespace App\Http\Controllers\Themes\Workspace;

use App\Core\Theme\ThemeManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkspaceThemeController extends Controller
{
    protected function initTheme(): void
    {
        $themeManager = app(ThemeManager::class);
        $themeManager->setActiveTheme('workspace');
    }

    /**
     * Workspace Theme Homepage
     */
    public function home(Request $request): View
    {
        $this->initTheme();

        return view('themes.workspace.pages.home');
    }
}
