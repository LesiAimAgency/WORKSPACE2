{{-- MODIFIED: 2025-12-19 --}}
@extends('cms.layouts.app')

@section('title', 'Chỉnh sửa nhóm thuộc tính')
@section('page-title', 'Chỉnh sửa nhóm thuộc tính')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
    <form method="POST" action="{{ route('project.admin.attributes.groups.update', [request()->route('projectCode'), $group]) }}">
        @csrf @method('PUT')
        
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên nhóm *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $group->name) }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                   required>
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug', $group->slug) }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror">
            @error('slug')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $group->description) }}</textarea>
            @error('description')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Thứ tự hiển thị</label>
            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $group->sort_order) }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $group->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Kích hoạt</span>
            </label>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('project.admin.attributes.groups.index', request()->route('projectCode')) }}" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                Hủy
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Cập nhật
            </button>
        </div>
    </form>
</div>
@endsection
