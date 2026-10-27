{{-- MODIFIED: 2025-12-19 --}}
@extends('cms.layouts.app')

@section('title', 'Chỉnh sửa giá trị thuộc tính')
@section('page-title', 'Chỉnh sửa giá trị: ' . $value->value)

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
    <form method="POST" action="{{ route('project.admin.attributes.values.update', [request()->route('projectCode'), $attribute->id, $value->id]) }}">
        @csrf @method('PUT')
        
        <div class="mb-6">
            <label for="value" class="block text-sm font-medium text-gray-700 mb-2">Giá trị *</label>
            <input type="text" id="value" name="value" value="{{ old('value', $value->value) }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('value') border-red-500 @enderror"
                   required>
            @error('value')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="display_value" class="block text-sm font-medium text-gray-700 mb-2">Giá trị hiển thị</label>
            <input type="text" id="display_value" name="display_value" value="{{ old('display_value', $value->display_value) }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        @if($attribute->type === 'color')
        <div class="mb-6">
            <label for="color_code" class="block text-sm font-medium text-gray-700 mb-2">Mã màu</label>
            <input type="color" id="color_code" name="color_code" value="{{ old('color_code', $value->color_code ?? '#000000') }}" 
                   class="w-full h-12 border border-gray-300 rounded-lg cursor-pointer">
        </div>
        @endif

        <div class="mb-6">
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Thứ tự hiển thị</label>
            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $value->sort_order) }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('project.admin.attributes.show', [request()->route('projectCode'), $attribute->id]) }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"></a>
                Hủy
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection
