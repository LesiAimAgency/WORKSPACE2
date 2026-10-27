{{-- MODIFIED: 2025-12-19 --}}
@extends('cms.layouts.app')

@section('title', 'Chi tiết thuộc tính')
@section('page-title', 'Thuộc tính: ' . $attribute->name)

@section('content')
<div class="flex justify-between items-center mb-6">
    <a href="{{ route('project.admin.attributes.index', request()->route('projectCode')) }}" class="text-blue-600 hover:text-blue-900">
        ← Quay lại danh sách
    </a>
    
    <div class="flex gap-2">
        <a href="{{ route('project.admin.attributes.edit', [request()->route('projectCode'), $attribute]) }}" 
           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Sửa thuộc tính
        </a>
        <a href="{{ route('project.admin.attributes.values.create', [request()->route('projectCode'), $attribute]) }}" 
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Thêm giá trị
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Attribute Info -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Thông tin thuộc tính</h3>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm text-gray-500">Tên</dt>
                <dd class="font-medium">{{ $attribute->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Slug</dt>
                <dd class="font-medium">{{ $attribute->slug }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Nhóm</dt>
                <dd class="font-medium">{{ $attribute->group?->name ?? 'Không có' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Loại</dt>
                <dd>
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">{{ $attribute->type }}</span>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Có thể lọc</dt>
                <dd>
                    <span class="px-2 py-1 rounded text-sm {{ $attribute->is_filterable ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $attribute->is_filterable ? 'Có' : 'Không' }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">Bắt buộc</dt>
                <dd>
                    <span class="px-2 py-1 rounded text-sm {{ $attribute->is_required ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $attribute->is_required ? 'Có' : 'Không' }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <!-- Attribute Values -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md h-fit overflow-scroll">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Giá trị thuộc tính ({{ $attribute->values->count() }})</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giá trị</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hiển thị</th>
                    @if($attribute->type === 'color')
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Màu</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($attribute->values as $value)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $value->value }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $value->display_value ?? $value->value }}</td>
                    @if($attribute->type === 'color')
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($value->color_code)
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded border" style="background-color: {{ $value->color_code }}"></div>
                                <span class="ml-2 text-sm">{{ $value->color_code }}</span>
                            </div>
                        @endif
                    </td>
                    @endif
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('project.admin.attributes.values.edit', [request()->route('projectCode'), $attribute, $value]) }}" class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                            <form method="POST" action="{{ route('project.admin.attributes.values.destroy', [request()->route('projectCode'), $attribute, $value]) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                        onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $attribute->type === 'color' ? 4 : 3 }}" class="px-6 py-4 text-center text-gray-500">
                        Chưa có giá trị nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
