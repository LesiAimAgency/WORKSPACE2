@extends('cms.layouts.app')

@section('title', 'Theme Options - Cấu hình Layout')
@section('page-title', 'Theme Options - Cấu hình Layout')

@push('head')
<link rel="preconnect" href="{{ asset('') }}">
<link rel="dns-prefetch" href="{{ asset('') }}">
<style>
img[data-cache-img]{will-change:transform;content-visibility:auto;}
.aspect-video,.aspect-square{contain:layout style paint;}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/theme-options-cache.js') }}" defer></script>
@endpush

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Sub Tabs for Layout -->
    <div class="bg-gray-50 border-b">
        <nav class="flex px-6">
            <a href="?tab=layout" class="px-5 py-3.5 {{ $tab === 'layout' ? 'bg-white border-t-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
                Layout Trang Chi Tiết
            </a>
            <a href="?tab=post-category" class="px-5 py-3.5 {{ $tab === 'post-category' ? 'bg-white border-t-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
                Layout Danh Mục Bài Viết
            </a>
            <a href="?tab=banner" class="px-5 py-3.5 {{ $tab === 'banner' ? 'bg-white border-t-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900 font-medium' }}">
                Cấu Hình Banner
            </a>
        </nav>
    </div>

    <!-- Content -->
    <div class="p-6">
        <form method="POST" action="{{ route('project.admin.theme-options.update', ['projectCode' => request()->segment(1)]) }}?tab={{ $tab }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="tab" value="{{ $tab }}">

            @if($tab === 'post-category')
                @include('cms.theme-options.tabs.post-category')
            @elseif($tab === 'banner')
                @include('cms.theme-options.tabs.banner')
            @else
                @include('cms.theme-options.tabs.layout')
            @endif

            <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-xs transition">Lưu cấu hình Layout</button>
            </div>
        </form>
    </div>
</div>
@endsection
