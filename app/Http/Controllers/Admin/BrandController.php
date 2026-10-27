<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\ProjectBrand;
use App\Traits\HasCrudAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    use HasCrudAlerts;

    public function index(Request $request)
    {
        // Multi-site: Always require project context
        $projectCode = request()->route('projectCode');
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        try {
            $query = ProjectBrand::query();

            if ($request->status === 'trashed') {
                $query->onlyTrashed();
            }

            $brands = $query->when($request->search, fn ($q) => $q->search($request->search))
                ->orderBy('name')
                ->paginate(config('app.admin_per_page', 20));

            // Debug: Check what we're getting
            if ($brands->count() > 0) {
                $firstBrand = $brands->first();
                if (! is_object($firstBrand) || ! ($firstBrand instanceof ProjectBrand)) {
                    \Log::error('Brand pagination issue', [
                        'first_item_type' => gettype($firstBrand),
                        'first_item_class' => is_object($firstBrand) ? get_class($firstBrand) : 'not_object',
                        'brands_count' => $brands->count(),
                        'brands_type' => gettype($brands),
                    ]);
                }
            }

            return view('cms.brands.index', compact('brands'));
        } catch (\Exception $e) {
            \Log::error('Brand index error: '.$e->getMessage());

            // Fallback: get brands without pagination
            $brands = ProjectBrand::orderBy('name')->get();

            return view('cms.brands.index', compact('brands'));
        }
    }

    public function create()
    {
        // Multi-site: Always require project context
        $projectCode = request()->route('projectCode');
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        return view('cms.brands.create');
    }

    public function store(BrandRequest $request)
    {
        $projectCode = request()->route('projectCode');
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        $data = $request->validated();

        // Check for duplicate name and show warning
        $existingBrand = ProjectBrand::where('name', $data['name'])->first();
        if ($existingBrand) {
            return back()
                ->withInput()
                ->with('alert', [
                    'type' => 'warning',
                    'message' => "Cảnh báo: Tên thương hiệu '{$data['name']}' đã tồn tại. Vui lòng nhập tên khác.",
                ]);
        }

        // Auto-generate slug if not provided
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        // Handle logo URL from media manager
        if ($request->filled('logo')) {
            $data['logo'] = $request->input('logo');
        }

        $brand = ProjectBrand::create($data);

        $this->alertCreated('thương hiệu', "Thương hiệu '{$brand->name}' đã được thêm vào hệ thống.");

        return redirect()->route('project.admin.brands.index', $projectCode);
    }

    public function show($projectCode, $id)
    {
        // Multi-site: Always require project context
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        $brand = ProjectBrand::findOrFail($id);

        return view('cms.brands.show', compact('brand'));
    }

    public function edit($projectCode, $id)
    {
        // Multi-site: Always require project context
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        $brand = ProjectBrand::findOrFail($id);

        return view('cms.brands.edit', compact('brand'));
    }

    public function update(BrandRequest $request, $projectCode, $id)
    {
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        $brand = ProjectBrand::findOrFail($id);
        $data = $request->validated();

        // Check for duplicate name (excluding current brand) and show warning
        $existingBrand = ProjectBrand::where('name', $data['name'])
            ->where('id', '!=', $id)
            ->first();
        if ($existingBrand) {
            return back()
                ->withInput()
                ->with('alert', [
                    'type' => 'warning',
                    'message' => "Cảnh báo: Tên thương hiệu '{$data['name']}' đã tồn tại. Vui lòng nhập tên khác.",
                ]);
        }

        // Auto-generate slug if not provided
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        // Handle logo URL from media manager
        if ($request->filled('logo')) {
            $data['logo'] = $request->input('logo');
        } elseif ($request->has('logo') && empty($request->input('logo'))) {
            // If logo field is present but empty, clear the logo
            $data['logo'] = null;
        }

        $brand->update($data);

        $this->alertUpdated('thương hiệu', "Thương hiệu '{$brand->name}' đã được cập nhật.");

        return redirect()->route('project.admin.brands.index', $projectCode);
    }

    public function destroy($projectCode, $id)
    {
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        $brand = ProjectBrand::findOrFail($id);
        $brandName = $brand->name;
        $brand->delete();

        $this->alertDeleted('thương hiệu', "Thương hiệu '{$brandName}' đã được xóa khỏi hệ thống.");

        return redirect()->route('project.admin.brands.index', $projectCode);
    }

    public function restore($projectCode, $id)
    {
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        $brand = ProjectBrand::withTrashed()->findOrFail($id);
        $brand->restore();
        $this->alertSuccess('Đã khôi phục thương hiệu thành công.');

        return redirect()->back();
    }

    public function forceDelete($projectCode, $id)
    {
        if (! $projectCode) {
            abort(404, 'Project context required');
        }

        $brand = ProjectBrand::withTrashed()->findOrFail($id);
        $brand->forceDelete();
        $this->alertSuccess('Đã xóa vĩnh viễn thương hiệu thành công.');

        return redirect()->back();
    }
}
