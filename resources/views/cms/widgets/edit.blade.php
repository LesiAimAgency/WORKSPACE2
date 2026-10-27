@extends('cms.layouts.app')

@section('title', 'Edit Widget')
@section('page-title', 'Edit Widget')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.widgets.update', $widget) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
            <input type="text" name="name" value="{{ $widget->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                <option value="html" {{ $widget->type == 'html' ? 'selected' : '' }}>HTML Widget</option>
                <option value="product_list" {{ $widget->type == 'product_list' ? 'selected' : '' }}>Product List</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Area</label>
            <input type="text" name="area" value="{{ $widget->area }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Settings (JSON)</label>
            <textarea name="settings" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg font-mono text-sm">{{ $widget->settings }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
            <input type="number" name="sort_order" value="{{ $widget->sort_order }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ $widget->is_active ? 'checked' : '' }} class="mr-2">
                <span class="text-sm font-medium text-gray-700">Active</span>
            </label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.widgets.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-[#98191F] text-white rounded-lg hover:bg-[#7a1419]">Update Widget</button>
        </div>
    </form>
</div>
@endsection
