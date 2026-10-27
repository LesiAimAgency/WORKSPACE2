<?php

// MODIFIED: 2025-01-21

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
use App\Http\Requests\AttributeValueRequest;
use App\Models\AttributeGroup;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Traits\HasCrudAlerts;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    use HasCrudAlerts;

    // ===== ATTRIBUTE GROUPS =====
    public function indexGroups($projectCode, Request $request)
    {
        $groups = AttributeGroup::when($request->search, fn ($q) => $q->search($request->search))
            ->orderBy('sort_order')
            ->paginate(config('app.admin_per_page', 20));

        return view('cms.attributes.groups.index', compact('groups'));
    }

    public function createGroup($projectCode)
    {
        return view('cms.attributes.groups.create');
    }

    public function storeGroup(AttributeRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        AttributeGroup::create($data);

        $this->alertCreated('nhóm thuộc tính', "Nhóm '{$data['name']}' đã được thêm vào hệ thống.");

        $projectCode = request()->route('projectCode');

        return redirect()->route('project.admin.attributes.index', $projectCode);
    }

    public function editGroup($projectCode, AttributeGroup $group)
    {
        return view('cms.attributes.groups.edit', compact('group'));
    }

    public function updateGroup(AttributeRequest $request, $projectCode, AttributeGroup $group)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $group->update($data);

        return redirect()->route('project.admin.attributes.index', $projectCode)
            ->with('alert', [
                'type' => 'success',
                'message' => 'Nhóm thuộc tính đã được cập nhật thành công!',
                'details' => "Nhóm '{$data['name']}' đã được cập nhật.",
            ]);
    }

    public function destroyGroup($projectCode, AttributeGroup $group)
    {
        $group->delete();

        return redirect()->route('project.admin.attributes.index', $projectCode)
            ->with('alert', [
                'type' => 'success',
                'message' => 'Nhóm thuộc tính đã được xóa thành công!',
                'details' => "Nhóm '{$group->name}' đã được xóa khỏi hệ thống.",
            ]);
    }

    // ===== PRODUCT ATTRIBUTES =====
    public function index($projectCode, Request $request)
    {
        try {
            $attributes = ProductAttribute::with(['group', 'values'])
                ->when($request->search, fn ($q) => $q->search($request->search))
                ->when($request->group_id, fn ($q) => $q->where('attribute_group_id', $request->group_id))
                ->orderBy('sort_order')
                ->paginate(config('app.admin_per_page', 20));
        } catch (\Exception $e) {
            $attributes = new LengthAwarePaginator([], 0, config('app.admin_per_page', 20), 1, ['path' => $request->url()]);
        }

        $groups = collect();
        try {
            $groups = AttributeGroup::active()->get();
        } catch (\Exception $e) {
        }

        return view('cms.attributes.index', compact('attributes', 'groups'));
    }

    public function create($projectCode)
    {
        $groups = AttributeGroup::active()->get();

        return view('cms.attributes.create', compact('groups'));
    }

    public function store(AttributeRequest $request, $projectCode)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $attribute = ProductAttribute::create($data);

        // Create attribute values if provided
        if ($request->has('values')) {
            $usedValues = [];
            $usedSlugs = [];

            foreach ($request->values as $index => $valueData) {
                if (! empty($valueData['value'])) {
                    $value = trim($valueData['value']);
                    $slug = Str::slug($value);

                    // Check for duplicates within the same request
                    if (in_array($value, $usedValues)) {
                        return back()->withErrors(['values' => "Giá trị '{$value}' bị trùng lặp trong danh sách."])->withInput();
                    }

                    if (in_array($slug, $usedSlugs)) {
                        return back()->withErrors(['values' => "Giá trị '{$value}' tạo ra slug trùng lặp."])->withInput();
                    }

                    $usedValues[] = $value;
                    $usedSlugs[] = $slug;

                    ProductAttributeValue::create([
                        'product_attribute_id' => $attribute->id,
                        'value' => $value,
                        'slug' => $slug,
                        'display_value' => $valueData['display_value'] ?? null,
                        'color_code' => $valueData['color_code'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('project.admin.attributes.index', $projectCode)
            ->with('alert', [
                'type' => 'success',
                'message' => 'Thuộc tính đã được tạo thành công!',
                'details' => "Thuộc tính '{$data['name']}' với ".count($request->values ?? []).' giá trị đã được thêm.',
            ]);
    }

    public function show($projectCode, $id)
    {
        $attribute = ProductAttribute::with(['group', 'values'])->findOrFail($id);

        return view('cms.attributes.show', compact('attribute'));
    }

    public function edit($projectCode, $id)
    {
        $attribute = ProductAttribute::with('values')->findOrFail($id);
        $groups = AttributeGroup::active()->get();

        return view('cms.attributes.edit', compact('attribute', 'groups'));
    }

    public function update(AttributeRequest $request, $projectCode, $id)
    {
        $attribute = ProductAttribute::findOrFail($id);

        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $attribute->update($data);

        // Update attribute values
        if ($request->has('values')) {
            // Delete existing values
            $attribute->values()->delete();

            // Create new values with duplicate checking
            $usedValues = [];
            $usedSlugs = [];

            foreach ($request->values as $index => $valueData) {
                if (! empty($valueData['value'])) {
                    $value = trim($valueData['value']);
                    $slug = Str::slug($value);

                    // Check for duplicates within the same request
                    if (in_array($value, $usedValues)) {
                        return back()->withErrors(['values' => "Giá trị '{$value}' bị trùng lặp trong danh sách."])->withInput();
                    }

                    if (in_array($slug, $usedSlugs)) {
                        return back()->withErrors(['values' => "Giá trị '{$value}' tạo ra slug trùng lặp."])->withInput();
                    }

                    $usedValues[] = $value;
                    $usedSlugs[] = $slug;

                    ProductAttributeValue::create([
                        'product_attribute_id' => $attribute->id,
                        'value' => $value,
                        'slug' => $slug,
                        'display_value' => $valueData['display_value'] ?? null,
                        'color_code' => $valueData['color_code'] ?? null,
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('project.admin.attributes.index', $projectCode)
            ->with('alert', [
                'type' => 'success',
                'message' => 'Thuộc tính đã được cập nhật thành công!',
                'details' => "Thuộc tính '{$data['name']}' với ".count($request->values ?? []).' giá trị đã được cập nhật.',
            ]);
    }

    public function destroy($projectCode, $id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        $attribute->delete();

        return redirect()->route('project.admin.attributes.index', $projectCode)
            ->with('alert', [
                'type' => 'success',
                'message' => 'Thuộc tính đã được xóa thành công!',
                'details' => "Thuộc tính '{$attribute->name}' đã được xóa khỏi hệ thống.",
            ]);
    }

    // ===== ATTRIBUTE VALUES =====
    public function indexValues($projectCode, $attributeId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);
        $values = $attribute->values()->orderBy('sort_order')->get();

        return view('cms.attributes.values.index', compact('attribute', 'values'));
    }

    public function createValue($projectCode, $attributeId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);

        return view('cms.attributes.values.create', compact('attribute'));
    }

    public function storeValue(AttributeValueRequest $request, $projectCode, $attributeId)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['value']);
        $data['product_attribute_id'] = $attributeId;

        // Check if slug is unique within the same attribute
        $existingSlug = ProductAttributeValue::where('product_attribute_id', $attributeId)
            ->where('slug', $data['slug'])
            ->exists();

        if ($existingSlug) {
            return back()->withErrors(['value' => 'Giá trị này tạo ra slug trùng lặp trong thuộc tính.'])->withInput();
        }

        ProductAttributeValue::create($data);

        return redirect()->route('project.admin.attributes.show', [$projectCode, $attributeId])
            ->with('success', 'Giá trị thuộc tính đã được tạo thành công.');
    }

    public function editValue($projectCode, $attributeId, $valueId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);
        $value = ProductAttributeValue::findOrFail($valueId);

        return view('cms.attributes.values.edit', compact('attribute', 'value'));
    }

    public function updateValue(AttributeValueRequest $request, $projectCode, $attributeId, $valueId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);
        $value = ProductAttributeValue::findOrFail($valueId);

        $data = $request->validated();
        $data['slug'] = Str::slug($data['value']);

        // Check if slug is unique within the same attribute (excluding current record)
        $existingSlug = ProductAttributeValue::where('product_attribute_id', $attributeId)
            ->where('slug', $data['slug'])
            ->where('id', '!=', $valueId)
            ->exists();

        if ($existingSlug) {
            return back()->withErrors(['value' => 'Giá trị này tạo ra slug trùng lặp trong thuộc tính.'])->withInput();
        }

        $value->update($data);

        return redirect()->route('project.admin.attributes.show', [$projectCode, $attribute->id])
            ->with('success', 'Giá trị thuộc tính đã được cập nhật thành công.');
    }

    public function destroyValue($projectCode, $attributeId, $valueId)
    {
        $attribute = ProductAttribute::findOrFail($attributeId);
        $value = ProductAttributeValue::findOrFail($valueId);

        $value->delete();

        return redirect()->route('project.admin.attributes.show', [$projectCode, $attribute->id])
            ->with('success', 'Giá trị thuộc tính đã được xóa thành công.');
    }
}
