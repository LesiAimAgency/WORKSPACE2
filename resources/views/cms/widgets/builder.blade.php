@extends('cms.layouts.app')

@section('title', 'Quản lý Widget')
@section('page-title', 'Widget Manager')

@php
    $baseUrl = '/admin';

    $widgetAreas = [
        'homepage-main'  => ['label' => 'Trang chủ', 'icon' => 'home'],
        'about-main'     => ['label' => 'Trang Giới Thiệu (About Us)', 'icon' => 'information-circle'],
        // 'sidebar'        => ['label' => 'Sidebar', 'icon' => 'view-list'],
        // 'footer'         => ['label' => 'Footer', 'icon' => 'template'],
        // 'blog-sidebar'   => ['label' => 'Blog Sidebar', 'icon' => 'document-text'],
    ];
@endphp

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* Modern Select2 Tailwind Integration */
.select2-container {
    width: 100%;
}
.select2-container--default .select2-selection--single {
    height: 42px;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    background-color: #ffffff;
    display: flex;
    align-items: center;
    transition: all 0.15s ease-in-out;
}
.select2-container--default.select2-container--open .select2-selection--single,
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    outline: none;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px;
    padding-left: 12px;
    padding-right: 32px;
    color: #1f2937;
    font-size: 0.875rem;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #9ca3af;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    right: 8px;
}
.select2-container--default .select2-selection--single .select2-selection__clear {
    margin-right: 12px;
    color: #9ca3af;
    font-weight: bold;
}

/* Select2 Multiple / Tags */
.select2-container--default .select2-selection--multiple {
    min-height: 42px;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    background-color: #ffffff;
    padding: 3px 6px;
    transition: all 0.15s ease-in-out;
}
.select2-container--default.select2-container--open .select2-selection--multiple,
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    outline: none;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
    border-radius: 0.375rem;
    padding: 3px 8px 3px 22px;
    font-size: 0.8125rem;
    font-weight: 500;
    margin-top: 3px;
    margin-bottom: 3px;
    position: relative;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    position: absolute;
    left: 5px;
    top: 50%;
    transform: translateY(-50%);
    color: #3b82f6;
    border: none;
    font-size: 14px;
    line-height: 1;
    padding: 0;
    margin: 0;
    background: transparent;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #ef4444;
}
.select2-container--default .select2-search--inline .select2-search__field {
    margin-top: 5px;
    font-size: 0.875rem;
    color: #1f2937;
    font-family: inherit;
    padding-left: 6px;
}

/* Dropdown Menu */
.select2-dropdown {
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    background-color: #ffffff;
    z-index: 99999;
    overflow: hidden;
}
.select2-search--dropdown {
    padding: 8px;
}
.select2-search--dropdown .select2-search__field {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 7px 10px;
    font-size: 0.875rem;
    outline: none;
    width: 100%;
    box-sizing: border-box;
}
.select2-search--dropdown .select2-search__field:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
}
.select2-results__option {
    padding: 8px 12px;
    font-size: 0.875rem;
    color: #374151;
    transition: background-color 0.1s ease;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #2563eb;
    color: #ffffff;
}
.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #eff6ff;
    color: #1d4ed8;
    font-weight: 500;
}

/* CKEditor modal & theme integration */
:root {
    --ck-z-default: 1000;
    --ck-z-modal: 12000;
}
.ck.ck-editor {
    width: 100%;
}
.ck-editor__editable {
    min-height: 120px;
    max-height: 380px;
    font-size: 0.9rem;
    line-height: 1.6;
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}
.ck-toolbar {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    border-color: #d1d5db;
    background-color: #f8fafc;
}
.ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
    border-color: #d1d5db;
}
.ck.ck-editor__main>.ck-editor__editable.ck-focused {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}
</style>
@endpush

@section('content')
<x-media-picker-modal />

{{-- Toast Notification --}}
<div id="wm-toast" class="fixed top-5 right-5 z-[9999] hidden">
    <div id="wm-toast-inner" class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl text-sm font-medium text-white min-w-[280px]">
        <svg id="wm-toast-icon" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
        <span id="wm-toast-msg"></span>
    </div>
</div>

<div id="config-drawer" class="fixed inset-0 z-[100] hidden items-center justify-center p-3 sm:p-6" style="background:rgba(15,23,42,0.75); backdrop-filter: blur(4px);">
    <div class="bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden w-full max-w-3xl max-h-[90vh] border border-gray-200">

      
        <div class="flex items-center justify-between px-6 py-4 bg-gray-900 text-white flex-shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-blue-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-bold text-base leading-tight truncate text-white" id="drawer-title">Cấu hình Widget</p>
                        <span class="hidden sm:inline-block px-2 py-0.5 text-[11px] font-mono bg-gray-800 text-blue-300 rounded border border-gray-700" id="drawer-subtitle"></span>
                    </div>
                    <p class="text-xs text-gray-400 leading-tight truncate mt-0.5">Tùy biến các tham số hiển thị cho widget này</p>
                </div>
            </div>

            <button id="btn-close-drawer" class="p-2 hover:bg-gray-800 text-gray-400 hover:text-white rounded-lg transition flex-shrink-0" title="Đóng (ESC)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ── Scrollable Form fields ───────────────────────────── --}}
        <div class="flex-1 overflow-y-auto px-6 py-6 bg-gray-50/70" id="drawer-body">
            <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                <svg class="w-6 h-6 animate-spin mb-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span class="text-sm font-medium text-gray-500">Đang tải biểu mẫu cấu hình...</span>
            </div>
        </div>

        {{-- ── Action bar ───────────────────────────────────────── --}}
        <div class="flex-shrink-0 px-6 py-4 border-t border-gray-200 bg-white flex items-center justify-end gap-3">
            <button id="btn-cancel-drawer"
                    class="py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-lg transition">
                Huỷ
            </button>
            <button id="btn-save-config"
                    class="flex items-center justify-center gap-2 py-2.5 px-6 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Lưu cấu hình
            </button>
        </div>
    </div>
</div>


{{-- Page Heading --}}
<div class="mb-6">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-gray-400 mb-3">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>Quản trị</span>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span>Giao diện</span>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-600 font-medium">Widget Manager</span>
    </nav>

    {{-- Title row --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Widget Manager</h1>
            <p class="text-sm text-gray-500 mt-1">Thêm, sắp xếp và cấu hình các block nội dung cho từng khu vực website.</p>
        </div>
        <button id="btn-clear-cache" class="flex-shrink-0 px-3 py-2 text-xs text-gray-600 hover:bg-gray-100 rounded-lg border border-gray-200 transition flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Xoá cache
        </button>
    </div>

    {{-- Stats bar --}}
    @php
        $totalActive = collect($existingWidgets)->sum(fn($ws) => $ws->count());
        $totalAvailable = array_sum(array_map('count', $availableWidgets));
        $totalAreas = count($widgetAreas);
    @endphp
    <div class="flex flex-wrap items-center gap-3 mt-4">
        <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-lg px-3 py-2 shadow-sm">
            <div class="w-6 h-6 bg-blue-50 rounded-md flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 leading-none">Khu vực</p>
                <p class="text-sm font-bold text-gray-800 leading-tight">{{ $totalAreas }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-lg px-3 py-2 shadow-sm">
            <div class="w-6 h-6 bg-emerald-50 rounded-md flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 leading-none">Đang active</p>
                <p class="text-sm font-bold text-gray-800 leading-tight" id="stat-active">{{ $totalActive }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2 bg-white border border-gray-100 rounded-lg px-3 py-2 shadow-sm">
            <div class="w-6 h-6 bg-purple-50 rounded-md flex items-center justify-center">
                <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 leading-none">Widget có sẵn</p>
                <p class="text-sm font-bold text-gray-800 leading-tight">{{ $totalAvailable }}</p>
            </div>
        </div>
        <div class="flex items-center gap-1.5 text-xs text-gray-400 ml-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Click vào widget bên phải để thêm vào khu vực đang chọn
        </div>
    </div>
</div>

{{-- Main 2-Column Layout --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    {{-- COL 1: Widget Areas (7/12) --}}
    <div class="lg:col-span-7 xl:col-span-7 space-y-4">
        @foreach($widgetAreas as $areaKey => $areaInfo)
            @php $areaWidgets = $existingWidgets[$areaKey] ?? collect([]); @endphp

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Area Header --}}
                <div class="flex items-center gap-3 px-5 py-3.5 bg-gray-50 border-b">
                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                        @if($areaInfo['icon'] === 'home')
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        @elseif($areaInfo['icon'] === 'view-list')
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        @elseif($areaInfo['icon'] === 'template')
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                            </svg>
                        @else
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-sm text-gray-800">{{ $areaInfo['label'] }}</h3>
                        <p class="text-xs text-gray-400 font-mono">{{ $areaKey }}</p>
                    </div>
                    <span class="bg-blue-50 text-blue-600 text-xs font-semibold px-2.5 py-1 rounded-full border border-blue-100" id="badge-{{ $areaKey }}">
                        {{ $areaWidgets->count() }} widget
                    </span>
                </div>

                {{-- Widget List --}}
                <div class="widget-area-list divide-y divide-gray-100" id="area-list-{{ $areaKey }}" data-area="{{ $areaKey }}">
                    @forelse($areaWidgets as $w)
                        <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50/80 transition group widget-row select-none"
                             data-id="{{ $w['id'] ?? '' }}"
                             data-type="{{ $w['type'] }}"
                             data-area="{{ $areaKey }}"
                             data-name="{{ $w['name'] }}">
                            <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-300 hover:text-blue-600 transition-colors p-1.5 -ml-1 flex-shrink-0" title="Kéo thả lên/xuống để đổi vị trí">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                </svg>
                            </div>
                            <div class="w-1.5 h-8 bg-blue-500 rounded-full flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $w['name'] }}</p>
                                <p class="text-xs text-gray-400 font-mono truncate">{{ $w['type'] }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition">
                                <button class="btn-open-config inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition"
                                        data-id="{{ $w['id'] ?? '' }}"
                                        data-type="{{ $w['type'] }}"
                                        data-name="{{ $w['name'] }}"
                                        data-settings="{{ htmlspecialchars(json_encode($w['settings'] ?? []), ENT_QUOTES) }}"
                                        data-variant="{{ $w['variant'] ?? 'default' }}"
                                        title="Cấu hình">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Cấu hình</span>
                                </button>
                                <button class="btn-remove-widget p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        data-id="{{ $w['id'] ?? '' }}"
                                        data-area="{{ $areaKey }}"
                                        title="Xoá">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center empty-area-placeholder" id="empty-{{ $areaKey }}">
                            <svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <p class="text-xs text-gray-400">Chưa có widget. Chọn từ danh sách bên phải để thêm.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- COL 2: Available Widgets (5/12) --}}
    <div class="lg:col-span-5 xl:col-span-5 sticky top-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Header --}}
            <div class="px-4 py-3 bg-gray-50 border-b flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <h3 class="font-semibold text-sm text-gray-800">Widget có sẵn</h3>
                </div>
                <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                    {{ array_sum(array_map('count', $availableWidgets)) }}
                </span>
            </div>

            {{-- Target Area & Search --}}
            <div class="p-3 border-b space-y-2.5 bg-gray-50/40">
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-medium text-gray-600 mb-1">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Thêm vào khu vực:
                    </label>
                    <select id="targetArea" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 bg-white outline-none font-medium text-gray-700">
                        @foreach($widgetAreas as $areaKey => $areaInfo)
                            <option value="{{ $areaKey }}">{{ $areaInfo['label'] }} ({{ $areaKey }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" id="widgetSearch"
                           placeholder="Tìm kiếm widget..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none bg-white">
                </div>
            </div>

            {{-- Widget Categories List --}}
            <div class="overflow-y-auto max-h-[calc(100vh-270px)] divide-y divide-gray-50" id="widgetTemplatesList">
                @forelse($availableWidgets as $category => $categoryWidgets)
                    @php
                        $isSingleCat = count($availableWidgets) === 1;
                        $isOpen = $isSingleCat || $loop->first;
                    @endphp
                    <div class="widget-category" data-category="{{ $category }}">
                        {{-- Category toggle button --}}
                        <button type="button"
                                data-cat="{{ $category }}"
                                class="btn-toggle-category w-full flex items-center justify-between px-4 py-2.5 text-left hover:bg-gray-50 transition border-b border-gray-100">
                            <span class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200 category-arrow" id="arrow-{{ $category }}"
                                     style="{{ $isOpen ? 'transform: rotate(90deg);' : '' }}"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                {{ ucfirst(str_replace('_', ' ', $category)) }}
                            </span>
                            <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">{{ count($categoryWidgets) }}</span>
                        </button>

                        {{-- Category items --}}
                        <div class="category-content {{ $isOpen ? '' : 'hidden' }} divide-y divide-gray-50" id="category-{{ $category }}">
                            @foreach($categoryWidgets as $widget)
                                <div class="widget-template flex items-center justify-between gap-3 px-4 py-2.5 cursor-pointer hover:bg-blue-50/70 transition group border-l-2 border-transparent hover:border-blue-400"
                                     data-type="{{ $widget['type'] }}"
                                     data-name="{{ $widget['metadata']['name'] ?? ($widget['name'] ?? $widget['type']) }}"
                                     data-cat="{{ $category }}"
                                     title="Click để thêm widget này vào khu vực">
                                    <div class="w-7 h-7 bg-gray-100 rounded-md flex items-center justify-center flex-shrink-0 group-hover:bg-blue-100 transition">
                                        <svg class="w-3.5 h-3.5 text-gray-500 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate group-hover:text-blue-700 transition">
                                            {{ $widget['metadata']['name'] ?? ($widget['name'] ?? $widget['type']) }}
                                        </p>
                                        <p class="text-xs text-gray-400 truncate">{{ $widget['type'] }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-1 text-xs text-blue-600 bg-blue-50 group-hover:bg-blue-600 group-hover:text-white px-2 py-1 rounded transition flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Thêm</span>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="text-sm">Không có widget nào khả dụng</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    'use strict';

    const BASE_URL = '{{ $baseUrl }}';
    const CSRF     = '{{ csrf_token() }}' || document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ── SAFE JSON ────────────────────────────────────────────────────────
    // Safely parse JSON — throws descriptive error if server returns HTML
    function safeJson(r) {
        var ct = r.headers.get('content-type') || '';
        if (! ct.includes('application/json') && ! ct.includes('text/json')) {
            return r.text().then(function (body) {
                var hint = '';
                if (r.status === 419) { hint = ' (CSRF token hết hạn — thử reload trang)'; }
                else if (r.status === 401 || r.status === 302) { hint = ' (chưa đăng nhập)'; }
                else if (r.status === 500) { hint = ' (lỗi server — kiểm tra log)'; }
                throw new Error('HTTP ' + r.status + hint + '. Body: ' + body.substring(0, 120));
            });
        }
        return r.json();
    }

    // ── SAFE JSON (ok-only routes: DELETE, PUT) ───────────────────────────
    // For routes that return 204/200 on success and JSON only on error
    function safeJsonOrOk(r) {
        if (r.ok || r.redirected) { return Promise.resolve({ success: true }); }
        return safeJson(r);
    }

    let drawerWidgetId       = null;
    let drawerWidgetType     = null;
    let drawerWidgetArea     = null;

    // ── SELECT2 INTEGRATION ─────────────────────────────────────────
    window.initSelect2 = function (container) {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
            return;
        }
        var $context = container ? $(container) : $('#drawer-body');
        $context.find('select.select2-enable').each(function () {
            var $select = $(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                try { $select.select2('destroy'); } catch (e) {}
            }
            var isMulti = $select.prop('multiple');
            var placeholderText = $select.attr('data-placeholder') 
                || ($select.find('option[value=""]').first().text() || (isMulti ? 'Gõ tìm kiếm và chọn...' : '-- Chọn --'));

            $select.select2({
                width: '100%',
                dropdownParent: $('#config-drawer'),
                placeholder: placeholderText,
                allowClear: !isMulti,
                closeOnSelect: !isMulti,
                language: {
                    noResults: function () { return 'Không tìm thấy kết quả phù hợp'; },
                    searching: function () { return 'Đang tìm kiếm...'; }
                }
            });

            // Sync with conditional fields (show_if) safely without recursive change event loops
            $select.on('select2:select select2:unselect select2:clear', function () {
                var nativeEvent = new Event('change', { bubbles: true });
                this.dispatchEvent(nativeEvent);
            });
        });
    };

    // ── CKEDITOR INTEGRATION ─────────────────────────────────────────
    window.activeCKEditors = window.activeCKEditors || {};

    window.destroyCKEditors = function () {
        if (window.activeCKEditors) {
            Object.keys(window.activeCKEditors).forEach(function (key) {
                try {
                    if (window.activeCKEditors[key]) {
                        window.activeCKEditors[key].destroy();
                    }
                } catch (e) {}
                delete window.activeCKEditors[key];
            });
        }
    };

    window.syncCKEditors = function () {
        if (window.activeCKEditors) {
            Object.keys(window.activeCKEditors).forEach(function (key) {
                try {
                    var inst = window.activeCKEditors[key];
                    if (!inst) return;
                    var textarea = (inst && inst.sourceElement) ? inst.sourceElement : document.getElementById(key);
                    if (textarea && textarea.isConnected) {
                        var data = inst.getData();
                        textarea.value = typeof window.sanitizeEditorHtml === 'function' ? window.sanitizeEditorHtml(data) : data;
                    }
                } catch (e) {}
            });
        }
    };

    window.initCKEditor = function (container, attempts) {
        attempts = attempts || 0;
        container = container || document.getElementById('drawer-body') || document;

        if (!window.ClassicEditor || !window.CKEditorPlugins) {
            if (attempts < 60) {
                setTimeout(function () {
                    window.initCKEditor(container, attempts + 1);
                }, 100);
            }
            return;
        }

        var targets = container.querySelectorAll('.ckeditor-editor, textarea.ckeditor, [data-editor="ckeditor"], textarea:not([data-no-ckeditor]):not([data-editor="none"]):not(.no-ckeditor):not(.font-mono):not([readonly])');
        targets.forEach(function (el) {
            if (!el.id) {
                el.id = 'ckeditor_' + Math.random().toString(36).substring(2, 9);
            }
            if (window.activeCKEditors[el.id]) {
                var existingInst = window.activeCKEditors[el.id];
                if (existingInst && existingInst.sourceElement && existingInst.sourceElement.isConnected && existingInst.sourceElement === el) {
                    return;
                }
                try { existingInst.destroy(); } catch (e) {}
                delete window.activeCKEditors[el.id];
            }

            var minHeight = el.getAttribute('data-height') || '140px';

            window.ClassicEditor
                .create(el, {
                    plugins: window.CKEditorPlugins,
                    toolbar: {
                        items: [
                            'undo', 'redo', '|',
                            'sourceEditing', '|',
                            'heading', '|',
                            'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'alignment', '|',
                            'bulletedList', 'numberedList', '|',
                            'link', 'insertTable', 'blockQuote', '|',
                            'removeFormat'
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                        ]
                    },
                    htmlSupport: {
                        allow: [
                            {
                                name: /.*/,
                                attributes: true,
                                classes: true,
                                styles: true
                            }
                        ],
                        disallow: [
                            {
                                name: /^(script|iframe|embed|object|noscript|applet|meta|link)$/i
                            },
                            {
                                name: /.*/,
                                attributes: {
                                    key: /^on/i
                                }
                            },
                            {
                                name: /.*/,
                                attributes: {
                                    key: /^(href|src|action|data)$/i,
                                    value: /^javascript:/i
                                }
                            }
                        ]
                    },
                    image: {
                        toolbar: [
                            'imageStyle:inline',
                            'imageStyle:block',
                            'imageStyle:side',
                            '|',
                            'toggleImageCaption',
                            'imageTextAlternative'
                        ]
                    },
                    table: {
                        contentToolbar: [
                            'tableColumn',
                            'tableRow',
                            'mergeTableCells'
                        ]
                    }
                })
                .then(function (editor) {
                    window.activeCKEditors[el.id] = editor;

                    var editable = editor.ui.view.editable.element;
                    if (editable) {
                        editable.style.minHeight = minHeight;
                    }

                    editor.model.document.on('change:data', function () {
                        var rawData = editor.getData();
                        var cleanData = typeof window.sanitizeEditorHtml === 'function' ? window.sanitizeEditorHtml(rawData) : rawData;
                        el.value = cleanData;
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                })
                .catch(function (err) {
                    console.error('CKEditor init error on #' + el.id, err);
                });
        });
    };

    window.addEventListener('ckeditor-module-ready', function () {
        var drawer = document.getElementById('drawer-body');
        if (drawer && typeof window.initCKEditor === 'function') {
            window.initCKEditor(drawer);
        }
    });

    // ── TOAST ──────────────────────────────────────────────────────
    const TOAST_ICONS = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        error:   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        info:    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    };
    const TOAST_BG = { success: 'bg-emerald-500', error: 'bg-red-500', info: 'bg-blue-500' };

    function showToast(msg, type) {
        type = type || 'success';
        const t   = document.getElementById('wm-toast');
        const inn = document.getElementById('wm-toast-inner');
        const ico = document.getElementById('wm-toast-icon');
        const tx  = document.getElementById('wm-toast-msg');
        inn.className = 'flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl text-sm font-medium text-white min-w-[280px] ' + (TOAST_BG[type] || TOAST_BG.success);
        ico.innerHTML = TOAST_ICONS[type] || TOAST_ICONS.success;
        tx.textContent = msg;
        t.classList.remove('hidden');
        clearTimeout(window._wmt);
        window._wmt = setTimeout(function () { t.classList.add('hidden'); }, 3500);
    }

    // ── HELPERS ────────────────────────────────────────────────────
    function escAttr(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function updateBadge(area) {
        var list  = document.getElementById('area-list-' + area);
        var badge = document.getElementById('badge-' + area);
        if (!list || !badge) { return; }
        badge.textContent = list.querySelectorAll('.widget-row').length + ' widget';
    }

    function checkEmptyArea(area) {
        var list = document.getElementById('area-list-' + area);
        if (!list || list.querySelector('.widget-row')) { return; }
        var empty = document.createElement('div');
        empty.id = 'empty-' + area;
        empty.className = 'px-4 py-8 text-center empty-area-placeholder';
        empty.innerHTML =
            '<svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>' +
            '</svg>' +
            '<p class="text-xs text-gray-400">Chưa có widget. Chọn từ danh sách bên phải để thêm.</p>';
        list.appendChild(empty);
    }

    function buildWidgetRow(area, widget) {
        var row = document.createElement('div');
        row.className = 'flex items-center gap-3 px-4 py-3 hover:bg-gray-50/80 transition group widget-row select-none';
        row.dataset.id   = widget.id || '';
        row.dataset.type = widget.type;
        row.dataset.area = area;
        row.dataset.name = widget.name;
        row.innerHTML =
            '<div class="drag-handle cursor-grab active:cursor-grabbing text-gray-300 hover:text-blue-600 transition-colors p-1.5 -ml-1 flex-shrink-0" title="Kéo thả lên/xuống để đổi vị trí">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>' +
                '</svg>' +
            '</div>' +
            '<div class="w-1.5 h-8 bg-blue-500 rounded-full flex-shrink-0"></div>' +
            '<div class="flex-1 min-w-0">' +
                '<p class="text-sm font-semibold text-gray-800 truncate">' + escAttr(widget.name) + '</p>' +
                '<p class="text-xs text-gray-400 font-mono truncate">' + escAttr(widget.type) + '</p>' +
            '</div>' +
            '<div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition">' +
                '<button class="btn-open-config inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition"' +
                    ' data-id="' + escAttr(widget.id || '') + '"' +
                    ' data-type="' + escAttr(widget.type) + '"' +
                    ' data-name="' + escAttr(widget.name) + '"' +
                    ' data-settings="' + escAttr(JSON.stringify(widget.settings || {})) + '"' +
                    ' data-variant="' + escAttr(widget.variant || 'default') + '"' +
                    ' title="Cấu hình">' +
                    '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>' +
                    '</svg>' +
                    '<span>Cấu hình</span>' +
                '</button>' +
                '<button class="btn-remove-widget p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"' +
                    ' data-id="' + escAttr(widget.id || '') + '"' +
                    ' data-area="' + escAttr(area) + '"' +
                    ' title="Xoá">' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>' +
                    '</svg>' +
                '</button>' +
            '</div>';
        return row;
    }

    // ── ADD WIDGET ─────────────────────────────────────────────────
    function addWidget(type, name) {
        var area = document.getElementById('targetArea').value;
        if (!area) { showToast('Vui lòng chọn khu vực', 'error'); return; }

        fetch(BASE_URL + '/widgets', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ name: name, type: type, area: area, sort_order: 999, is_active: true })
        })
        .then(safeJson)
        .then(function (data) {
            if (data.success === false) { showToast(data.message || 'Lỗi khi thêm widget', 'error'); return; }
            var id = (data.widget && data.widget.id) ? data.widget.id : (data.id || '');
            showToast('Đã thêm widget vào khu vực', 'success');
            var list = document.getElementById('area-list-' + area);
            if (list) {
                var empty = document.getElementById('empty-' + area);
                if (empty) { empty.remove(); }
                list.appendChild(buildWidgetRow(area, { id: id, type: type, name: name, settings: {}, variant: 'default' }));
                updateBadge(area);
                initSortable();
            }
        })
        .catch(function () { showToast('Lỗi kết nối server', 'error'); });
    }

    // ── REMOVE WIDGET ──────────────────────────────────────────────
    function removeWidget(btn) {
        var id   = btn.dataset.id;
        var area = btn.dataset.area;
        if (!id) { showToast('Không tìm thấy ID widget', 'error'); return; }
        if (!confirm('Xoá widget này khỏi khu vực?')) { return; }

        fetch(BASE_URL + '/widgets/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(safeJsonOrOk)
        .then(function (data) {
            if (data && data.success === false) { showToast(data.message || 'Lỗi', 'error'); return; }
            var row = btn.closest('.widget-row');
            if (row) { row.remove(); }
            updateBadge(area);
            checkEmptyArea(area);
            showToast('Đã xoá widget', 'success');
        })
        .catch(function () { showToast('Lỗi kết nối', 'error'); });
    }

    // ── SORTABLE DRAG & DROP (UP / DOWN ONLY) ──────────────────────
    function initSortable() {
        if (typeof Sortable === 'undefined') return;

        document.querySelectorAll('.widget-area-list').forEach(function (listEl) {
            if (listEl._sortable) {
                listEl._sortable.destroy();
            }
            var areaKey = listEl.dataset.area;
            listEl._sortable = new Sortable(listEl, {
                handle: '.drag-handle',
                animation: 180,
                ghostClass: 'bg-blue-50/80',
                chosenClass: 'bg-blue-100/70',
                dragClass: 'shadow-lg',
                direction: 'vertical',
                onEnd: function (evt) {
                    if (evt.oldIndex === evt.newIndex) return;

                    var rows = listEl.querySelectorAll('.widget-row');
                    var widgetIds = Array.from(rows).map(function (row) {
                        return row.dataset.id;
                    }).filter(Boolean);

                    fetch(BASE_URL + '/widgets/reorder', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            area: areaKey,
                            widget_ids: widgetIds
                        })
                    })
                    .then(safeJson)
                    .then(function (data) {
                        if (data && data.success) {
                            showToast(data.message || 'Đã cập nhật vị trí widget', 'success');
                        } else {
                            showToast((data && data.message) || 'Lỗi cập nhật vị trí', 'error');
                        }
                    })
                    .catch(function () {
                        showToast('Lỗi kết nối khi cập nhật vị trí', 'error');
                    });
                }
            });
        });
    }

    // ── CONDITIONAL FIELDS ─────────────────────────────────────────
    function initConditionalFields() {
        var conditionalFields = document.querySelectorAll('[data-show-if]');
        
        conditionalFields.forEach(function(fieldWrapper) {
            var showIf;
            try {
                showIf = JSON.parse(fieldWrapper.dataset.showIf);
            } catch(e) { return; }
            
            Object.keys(showIf).forEach(function(controlFieldName) {
                var controlElements = document.querySelectorAll(
                    '[name="' + controlFieldName + '"], [name="' + controlFieldName + '[]"]'
                );
                if (!controlElements.length) return;
                
                checkConditional(fieldWrapper, controlFieldName, showIf);
                
                controlElements.forEach(function(ctrl) {
                    ctrl.addEventListener('change', function() {
                        conditionalFields.forEach(function(fw) {
                            try {
                                var fwShowIf = JSON.parse(fw.dataset.showIf);
                                if (fwShowIf[controlFieldName] !== undefined) {
                                    checkConditional(fw, controlFieldName, fwShowIf);
                                }
                            } catch(e) {}
                        });
                    });
                });
            });
        });
    }
    
    function checkConditional(targetWrapper, controlFieldName, showIf) {
        var expectedValue = showIf[controlFieldName];
        
        var checkedRadio = document.querySelector('[name="' + controlFieldName + '"]:checked');
        var currentControl = checkedRadio 
            || document.querySelector('[name="' + controlFieldName + '"]') 
            || document.querySelector('[name="' + controlFieldName + '[]"]');
        
        if (!currentControl) return;
        
        var currentValue = currentControl.value;
        var isMatch = false;
        if (Array.isArray(expectedValue)) {
            isMatch = expectedValue.map(String).indexOf(String(currentValue)) !== -1;
        } else {
            isMatch = (currentValue === expectedValue || String(currentValue) === String(expectedValue));
        }

        targetWrapper.style.display = isMatch ? '' : 'none';
        if (isMatch && window.jQuery) {
            $(targetWrapper).find('.select2-container').css('width', '100%');
        }
    }
    
    // ── OPEN CONFIG DRAWER ─────────────────────────────────────────
    function openConfig(btn) {
        drawerWidgetId   = btn.dataset.id;
        drawerWidgetType = btn.dataset.type;
        var row = btn.closest('.widget-row');
        drawerWidgetArea = row ? row.dataset.area : null;

        var settings = {};
        var rawSettings = btn.dataset.settings || '{}';
        for (var i = 0; i < 3; i++) {
            if (typeof rawSettings === 'string' && rawSettings.trim().length > 0) {
                try {
                    var parsed = JSON.parse(rawSettings);
                    if (typeof parsed === 'object' && parsed !== null) {
                        settings = parsed;
                        break;
                    } else {
                        rawSettings = parsed;
                    }
                } catch (e) {
                    break;
                }
            } else if (typeof rawSettings === 'object' && rawSettings !== null) {
                settings = rawSettings;
                break;
            }
        }

        if (typeof window.destroyCKEditors === 'function') {
            window.destroyCKEditors();
        }

        document.getElementById('drawer-title').textContent    = btn.dataset.name || drawerWidgetType;
        document.getElementById('drawer-subtitle').textContent = drawerWidgetType;
        document.getElementById('drawer-body').innerHTML =
            '<div class="flex items-center justify-center h-32 text-gray-400">' +
            '<svg class="w-5 h-5 animate-spin mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>' +
            '<span class="text-sm">Đang tải form...</span></div>';

        var drawer = document.getElementById('config-drawer');
        drawer.classList.remove('hidden');
        drawer.classList.add('flex');

        var params = new URLSearchParams({ id: drawerWidgetId || '', type: drawerWidgetType, settings: JSON.stringify(settings) });
        fetch(BASE_URL + '/widgets/fields?' + params.toString(), { headers: { 'Accept': 'application/json' } })
        .then(safeJson)
        .then(function (data) {
            if (!data.success) { showToast(data.message || 'Không tải được form', 'error'); return; }
            var drawerContainer = document.getElementById('drawer-body');
            drawerContainer.innerHTML = data.form_html || '<p class="text-gray-500 text-sm p-4">Widget này không có trường cấu hình.</p>';
            
            // Re-evaluate script tags injected via innerHTML
            drawerContainer.querySelectorAll('script').forEach(function(oldScript) {
                var newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(function(attr) {
                    newScript.setAttribute(attr.name, attr.value);
                });
                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                if (oldScript.parentNode) {
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                }
            });

            if (window.Alpine) {
                try { window.Alpine.initTree(drawerContainer); } catch(e) {}
            }
            if (typeof window.initTinyMCE === 'function') {
                window.initTinyMCE();
            }
            if (typeof window.initCKEditor === 'function') {
                window.initCKEditor(drawerContainer);
            }
            document.dispatchEvent(new CustomEvent('widget-form-loaded'));
            if (typeof window.initRepeatableSortable === 'function') {
                window.initRepeatableSortable(drawerContainer);
            }
            // Init conditional fields
            initConditionalFields();

            // Init Select2
            if (typeof window.initSelect2 === 'function') {
                window.initSelect2(drawerContainer);
            }
        })
        .catch(function (err) {
            console.error('Widget form load error:', err);
            document.getElementById('drawer-body').innerHTML = '<p class="text-red-500 text-sm p-4">Lỗi khi tải form cấu hình: ' + (err.message || 'Lỗi mạng hoặc hệ thống') + '</p>';
        });
    }

    function closeDrawer() {
        if (typeof window.destroyCKEditors === 'function') {
            window.destroyCKEditors();
        }
        if (window.jQuery && typeof window.jQuery.fn.select2 !== 'undefined') {
            $('#drawer-body select.select2-enable').each(function () {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    try { $(this).select2('destroy'); } catch (e) {}
                }
            });
        }
        var drawer = document.getElementById('config-drawer');
        drawer.classList.add('hidden');
        drawer.classList.remove('flex');
        drawerWidgetId = null;
        drawerWidgetType = null;
        drawerWidgetArea = null;
    }

    // ── FORM SETTINGS ──────────────────────────────────────────────
    function setNestedValue(obj, path, value) {
        var keys = path.replace(/\]/g, '').split(/\[/);
        var current = obj;
        for (var i = 0; i < keys.length - 1; i++) {
            var key = keys[i];
            if (current[key] === undefined) {
                current[key] = /^\d+$/.test(keys[i + 1]) ? [] : {};
            }
            current = current[key];
        }
        current[keys[keys.length - 1]] = value;
    }

    function getFormSettings() {
        var settings = {};
        var drawerBody = document.getElementById('drawer-body');
        if (!drawerBody) return settings;

        if (typeof window.syncCKEditors === 'function') {
            window.syncCKEditors();
        }

        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }

        // First, trigger Alpine to sync all x-model bindings
        if (window.Alpine) {
            window.Alpine.nextTick(function() {
                // Alpine has updated all bindings
            });
        }

        drawerBody.querySelectorAll('input, textarea, select').forEach(function (input) {
            if (!input.name) { return; }

            // Skip hidden conditional fields (only data-show-if conditional wrappers)
            var conditionalWrapper = input.closest('[data-show-if]');
            if (conditionalWrapper && conditionalWrapper.style.display === 'none') {
                return;
            }

            if (input.name.endsWith('_url_input')) {
                return;
            }

            if (input.tagName === 'SELECT' && input.multiple) {
                var selectedVals = Array.from(input.selectedOptions).map(function(opt) { return opt.value; });
                setNestedValue(settings, input.name.replace(/\[\]$/, ''), selectedVals);
                return;
            }

            var val = input.value;

            // Check Alpine.js imageUrl property if non-empty
            var alpineComponent = input.closest('[x-data]');
            if (alpineComponent && alpineComponent.tagName !== 'BODY') {
                try {
                    var alpineData = window.Alpine ? window.Alpine.$data(alpineComponent) : (alpineComponent._x_dataStack ? alpineComponent._x_dataStack[0] : null);
                    if (alpineData && alpineData.imageUrl) {
                        val = alpineData.imageUrl;
                    }
                } catch(e) {}
            }

            if (input.type === 'checkbox') {
                setNestedValue(settings, input.name, input.checked);
            } else if (input.type === 'radio') {
                if (input.checked) { setNestedValue(settings, input.name, val); }
            } else {
                setNestedValue(settings, input.name, val);
            }
        });

        return settings;
    }

    // ── SAVE CONFIG ────────────────────────────────────────────────
    function saveWidgetConfig() {
        if (!drawerWidgetId) { showToast('Không có widget nào được chọn', 'error'); return; }
        var settings = getFormSettings();

        var btn      = document.getElementById('btn-save-config');
        var origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML =
            '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Đang lưu...';

        fetch(BASE_URL + '/widgets/' + drawerWidgetId, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                name: document.getElementById('drawer-title').textContent,
                type: drawerWidgetType,
                area: drawerWidgetArea || 'homepage-main',
                settings: JSON.stringify(settings),
                is_active: true
            })
        })
        .then(safeJson)
        .then(function (data) {
            showToast('Đã lưu cấu hình widget', 'success');
            // Update dataset on config button and row so reopening drawer reflects new state
            var configBtn = document.querySelector('.btn-open-config[data-id="' + drawerWidgetId + '"]');
            if (!configBtn && drawerWidgetId) {
                configBtn = document.querySelector('.widget-row[data-id="' + drawerWidgetId + '"] .btn-open-config');
            }
            if (configBtn && data.widget) {
                var updatedSettings = data.widget.settings || settings;
                if (typeof updatedSettings === 'string') {
                    try { updatedSettings = JSON.parse(updatedSettings); } catch(e) {}
                }
                configBtn.dataset.settings = JSON.stringify(updatedSettings);
                configBtn.dataset.name = data.widget.name || document.getElementById('drawer-title').textContent;
                var row = configBtn.closest('.widget-row');
                if (row) {
                    row.dataset.name = configBtn.dataset.name;
                    var rowName = row.querySelector('.font-medium');
                    if (rowName) rowName.textContent = configBtn.dataset.name;
                }
            }
            // Flash save button green briefly for visual feedback (keep drawer open)
            btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            btn.classList.add('bg-emerald-500');
            btn.innerHTML =
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' +
                '</svg> Đã lưu!';
            setTimeout(function () {
                btn.classList.remove('bg-emerald-500');
                btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                btn.innerHTML = origHtml;
            }, 1500);
        })
        .catch(function () { showToast('Lỗi khi lưu', 'error'); })
        .finally(function () { btn.disabled = false; });
    }

    // ── CLEAR CACHE ────────────────────────────────────────────────
    function clearCache() {
        fetch(BASE_URL + '/widgets/clear-cache', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(safeJson)
        .then(function () { showToast('Đã xoá cache widget', 'success'); })
        .catch(function () { showToast('Lỗi khi xoá cache', 'error'); });
    }

    // ── CATEGORY TOGGLE ────────────────────────────────────────────
    function toggleCategory(cat) {
        var el    = document.getElementById('category-' + cat);
        var arrow = document.getElementById('arrow-' + cat);
        if (!el) { return; }
        el.classList.toggle('hidden');
        if (arrow) { arrow.style.transform = el.classList.contains('hidden') ? '' : 'rotate(90deg)'; }
    }

    // ── SEARCH ─────────────────────────────────────────────────────
    function filterWidgets(q) {
        q = q.toLowerCase().trim();
        document.querySelectorAll('.widget-template').forEach(function (el) {
            var show = !q || (el.dataset.name || '').toLowerCase().includes(q) || (el.dataset.type || '').toLowerCase().includes(q);
            el.style.display = show ? '' : 'none';
        });
        document.querySelectorAll('.widget-category').forEach(function (cat) {
            var visible = cat.querySelectorAll('.widget-template:not([style*="display: none"])');
            cat.style.display = visible.length ? '' : 'none';
            if (q && visible.length) {
                var catKey = cat.dataset.category;
                var el     = document.getElementById('category-' + catKey);
                var arrow  = document.getElementById('arrow-' + catKey);
                if (el) { el.classList.remove('hidden'); if (arrow) { arrow.style.transform = 'rotate(90deg)'; } }
            }
        });
    }

    // ── EVENT DELEGATION ───────────────────────────────────────────
    document.addEventListener('click', function (e) {
        // Open config
        var configBtn = e.target.closest('.btn-open-config');
        if (configBtn) { openConfig(configBtn); return; }

        // Remove widget
        var removeBtn = e.target.closest('.btn-remove-widget');
        if (removeBtn) { removeWidget(removeBtn); return; }

        // Category toggle
        var catBtn = e.target.closest('.btn-toggle-category');
        if (catBtn) { toggleCategory(catBtn.dataset.cat); return; }

        // Widget template click → add widget to selected area
        var tpl = e.target.closest('.widget-template');
        if (tpl) {
            addWidget(tpl.dataset.type, tpl.dataset.name);
            return;
        }

        // Drawer close
        if (e.target.id === 'drawer-backdrop' || e.target.closest('#btn-close-drawer') || e.target.closest('#btn-cancel-drawer')) {
            closeDrawer();
            return;
        }
    });

    // Search input
    document.getElementById('widgetSearch').addEventListener('input', function () {
        filterWidgets(this.value);
    });

    // Save config button
    var btnSaveConfig = document.getElementById('btn-save-config');
    if (btnSaveConfig) btnSaveConfig.addEventListener('click', saveWidgetConfig);

    // Clear cache button
    var btnClearCache = document.getElementById('btn-clear-cache');
    if (btnClearCache) btnClearCache.addEventListener('click', clearCache);

    // ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeDrawer(); }
    });

    // Initialize drag-and-drop vertical sorting
    initSortable();
}());
</script>
@endpush
