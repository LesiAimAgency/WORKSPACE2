<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxonomy;
use Illuminate\Http\Request;

class PropertyCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Taxonomy::where('taxonomy', 'property_category');

        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $categories = $query->orderBy('order', 'asc')->get();

        // Xây dựng cây danh mục cho view nested (cấp 1, cấp 2, ...)
        $categoriesTree = $this->buildTree($categories);
        // Chuyển lại thành mảng phẳng để lặp hiển thị trong bảng
        $flattenedCategories = $this->flattenTree($categoriesTree);

        return view('cms.property-categories.index', ['categories' => $flattenedCategories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = Taxonomy::where('taxonomy', 'property_category')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('cms.property-categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|max:255',
            'parent_id' => 'nullable|exists:taxonomies,id',
            'description' => 'nullable',
            'order' => 'integer|nullable',
        ]);

        $validated['taxonomy'] = 'property_category';
        $validated['tenant_id'] = session('current_tenant_id');
        $validated['project_id'] = session('current_project_id');

        // Meta data
        $metaData = [];
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('taxonomies', 'public');
            $metaData['image'] = '/storage/'.$path;
        }
        $validated['meta_data'] = $metaData;

        Taxonomy::create($validated);

        return redirect()->route('project.admin.property-categories.index', session('current_project')->code)
            ->with('success', 'Tạo danh mục bất động sản thành công.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Taxonomy::findOrFail($id);
        $parentCategories = Taxonomy::where('taxonomy', 'property_category')
            ->whereNull('parent_id')
            ->where('id', '!=', $id) // Không chọn chính nó làm cha
            ->orderBy('name')
            ->get();

        return view('cms.property-categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Taxonomy::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|max:255',
            'parent_id' => 'nullable|exists:taxonomies,id',
            'description' => 'nullable',
            'order' => 'integer|nullable',
        ]);

        $metaData = $category->meta_data ?? [];
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('taxonomies', 'public');
            $metaData['image'] = '/storage/'.$path;
        }
        $validated['meta_data'] = $metaData;

        $category->update($validated);

        return redirect()->route('project.admin.property-categories.index', session('current_project')->code)
            ->with('success', 'Cập nhật danh mục bất động sản thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Taxonomy::findOrFail($id);
        // Cập nhật các con thành null parent
        Taxonomy::where('parent_id', $category->id)->update(['parent_id' => null]);
        $category->delete();

        return redirect()->route('project.admin.property-categories.index', session('current_project')->code)
            ->with('success', 'Xóa danh mục bất động sản thành công.');
    }

    /**
     * Build nested tree for categories
     */
    private function buildTree($elements, $parentId = null)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    /**
     * Flatten nested tree for table rendering
     */
    private function flattenTree($elements, $level = 0, &$result = [])
    {
        foreach ($elements as $element) {
            $element->level = $level;
            $result[] = $element;
            if (isset($element->children) && count($element->children) > 0) {
                $this->flattenTree($element->children, $level + 1, $result);
            }
        }

        return $result;
    }
}
