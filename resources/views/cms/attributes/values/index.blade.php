{{-- MODIFIED: 2025-12-19 --}}
@extends('cms.layouts.app')

@section('title', 'Quản lý giá trị thuộc tính')
@section('page-title', 'Giá trị thuộc tính: ' . $attribute->name)

@section('content')
<div class="flex justify-between items-center mb-6">
    <a href="{{ route('project.admin.attributes.index', request()->route('projectCode')) }}" class="text-blue-600 hover:text-blue-900">
        ← Quay lại danh sách
    </a>
    
    <a href="{{ route('project.admin.attributes.values.create', [request()->route('projectCode'), $attribute->id]) }}" 
       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
        Thêm giá trị
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
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
            @forelse($values as $value)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $value->value }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $value->display_value ?? $value->value }}</td>
                @if($attribute->type === 'color')
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($value->color_code)
                        <div class="flex items-center">
                            <div class="w-6 h-6 rounded" style="background-color: {{ $value->color_code }}"></div>
                            <span class="ml-2 text-sm">{{ $value->color_code }}</span>
                        </div>
                    @endif
                </td>
                @endif
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <a href="{{ route('project.admin.attributes.values.edit', [request()->route('projectCode'), $attribute->id, $value->id]) }}" class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                        <form method="POST" action="{{ route('project.admin.attributes.values.destroy', [request()->route('projectCode'), $attribute->id, $value->id]) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                    onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $attribute->type === 'color' ? 4 : 3 }}" class="px-6 py-4 text-center text-gray-500">Không có giá trị nào</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
