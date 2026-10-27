<?php

// MODIFIED: 2026-08-07

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Taxonomy;
use App\Traits\HasCrudAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use HasCrudAlerts;

    const MAX_DEPTH = 3;

    private function getTaxonomyType(): string
    {
        $tax = request('type') ?? request('taxonomy') ?? 'product_cat';
        if ($tax === 'project' || $tax === 'project_cat') {
            return 'project_cat';
        }
        if ($tax === 'product' || $tax === 'product_cat') {
            return 'product_cat';
        }
        if ($tax === 'post' || $tax === 'category' || $tax === 'news' || $tax === 'post_cat' || $tax === 'news_cat') {
            return 'category';
        }
        return $tax;
    }

    private function getCategoryQuery()
    {
        return Taxonomy::where('taxonomy', $this->getTaxonomyType());
    }

    private function generateUniqueSlug($name, $excludeId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;
        $tax = $this->getTaxonomyType();

        while (true) {
            $query = Taxonomy::where('taxonomy', $tax)->where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (! $query->exists()) {
                break;
            }

            $counter++;
            $slug = $baseSlug.'-'.$counter;
        }

        return $slug;
    }

    public function index(Request $request)
    {
        $taxonomyType = $this->getTaxonomyType();
        $pageTitle = ($taxonomyType === 'project_cat') 
            ? 'Danh mục dự án' 
            : (($taxonomyType === 'category') ? 'Chuyên mục tin tức' : 'Danh mục sản phẩm');

        if (! \Illuminate\Support\Facades\Schema::hasTable('taxonomies')) {
            $categories = collect();
            $parentCategories = collect();
            $stats = ['total' => 0, 'level_1' => 0, 'level_2' => 0, 'level_3' => 0, 'total_products' => 0];
            return view('cms.categories.index', compact('categories', 'parentCategories', 'stats', 'taxonomyType', 'pageTitle'));
        }

        $query = $this->getCategoryQuery();

        if ($request->status === 'trashed') {
            $query->onlyTrashed();
        }

        $rawCategories = $query->with(['parent', 'children'])
            ->withCount('posts as products_count')
            ->when($request->search, function ($q) use ($request) {
                $term = $request->search;
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%");
                });
            })
            ->when($request->parent_id, fn ($q) => $q->where('parent_id', $request->parent_id))
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        // Sắp xếp phân cấp dạng cây thư mục (Cấp 1 -> Cấp 2 -> Cấp 3)
        $categories = $this->buildTreeAndFlatten($rawCategories);

        // Lọc trạng thái nếu có
        if ($request->status === 'active') {
            $categories = $categories->filter(fn ($cat) => $cat->is_active);
        } elseif ($request->status === 'inactive') {
            $categories = $categories->filter(fn ($cat) => ! $cat->is_active);
        }

        // Lọc theo cấp độ nếu có
        if ($request->filled('level')) {
            $targetLevel = (int) $request->level;
            $categories = $categories->filter(fn ($cat) => ($cat->tree_level ?? $cat->level) === $targetLevel);
        }

        $stats = [
            'total' => $rawCategories->count(),
            'level_1' => $rawCategories->filter(fn ($c) => empty($c->parent_id))->count(),
            'level_2' => $rawCategories->filter(function ($c) use ($rawCategories) {
                if (empty($c->parent_id)) return false;
                $parent = $rawCategories->firstWhere('id', $c->parent_id);
                return $parent && empty($parent->parent_id);
            })->count(),
            'level_3' => $rawCategories->filter(function ($c) use ($rawCategories) {
                if (empty($c->parent_id)) return false;
                $parent = $rawCategories->firstWhere('id', $c->parent_id);
                if (! $parent || empty($parent->parent_id)) return false;
                return true;
            })->count(),
            'total_products' => $rawCategories->sum('products_count'),
        ];

        $parentCategories = $this->getCategoryQuery()->whereNull('parent_id')->orderBy('order')->get();

        return view('cms.categories.index', compact('categories', 'parentCategories', 'stats', 'taxonomyType', 'pageTitle'));
    }

    public function create()
    {
        $taxonomyType = $this->getTaxonomyType();
        $pageTitle = ($taxonomyType === 'project_cat') 
            ? 'Thêm danh mục dự án' 
            : (($taxonomyType === 'category') ? 'Thêm chuyên mục tin tức' : 'Thêm danh mục mới');

        $allCategories = \Illuminate\Support\Facades\Schema::hasTable('taxonomies')
            ? $this->getCategoryQuery()->orderBy('order')->orderBy('id')->get()
            : collect();
        $parentCategories = $this->buildTreeAndFlatten($allCategories);

        return view('cms.categories.create', compact('parentCategories', 'taxonomyType', 'pageTitle'));
    }

    /**
     * Build nested tree and flatten it sequentially for directory-style hierarchy rendering
     */
    private function buildTreeAndFlatten($categories)
    {
        $grouped = [];
        $byId = [];
        foreach ($categories as $cat) {
            $byId[$cat->id] = $cat;
            $pid = $cat->parent_id ?: 0;
            $grouped[$pid][] = $cat;
        }

        // Roots are elements whose parent_id is null/0 OR whose parent_id does not exist in current set
        $roots = [];
        foreach ($categories as $cat) {
            $pid = $cat->parent_id ?: 0;
            if (! $pid || ! isset($byId[$pid])) {
                $roots[] = $cat;
            }
        }

        usort($roots, fn ($a, $b) => ($a->order ?? 0) <=> ($b->order ?? 0) ?: ($a->id <=> $b->id));

        $flattened = [];
        $traverse = function ($items, $depth = 0, $ancestorIds = []) use (&$traverse, &$flattened, &$grouped, &$byId) {
            $total = count($items);
            foreach ($items as $idx => $item) {
                $item->tree_level = $depth;
                $item->is_last_child = ($idx === $total - 1);
                $children = $grouped[$item->id] ?? [];
                usort($children, fn ($a, $b) => ($a->order ?? 0) <=> ($b->order ?? 0) ?: ($a->id <=> $b->id));

                $item->children_list = $children;
                $item->children_count = count($children);
                $item->ancestor_ids = $ancestorIds;

                // Count all descendants recursively
                $countDescendants = function ($cList) use (&$countDescendants, &$grouped) {
                    $cnt = count($cList);
                    foreach ($cList as $c) {
                        if (isset($grouped[$c->id])) {
                            $cnt += $countDescendants($grouped[$c->id]);
                        }
                    }
                    return $cnt;
                };
                $item->total_descendants_count = $countDescendants($children);

                // Parent name
                if ($item->parent_id && isset($byId[$item->parent_id])) {
                    $item->parent_name = $byId[$item->parent_id]->name;
                } else {
                    $item->parent_name = null;
                }

                $flattened[] = $item;

                if (! empty($children)) {
                    $nextAncestors = array_merge($ancestorIds, [$item->id]);
                    $traverse($children, $depth + 1, $nextAncestors);
                }
            }
        };

        $traverse($roots, 0, []);

        return collect($flattened);
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        $slug = empty($data['slug']) ? $this->generateUniqueSlug($data['name']) : $this->generateUniqueSlug($data['slug']);

        $metaData = [];

        if ($request->has('image') && ! empty($request->input('image'))) {
            $metaData['image'] = $request->input('image');
        }

        $level = 0;
        $path = $slug;

        if (! empty($data['parent_id'])) {
            $parent = $this->getCategoryQuery()->find($data['parent_id']);
            if (! $parent) {
                return back()->withInput()->with('alert', ['type' => 'error', 'message' => 'Danh mục cha không tồn tại!']);
            }

            $parentMeta = is_string($parent->meta_data) ? json_decode($parent->meta_data, true) : ($parent->meta_data ?? []);
            $parentLevel = $parentMeta['level'] ?? 0;
            $parentPath = $parentMeta['path'] ?? $parent->slug;

            $level = $parentLevel + 1;

            if ($level > self::MAX_DEPTH) {
                return back()->withInput()->with('alert', ['type' => 'error', 'message' => 'Không thể tạo danh mục quá '.(self::MAX_DEPTH + 1).' cấp độ!']);
            }

            $path = $parentPath.'/'.$slug;
        }

        $metaData['level'] = $level;
        $metaData['path'] = $path;
        $metaData['is_active'] = $request->has('is_active') ? true : false;

        $taxonomyType = $this->getTaxonomyType();

        $category = Taxonomy::create([
            'name' => $data['name'],
            'slug' => $slug,
            'taxonomy' => $taxonomyType,
            'description' => $data['description'] ?? '',
            'parent_id' => $data['parent_id'] ?? null,
            'order' => $data['sort_order'] ?? 0,
            'status' => $request->has('is_active') ? 'published' : 'draft',
            'meta_data' => $metaData,
        ]);

        $this->alertCreated('danh mục', "Danh mục '{$category->name}' đã được thêm vào hệ thống.");

        return redirect($this->getRedirectRoute($taxonomyType));
    }

    public function show($projectCodeOrId, $categoryId = null)
    {
        $id = $categoryId ?? $projectCodeOrId;
        $category = Taxonomy::with(['parent', 'children'])->findOrFail($id);

        return view('cms.categories.show', compact('category'));
    }

    public function edit($projectCodeOrId, $categoryId = null)
    {
        $id = $categoryId ?? $projectCodeOrId;
        $category = Taxonomy::findOrFail($id);
        $taxonomyType = $category->taxonomy ?: $this->getTaxonomyType();
        $pageTitle = ($taxonomyType === 'project_cat') 
            ? 'Chỉnh sửa danh mục dự án' 
            : (($taxonomyType === 'category') ? 'Chỉnh sửa chuyên mục tin tức' : 'Chỉnh sửa danh mục');

        $allCategories = Taxonomy::where('taxonomy', $taxonomyType)->where('id', '!=', $category->id)->orderBy('order')->orderBy('id')->get();
        $parentCategories = $this->buildTreeAndFlatten($allCategories);

        return view('cms.categories.edit', compact('category', 'parentCategories', 'taxonomyType', 'pageTitle'));
    }

    public function update(CategoryRequest $request, $projectCodeOrId, $categoryId = null)
    {
        $id = $categoryId ?? $projectCodeOrId;
        $category = Taxonomy::findOrFail($id);
        $taxonomyType = $category->taxonomy ?: $this->getTaxonomyType();
        $data = $request->validated();

        $slug = empty($data['slug']) ? $this->generateUniqueSlug($data['name'], $category->id) : $this->generateUniqueSlug($data['slug'], $category->id);

        $metaData = is_string($category->meta_data) ? json_decode($category->meta_data, true) : ($category->meta_data ?? []);

        if ($request->has('image')) {
            $imageValue = $request->input('image');
            if (! empty($imageValue) && $imageValue !== 'null' && $imageValue !== '') {
                $metaData['image'] = $imageValue;
            } else {
                unset($metaData['image']);
            }
        }

        $level = 0;
        $path = $slug;

        if (! empty($data['parent_id'])) {
            $parent = Taxonomy::where('taxonomy', $taxonomyType)->find($data['parent_id']);
            if (! $parent) {
                return back()->withInput()->with('alert', ['type' => 'error', 'message' => 'Danh mục cha không tồn tại!']);
            }

            $parentMeta = is_string($parent->meta_data) ? json_decode($parent->meta_data, true) : ($parent->meta_data ?? []);
            $parentLevel = $parentMeta['level'] ?? 0;
            $parentPath = $parentMeta['path'] ?? $parent->slug;

            $level = $parentLevel + 1;

            if ($level > self::MAX_DEPTH) {
                return back()->withInput()->with('alert', ['type' => 'error', 'message' => 'Không thể chuyển danh mục này! Hệ thống chỉ hỗ trợ tối đa '.(self::MAX_DEPTH + 1).' cấp.']);
            }

            $path = $parentPath.'/'.$slug;
        }

        $metaData['level'] = $level;
        $metaData['path'] = $path;
        $metaData['is_active'] = $request->has('is_active') ? true : false;

        $category->update([
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? $category->description,
            'parent_id' => $data['parent_id'] ?? null,
            'order' => $data['sort_order'] ?? $category->order,
            'status' => $request->has('is_active') ? 'published' : 'draft',
            'meta_data' => $metaData,
        ]);

        $this->alertUpdated('danh mục', "Danh mục '{$category->name}' đã được cập nhật.");

        return redirect($this->getRedirectRoute($taxonomyType));
    }

    public function destroy($projectCodeOrId, $categoryId = null)
    {
        $id = $categoryId ?? $projectCodeOrId;
        $category = Taxonomy::findOrFail($id);
        $taxonomyType = $category->taxonomy ?: $this->getTaxonomyType();

        if ($category->posts()->count() > 0) {
            return back()->with('alert', ['type' => 'error', 'message' => 'Không thể xóa danh mục đang có dự án hoặc sản phẩm/bài viết!']);
        }

        $categoryName = $category->name;
        $category->delete();

        $this->alertDeleted('danh mục', "Danh mục '{$categoryName}' đã được xóa.");

        return redirect($this->getRedirectRoute($taxonomyType));
    }

    private function getRedirectRoute($taxonomy = null)
    {
        $type = $taxonomy ?? $this->getTaxonomyType();
        $params = ($type !== 'product_cat') ? ['type' => $type] : [];

        if (request()->routeIs('admin.*')) {
            return route('admin.categories.index', $params);
        }

        $projectCode = request()->route('projectCode');
        if ($projectCode && \Illuminate\Support\Facades\Route::has('project.admin.categories.index')) {
            return route('project.admin.categories.index', array_merge([$projectCode], $params));
        }

        return route('cms.categories.index', $params);
    }

    public function getSubcategories(Request $request)
    {
        $subcategories = $this->getCategoryQuery()->where('parent_id', $request->parent_id)
            ->orderBy('order')
            ->get();

        return response()->json($subcategories);
    }

    public function restore($projectCodeOrId, $categoryId = null)
    {
        $id = $categoryId ?? $projectCodeOrId;
        $category = $this->getCategoryQuery()->withTrashed()->findOrFail($id);
        $category->restore();

        $this->alertSuccess('Đã khôi phục danh mục thành công.');

        return redirect()->back();
    }

    public function forceDelete($projectCodeOrId, $categoryId = null)
    {
        $id = $categoryId ?? $projectCodeOrId;
        $category = $this->getCategoryQuery()->withTrashed()->findOrFail($id);
        $category->forceDelete();

        $this->alertSuccess('Đã xóa vĩnh viễn danh mục thành công.');

        return redirect()->back();
    }
}
