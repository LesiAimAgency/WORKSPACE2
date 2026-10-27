@extends('cms.layouts.app')

@section('title', $title ?? 'Cấu hình')
@section('page-title', $title ?? 'Cấu hình')

@section('content')
@php
$backRoute = route('admin.dashboard');
$saveRoute = route('admin.settings.save');
$isMainSettingsPage = true;
@endphp

<form method="POST" action="{{ $action ?? $saveRoute }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if(isset($method)) @method($method) @endif

    <!-- Back to Settings Index -->
    <div class="mb-4">
        <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Quay lại tất cả cài đặt</span>
        </a>
    </div>

    <!-- Settings Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 pb-3 mb-2 overflow-x-auto">
        <a href="{{ route('admin.settings.group', 'contact') }}" 
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->route('group') === 'contact' ? 'bg-[#EC460B] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            <i class="fa-solid fa-address-book text-xs"></i>
            Thông Tin Liên Hệ
        </a>
        <a href="{{ route('admin.settings.seo') }}" 
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('admin.settings.seo') || (!request()->routeIs('admin.settings.modal-box') && !request()->routeIs('admin.settings.notifications') && request()->route('group') !== 'modal-box' && request()->route('group') !== 'contact-modal' && request()->route('group') !== 'notifications' && request()->route('group') !== 'contact') ? 'bg-[#EC460B] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            <i class="fa-solid fa-magnifying-glass text-xs"></i>
            Cài Đặt SEO &amp; Favicon
        </a>
        <a href="{{ route('admin.settings.modal-box') }}" 
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('admin.settings.modal-box') || request()->route('group') === 'modal-box' || request()->route('group') === 'contact-modal' ? 'bg-[#EC460B] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            <i class="fa-solid fa-window-maximize text-xs"></i>
            Quản Lý Modal Box Liên Hệ
        </a>
        <a href="{{ route('admin.settings.notifications') }}" 
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 {{ request()->routeIs('admin.settings.notifications') || request()->route('group') === 'notifications' ? 'bg-[#EC460B] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            <i class="fa-solid fa-envelope text-xs"></i>
            Cấu Hình Email SMTP
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        @yield('form-content')
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('admin.settings.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition">
            Hủy bỏ
        </a>
        <button type="submit" class="px-6 py-2.5 bg-[#EC460B] text-white rounded-lg hover:bg-[#d03d09] font-semibold transition shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-floppy-disk text-sm"></i>
            Lưu cấu hình
        </button>
    </div>
</form>
@endsection
