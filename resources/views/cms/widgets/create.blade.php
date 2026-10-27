@extends('cms.layouts.app')

@section('title', 'Create Widget')
@section('page-title', 'Create Widget')

@section('content')
<!-- Debug: Current URL: {{ url()->current() }} -->
@php
    $cCode = is_object($currentProject ?? null) ? ($currentProject->code ?? '') : (is_string($currentProject ?? null) ? $currentProject : '');
@endphp
<!-- Debug: Has Project: {{ $cCode ? 'Yes - ' . $cCode : 'No' }} -->
<!-- Debug: Form Action: {{ $cCode ? route('project.admin.widgets.store', $cCode) : route('cms.widgets.store') }} -->
<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.widgets.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                <option value="html">HTML Widget</option>
                <option value="product_list">Product List</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Area</label>
            <input type="text" name="area" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="e.g., homepage-top, sidebar" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Settings (JSON)</label>
            <textarea name="settings" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg font-mono text-sm" placeholder='{"content": "Hello World"}'>{}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
            <input type="number" name="sort_order" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.widgets.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-[#98191F] text-white rounded-lg hover:bg-[#7a1419]">Create Widget</button>
        </div>
    </form>
</div>
@endsection
