<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Taxonomy;
use App\Traits\HasCrudAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductAttributeController extends Controller
{
    use HasCrudAlerts;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Taxonomy::attributes()
            ->with(['children' => fn ($q) => $q->orderBy('order')->orderBy('name')])
            ->withCount('posts');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $attributes = $query->orderBy('order')->orderBy('name')->paginate(20);
        $currentProject = Project::where('code', request()->route('projectCode'))->first();

        return view('cms.attributes.index', compact('attributes', 'currentProject'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $currentProject = Project::where('code', request()->route('projectCode'))->first();

        return view('cms.attributes.create', compact('currentProject'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:select,color,button,text',
            'description' => 'nullable|string',
            'values' => 'nullable',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $name = trim($validated['name']);
            $rawSlug = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($name);
            $slug = $this->generateUniqueSlug($rawSlug);

            $attribute = Taxonomy::create([
                'name' => $name,
                'slug' => $slug,
                'taxonomy' => 'product_attribute',
                'description' => $validated['description'] ?? null,
                'parent_id' => null,
                'status' => 'published',
                'order' => (int) ($request->order ?? 0),
                'meta_data' => [
                    'type' => $validated['type'] ?? 'select',
                ],
            ]);

            // Process values (can be array or comma-separated string)
            $this->syncValues($attribute, $request->input('values', []));

            $this->alertCreated('thuộc tính', "Thuộc tính '{$attribute->name}' đã được tạo thành công.");

            return redirect()->route('admin.attributes.index');
        });
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $attribute = Taxonomy::attributes()->with('children')->findOrFail($id);
        $currentProject = Project::where('code', request()->route('projectCode'))->first();

        return view('cms.attributes.edit', compact('attribute', 'currentProject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $attribute = Taxonomy::attributes()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'nullable|string|in:select,color,button,text',
            'description' => 'nullable|string',
            'values' => 'nullable',
        ]);

        return DB::transaction(function () use ($request, $attribute, $validated) {
            $name = trim($validated['name']);
            $rawSlug = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($name);
            $slug = $this->generateUniqueSlug($rawSlug, $attribute->id);

            $currentMeta = (array) ($attribute->meta_data ?? []);
            $currentMeta['type'] = $validated['type'] ?? ($currentMeta['type'] ?? 'select');

            $attribute->update([
                'name' => $name,
                'slug' => $slug,
                'description' => $validated['description'] ?? null,
                'order' => (int) ($request->order ?? $attribute->order),
                'meta_data' => $currentMeta,
            ]);

            // Sync values
            $this->syncValues($attribute, $request->input('values', []));

            $this->alertUpdated('thuộc tính', "Thuộc tính '{$attribute->name}' đã được cập nhật thành công.");

            return redirect()->route('admin.attributes.index');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $attribute = Taxonomy::attributes()->findOrFail($id);
        $name = $attribute->name;

        DB::transaction(function () use ($attribute) {
            // Delete child values
            $attribute->children()->delete();
            // Detach relationships
            DB::table('term_relationships')->where('term_taxonomy_id', $attribute->id)->delete();
            $attribute->delete();
        });

        $this->alertDeleted('thuộc tính', "Thuộc tính '{$name}' và các giá trị liên kết đã được xóa.");

        return redirect()->route('admin.attributes.index');
    }

    /**
     * Quick AJAX/Form route to add a single value to an attribute.
     */
    public function storeValue(Request $request, $attributeId)
    {
        $attribute = Taxonomy::attributes()->findOrFail($attributeId);

        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:50',
        ]);

        $valName = trim($validated['value']);
        $rawSlug = Str::slug($valName);
        $slug = $this->generateUniqueSlug($rawSlug);

        $val = Taxonomy::create([
            'name' => $valName,
            'slug' => $slug,
            'taxonomy' => 'product_attribute',
            'parent_id' => $attribute->id,
            'status' => 'published',
            'meta_data' => [
                'color_code' => $validated['color_code'] ?? null,
            ],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'value' => $val,
            ]);
        }

        $this->alertSuccess("Đã thêm giá trị '{$valName}' vào thuộc tính '{$attribute->name}'.");

        return redirect()->back();
    }

    /**
     * Delete a single attribute value.
     */
    public function destroyValue($valueId)
    {
        $value = Taxonomy::attributeValues()->findOrFail($valueId);
        $valName = $value->name;

        DB::table('term_relationships')->where('term_taxonomy_id', $value->id)->delete();
        $value->delete();

        $this->alertSuccess("Đã xóa giá trị '{$valName}'.");

        return redirect()->back();
    }

    /**
     * Synchronize child values for an attribute.
     */
    protected function syncValues(Taxonomy $attribute, mixed $rawValues): void
    {
        $valuesList = [];
        if (is_string($rawValues)) {
            $parts = explode(',', $rawValues);
            foreach ($parts as $p) {
                $p = trim($p);
                if ($p !== '') {
                    $valuesList[] = ['name' => $p];
                }
            }
        } elseif (is_array($rawValues)) {
            foreach ($rawValues as $item) {
                if (is_string($item)) {
                    $item = trim($item);
                    if ($item !== '') {
                        $valuesList[] = ['name' => $item];
                    }
                } elseif (is_array($item)) {
                    $name = trim($item['name'] ?? ($item['value'] ?? ''));
                    if ($name !== '') {
                        $valuesList[] = [
                            'name' => $name,
                            'color_code' => $item['color_code'] ?? null,
                        ];
                    }
                }
            }
        }

        if (empty($valuesList)) {
            return;
        }

        $existing = $attribute->children()->get()->keyBy(fn ($item) => mb_strtolower(trim($item->name)));
        $keptIds = [];

        foreach ($valuesList as $idx => $vData) {
            $vName = $vData['name'];
            $key = mb_strtolower($vName);

            if ($existing->has($key)) {
                $child = $existing->get($key);
                $meta = (array) ($child->meta_data ?? []);
                if (isset($vData['color_code'])) {
                    $meta['color_code'] = $vData['color_code'];
                }
                $child->update([
                    'order' => $idx,
                    'meta_data' => $meta,
                ]);
                $keptIds[] = $child->id;
            } else {
                $rawSlug = Str::slug($vName);
                $slug = $this->generateUniqueSlug($rawSlug);
                $child = Taxonomy::create([
                    'name' => $vName,
                    'slug' => $slug,
                    'taxonomy' => 'product_attribute',
                    'parent_id' => $attribute->id,
                    'status' => 'published',
                    'order' => $idx,
                    'meta_data' => [
                        'color_code' => $vData['color_code'] ?? null,
                    ],
                ]);
                $keptIds[] = $child->id;
            }
        }
    }

    /**
     * Generate unique slug for taxonomy items.
     */
    protected function generateUniqueSlug(string $baseSlug, ?int $excludeId = null): string
    {
        $slug = $baseSlug ?: 'attr';
        $counter = 1;

        while (true) {
            $query = Taxonomy::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (! $query->exists()) {
                break;
            }

            $counter++;
            $slug = "{$baseSlug}-{$counter}";
        }

        return $slug;
    }
}
