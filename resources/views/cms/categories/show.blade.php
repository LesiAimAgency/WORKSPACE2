{{-- MODIFIED: 2025-11-22 --}}
@extends('cms.layouts.app')

@section('title', $category->name)
@section('page-title', $category->name)

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center space-x-6">
        <div class="w-36">
            @if($category->image)
                <img src="{{ $category->image }}" class="w-full rounded-lg object-cover" alt="">
            @else
                <div class="h-36 bg-gray-100 rounded-lg flex items-center justify-center">No Image</div>
            @endif
        </div>
        <div>
            <h2 class="text-xl font-bold">{{ $category->name }}</h2>
            <p class="text-sm text-gray-600">Slug: {{ $category->slug }}</p>
            <p class="mt-2 text-gray-700">{{ $category->description }}</p>

            <div class="mt-4">
                <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.categories.edit', [$currentProject->code, $category->id]) : route('cms.categories.edit', $category->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Sửa</a>
                <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.categories.index', $currentProject->code) : route('cms.categories.index') }}" class="px-4 py-2 ml-2 text-gray-700 border rounded-lg">Quay lại</a>
            </div>
        </div>
    </div>
</div>
@endsection
