<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\Taxonomy;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller
{
    public function index($projectCode = null)
    {
        $project = current_project();
        if (! $project && is_string($projectCode) && ! is_numeric($projectCode)) {
            $project = Project::where('code', $projectCode)->first();
        }
        if (! $project) {
            $defaultCode = config('app.project_code') ?: env('PROJECT_CODE', 'DA005');
            $project = Project::where('code', $defaultCode)->first() ?: Project::first();
        }

        if ($project) {
            $this->autoInitializeProjectMenus($project);
            $tenantId = $project->tenant_id ?? session('current_tenant_id') ?? 3;
            $menus = Menu::withoutGlobalScopes()
                ->where(function ($q) use ($project, $tenantId) {
                    $q->where('project_id', $project->id);
                    if ($tenantId) {
                        $q->orWhere('tenant_id', $tenantId);
                    }
                    if ($tenantId == 3) {
                        $q->orWhere('project_id', 10);
                    }
                })
                ->with(['items' => function ($q) {
                    $q->withoutGlobalScopes()->whereNull('parent_id')->with(['children' => function ($cq) {
                        $cq->withoutGlobalScopes()->orderBy('order');
                    }])->orderBy('order');
                }])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        } else {
            $menus = Menu::withoutGlobalScopes()
                ->with(['items' => function ($q) {
                    $q->withoutGlobalScopes()->whereNull('parent_id')->with(['children' => function ($cq) {
                        $cq->withoutGlobalScopes()->orderBy('order');
                    }])->orderBy('order');
                }])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        $selectedMenu = $menus->firstWhere('slug', 'inbetween-v2-header')
            ?? $menus->firstWhere('location', 'header')
            ?? $menus->firstWhere('project_id', $project?->id)
            ?? $menus->first();

        $sources = $this->getSourcesData($project);

        return view('cms.menus.index', array_merge([
            'menus' => $menus,
            'selectedMenu' => $selectedMenu,
            'currentProject' => $project,
            'projCode' => $project?->code ?? $projectCode,
        ], $sources));
    }

    public function show($projectCode = null, $id = null)
    {
        if ($id === null) {
            $id = $projectCode;
        }

        $project = current_project();
        if (! $project && is_string($projectCode) && ! is_numeric($projectCode)) {
            $project = Project::where('code', $projectCode)->first();
        }
        if (! $project) {
            $defaultCode = config('app.project_code') ?: env('PROJECT_CODE', 'DA005');
            $project = Project::where('code', $defaultCode)->first() ?: Project::first();
        }

        if ($project) {
            $this->autoInitializeProjectMenus($project);
            $tenantId = $project->tenant_id ?? session('current_tenant_id') ?? 3;
            $menus = Menu::withoutGlobalScopes()
                ->where(function ($q) use ($project, $tenantId) {
                    $q->where('project_id', $project->id);
                    if ($tenantId) {
                        $q->orWhere('tenant_id', $tenantId);
                    }
                    if ($tenantId == 3) {
                        $q->orWhere('project_id', 10);
                    }
                })
                ->with(['items' => function ($q) {
                    $q->withoutGlobalScopes()->whereNull('parent_id')->with(['children' => function ($cq) {
                        $cq->withoutGlobalScopes()->orderBy('order');
                    }])->orderBy('order');
                }])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        } else {
            $menus = Menu::withoutGlobalScopes()
                ->with(['items' => function ($q) {
                    $q->withoutGlobalScopes()->whereNull('parent_id')->with(['children' => function ($cq) {
                        $cq->withoutGlobalScopes()->orderBy('order');
                    }])->orderBy('order');
                }])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        $menu = Menu::withoutGlobalScopes()
            ->with(['items' => function ($q) {
                $q->withoutGlobalScopes()->whereNull('parent_id')->with(['children' => function ($cq) {
                    $cq->withoutGlobalScopes()->orderBy('order');
                }])->orderBy('order');
            }])
            ->find($id)
            ?? $menus->firstWhere('id', $id)
            ?? $menus->first();

        $sources = $this->getSourcesData($project);

        return view('cms.menus.index', array_merge([
            'menus' => $menus,
            'selectedMenu' => $menu,
            'currentProject' => $project,
            'projCode' => $project?->code ?? $projectCode,
        ], $sources));
    }

    public function store(Request $request, $projectCode = null)
    {
        try {
            $project = current_project();
            if (! $project && is_string($projectCode) && ! is_numeric($projectCode)) {
                $project = Project::where('code', $projectCode)->first();
            }
            if (! $project) {
                $defaultCode = config('app.project_code') ?: env('PROJECT_CODE', 'DA005');
                $project = Project::where('code', $defaultCode)->first() ?: Project::first();
            }
            $projectId = $project?->id;
            $tenantId = $project?->tenant_id ?? $projectId;

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255',
                'location' => 'nullable|string|max:50',
                'sort_order' => 'nullable|integer',
                'is_active' => 'nullable|boolean',
            ]);

            $existsQuery = Menu::withoutGlobalScopes()->where('slug', $validated['slug']);
            if ($projectId) {
                $existsQuery->where('project_id', $projectId);
            } else {
                $existsQuery->whereNull('project_id');
            }

            if ($existsQuery->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Slug đã tồn tại cho dự án này. Vui lòng chọn tên khác.',
                ], 422);
            }

            $menu = Menu::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'location' => $validated['location'] ?? 'header',
                'sort_order' => isset($validated['sort_order']) ? (int) $validated['sort_order'] : 0,
                'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
            ]);

            MenuService::clearMenuCache($projectId);
            if (function_exists('clear_widget_cache')) {
                clear_widget_cache();
            }
            Cache::flush();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Menu đã được tạo thành công!',
                    'menu' => $menu,
                ]);
            }

            return back()->with('success', 'Menu đã được tạo thành công!');
        } catch (\Exception $e) {
            Log::error('Menu creation failed: '.$e->getMessage());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Lỗi: '.$e->getMessage());
        }
    }

    public function update(Request $request, $projectCode = null, $id = null)
    {
        if ($id === null) {
            $id = $projectCode;
        }

        $menu = Menu::withoutGlobalScopes()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'location' => $validated['location'] ?? $menu->location,
        ];

        if ($request->has('sort_order')) {
            $updateData['sort_order'] = (int) $validated['sort_order'];
        }
        if ($request->has('is_active')) {
            $updateData['is_active'] = (bool) $request->input('is_active');
        }

        $menu->update($updateData);
        $menu->touch();

        MenuService::clearMenuCache($menu->project_id);
        if (function_exists('clear_widget_cache')) {
            clear_widget_cache();
        }
        Cache::flush();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Menu đã được cập nhật!',
                'menu' => $menu->fresh(),
            ]);
        }

        return back()->with('success', 'Menu đã được cập nhật!');
    }

    public function create($projectCode = null)
    {
        return $this->index($projectCode);
    }

    public function edit($projectCode = null, $menu = null)
    {
        return $this->show($projectCode, $menu);
    }

    public function storeItem(Request $request, $projectCode = null, $menu = null)
    {
        try {
            $resolvedMenuId = $menu
                ?? $request->route('menu')
                ?? $request->route('menuId')
                ?? (is_numeric($projectCode) ? $projectCode : null);

            if (! $resolvedMenuId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy ID menu hợp lệ.',
                ], 400);
            }

            $menuModel = Menu::withoutGlobalScopes()->find($resolvedMenuId);
            if (! $menuModel) {
                return response()->json([
                    'success' => false,
                    'message' => "Menu với ID {$resolvedMenuId} không tồn tại.",
                ], 404);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'url' => 'nullable|string|max:1000',
                'target' => 'nullable|in:_self,_blank',
                'linkable_type' => 'nullable|string|max:255',
                'linkable_id' => 'nullable|integer',
                'parent_id' => 'nullable',
                'icon' => 'nullable|string|max:255',
                'css_class' => 'nullable|string|max:255',
                'image' => 'nullable|string|max:500',
                'badge' => 'nullable|string|max:100',
                'badge_color' => 'nullable|string|max:50',
                'is_active' => 'nullable|boolean',
            ]);

            $parentId = ! empty($request->input('parent_id')) ? (int) $request->input('parent_id') : null;
            if ($parentId && ! MenuItem::withoutGlobalScopes()->where('id', $parentId)->exists()) {
                $parentId = null;
            }

            $maxOrder = MenuItem::withoutGlobalScopes()
                ->where('menu_id', $menuModel->id)
                ->where(function ($query) use ($parentId) {
                    if ($parentId) {
                        $query->where('parent_id', $parentId);
                    } else {
                        $query->whereNull('parent_id');
                    }
                })
                ->max('order');

            $order = ((int) $maxOrder) + 1;

            $data = [
                'menu_id' => $menuModel->id,
                'project_id' => $menuModel->project_id,
                'tenant_id' => $menuModel->tenant_id,
                'title' => trim($validated['title']),
                'url' => $validated['url'] ?? '#',
                'target' => ! empty($validated['target']) ? $validated['target'] : '_self',
                'linkable_type' => $validated['linkable_type'] ?? null,
                'linkable_id' => $validated['linkable_id'] ?? null,
                'parent_id' => $parentId,
                'icon' => $validated['icon'] ?? null,
                'css_class' => $validated['css_class'] ?? null,
                'image' => $validated['image'] ?? null,
                'badge' => $validated['badge'] ?? null,
                'badge_color' => $validated['badge_color'] ?? null,
                'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
                'order' => $order,
                '_lft' => 0,
                '_rgt' => 0,
            ];

            $menuItem = MenuItem::withoutGlobalScopes()->create($data);

            $menuModel->touch();
            $this->syncFooterMenuToWidget($menuModel->id, $menuModel->project_id);
            MenuService::clearMenuCache($menuModel->project_id);
            if (function_exists('clear_widget_cache')) {
                clear_widget_cache();
            }
            Cache::flush();
            try { \Illuminate\Support\Facades\Artisan::call('view:clear'); } catch (\Throwable $e) {}

            return response()->json([
                'success' => true,
                'message' => 'Mục menu đã được thêm thành công!',
                'item' => $menuItem,
            ]);
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => collect($ve->errors())->flatten()->first() ?? 'Dữ liệu không hợp lệ.',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Menu item creation failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo mục menu: '.$e->getMessage(),
            ], 500);
        }
    }

    public function updateItem(Request $request, $projectCode = null, $item = null)
    {
        try {
            $resolvedItemId = $item
                ?? $request->route('item')
                ?? $request->route('itemId')
                ?? (is_numeric($projectCode) ? $projectCode : null);

            $menuItem = MenuItem::withoutGlobalScopes()->find($resolvedItemId);
            if (! $menuItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mục menu không tồn tại.',
                ], 404);
            }

            $data = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'url' => 'nullable|string|max:1000',
                'target' => 'sometimes|required|in:_self,_blank',
                'icon' => 'nullable|string|max:255',
                'css_class' => 'nullable|string|max:255',
                'image' => 'nullable|string|max:500',
                'badge' => 'nullable|string|max:100',
                'badge_color' => 'nullable|string|max:50',
                'is_active' => 'nullable|boolean',
                'order' => 'nullable|integer',
                'parent_id' => 'nullable',
            ]);

            if ($request->has('is_active')) {
                $data['is_active'] = (bool) $request->input('is_active');
            }

            $menuItem->update($data);
            $menuItem->menu?->touch();
            $this->syncFooterMenuToWidget($menuItem->menu_id, $menuItem->project_id);

            MenuService::clearMenuCache($menuItem->project_id);
            if (function_exists('clear_widget_cache')) {
                clear_widget_cache();
            }
            Cache::flush();
            try { \Illuminate\Support\Facades\Artisan::call('view:clear'); } catch (\Throwable $e) {}

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật mục menu thành công!',
                    'item' => $menuItem->fresh(),
                ]);
            }

            return back()->with('success', 'Đã cập nhật!');
        } catch (ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => collect($ve->errors())->flatten()->first() ?? 'Dữ liệu không hợp lệ.',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Menu item update failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroyItem($projectCode = null, $item = null)
    {
        try {
            $resolvedItemId = $item
                ?? request()->route('item')
                ?? request()->route('itemId')
                ?? (is_numeric($projectCode) ? $projectCode : null);

            $menuItem = MenuItem::withoutGlobalScopes()->find($resolvedItemId);
            if (! $menuItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mục menu không tồn tại.',
                ], 404);
            }

            $projectId = $menuItem->project_id;
            $parentMenu = $menuItem->menu;
            $menuId = $menuItem->menu_id;
            $menuItem->delete();
            $parentMenu?->touch();
            $this->syncFooterMenuToWidget($menuId, $projectId);

            MenuService::clearMenuCache($projectId);
            if (function_exists('clear_widget_cache')) {
                clear_widget_cache();
            }
            Cache::flush();
            try { \Illuminate\Support\Facades\Artisan::call('view:clear'); } catch (\Throwable $e) {}

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa mục menu!',
                ]);
            }

            return back()->with('success', 'Đã xóa mục menu!');
        } catch (\Throwable $e) {
            Log::error('Menu item delete failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa mục menu: '.$e->getMessage(),
            ], 500);
        }
    }

    public function updateTree(Request $request, $projectCode = null, $menu = null)
    {
        try {
            $resolvedMenuId = $menu
                ?? $request->route('menu')
                ?? $request->route('menuId')
                ?? (is_numeric($projectCode) ? $projectCode : null);

            $menuModel = Menu::withoutGlobalScopes()->find($resolvedMenuId);
            if (! $menuModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu không tồn tại.',
                ], 404);
            }

            $tree = $request->input('tree', []);

            // Check if tree is already a flat array [{id, parent_id, order, depth}]
            $isFlat = ! empty($tree) && isset($tree[0]['id']) && array_key_exists('parent_id', $tree[0]);
            $flatItems = $isFlat ? $tree : $this->flattenTree($tree);

            DB::transaction(function () use ($flatItems, $menuModel) {
                foreach ($flatItems as $index => $item) {
                    $parentId = ! empty($item['parent_id']) ? (int) $item['parent_id'] : null;
                    // Prevent circular reference
                    if ($parentId === (int) $item['id']) {
                        $parentId = null;
                    }

                    MenuItem::withoutGlobalScopes()
                        ->where('menu_id', $menuModel->id)
                        ->where('id', $item['id'])
                        ->update([
                            'parent_id' => $parentId,
                            'order' => isset($item['order']) ? (int) $item['order'] : $index,
                        ]);
                }
            });

            $menuModel->touch();
            $this->syncFooterMenuToWidget($menuModel->id, $menuModel->project_id);
            MenuService::clearMenuCache($menuModel->project_id);
            if (function_exists('clear_widget_cache')) {
                clear_widget_cache();
            }
            Cache::flush();
            try { \Illuminate\Support\Facades\Artisan::call('view:clear'); } catch (\Throwable $e) {}

            return response()->json([
                'success' => true,
                'message' => 'Cấu trúc menu đã được cập nhật thành công!',
            ]);
        } catch (\Throwable $e) {
            Log::error('Menu tree update failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật cấu trúc menu: '.$e->getMessage(),
            ], 500);
        }
    }

    private function flattenTree($items, $parentId = null, &$result = [])
    {
        foreach ($items as $index => $item) {
            $result[] = [
                'id' => $item['id'],
                'parent_id' => $parentId,
                'order' => $index,
                'depth' => $item['depth'] ?? 0,
            ];

            if (! empty($item['children'])) {
                $this->flattenTree($item['children'], $item['id'], $result);
            }
        }

        return $result;
    }

    public function destroy($projectCode = null, $menuId = null)
    {
        if ($menuId === null) {
            $menuId = $projectCode;
        }

        $menu = Menu::withoutGlobalScopes()->findOrFail($menuId);
        $menuName = $menu->name;
        $projectId = $menu->project_id;
        $menu->delete();

        MenuService::clearMenuCache($projectId);
        if (function_exists('clear_widget_cache')) {
            clear_widget_cache();
        }
        Cache::flush();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xóa menu '{$menuName}' và tất cả mục con!",
            ]);
        }

        $project = current_project();
        $code = $projectCode ?: $project?->code;
        if ($code) {
            return redirect()->route('project.admin.menus.index', ['projectCode' => $code])->with('success', "Đã xóa menu '{$menuName}' và tất cả mục con!");
        }

        return redirect()->route('cms.menus.index')->with('success', "Đã xóa menu '{$menuName}' và tất cả mục con!");
    }

    private function getSourcesData($project): array
    {
        $projectId = $project?->id;
        $tenantId = $project?->tenant_id ?? session('current_tenant_id') ?? 3;
        $scopeFilter = function ($q) use ($projectId, $tenantId) {
            $q->where(function ($sub) use ($projectId, $tenantId) {
                if ($projectId) {
                    $sub->where('project_id', $projectId);
                }
                if ($tenantId) {
                    $sub->orWhere('tenant_id', $tenantId);
                }
                if ($tenantId == 3) {
                    $sub->orWhere('project_id', 10);
                }
            });
        };

        $pages = Post::withoutGlobalScopes()
            ->when($projectId || $tenantId, $scopeFilter)
            ->where('post_type', 'page')
            ->select('id', 'title', 'slug')
            ->get();

        $posts = Post::withoutGlobalScopes()
            ->when($projectId || $tenantId, $scopeFilter)
            ->where('post_type', 'post')
            ->select('id', 'title', 'slug')
            ->latest()
            ->limit(50)
            ->get();

        $postCategories = Schema::hasTable('taxonomies')
            ? Taxonomy::withoutGlobalScopes()
                ->when(($projectId || $tenantId) && Schema::hasColumn('taxonomies', 'project_id'), $scopeFilter)
                ->where('taxonomy', 'category')
                ->select('id', 'name', 'slug')
                ->get()
            : collect();

        $productCategories = Schema::hasTable('product_categories')
            ? ProductCategory::withoutGlobalScopes()
                ->when(($projectId || $tenantId) && Schema::hasColumn('product_categories', 'project_id'), $scopeFilter)
                ->whereNull('parent_id')
                ->with(['children' => function ($cq) use ($scopeFilter) {
                    $cq->withoutGlobalScopes()
                        ->when(Schema::hasColumn('product_categories', 'project_id'), $scopeFilter);
                }])
                ->get()
            : collect();

        $products = Schema::hasTable('products')
            ? Product::withoutGlobalScopes()
                ->when(($projectId || $tenantId) && Schema::hasColumn('products', 'project_id'), $scopeFilter)
                ->select('id', 'name', 'slug')
                ->latest()
                ->limit(60)
                ->get()
            : collect();

        $brands = Schema::hasTable('brands')
            ? Brand::withoutGlobalScopes()
                ->when(($projectId || $tenantId) && Schema::hasColumn('brands', 'project_id'), $scopeFilter)
                ->select('id', 'name', 'slug')
                ->get()
            : collect();

        $projectCode = $project?->code;
        $mediaPath = $projectCode ? "media/project-{$projectCode}" : 'media';
        $mediaFiles = collect();
        if (Storage::disk('public')->exists($mediaPath)) {
            $files = Storage::disk('public')->files($mediaPath);
            $mediaFiles = collect($files)->filter(function ($f) {
                return in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp4', 'pdf']);
            })->map(function ($f) {
                return [
                    'name' => basename($f),
                    'url' => Storage::disk('public')->url($f),
                    'path' => $f,
                ];
            })->values();
        }

        return compact('pages', 'posts', 'postCategories', 'productCategories', 'products', 'brands', 'mediaFiles');
    }

    private function autoInitializeProjectMenus($project): void
    {
        if (! $project) {
            return;
        }

        $projectId = $project->id;
        $tenantId = $project->tenant_id ?? $projectId;
        $isIb = ($project->code === 'DA005' || $project->code === 'inbetween_v2' || ($project->features['theme'] ?? '') === 'inbetween_v2');

        // 1. Menu chính (Header)
        $hasHeader = Menu::withoutGlobalScopes()
            ->where('project_id', $projectId)
            ->where(function ($q) use ($isIb) {
                $q->where('location', 'header');
                if ($isIb) {
                    $q->orWhere('slug', 'inbetween-v2-header');
                }
            })
            ->exists();

        if (! $hasHeader) {
            $mainMenu = Menu::create([
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
                'name' => $isIb ? 'Inbetween V2 Header Navigation' : 'Menu chính',
                'slug' => $isIb ? 'inbetween-v2-header' : 'main-menu',
                'location' => 'header',
                'is_active' => true,
            ]);

            $mainItems = $isIb ? [
                ['title' => 'HOME', 'url' => '#inbetween-intro', 'order' => 1],
                ['title' => 'ABOUT', 'url' => '#inbetween-hero', 'order' => 2],
                ['title' => 'WHAT WE DO', 'url' => '#inbetween-what-we-do', 'order' => 3],
                ['title' => 'WHERE WE FOCUS', 'url' => '#inbetween-where-we-focus', 'order' => 4],
                ['title' => 'FOUNDER', 'url' => '#inbetween-founder', 'order' => 5],
                ['title' => 'OUR CLIENTS', 'url' => '#inbetween-our-clients', 'order' => 6],
                ['title' => 'BEYOND BUSINESS', 'url' => '#inbetween-business', 'order' => 7],
                ['title' => 'MEDIA', 'url' => '#inbetween-business', 'order' => 8],
                ['title' => 'CONTACT', 'url' => '#inbetween-footer', 'order' => 9],
            ] : [
                ['title' => 'Trang chủ', 'url' => '/', 'order' => 1],
                ['title' => 'Cửa hàng', 'url' => '/cua-hang', 'order' => 2],
                ['title' => 'Tin tức', 'url' => '/blog', 'order' => 3],
                ['title' => 'Liên hệ', 'url' => '/lien-he', 'order' => 4],
            ];

            foreach ($mainItems as $mItem) {
                MenuItem::create([
                    'menu_id' => $mainMenu->id,
                    'project_id' => $projectId,
                    'tenant_id' => $tenantId,
                    'title' => $mItem['title'],
                    'url' => $mItem['url'],
                    'order' => $mItem['order'],
                    'target' => '_self',
                    'is_active' => true,
                ]);
            }
        }

        // 2. Menu chân trang (Footer)
        $footerMenu = Menu::withoutGlobalScopes()
            ->where('project_id', $projectId)
            ->where(function ($q) {
                $q->where('location', 'footer')->orWhere('slug', 'footer-menu');
            })
            ->first();

        if (! $footerMenu) {
            $footerMenu = Menu::create([
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
                'name' => $isIb ? 'Footer Menu' : 'Menu chân trang',
                'slug' => 'footer-menu',
                'location' => 'footer',
                'is_active' => true,
            ]);
        }

        // Nếu footer menu chưa có item nào, khởi tạo các item mặc định
        $hasFooterItems = MenuItem::withoutGlobalScopes()->where('menu_id', $footerMenu->id)->exists();
        if (! $hasFooterItems) {
            $footerItems = $isIb ? [
                ['title' => 'About Us', 'url' => '#inbetween-hero', 'order' => 1],
                ['title' => 'What We Do', 'url' => '#inbetween-what-we-do', 'order' => 2],
                ['title' => 'Where We Focus', 'url' => '#inbetween-where-we-focus', 'order' => 3],
                ['title' => 'The Founder', 'url' => '#inbetween-founder', 'order' => 4],
                ['title' => 'Our Clients', 'url' => '#inbetween-our-clients', 'order' => 5],
                ['title' => 'Beyond Business', 'url' => '#inbetween-business', 'order' => 6],
            ] : [
                ['title' => 'Giới thiệu', 'url' => '/gioi-thieu', 'order' => 1],
                ['title' => 'Chính sách bảo mật', 'url' => '/chinh-sach-bao-mat', 'order' => 2],
                ['title' => 'Điều khoản sử dụng', 'url' => '/dieu-khoan-su-dung', 'order' => 3],
                ['title' => 'Câu hỏi thường gặp', 'url' => '/faq', 'order' => 4],
            ];

            foreach ($footerItems as $fItem) {
                MenuItem::create([
                    'menu_id' => $footerMenu->id,
                    'project_id' => $projectId,
                    'tenant_id' => $tenantId,
                    'title' => $fItem['title'],
                    'url' => $fItem['url'],
                    'order' => $fItem['order'],
                    'target' => '_self',
                    'is_active' => true,
                ]);
            }
        }
    }

    protected function syncFooterMenuToWidget($menuId, $projectId = null)
    {
        try {
            $menu = Menu::withoutGlobalScopes()->find($menuId);
            if (! $menu || ($menu->location !== 'footer' && $menu->slug !== 'footer-menu')) {
                return;
            }

            $projId = $projectId ?? $menu->project_id ?? 7;
            $footerWidget = Widget::withoutGlobalScope('tenant')
                ->where('type', 'inbetween_v2_footer')
                ->where(function ($q) use ($projId) {
                    $q->where('project_id', $projId);
                    if ($projId != 7) {
                        $q->orWhere('project_id', 7);
                    }
                })
                ->first();

            if ($footerWidget) {
                $items = MenuItem::withoutGlobalScopes()
                    ->where('menu_id', $menu->id)
                    ->where('is_active', true)
                    ->whereNull('parent_id')
                    ->orderBy('order')
                    ->get();

                $quickLinks = $items->map(fn($item) => [
                    'title' => $item->title,
                    'url' => $item->url,
                    'target' => $item->target ?? '_self',
                ])->toArray();

                $currentSettings = $footerWidget->settings ?? [];
                $currentSettings['quick_links'] = $quickLinks;
                $footerWidget->settings = $currentSettings;
                $footerWidget->save();
            }
        } catch (\Throwable $e) {
            Log::warning('Sync footer menu to widget failed: '.$e->getMessage());
        }
    }
}
