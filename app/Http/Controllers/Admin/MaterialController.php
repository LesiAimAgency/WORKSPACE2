<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MaterialController extends Controller
{
    /**
     * Display a listing of materials in admin portal.
     */
    public function index(Request $request): View
    {
        $query = Material::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%")
                    ->orWhere('brand', 'like', "%{$s}%")
                    ->orWhere('category', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('origin')) {
            $query->where('origin', $request->origin);
        }

        if ($request->filled('budget_level')) {
            $query->where('budget_level', $request->budget_level);
        }

        $materials = $query->orderBy('sort_order')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $categories = Material::select('category')->distinct()->pluck('category');

        return view('cms.material-lab.index', compact('materials', 'categories'));
    }

    /**
     * Show form to create a new material.
     */
    public function create(): View
    {
        $surfaces = setting('material_lab_surfaces') ?: [
            ['en' => 'Floor', 'vi' => 'Bề mặt sàn'],
            ['en' => 'Wall', 'vi' => 'Bề mặt tường'],
            ['en' => 'Ceiling', 'vi' => 'Hệ trần'],
            ['en' => 'Loose Furniture', 'vi' => 'Đồ rời'],
            ['en' => 'Detail Surface', 'vi' => 'Chi tiết kỹ thuật'],
        ];
        if (is_string($surfaces)) {
            $surfaces = json_decode($surfaces, true) ?: [];
        }

        return view('cms.material-lab.create', compact('surfaces'));
    }

    /**
     * Store a newly created material.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:materials,code',
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'origin' => 'required|string|in:Vietnam,Import,Both',
            'origin_label' => 'nullable|string|max:255',
            'availability' => 'required|string|max:100',
            'durability_years' => 'required|integer|min:1|max:50',
            'cost_label' => 'nullable|string|max:100',
            'budget_level' => 'required|integer|min:1|max:4',
            'finish' => 'nullable|string|max:255',
            'tech_specs' => 'nullable|string|max:255',
            'application' => 'nullable|string|max:255',
            'suitable_locations' => 'nullable|array',
            'tech_description' => 'nullable|string',
            'experience_description' => 'nullable|string',
            'image_url' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ], [
            'code.required' => 'Vui lòng nhập Mã định danh vật liệu (ví dụ: lvt, terrazzo).',
            'code.unique' => 'Mã định danh vật liệu đã tồn tại.',
            'name.required' => 'Vui lòng nhập Tên vật liệu.',
            'brand.required' => 'Vui lòng nhập Thương hiệu / Nhà cung cấp.',
            'category.required' => 'Vui lòng nhập Phân loại vật liệu.',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = (int) ($request->input('sort_order') ?? 0);
        $validated['suitable_locations'] = $request->input('suitable_locations', []);

        Material::create($validated);

        return redirect()->route('admin.material-lab.index')->with('success', 'Thêm mới vật liệu thành công!');
    }

    /**
     * Show form to edit an existing material.
     */
    public function edit(Material $material): View
    {
        $surfaces = setting('material_lab_surfaces') ?: [
            ['en' => 'Floor', 'vi' => 'Bề mặt sàn'],
            ['en' => 'Wall', 'vi' => 'Bề mặt tường'],
            ['en' => 'Ceiling', 'vi' => 'Hệ trần'],
            ['en' => 'Loose Furniture', 'vi' => 'Đồ rời'],
            ['en' => 'Detail Surface', 'vi' => 'Chi tiết kỹ thuật'],
        ];
        if (is_string($surfaces)) {
            $surfaces = json_decode($surfaces, true) ?: [];
        }

        return view('cms.material-lab.edit', compact('material', 'surfaces'));
    }

    /**
     * Update material in storage.
     */
    public function update(Request $request, Material $material): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:materials,code,' . $material->id,
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'origin' => 'required|string|in:Vietnam,Import,Both',
            'origin_label' => 'nullable|string|max:255',
            'availability' => 'required|string|max:100',
            'durability_years' => 'required|integer|min:1|max:50',
            'cost_label' => 'nullable|string|max:100',
            'budget_level' => 'required|integer|min:1|max:4',
            'finish' => 'nullable|string|max:255',
            'tech_specs' => 'nullable|string|max:255',
            'application' => 'nullable|string|max:255',
            'suitable_locations' => 'nullable|array',
            'tech_description' => 'nullable|string',
            'experience_description' => 'nullable|string',
            'image_url' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = (int) ($request->input('sort_order') ?? 0);
        $validated['suitable_locations'] = $request->input('suitable_locations', []);

        $material->update($validated);

        return redirect()->route('admin.material-lab.index')->with('success', 'Cập nhật vật liệu thành công!');
    }

    /**
     * Delete a material.
     */
    public function destroy(Material $material): RedirectResponse
    {
        $material->delete();

        return redirect()->route('admin.material-lab.index')->with('success', 'Đã xóa vật liệu thành công!');
    }

    /**
     * Toggle active state via AJAX/POST.
     */
    public function toggleActive(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $material = Material::findOrFail($id);
        $material->is_active = ! $material->is_active;
        $material->save();

        return response()->json([
            'success' => true,
            'is_active' => $material->is_active,
            'message' => $material->is_active ? 'Đã kích hoạt hiển thị' : 'Đã ẩn vật liệu',
        ]);
    }

    /**
     * View configuration page for Material Lab.
     */
    public function config(): View
    {
        $typologies = setting('material_lab_typologies') ?: [];
        if (is_string($typologies)) {
            $typologies = json_decode($typologies, true) ?: [];
        }

        $surfaces = setting('material_lab_surfaces') ?: [];
        if (is_string($surfaces)) {
            $surfaces = json_decode($surfaces, true) ?: [];
        }

        $title = setting('material_lab_title', 'Thư viện vật liệu');
        $subtitle = setting('material_lab_subtitle', 'Material Lab • V1.0');
        $step1Title = setting('material_lab_step1_title', 'Công trình của bạn là gì?');
        $step1Desc = setting('material_lab_step1_desc', 'Chọn loại hình để gợi ý vật liệu phù hợp.');
        $step2Title = setting('material_lab_step2_title', 'Vị trí nào cần vật liệu?');
        $step2Desc = setting('material_lab_step2_desc', 'Chọn bề mặt để lọc vật liệu phù hợp.');
        $step3Title = setting('material_lab_step3_title', 'Khu vực chức năng nào?');
        $step4Title = setting('material_lab_step4_title', 'Nhu cầu thực tế?');
        $step4Desc = setting('material_lab_step4_desc', 'Chọn độ bền, ngân sách, nguồn gốc và tồn kho.');

        return view('cms.material-lab.config', compact(
            'typologies',
            'surfaces',
            'title',
            'subtitle',
            'step1Title',
            'step1Desc',
            'step2Title',
            'step2Desc',
            'step3Title',
            'step4Title',
            'step4Desc'
        ));
    }

    /**
     * Save configuration for Material Lab.
     */
    public function saveConfig(Request $request): RedirectResponse
    {
        $fields = [
            'material_lab_title' => $request->input('material_lab_title', 'Thư viện vật liệu'),
            'material_lab_subtitle' => $request->input('material_lab_subtitle', 'Material Lab • V1.0'),
            'material_lab_step1_title' => $request->input('material_lab_step1_title', 'Công trình của bạn là gì?'),
            'material_lab_step1_desc' => $request->input('material_lab_step1_desc', 'Chọn loại hình để gợi ý vật liệu phù hợp.'),
            'material_lab_step2_title' => $request->input('material_lab_step2_title', 'Vị trí nào cần vật liệu?'),
            'material_lab_step2_desc' => $request->input('material_lab_step2_desc', 'Chọn bề mặt để lọc vật liệu phù hợp.'),
            'material_lab_step3_title' => $request->input('material_lab_step3_title', 'Khu vực chức năng nào?'),
            'material_lab_step4_title' => $request->input('material_lab_step4_title', 'Nhu cầu thực tế?'),
            'material_lab_step4_desc' => $request->input('material_lab_step4_desc', 'Chọn độ bền, ngân sách, nguồn gốc và tồn kho.'),
        ];

        foreach ($fields as $key => $val) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['payload' => json_encode(['value' => $val]), 'value' => $val, 'group' => 'general', 'updated_at' => now()]
            );
        }

        return redirect()->route('admin.material-lab.config')->with('success', 'Đã lưu cấu hình Material Lab thành công!');
    }
}
