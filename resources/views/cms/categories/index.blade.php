{{-- MODIFIED: 2026-10-16 Giao diện Danh mục dạng Case Thư Mục Cấp 1 - 2 - 3 --}}
@extends('cms.layouts.app')

@section('title', $pageTitle ?? 'Quản lý danh mục')
@section('page-title', $pageTitle ?? 'Danh mục')

@section('content')
@php
    $currentProject = $currentProject ?? session('current_project');
    $taxonomyType = $taxonomyType ?? request('type', 'product_cat');

    $getCreateUrl = function($parentId = null) use ($currentProject, $taxonomyType) {
        $params = $parentId ? ['parent_id' => $parentId] : [];
        if (!empty($taxonomyType) && $taxonomyType !== 'product_cat') {
            $params['type'] = $taxonomyType;
        }
        if (isset($currentProject) && $currentProject && \Illuminate\Support\Facades\Route::has('project.admin.categories.create')) {
            return route('project.admin.categories.create', array_merge(['projectCode' => $currentProject->code], $params));
        }
        if (request()->routeIs('admin.*')) {
            return route('admin.categories.create', $params);
        }
        return route('cms.categories.create', $params);
    };

    $getEditUrl = function($catId) use ($currentProject, $taxonomyType) {
        $params = (!empty($taxonomyType) && $taxonomyType !== 'product_cat') ? ['type' => $taxonomyType] : [];
        if (isset($currentProject) && $currentProject && \Illuminate\Support\Facades\Route::has('project.admin.categories.edit')) {
            return route('project.admin.categories.edit', array_merge([$currentProject->code, $catId], $params));
        }
        if (request()->routeIs('admin.*')) {
            return route('admin.categories.edit', array_merge([$catId], $params));
        }
        return route('cms.categories.edit', array_merge([$catId], $params));
    };

    $getDestroyUrl = function($catId) use ($currentProject) {
        if (isset($currentProject) && $currentProject && \Illuminate\Support\Facades\Route::has('project.admin.categories.destroy')) {
            return route('project.admin.categories.destroy', [$currentProject->code, $catId]);
        }
        if (request()->routeIs('admin.*')) {
            return route('admin.categories.destroy', $catId);
        }
        return route('cms.categories.destroy', $catId);
    };

    $getRestoreUrl = function($catId) use ($currentProject) {
        if (isset($currentProject) && $currentProject && \Illuminate\Support\Facades\Route::has('project.admin.categories.restore')) {
            return route('project.admin.categories.restore', [$currentProject->code, $catId]);
        }
        if (request()->routeIs('admin.*')) {
            return route('admin.categories.restore', $catId);
        }
        return route('cms.categories.restore', $catId);
    };

    $getForceDeleteUrl = function($catId) use ($currentProject) {
        if (isset($currentProject) && $currentProject && \Illuminate\Support\Facades\Route::has('project.admin.categories.force-delete')) {
            return route('project.admin.categories.force-delete', [$currentProject->code, $catId]);
        }
        if (request()->routeIs('admin.*')) {
            return route('admin.categories.force-delete', $catId);
        }
        return route('cms.categories.force-delete', $catId);
    };
@endphp

<div class="space-y-6">
    <!-- Taxonomy Navigation Tabs (product_cat vs project_cat vs category) -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-200/80 p-2 sm:p-3 flex items-center justify-between flex-wrap gap-2">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ request()->routeIs('admin.*') ? route('admin.categories.index', ['type' => 'product_cat']) : route('cms.categories.index', ['type' => 'product_cat']) }}"
               class="px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 {{ ($taxonomyType === 'product_cat') ? 'bg-amber-500 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span>Danh mục Sản phẩm</span>
                <span class="text-[11px] px-1.5 py-0.5 rounded font-mono {{ ($taxonomyType === 'product_cat') ? 'bg-amber-600 text-white' : 'bg-gray-200 text-gray-600' }}">product_cat</span>
            </a>

            <a href="{{ request()->routeIs('admin.*') ? route('admin.categories.index', ['type' => 'project_cat']) : route('cms.categories.index', ['type' => 'project_cat']) }}"
               class="px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 {{ ($taxonomyType === 'project_cat') ? 'bg-emerald-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Danh mục Dự án</span>
                <span class="text-[11px] px-1.5 py-0.5 rounded font-mono {{ ($taxonomyType === 'project_cat') ? 'bg-emerald-700 text-white' : 'bg-gray-200 text-gray-600' }}">project_cat</span>
            </a>

            <a href="{{ request()->routeIs('admin.*') ? route('admin.categories.index', ['type' => 'category']) : route('cms.categories.index', ['type' => 'category']) }}"
               class="px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 {{ ($taxonomyType === 'category') ? 'bg-blue-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <span>Chuyên mục Tin tức</span>
                <span class="text-[11px] px-1.5 py-0.5 rounded font-mono {{ ($taxonomyType === 'category') ? 'bg-blue-700 text-white' : 'bg-gray-200 text-gray-600' }}">category</span>
            </a>
        </div>
        <div class="text-xs text-gray-500 font-medium px-2">
            Phân loại hiện tại: <span class="font-bold text-gray-800 font-mono">{{ $taxonomyType }}</span>
        </div>
    </div>

    <!-- Header with Actions & Tree Controls -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-200/80 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $pageTitle ?? 'Cấu trúc cây thư mục danh mục' }}</h1>
                        <p class="text-gray-500 text-sm mt-0.5">Sắp xếp phân cấp trực quan theo dạng Case Thư Mục (Cấp 1 - Cấp 2 - Cấp 3)</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center flex-wrap gap-2.5">
                <!-- Tree Controls -->
                <button type="button" id="btn-expand-all" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition cursor-pointer shadow-2xs border border-gray-200" title="Mở rộng tất cả các thư mục">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                    Mở rộng tất cả
                </button>

                <button type="button" id="btn-collapse-all" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition cursor-pointer shadow-2xs border border-gray-200" title="Thu gọn chỉ hiện danh mục Cấp 1">
                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l5 5m0 0H5m4 0V5m7 4l5-5m-5 5h4m-4 0V5M9 15l-5 5m5-5H5m4 0v4m7-4l5 5m-5-5h4m-4 0v4"></path>
                    </svg>
                    Thu gọn tất cả
                </button>

                <!-- Add Button -->
                <a href="{{ $getCreateUrl() }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-sm font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 hover:shadow-md transition shadow-2xs">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Thêm danh mục
                </a>
            </div>
        </div>

        <!-- Hierarchy Stats Bar -->
        <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3 pt-5 border-t border-gray-100">
            <div class="flex items-center gap-3 p-3 bg-blue-50/60 rounded-xl border border-blue-100/80">
                <div class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-2xs">
                    1
                </div>
                <div>
                    <div class="text-xs font-medium text-blue-900">Cấp 1 (Gốc)</div>
                    <div class="text-lg font-bold text-blue-700 leading-tight">{{ $stats['level_1'] ?? 0 }} <span class="text-xs font-normal text-blue-500">mục</span></div>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 bg-emerald-50/60 rounded-xl border border-emerald-100/80">
                <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-2xs">
                    2
                </div>
                <div>
                    <div class="text-xs font-medium text-emerald-900">Cấp 2 (Nhánh)</div>
                    <div class="text-lg font-bold text-emerald-700 leading-tight">{{ $stats['level_2'] ?? 0 }} <span class="text-xs font-normal text-emerald-500">mục</span></div>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 bg-amber-50/60 rounded-xl border border-amber-100/80">
                <div class="w-9 h-9 rounded-lg bg-amber-600 text-white flex items-center justify-center font-bold text-sm shadow-2xs">
                    3
                </div>
                <div>
                    <div class="text-xs font-medium text-amber-900">Cấp 3 (Lá)</div>
                    <div class="text-lg font-bold text-amber-700 leading-tight">{{ $stats['level_3'] ?? 0 }} <span class="text-xs font-normal text-amber-500">mục</span></div>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                <div class="w-9 h-9 rounded-lg bg-slate-700 text-white flex items-center justify-center shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-slate-700">Tổng sản phẩm</div>
                    <div class="text-lg font-bold text-slate-800 leading-tight">{{ $stats['total_products'] ?? 0 }} <span class="text-xs font-normal text-slate-500">SP</span></div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Form -->
        <div class="mt-4 pt-4 border-t border-gray-100">
            <form method="GET" class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm tên danh mục, đường dẫn slug..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-gray-50/50 hover:bg-white focus:bg-white">
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <select name="level" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Tất cả cấp độ</option>
                        <option value="0" {{ request('level') === '0' ? 'selected' : '' }}>Chỉ Cấp 1 (Gốc)</option>
                        <option value="1" {{ request('level') === '1' ? 'selected' : '' }}>Chỉ Cấp 2 (Nhánh)</option>
                        <option value="2" {{ request('level') === '2' ? 'selected' : '' }}>Chỉ Cấp 3 (Lá)</option>
                    </select>

                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm dừng</option>
                        <option value="trashed" {{ request('status') == 'trashed' ? 'selected' : '' }}>Thùng rác</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-black transition text-sm font-medium shadow-2xs">
                        Lọc dữ liệu
                    </button>

                    @if(request('search') || request('status') || request()->filled('level'))
                        <a href="{{ request()->routeIs('admin.*') ? route('admin.categories.index') : route('cms.categories.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm transition">
                            Xóa lọc
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Directory Tree Table -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="categories-tree-table">
                <thead class="bg-slate-50 border-b border-gray-200 text-gray-600 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 min-w-[320px]">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                </svg>
                                <span>Cấu Trúc Cây Thư Mục</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left w-36">Cấp độ</th>
                        <th class="px-6 py-4 text-left w-32">Sản phẩm</th>
                        <th class="px-6 py-4 text-left w-36">Trạng thái</th>
                        <th class="px-6 py-4 text-center w-40">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($categories as $category)
                        @php
                            $level = $category->tree_level ?? $category->level;
                            $hasChildren = ($category->children_count ?? 0) > 0;
                            $ancestorsStr = implode(',', $category->ancestor_ids ?? []);
                            
                            // Visual indentation:
                            // Cấp 1: 0px indent
                            // Cấp 2: 36px indent
                            // Cấp 3: 72px indent
                            $indentPx = $level * 36;

                            // Left highlight color based on level
                            $levelBorderColor = match($level) {
                                0 => '#2563eb', // Blue
                                1 => '#10b981', // Emerald
                                2 => '#f59e0b', // Amber
                                default => '#8b5cf6' // Purple
                            };
                        @endphp
                        <tr class="category-tree-row group hover:bg-slate-50/80 transition duration-150"
                            data-id="{{ $category->id }}"
                            data-parent="{{ $category->parent_id ?? '' }}"
                            data-ancestors="{{ $ancestorsStr }}"
                            data-level="{{ $level }}"
                            style="border-left: 4px solid {{ $levelBorderColor }};">
                            
                            <!-- CỘT DANH MỤC (CẤU TRÚC CÂY THƯ MỤC) -->
                            <td class="px-6 py-3.5">
                                <div class="flex items-center" style="padding-left: {{ $indentPx }}px;">
                                    
                                    {{-- Tree Branch Connector Lines for Sub-levels --}}
                                    @if($level > 0)
                                        <div class="flex items-center text-gray-300 mr-2 flex-shrink-0 select-none" aria-hidden="true">
                                            @if($level == 1)
                                                {{-- Cấp 2 branch connector: ├── or └── --}}
                                                <svg class="w-5 h-7 text-gray-300" viewBox="0 0 20 28" fill="none" stroke="currentColor">
                                                    <path d="M 4 0 V 15 H 18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            @else
                                                {{-- Cấp 3 nested branch connector --}}
                                                <svg class="w-6 h-7 text-gray-300" viewBox="0 0 24 28" fill="none" stroke="currentColor">
                                                    <path d="M 2 0 V 28 M 10 0 V 15 H 22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Collapse/Expand Toggle Chevron Button --}}
                                    <div class="w-6 h-6 flex items-center justify-center mr-1.5 flex-shrink-0">
                                        @if($hasChildren)
                                            <button type="button" 
                                                    class="tree-toggle-btn w-6 h-6 rounded hover:bg-gray-200/80 text-gray-500 hover:text-gray-900 flex items-center justify-center transition cursor-pointer"
                                                    data-target-id="{{ $category->id }}"
                                                    title="Mở rộng / Thu gọn thư mục">
                                                <svg class="tree-chevron w-3.5 h-3.5 transform transition-transform duration-200 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        @else
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                        @endif
                                    </div>

                                    {{-- Directory Folder / File Icon --}}
                                    <div class="mr-3 flex-shrink-0">
                                        @if($category->image)
                                            <div class="w-9 h-9 rounded-lg overflow-hidden border border-gray-200 shadow-2xs">
                                                <img src="{{ $category->image }}" class="w-full h-full object-cover" alt="{{ $category->name }}">
                                            </div>
                                        @elseif($level == 0)
                                            {{-- Cấp 1: Thư mục gốc to, nổi bật --}}
                                            <div class="folder-icon-wrapper w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200 shadow-2xs group-hover:bg-blue-600 group-hover:text-white transition duration-200">
                                                <svg class="w-5 h-5 folder-open-icon hidden" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1H8a3 3 0 00-3 3v1.5a1.5 1.5 0 01-3 0V6z" clip-rule="evenodd"></path>
                                                    <path d="M6 12a2 2 0 012-2h8a2 2 0 012 2v2a2 2 0 01-2 2H2v-2a2 2 0 012-2h2z"></path>
                                                </svg>
                                                <svg class="w-5 h-5 folder-closed-icon" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                                </svg>
                                            </div>
                                        @elseif($level == 1)
                                            {{-- Cấp 2: Thư mục nhánh xanh lá --}}
                                            <div class="folder-icon-wrapper w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200 shadow-2xs group-hover:bg-emerald-600 group-hover:text-white transition duration-200">
                                                <svg class="w-4 h-4 folder-open-icon hidden" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1H8a3 3 0 00-3 3v1.5a1.5 1.5 0 01-3 0V6z" clip-rule="evenodd"></path>
                                                    <path d="M6 12a2 2 0 012-2h8a2 2 0 012 2v2a2 2 0 01-2 2H2v-2a2 2 0 012-2h2z"></path>
                                                </svg>
                                                <svg class="w-4 h-4 folder-closed-icon" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                                </svg>
                                            </div>
                                        @else
                                            {{-- Cấp 3: File / Mục chi tiết con màu vàng cam --}}
                                            <div class="folder-icon-wrapper w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200 shadow-2xs group-hover:bg-amber-600 group-hover:text-white transition duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Tên danh mục & Chi tiết --}}
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="{{ $getEditUrl($category->id) }}" class="font-semibold text-gray-900 group-hover:text-blue-600 transition truncate hover:underline">
                                                {{ $category->name }}
                                            </a>

                                            {{-- Subcategory Count Badge --}}
                                            @if($hasChildren)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $level == 0 ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path>
                                                    </svg>
                                                    {{ $category->children_count }} mục con
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Slug & Parent Context Breadcrumb --}}
                                        <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5">
                                            @if($category->parent_name)
                                                <span class="text-gray-500 font-medium">Thuộc: <span class="text-gray-700 font-semibold">{{ $category->parent_name }}</span></span>
                                                <span>•</span>
                                            @endif
                                            <span class="font-mono text-gray-400">/{{ $category->slug }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- CỘT CẤP ĐỘ (BADGE RIÊNG CHO TỪNG CẤP) -->
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if($level == 0)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                        Cấp 1 (Gốc)
                                    </span>
                                @elseif($level == 1)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                        Cấp 2
                                    </span>
                                @elseif($level == 2)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                                        Cấp 3
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                        Cấp {{ $level + 1 }}
                                    </span>
                                @endif
                            </td>

                            <!-- CỘT SẢN PHẨM -->
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold {{ ($category->products_count ?? 0) > 0 ? 'bg-slate-100 text-slate-800 border border-slate-200' : 'bg-gray-50 text-gray-400' }}">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    {{ $category->products_count ?? 0 }}
                                </span>
                            </td>

                            <!-- CỘT TRẠNG THÁI -->
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if($category->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Tạm dừng
                                    </span>
                                @endif
                            </td>

                            <!-- CỘT THAO TÁC -->
                            <td class="px-6 py-3.5 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if(request('status') === 'trashed')
                                        {{-- Khôi phục từ thùng rác --}}
                                        <form method="POST" action="{{ $getRestoreUrl($category->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" title="Khôi phục danh mục" class="p-1.5 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition border border-transparent hover:border-emerald-200 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                </svg>
                                            </button>
                                        </form>

                                        {{-- Xóa vĩnh viễn --}}
                                        <form method="POST" action="{{ $getForceDeleteUrl($category->id) }}" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn danh mục này? Dữ liệu không thể khôi phục!')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Xóa vĩnh viễn" class="p-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition border border-transparent hover:border-red-200 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Nút Thêm danh mục con (Cấp 1 & Cấp 2) --}}
                                        @if($level < 2)
                                            <a href="{{ $getCreateUrl($category->id) }}" 
                                               title="Thêm danh mục con (Cấp {{ $level + 2 }}) thuộc '{{ $category->name }}'" 
                                               class="p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition border border-transparent hover:border-indigo-200 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </a>
                                        @endif

                                        {{-- Chỉnh sửa --}}
                                        <a href="{{ $getEditUrl($category->id) }}" 
                                           title="Chỉnh sửa danh mục" 
                                           class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition border border-transparent hover:border-blue-200 cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>

                                        {{-- Chuyển vào thùng rác --}}
                                        <form method="POST" action="{{ $getDestroyUrl($category->id) }}" class="inline" onsubmit="return confirm('Bạn có chắc muốn chuyển danh mục \'{{ $category->name }}\' vào thùng rác?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Chuyển vào thùng rác" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition border border-transparent hover:border-red-200 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="max-w-md mx-auto text-gray-500">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-800">Chưa có danh mục nào</h3>
                                    <p class="text-sm text-gray-500 mt-1">Hãy bắt đầu xây dựng cây danh mục bằng cách tạo thư mục gốc (Cấp 1) đầu tiên.</p>
                                    <div class="mt-4">
                                        <a href="{{ $getCreateUrl() }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition shadow-2xs">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Tạo danh mục gốc
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Interactive Folder Tree Script --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('categories-tree-table');
    if (!table) return;

    // Set of collapsed parent category IDs
    const collapsedParents = new Set();

    // Toggle button handler
    const toggleButtons = table.querySelectorAll('.tree-toggle-btn');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const targetId = this.getAttribute('data-target-id');
            if (!targetId) return;

            if (collapsedParents.has(targetId)) {
                collapsedParents.delete(targetId);
            } else {
                collapsedParents.add(targetId);
            }

            updateTreeVisibility();
        });
    });

    // Expand All Button
    const expandAllBtn = document.getElementById('btn-expand-all');
    if (expandAllBtn) {
        expandAllBtn.addEventListener('click', function () {
            collapsedParents.clear();
            updateTreeVisibility();
        });
    }

    // Collapse All Button
    const collapseAllBtn = document.getElementById('btn-collapse-all');
    if (collapseAllBtn) {
        collapseAllBtn.addEventListener('click', function () {
            collapsedParents.clear();
            toggleButtons.forEach(btn => {
                const targetId = btn.getAttribute('data-target-id');
                if (targetId) {
                    collapsedParents.add(targetId);
                }
            });
            updateTreeVisibility();
        });
    }

    function updateTreeVisibility() {
        const rows = table.querySelectorAll('.category-tree-row');
        
        rows.forEach(row => {
            const level = parseInt(row.getAttribute('data-level') || '0', 10);
            const ancestors = (row.getAttribute('data-ancestors') || '')
                .split(',')
                .map(s => s.trim())
                .filter(Boolean);

            // Check if any ancestor is collapsed
            const isHiddenByAncestor = ancestors.some(aId => collapsedParents.has(aId));

            if (isHiddenByAncestor) {
                row.classList.add('hidden');
            } else {
                row.classList.remove('hidden');
            }

            // Update chevron and folder icon for this row if it is a parent
            const rowId = row.getAttribute('data-id');
            const toggleBtn = row.querySelector('.tree-toggle-btn');
            const chevron = row.querySelector('.tree-chevron');
            const folderOpen = row.querySelector('.folder-open-icon');
            const folderClosed = row.querySelector('.folder-closed-icon');

            if (toggleBtn && chevron) {
                const isCollapsed = collapsedParents.has(rowId);
                if (isCollapsed) {
                    chevron.classList.remove('rotate-90');
                    chevron.classList.add('rotate-0');
                    if (folderOpen && folderClosed) {
                        folderOpen.classList.add('hidden');
                        folderClosed.classList.remove('hidden');
                    }
                } else {
                    chevron.classList.remove('rotate-0');
                    chevron.classList.add('rotate-90');
                    if (folderOpen && folderClosed) {
                        folderOpen.classList.remove('hidden');
                        folderClosed.classList.add('hidden');
                    }
                }
            }
        });
    }

    // Initialize: default all expanded
    updateTreeVisibility();
});
</script>
@endsection
