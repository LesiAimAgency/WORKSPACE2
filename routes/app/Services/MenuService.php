<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Theme\ThemeManager;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class MenuService
{
    /**
     * Cache TTL in seconds (10 minutes)
     */
    public const CACHE_TTL = 600;

    /**
     * Available menu locations supported by the system
     */
    public static function getAvailableLocations(): array
    {
        return [
            'header' => 'Header (Thanh điều hướng chính)',
            'sub_location' => 'Thanh Tỉnh Thành (Sub-location / Quick Filter)',
            'footer' => 'Footer (Chân trang)',
            'footer_bottom' => 'Footer Bottom (Liên kết bản quyền & chính sách)',
            'mobile' => 'Mobile (Menu điều hướng di động)',
            'sidebar' => 'Sidebar (Thanh bên)',
            'topbar' => 'Topbar (Thanh tiêu đề trên cùng)',
            'custom' => 'Custom (Vị trí tùy biến mở rộng)',
        ];
    }

    /**
     * Resolve project context
     */
    public static function resolveProject(?Project $project = null): ?Project
    {
        if ($project) {
            return $project;
        }

        if (function_exists('current_project')) {
            $current = current_project();
            if ($current instanceof Project) {
                return $current;
            }
        }

        if (config('app.standalone_mode') || env('STANDALONE_MODE')) {
            try {
                return Project::first();
            } catch (\Throwable $e) {
            }
        }

        try {
            if (Project::count() === 1) {
                return Project::first();
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    /**
     * Get all active menus for a specific location
     * Supports multiple menus per location (e.g. multiple footer menus)
     *
     * @return Collection<int, Menu>
     */
    public static function getMenusByLocation(string $location, ?Project $project = null): Collection
    {
        $project = static::resolveProject($project);
        $projectId = $project?->id ?? 0;
        $cacheKey = "menus_loc_{$projectId}_{$location}";

        try {
            return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($location, $project) {
                try {
                    $query = Menu::withoutGlobalScopes()
                        ->where('is_active', true)
                        ->where('location', $location);

                    if ($project) {
                        $tenantId = $project->tenant_id;
                        $query->where(function ($q) use ($project, $tenantId) {
                            $q->where('project_id', $project->id);
                            if ($tenantId) {
                                $q->orWhere(function ($sq) use ($tenantId) {
                                    $sq->whereNull('project_id')->where('tenant_id', $tenantId);
                                });
                            }
                        });
                    } else {
                        $hasGlobal = (clone $query)->whereNull('project_id')->exists();
                        if ($hasGlobal) {
                            $query->whereNull('project_id');
                        }
                    }

                    $results = $query->orderBy('sort_order', 'asc')
                        ->orderBy('id', 'asc')
                        ->with(['items' => function ($q) {
                            $q->withoutGlobalScopes()
                                ->where(function ($sq) {
                                    $sq->whereNull('is_active')->orWhere('is_active', true);
                                })
                                ->whereNull('parent_id')
                                ->with(['children' => function ($cq) {
                                    $cq->withoutGlobalScopes()
                                        ->where(function ($csq) {
                                            $csq->whereNull('is_active')->orWhere('is_active', true);
                                        })
                                        ->orderBy('order', 'asc');
                                }])
                                ->orderBy('order', 'asc');
                        }])
                        ->get();

                    if ($results->isEmpty()) {
                        return static::buildThemeDefaultMenus($location);
                    }

                    return $results;
                } catch (\Throwable $e) {
                    return static::buildThemeDefaultMenus($location);
                }
            });
        } catch (\Throwable $e) {
            return static::buildThemeDefaultMenus($location);
        }
    }

    /**
     * Build virtual Menu and MenuItem collection from active theme manifest
     */
    public static function buildThemeDefaultMenus(string $location): Collection
    {
        $themeManager = app(ThemeManager::class);
        $manifest = $themeManager->getManifest($themeManager->getActiveTheme());
        if (! $manifest) {
            return new Collection;
        }

        $themeMenus = $manifest->getMenus();
        if (empty($themeMenus[$location])) {
            return new Collection;
        }

        $menuDef = $themeMenus[$location];
        $virtualMenu = new Menu([
            'name' => $menuDef['name'] ?? ucfirst($location).' Menu',
            'slug' => $location,
            'location' => $location,
            'is_active' => true,
        ]);

        $items = new Collection;
        $order = 1;
        foreach ($menuDef['items'] ?? [] as $itemData) {
            $items->push(new MenuItem([
                'title' => $itemData['title'] ?? '',
                'url' => $itemData['url'] ?? '#',
                'icon' => $itemData['icon'] ?? null,
                'css_class' => $itemData['css_class'] ?? null,
                'order' => $order++,
                'is_active' => true,
            ]));
        }

        $virtualMenu->setRelation('items', $items);

        return new Collection([$virtualMenu]);
    }

    /**
     * Get single active menu by location (e.g. Header Main Menu)
     */
    public static function getMenuByLocation(string $location, ?Project $project = null): ?Menu
    {
        $menus = static::getMenusByLocation($location, $project);

        return $menus->first();
    }

    /**
     * Get menu by slug or location
     */
    public static function getMenu(string $slugOrLocation, ?Project $project = null): ?Menu
    {
        $project = static::resolveProject($project);
        $projectId = $project?->id ?? 0;
        $cacheKey = "menu_single_{$projectId}_{$slugOrLocation}";

        try {
            return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($slugOrLocation, $project) {
                try {
                    $query = Menu::withoutGlobalScopes()
                        ->where('is_active', true)
                        ->where(function ($q) use ($slugOrLocation) {
                            $q->where('slug', $slugOrLocation)
                                ->orWhere('location', $slugOrLocation);
                        });

                    if ($project) {
                        $tenantId = $project->tenant_id;
                        $query->where(function ($q) use ($project, $tenantId) {
                            $q->where('project_id', $project->id);
                            if ($tenantId) {
                                $q->orWhere(function ($sq) use ($tenantId) {
                                    $sq->whereNull('project_id')->where('tenant_id', $tenantId);
                                });
                            }
                        });
                    }

                    return $query->orderBy('sort_order', 'asc')
                        ->with(['items' => function ($q) {
                            $q->withoutGlobalScopes()
                                ->where(function ($sq) {
                                    $sq->whereNull('is_active')->orWhere('is_active', true);
                                })
                                ->whereNull('parent_id')
                                ->with(['children' => function ($cq) {
                                    $cq->withoutGlobalScopes()
                                        ->where(function ($csq) {
                                            $csq->whereNull('is_active')->orWhere('is_active', true);
                                        })
                                        ->orderBy('order', 'asc');
                                }])
                                ->orderBy('order', 'asc');
                        }])
                        ->first();
                } catch (\Throwable $e) {
                    return null;
                }
            });
        } catch (\Throwable $e) {
            return static::buildThemeDefaultMenus($slugOrLocation)->first();
        }
    }

    /**
     * Get all menus belonging to a project for admin listing
     */
    public static function getMenusForProject(?Project $project = null): Collection
    {
        $project = static::resolveProject($project);

        try {
            $query = Menu::withoutGlobalScopes();
            if ($project) {
                $tenantId = $project->tenant_id;
                $query->where(function ($q) use ($project, $tenantId) {
                    $q->where('project_id', $project->id);
                    if ($tenantId) {
                        $q->orWhere(function ($sq) use ($tenantId) {
                            $sq->whereNull('project_id')->where('tenant_id', $tenantId);
                        });
                    }
                });
            }

            return $query->orderBy('location', 'asc')
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc')
                ->with(['items' => function ($q) {
                    $q->withoutGlobalScopes()
                        ->whereNull('parent_id')
                        ->with(['children' => function ($cq) {
                            $cq->withoutGlobalScopes()->orderBy('order', 'asc');
                        }])
                        ->orderBy('order', 'asc');
                }])
                ->get();
        } catch (\Throwable $e) {
            return new Collection;
        }
    }

    /**
     * Clear all cached menus for a project or globally
     */
    public static function clearMenuCache(?int $projectId = null): void
    {
        try {
            $locations = array_keys(static::getAvailableLocations());
            $locations[] = 'footer_bottom';

            $pIds = $projectId ? [$projectId] : [0, 1, 4, 5, 7, 10, 11, 14, 15];

            foreach ($pIds as $pId) {
                foreach ($locations as $loc) {
                    Cache::forget("menus_loc_{$pId}_{$loc}");
                    Cache::forget("menu_single_{$pId}_{$loc}");
                }
                Cache::forget("menus_loc_{$pId}_main-menu");
                Cache::forget("menu_single_{$pId}_main-menu");
                Cache::forget("menus_loc_{$pId}_footer-menu");
                Cache::forget("menu_single_{$pId}_footer-menu");
            }

            if (function_exists('cache')) {
                // Also flush tags if available
                try {
                    cache()->tags(['menus'])->flush();
                } catch (\Exception $e) {
                    // Ignore if tag flush is not supported by driver
                }
            }
        } catch (\Throwable $e) {
        }
    }
}
