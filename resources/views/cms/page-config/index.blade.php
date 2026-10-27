@extends('cms.layouts.app')

@section('title', 'Cấu hình Page')
@section('page-title', 'Cấu hình Page')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-bold mb-4">Danh sách Page</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($pages as $key => $name)
        <a href="{{ isset($currentProject) ? route('project.admin.page-config.edit', [$currentProject->code, $key]) : route('cms.page-config.edit', $key) }}" class="border rounded-lg p-4 hover:border-blue-500 hover:bg-blue-50 transition">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-semibold">{{ $name }}</h4>
                    <p class="text-sm text-gray-500">{{ $key }}</p>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
