@extends('cms.layouts.app')

@section('title', 'Cấu hình Bộ Lọc Sản Phẩm (Filter)')
@section('page-title', 'Bộ Lọc Sản Phẩm')

@section('content')
@include('cms.settings.partials.back-link')

@php
    $settingsSaveUrl = route('admin.settings.save');
    $categoriesList = $productCategories ?? collect();
    $brandsList = $productBrands ?? collect();

    // Prepare JSON data for Alpine
    $initialSettings = $filterSettings ?? [];

    $categoryFilterData = $initialSettings['category_filter'] ?? [
        'enabled' => true,
        'title' => 'LOẠI SẢN PHẨM',
        'mode' => 'custom',
        'selected_categories' => [1, 2, 3],
        'selection_type' => 'checkbox',
        'is_expanded' => true,
    ];

    $brandFilterData = $initialSettings['brand_filter'] ?? [
        'enabled' => true,
        'title' => 'THƯƠNG HIỆU',
        'mode' => 'all',
        'selected_brands' => $brandsList->pluck('id')->toArray(),
        'display_type' => 'logo',
        'is_expanded' => true,
    ];

    $priceFilterData = array_merge([
        'enabled' => true,
        'title' => 'KHOẢNG GIÁ',
        'type' => 'milestones', // 'milestones' or 'slider'
        'min_price' => 0,
        'max_price' => 1000000,
        'step' => 50000,
        'allow_contact' => true,
        'is_expanded' => true,
        'ranges' => [
            ['id' => 'under_200', 'label' => 'Dưới 200.000đ', 'min' => 0, 'max' => 200000, 'type' => 'range'],
            ['id' => '200_400', 'label' => '200.000đ - 400.000đ', 'min' => 200000, 'max' => 400000, 'type' => 'range'],
            ['id' => 'over_400', 'label' => 'Trên 400.000đ', 'min' => 400000, 'max' => null, 'type' => 'range'],
            ['id' => 'contact', 'label' => 'Giá liên hệ dự án', 'min' => null, 'max' => null, 'type' => 'contact'],
        ],
    ], $initialSettings['price_filter'] ?? []);

    $featuredCategoriesData = $initialSettings['featured_categories'] ?? [1, 6, 7];

    $categoriesJson = $categoriesList->map(function($c) {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
            'level' => $c->tree_level ?? 0,
            'parent_id' => $c->parent_id,
            'parent_name' => $c->parent_name,
            'products_count' => $c->products_count ?? 0,
        ];
    })->values();

    $brandsJson = $brandsList->map(function($b) {
        return [
            'id' => $b->id,
            'name' => $b->name,
            'slug' => $b->slug,
            'logo' => $b->image ?: ($b->meta_data['logo'] ?? null),
            'products_count' => $b->products_count ?? 0,
        ];
    })->values();
@endphp

<div x-data="productFilterManager()" class="space-y-6 max-w-7xl mx-auto pb-16">

    @if(session('alert'))
        <div class="p-4 rounded-xl {{ session('alert.type') === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200' }} flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 {{ session('alert.type') === 'success' ? 'text-emerald-500' : 'text-red-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-sm font-medium">{{ session('alert.message') }}</span>
            </div>
        </div>
    @endif

    {{-- Main Header Banner --}}
    <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/90 shadow-sm relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold uppercase tracking-wider mb-2 border border-blue-100">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    Cấu Hình Sidebar Danh Mục &amp; Bộ Lọc
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Cấu Hình Bộ Lọc Sản Phẩm (Filter)
                </h1>
                <p class="text-slate-500 mt-1 text-sm sm:text-base">
                    Tùy chỉnh danh mục hiển thị, thương hiệu và khoảng giá trên thanh bộ lọc trang danh mục sản phẩm (/products).
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('products.index') }}" target="_blank" class="px-4 py-2.5 rounded-xl border border-slate-200 hover:border-slate-300 text-slate-700 hover:text-blue-600 bg-white hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-all shadow-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    <span>Xem Ngoài Web</span>
                </a>
                <button type="button" @click="saveSettings()" :disabled="isSaving" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-500/20 transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50">
                    <svg x-show="!isSaving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <svg x-show="isSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="isSaving ? 'Đang lưu...' : 'Lưu Cấu Hình'">Lưu Cấu Hình</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex items-center gap-2 border-b border-slate-200/90 pb-1 overflow-x-auto text-sm font-semibold">
        <button type="button" @click="activeTab = 'category'" 
                :class="activeTab === 'category' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200/80'"
                class="px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
            <span>Nhóm Loại Sản Phẩm</span>
            <span class="ml-1 px-1.5 py-0.5 text-[11px] rounded-full" :class="activeTab === 'category' ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-600'" x-text="selectedCatCountText()"></span>
        </button>

        <button type="button" @click="activeTab = 'brand'" 
                :class="activeTab === 'brand' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200/80'"
                class="px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            <span>Nhóm Thương Hiệu</span>
            <span class="ml-1 px-1.5 py-0.5 text-[11px] rounded-full" :class="activeTab === 'brand' ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-600'" x-text="categoryBrandText()"></span>
        </button>

        <button type="button" @click="activeTab = 'price'" 
                :class="activeTab === 'price' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200/80'"
                class="px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Nhóm Khoảng Giá</span>
            <span class="ml-1 px-1.5 py-0.5 text-[11px] rounded-full" :class="activeTab === 'price' ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-600'" x-text="priceFilter.type === 'slider' ? 'Thanh trượt' : ((priceFilter.ranges ? priceFilter.ranges.length : 0) + ' mốc')"></span>
        </button>

        <button type="button" @click="activeTab = 'preview'" 
                :class="activeTab === 'preview' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200/80'"
                class="px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer ml-auto">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            <span>Xem Trước Bộ Lọc (Live Preview)</span>
        </button>
    </div>

    {{-- Hidden Form for Standard Submission fallback --}}
    <form id="filterForm" action="{{ $settingsSaveUrl }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="product_filter_settings" :value="JSON.stringify(getDataPayload())">
    </form>

    {{-- TAB 1: DANH MỤC SẢN PHẨM --}}
    <div x-show="activeTab === 'category'" class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7 space-y-6">
            
            {{-- Header & Master Toggle --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Nhóm Bộ Lọc: Loại Sản Phẩm</h2>
                        <p class="text-xs text-slate-500">Cấu hình danh mục xuất hiện trong khối lọc đầu tiên trên thanh sidebar.</p>
                    </div>
                </div>

                {{-- Toggle Enable/Disable --}}
                <label class="relative inline-flex items-center cursor-pointer select-none">
                    <input type="checkbox" x-model="categoryFilter.enabled" class="sr-only peer">
                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-bold text-slate-700" x-text="categoryFilter.enabled ? 'Đang BẬT' : 'Đang TẮT'"></span>
                </label>
            </div>

            {{-- Filter Title & Accordion options --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-1">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tiêu đề nhóm hiển thị</label>
                    <input type="text" x-model="categoryFilter.title" placeholder="LOẠI SẢN PHẨM" class="w-full px-4 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Mặc định: <span class="font-mono text-slate-600">LOẠI SẢN PHẨM</span></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kiểu chọn (Selection Type)</label>
                    <select x-model="categoryFilter.selection_type" class="w-full px-4 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none cursor-pointer">
                        <option value="checkbox">Nhiều lựa chọn (Checkbox) - Khuyên dùng</option>
                        <option value="radio">Chọn 1 danh mục duy nhất (Radio)</option>
                        <option value="dropdown">Menu thả xuống (Dropdown Select)</option>
                        <option value="tree">Cây phân cấp thu gọn (Hierarchical Tree)</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Định dạng hiển thị các danh mục cho khách hàng chọn.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Trạng thái khối (Accordion)</label>
                    <select x-model="categoryFilter.is_expanded" class="w-full px-4 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none cursor-pointer">
                        <option :value="true">Mở sẵn (Expanded) - Mặc định</option>
                        <option :value="false">Thu gọn sẵn (Collapsed)</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Trạng thái ban đầu khi khách truy cập trang.</p>
                </div>
            </div>

            {{-- Mode Selection --}}
            <div class="space-y-3 pt-3">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Chế độ danh mục trong bộ lọc</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Mode: custom --}}
                    <label @click="categoryFilter.mode = 'custom'" 
                           :class="categoryFilter.mode === 'custom' ? 'border-blue-600 bg-blue-50/50 shadow-xs ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-300 bg-white'"
                           class="relative p-4 rounded-xl border-2 cursor-pointer flex flex-col justify-between transition-all">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="block text-sm font-bold text-slate-900">Tự Chọn Danh Mục Thủ Công</span>
                                <span class="block text-xs text-slate-500 mt-1">Tích chọn chính xác các danh mục muốn đưa vào bộ lọc từ bảng bên dưới.</span>
                            </div>
                            <input type="radio" name="cat_mode" value="custom" x-model="categoryFilter.mode" class="mt-1 text-blue-600 focus:ring-0">
                        </div>
                        <span class="mt-3 inline-flex items-center text-[11px] font-semibold text-blue-700 bg-blue-100/60 px-2 py-0.5 rounded w-max">
                            Khuyên dùng &amp; Linh hoạt nhất
                        </span>
                    </label>

                    {{-- Mode: all_root --}}
                    <label @click="categoryFilter.mode = 'all_root'" 
                           :class="categoryFilter.mode === 'all_root' ? 'border-blue-600 bg-blue-50/50 shadow-xs ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-300 bg-white'"
                           class="relative p-4 rounded-xl border-2 cursor-pointer flex flex-col justify-between transition-all">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="block text-sm font-bold text-slate-900">Chỉ Danh Mục Cấp 1 (Gốc)</span>
                                <span class="block text-xs text-slate-500 mt-1">Tự động lấy tất cả danh mục cha cao nhất trong cây thư mục (như Đóng Cắt Trung Thế, Thiết Bị Đo Lường,...).</span>
                            </div>
                            <input type="radio" name="cat_mode" value="all_root" x-model="categoryFilter.mode" class="mt-1 text-blue-600 focus:ring-0">
                        </div>
                        <span class="mt-3 inline-flex items-center text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded w-max">
                            Tự động đồng bộ cấp gốc
                        </span>
                    </label>

                    {{-- Mode: all --}}
                    <label @click="categoryFilter.mode = 'all'" 
                           :class="categoryFilter.mode === 'all' ? 'border-blue-600 bg-blue-50/50 shadow-xs ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-300 bg-white'"
                           class="relative p-4 rounded-xl border-2 cursor-pointer flex flex-col justify-between transition-all">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="block text-sm font-bold text-slate-900">Tất Cả Danh Mục (Mọi Cấp)</span>
                                <span class="block text-xs text-slate-500 mt-1">Tự động nạp toàn bộ danh mục cây (Cấp 1, Cấp 2, Cấp 3...) vào bộ lọc.</span>
                            </div>
                            <input type="radio" name="cat_mode" value="all" x-model="categoryFilter.mode" class="mt-1 text-blue-600 focus:ring-0">
                        </div>
                        <span class="mt-3 inline-flex items-center text-[11px] font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded w-max">
                            Hiển thị toàn diện
                        </span>
                    </label>
                </div>
            </div>

            {{-- Banner Nổi Bật Đầu Trang (Top Showcase Banner) --}}
            <div class="p-4 sm:p-5 rounded-xl bg-slate-50 border border-slate-200/90 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            <span>Danh Mục Nổi Bật Đầu Trang (Showcase Banner)</span>
                        </h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Chọn các danh mục hiển thị dạng thẻ banner lớn ở đầu trang sản phẩm (Khuyên dùng 3 danh mục để đạt bố cục chuẩn đẹp nhất).</p>
                    </div>
                    <span class="text-xs font-bold text-blue-600 shrink-0" x-text="(featuredCategories.length || 0) + ' danh mục đã chọn'"></span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5 pt-1">
                    <template x-for="cat in allCategories" :key="'featured-' + cat.id">
                        <label class="flex items-center gap-2 p-2.5 rounded-lg border bg-white cursor-pointer transition-all text-xs select-none"
                               :class="featuredCategories.includes(cat.id) ? 'border-amber-500 bg-amber-50/50 text-amber-900 font-bold shadow-xs' : 'border-slate-200 text-slate-700 hover:border-slate-300'">
                            <input type="checkbox" 
                                   :value="cat.id" 
                                   :checked="featuredCategories.includes(cat.id)"
                                   @change="toggleFeaturedCategory(cat.id)"
                                   class="rounded border-slate-300 text-amber-600 focus:ring-0 cursor-pointer">
                            <span class="truncate" x-text="cat.name"></span>
                        </label>
                    </template>
                </div>
            </div>

            {{-- Categories Table (Tree List Selection) --}}
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span>Danh Sách Cây Thư Mục Danh Mục</span>
                            <span class="text-xs font-normal text-slate-500">(Đã chọn: <strong class="text-blue-600" x-text="categoryFilter.selected_categories.length"></strong>/{{ count($categoriesList) }})</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tích chọn các ô vuông để kích hoạt mục tương ứng trên thanh bộ lọc.</p>
                    </div>

                    {{-- Quick Action Buttons & Search --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <input type="text" x-model="catSearch" placeholder="Lọc danh mục nhanh..." class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs w-44 focus:bg-white focus:ring-1 focus:ring-blue-500 outline-none">
                        <button type="button" @click="selectAllCategories()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition cursor-pointer">
                            Chọn tất cả
                        </button>
                        <button type="button" @click="selectRootOnlyCategories()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition cursor-pointer">
                            Chỉ chọn Cấp 1
                        </button>
                        <button type="button" @click="deselectAllCategories()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 rounded-lg text-xs font-semibold transition cursor-pointer">
                            Bỏ chọn hết
                        </button>
                    </div>
                </div>

                {{-- Categories Table Display --}}
                <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">
                                    <input type="checkbox" :checked="isAllCategoriesSelected()" @change="toggleSelectAllCategories($event)" class="rounded border-slate-300 text-blue-600 focus:ring-0 cursor-pointer">
                                </th>
                                <th class="py-3 px-4">Cấu Trúc Cây Thư Mục</th>
                                <th class="py-3 px-4 w-32">Cấp Độ</th>
                                <th class="py-3 px-4 w-28 text-center">Số Sản Phẩm</th>
                                <th class="py-3 px-4 w-32 text-center">Trạng Thái Bộ Lọc</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700 bg-white">
                            @forelse($categoriesList as $cat)
                                @php
                                    $lvl = $cat->tree_level ?? 0;
                                    $indent = $lvl * 32;
                                @endphp
                                <tr x-show="matchesCatSearch('{{ strtolower(addslashes($cat->name)) }}', '{{ strtolower(addslashes($cat->slug)) }}')"
                                    :class="isCategorySelected({{ $cat->id }}) ? 'bg-blue-50/40' : 'hover:bg-slate-50/60'"
                                    class="transition-colors">
                                    {{-- Checkbox --}}
                                    <td class="py-3 px-4 text-center">
                                        <input type="checkbox" 
                                               :checked="isCategorySelected({{ $cat->id }})" 
                                               @change="toggleCategory({{ $cat->id }})"
                                               class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-0 cursor-pointer">
                                    </td>

                                    {{-- Category Name & Indentation --}}
                                    <td class="py-3 px-4">
                                        <div class="flex items-center" style="padding-left: {{ $indent }}px;">
                                            @if($lvl > 0)
                                                <div class="text-slate-300 mr-2 flex-shrink-0 select-none">
                                                    @if($lvl == 1)
                                                        <svg class="w-4 h-5" viewBox="0 0 20 28" fill="none" stroke="currentColor">
                                                            <path d="M 4 0 V 15 H 18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5" viewBox="0 0 24 28" fill="none" stroke="currentColor">
                                                            <path d="M 2 0 V 28 M 10 0 V 15 H 22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- Folder Icon --}}
                                            <div class="mr-2.5 flex-shrink-0">
                                                @if($lvl == 0)
                                                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                                    </div>
                                                @elseif($lvl == 1)
                                                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                                    </div>
                                                @else
                                                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <div>
                                                <div class="font-bold text-slate-900 flex items-center gap-2">
                                                    <span>{{ $cat->name }}</span>
                                                    @if(($cat->children_count ?? 0) > 0)
                                                        <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-1.5 py-0.2 rounded-full">
                                                            {{ $cat->children_count }} mục con
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                                    @if($cat->parent_name)
                                                        <span class="text-slate-500">Thuộc: {{ $cat->parent_name }}</span> • 
                                                    @endif
                                                    <span>/{{ $cat->slug }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Level Badge --}}
                                    <td class="py-3 px-4">
                                        @if($lvl == 0)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                Cấp 1 (Gốc)
                                            </span>
                                        @elseif($lvl == 1)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Cấp 2
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Cấp 3
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Product count --}}
                                    <td class="py-3 px-4 text-center font-bold text-slate-700">
                                        <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-[11px]">
                                            📦 {{ $cat->products_count ?? 0 }}
                                        </span>
                                    </td>

                                    {{-- Status in Filter --}}
                                    <td class="py-3 px-4 text-center">
                                        <span x-show="isCategorySelected({{ $cat->id }})" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            Hiển thị
                                        </span>
                                        <span x-show="!isCategorySelected({{ $cat->id }})" class="inline-flex items-center text-[11px] font-medium text-slate-400 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-full">
                                            Ẩn
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        Chưa có danh mục nào trong hệ thống. Vui lòng tạo danh mục tại <a href="{{ route('admin.categories.index') }}" class="text-blue-600 underline">Quản lý danh mục</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- TAB 2: THƯƠNG HIỆU --}}
    <div x-show="activeTab === 'brand'" class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7 space-y-6">
            
            {{-- Header & Master Toggle --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Nhóm Bộ Lọc: Thương Hiệu</h2>
                        <p class="text-xs text-slate-500">Cấu hình danh sách các hãng sản xuất và nhà cung cấp trong thanh bộ lọc.</p>
                    </div>
                </div>

                {{-- Toggle Enable/Disable --}}
                <label class="relative inline-flex items-center cursor-pointer select-none">
                    <input type="checkbox" x-model="brandFilter.enabled" class="sr-only peer">
                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-bold text-slate-700" x-text="brandFilter.enabled ? 'Đang BẬT' : 'Đang TẮT'"></span>
                </label>
            </div>

            {{-- Title and Options --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tiêu đề nhóm hiển thị</label>
                    <input type="text" x-model="brandFilter.title" placeholder="THƯƠNG HIỆU" class="w-full px-4 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Chế độ hiển thị Thương Hiệu</label>
                    <select x-model="brandFilter.mode" class="w-full px-4 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                        <option value="all">Tất cả thương hiệu có sẵn</option>
                        <option value="custom">Tùy chọn thủ công các thương hiệu bên dưới</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Định dạng hiển thị trên Web</label>
                    <select x-model="brandFilter.display_type" class="w-full px-4 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                        <option value="logo">Ưu tiên Logo hãng (ảnh/SVG)</option>
                        <option value="text">Chỉ hiển thị tên chữ</option>
                    </select>
                </div>
            </div>

            {{-- Brands Grid --}}
            <div class="pt-4 border-t border-slate-100 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Danh Sách Thương Hiệu Trong Hệ Thống</h3>
                        <p class="text-xs text-slate-500">Tích chọn các thương hiệu bạn muốn khách hàng có thể lọc.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="selectAllBrands()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition cursor-pointer">
                            Chọn tất cả
                        </button>
                        <button type="button" @click="deselectAllBrands()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 rounded-lg text-xs font-semibold transition cursor-pointer">
                            Bỏ chọn hết
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5">
                    @forelse($brandsList as $b)
                        <label @click="toggleBrand({{ $b->id }})" 
                               :class="isBrandSelected({{ $b->id }}) ? 'border-blue-600 bg-blue-50/40 ring-1 ring-blue-500' : 'border-slate-200 hover:border-slate-300 bg-white'"
                               class="p-3.5 rounded-xl border flex flex-col justify-between cursor-pointer transition-all select-none min-h-[90px]">
                            <div class="flex items-start justify-between">
                                <span class="text-xs font-bold text-slate-900 truncate">{{ $b->name }}</span>
                                <input type="checkbox" :checked="isBrandSelected({{ $b->id }})" class="rounded border-slate-300 text-blue-600 focus:ring-0">
                            </div>
                            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-400">
                                <span>/{{ $b->slug }}</span>
                                <span class="font-mono text-slate-500">📦 {{ $b->products_count ?? 0 }}</span>
                            </div>
                        </label>
                    @empty
                        <div class="col-span-full py-8 text-center text-slate-400">
                            Chưa có thương hiệu nào.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- TAB 3: KHOẢNG GIÁ --}}
    <div x-show="activeTab === 'price'" class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7 space-y-6">
            
            {{-- Header & Master Toggle --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Nhóm Bộ Lọc: Khoảng Giá</h2>
                        <p class="text-xs text-slate-500">Cấu hình các mức giá tùy chọn lọc sản phẩm theo nhu cầu ngân sách.</p>
                    </div>
                </div>

                {{-- Toggle Enable/Disable --}}
                <label class="relative inline-flex items-center cursor-pointer select-none">
                    <input type="checkbox" x-model="priceFilter.enabled" class="sr-only peer">
                    <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                    <span class="ml-3 text-sm font-bold text-slate-700" x-text="priceFilter.enabled ? 'Đang BẬT' : 'Đang TẮT'"></span>
                </label>
            </div>

            {{-- Title input --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tiêu đề nhóm hiển thị</label>
                    <input type="text" x-model="priceFilter.title" placeholder="KHOẢNG GIÁ" class="w-full px-4 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Trạng thái khối (Accordion)</label>
                    <select x-model="priceFilter.is_expanded" class="w-full px-4 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                        <option :value="true">Mở sẵn (Expanded) - Mặc định</option>
                        <option :value="false">Thu gọn sẵn (Collapsed)</option>
                    </select>
                </div>
            </div>

            {{-- Price Filter Type Selector (Visual Cards) --}}
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kiểu Lọc Giá (Price Filter Type) <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-xs text-slate-400">Chọn cách người dùng tương tác lọc giá trên website</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Option 1: Milestones (Mốc giá) --}}
                    <label @click="priceFilter.type = 'milestones'" 
                           :class="priceFilter.type === 'milestones' ? 'border-emerald-600 bg-emerald-50/50 shadow-xs ring-2 ring-emerald-100' : 'border-slate-200 hover:border-slate-300 bg-white'"
                           class="relative p-5 rounded-2xl border-2 cursor-pointer flex flex-col justify-between transition-all select-none">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                </div>
                                <div>
                                    <span class="block text-sm font-bold text-slate-900">Mốc Giá Định Sẵn (Milestones)</span>
                                    <span class="block text-xs text-slate-500 mt-0.5">Tích chọn các ô checkbox theo từng mốc khoảng giá</span>
                                </div>
                            </div>
                            <input type="radio" name="price_type" value="milestones" x-model="priceFilter.type" class="mt-1 text-emerald-600 focus:ring-0">
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                            <span class="font-semibold text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded">
                                ✓ Dễ chọn nhanh theo khoảng
                            </span>
                            <span class="text-slate-400 font-mono" x-text="(priceFilter.ranges ? priceFilter.ranges.length : 0) + ' mốc thiết lập'"></span>
                        </div>
                    </label>

                    {{-- Option 2: Slider Range (Thanh trượt) --}}
                    <label @click="priceFilter.type = 'slider'" 
                           :class="priceFilter.type === 'slider' ? 'border-emerald-600 bg-emerald-50/50 shadow-xs ring-2 ring-emerald-100' : 'border-slate-200 hover:border-slate-300 bg-white'"
                           class="relative p-5 rounded-2xl border-2 cursor-pointer flex flex-col justify-between transition-all select-none">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                </div>
                                <div>
                                    <span class="block text-sm font-bold text-slate-900">Thanh Trượt Khoảng Giá (Slider Range)</span>
                                    <span class="block text-xs text-slate-500 mt-0.5">Kéo 2 đầu Min - Max linh hoạt theo ngân sách</span>
                                </div>
                            </div>
                            <input type="radio" name="price_type" value="slider" x-model="priceFilter.type" class="mt-1 text-blue-600 focus:ring-0">
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                            <span class="font-semibold text-blue-700 bg-blue-100/70 px-2 py-0.5 rounded">
                                ★ Hiện đại &amp; Tương tác mượt mà
                            </span>
                            <span class="text-slate-400 font-mono" x-text="formatCurrency(priceFilter.min_price || 0) + ' → ' + formatCurrency(priceFilter.max_price || 1000000)"></span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Configuration when type === 'slider' --}}
            <div x-show="priceFilter.type === 'slider'" class="pt-4 border-t border-slate-100 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Cấu Hình Thanh Trượt Giá (Slider Settings)</h3>
                        <p class="text-xs text-slate-500">Thiết lập phạm vi số tiền tối thiểu, tối đa và bước nhảy khi kéo thanh trượt.</p>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500 flex-wrap">
                        <span class="font-semibold text-slate-700">Mẫu nhanh:</span>
                        <button type="button" @click="setPricePreset(0, 1000000, 50000)" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 font-medium text-[11px] transition cursor-pointer">0 - 1 Tr</button>
                        <button type="button" @click="setPricePreset(0, 5000000, 100000)" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 font-medium text-[11px] transition cursor-pointer">0 - 5 Tr</button>
                        <button type="button" @click="setPricePreset(0, 10000000, 200000)" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 font-medium text-[11px] transition cursor-pointer">0 - 10 Tr</button>
                        <button type="button" @click="setPricePreset(0, 20000000, 500000)" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 font-medium text-[11px] transition cursor-pointer">0 - 20 Tr</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Giá Nhỏ Nhất (Min Price)</label>
                        <div class="relative">
                            <input type="number" x-model.number="priceFilter.min_price" step="10000" class="w-full pl-4 pr-12 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                            <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400">VNĐ</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1" x-text="'Hiển thị: ' + formatCurrency(priceFilter.min_price || 0)"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Giá Lớn Nhất (Max Price)</label>
                        <div class="relative">
                            <input type="number" x-model.number="priceFilter.max_price" step="50000" class="w-full pl-4 pr-12 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                            <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400">VNĐ</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1" x-text="'Hiển thị: ' + formatCurrency(priceFilter.max_price || 1000000)"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bước Nhảy (Step)</label>
                        <div class="relative">
                            <input type="number" x-model.number="priceFilter.step" step="5000" class="w-full pl-4 pr-12 py-2.5 bg-slate-50 focus:bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                            <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400">VNĐ</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1" x-text="'Mỗi bước: ' + formatCurrency(priceFilter.step || 50000)"></p>
                    </div>
                </div>

                {{-- Allow Contact Option --}}
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Hỗ Trợ Sản Phẩm "Giá Liên Hệ"</span>
                        <span class="block text-xs text-slate-500 mt-0.5">Hiển thị thêm ô checkbox "Bao gồm giá liên hệ" bên dưới thanh trượt để khách không bỏ sót các sản phẩm báo giá dự án.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" x-model="priceFilter.allow_contact" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                    </label>
                </div>

                {{-- Interactive Admin Mini Preview of Slider --}}
                <div class="p-5 bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl text-white space-y-4">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold uppercase tracking-wider text-slate-300">Mô phỏng thanh trượt thực tế ngoài website:</span>
                        <span class="font-mono text-emerald-400 font-bold" x-text="formatCurrency(priceFilter.min_price || 0) + ' — ' + formatCurrency(priceFilter.max_price || 1000000)"></span>
                    </div>
                    <div class="relative w-full h-4 flex items-center">
                        <div class="w-full h-2 bg-slate-700 rounded-full relative overflow-hidden">
                            <div class="absolute h-full bg-blue-500 rounded-full" style="left: 0%; right: 0%;"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400">
                        <span x-text="'Thấp nhất: ' + formatCurrency(priceFilter.min_price || 0)"></span>
                        <span x-text="'Bước nhảy: ' + formatCurrency(priceFilter.step || 50000)"></span>
                        <span x-text="'Cao nhất: ' + formatCurrency(priceFilter.max_price || 1000000)"></span>
                    </div>
                </div>
            </div>

            {{-- Configuration when type === 'milestones' (Price Ranges List) --}}
            <div x-show="priceFilter.type === 'milestones'" class="pt-4 border-t border-slate-100 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <span>Danh Sách Các Mốc Giá Định Sẵn</span>
                            <span class="text-xs font-normal text-slate-500">(Hiện có: <strong class="text-blue-600" x-text="(priceFilter.ranges || []).length"></strong> mốc)</span>
                        </h3>
                        <p class="text-xs text-slate-500">Người dùng chỉ cần nhập khoảng tiền, hệ thống sẽ tự động tạo nhãn và sinh mã ID ngầm.</p>
                    </div>
                    <button type="button" @click="addPriceRange()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Thêm Mốc Giá Mới</span>
                    </button>
                </div>

                {{-- Hướng dẫn thân thiện cho người không chuyên --}}
                <div class="p-3.5 bg-blue-50/80 border border-blue-100 rounded-xl flex items-start gap-2.5 text-xs text-blue-900">
                    <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <strong class="font-bold">Dành cho người quản trị:</strong>
                        <span>Bạn chỉ cần điền <strong>Giá từ</strong> và <strong>Giá đến</strong> (hoặc chọn <em>Giá liên hệ</em>). Hệ thống sẽ <strong>tự động sinh mã kỹ thuật (ID)</strong> và <strong>tự động tạo tên nhãn chuẩn</strong> (vd: <em>"200.000đ - 500.000đ"</em>). Bạn hoàn toàn không cần phải biết về lập trình hay tự gõ mã ID!</span>
                    </div>
                </div>

                {{-- 1-Click Preset Kits --}}
                <div class="flex flex-wrap items-center gap-2 pt-1 pb-1">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"></path></svg>
                        Nạp nhanh bộ mốc có sẵn:
                    </span>
                    <button type="button" @click="applyPricePreset('electrical')" class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 hover:text-blue-700 rounded-lg text-xs font-semibold transition cursor-pointer shadow-2xs">
                        ⚡ Thiết Bị Điện &amp; Vật Tư (&lt; 500k → &gt; 10tr)
                    </button>
                    <button type="button" @click="applyPricePreset('popular')" class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 hover:text-blue-700 rounded-lg text-xs font-semibold transition cursor-pointer shadow-2xs">
                        ⚡ Hàng Phổ Thông (&lt; 200k → &gt; 1tr)
                    </button>
                    <button type="button" @click="applyPricePreset('heavy')" class="px-3 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 hover:text-blue-700 rounded-lg text-xs font-semibold transition cursor-pointer shadow-2xs">
                        ⚡ Công Nghiệp Nặng / Trạm Biến Áp (&lt; 5tr → &gt; 50tr)
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(range, idx) in priceFilter.ranges" :key="idx">
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-slate-300 transition-all shadow-xs space-y-3">
                            {{-- Top Header of Item --}}
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center" x-text="idx + 1"></span>
                                    <span class="text-xs font-bold text-slate-800" x-text="range.label || 'Mốc giá #' + (idx + 1)"></span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono bg-slate-100 text-slate-500" :title="'Mã ID hệ thống: ' + range.id">
                                        Mã ID: <strong class="ml-1 text-slate-700" x-text="range.id || 'auto'"></strong>
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" @click="range.show_advanced = !range.show_advanced" class="px-2 py-1 text-[11px] text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-md transition cursor-pointer" title="Cấu hình nâng cao">
                                        <span x-text="range.show_advanced ? 'Ẩn mã kỹ thuật' : '⚙️ Tùy chỉnh mã ID'"></span>
                                    </button>
                                    <button type="button" @click="removePriceRange(idx)" class="w-7 h-7 rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-700 flex items-center justify-center transition cursor-pointer" title="Xóa mức giá này">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Main Inputs Grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end">
                                {{-- Loại giá --}}
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Loại mốc giá</label>
                                    <select x-model="range.type" @change="onPriceRangeTypeChange(range)" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 cursor-pointer focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                        <option value="range">Khoảng số tiền (VNĐ)</option>
                                        <option value="contact">Giá liên hệ dự án</option>
                                    </select>
                                </div>

                                {{-- Giá từ (VNĐ) --}}
                                <div class="md:col-span-2" x-show="range.type !== 'contact'">
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Giá từ (VNĐ)</label>
                                    <input type="number" x-model.number="range.min" @input="onPriceRangeInputChange(range)" placeholder="0" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold font-mono focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                    <div class="text-[10px] text-emerald-600 font-semibold mt-1 truncate" x-text="formatCurrency(range.min || 0)"></div>
                                </div>

                                {{-- Giá đến (VNĐ) --}}
                                <div class="md:col-span-2" x-show="range.type !== 'contact'">
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Giá đến (VNĐ)</label>
                                    <input type="number" x-model.number="range.max" @input="onPriceRangeInputChange(range)" placeholder="Không giới hạn" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold font-mono focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                    <div class="text-[10px] text-slate-500 font-semibold mt-1 truncate" x-text="range.max ? formatCurrency(range.max) : 'Trở lên (không giới hạn)'"></div>
                                </div>

                                {{-- Nhãn hiển thị --}}
                                <div :class="range.type === 'contact' ? 'md:col-span-9' : 'md:col-span-5'">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-xs font-bold text-slate-700">Tên nhãn hiển thị ngoài web</label>
                                        <button type="button" @click="autoFormatRangeLabel(range)" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 transition cursor-pointer flex items-center gap-1" title="Tự động đặt lại tên chuẩn theo số tiền">
                                            <span>🪄 Tự tạo tên chuẩn</span>
                                        </button>
                                    </div>
                                    <input type="text" x-model="range.label" @input="range.is_custom_label = true" placeholder="vd: Dưới 200.000đ" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                            </div>

                            {{-- Advanced Collapsible: Custom ID --}}
                            <div x-show="range.show_advanced" x-transition class="pt-2.5 border-t border-dashed border-slate-200 flex items-center gap-3 bg-slate-50/70 p-3 rounded-xl">
                                <div class="w-full sm:w-80">
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Mã kỹ thuật (ID slug - Hệ thống tự động quản lý)</label>
                                    <input type="text" x-model="range.id" placeholder="vd: under_200" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono">
                                    <p class="text-[10px] text-slate-400 mt-0.5">Lưu ý: Không đổi nếu không cần thiết, mã được tạo tự động để lọc sản phẩm.</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

    {{-- TAB 4: LIVE PREVIEW BỘ LỌC --}}
    <div x-show="activeTab === 'preview'" class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Xem Trước Trực Quan Giao Diện Bộ Lọc (Frontend Live Preview)</h2>
                    <p class="text-xs text-slate-500">Mô phỏng chính xác thanh sidebar bộ lọc thực tế ngoài website như trên trang sản phẩm.</p>
                </div>
                <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-semibold">
                    Đồng bộ thời gian thực
                </span>
            </div>

            {{-- Live Mock Sidebar Container --}}
            <div class="max-w-[320px] mx-auto bg-white p-6 rounded-2xl border-2 border-slate-800 shadow-xl space-y-6 select-none font-sans">
                
                {{-- Filter Header --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 6h16M4 12h10M4 18h6"></path>
                        </svg>
                        <span class="text-[14px] font-bold uppercase tracking-wider text-black">BỘ LỌC</span>
                    </div>
                    <button class="w-8 h-8 rounded-full border border-neutral-400 text-neutral-700 flex items-center justify-center" type="button" title="Đặt lại">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                </div>

                <div class="w-full border-t border-black"></div>

                {{-- GROUP 1: CATEGORIES --}}
                <div x-show="categoryFilter.enabled" class="space-y-4">
                    <div class="flex items-center justify-between cursor-pointer py-0.5">
                        <h3 class="text-[13px] font-bold uppercase tracking-wider text-black" x-text="categoryFilter.title || 'LOẠI SẢN PHẨM'"></h3>
                        <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                    </div>

                    {{-- Dropdown selection type --}}
                    <div x-show="categoryFilter.selection_type === 'dropdown'" class="pt-1">
                        <select class="w-full px-3 py-2 bg-neutral-100 border border-neutral-300 rounded-lg text-xs font-bold uppercase text-black outline-none cursor-pointer">
                            <option value="">-- Tất cả danh mục --</option>
                            <template x-for="cat in getPreviewCategories()" :key="cat.id">
                                <option :value="cat.slug" x-text="cat.name"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Tree or List selection types --}}
                    <div x-show="categoryFilter.selection_type !== 'dropdown'" class="space-y-3 pt-1 text-sm">
                        <template x-for="cat in getPreviewCategories()" :key="cat.id">
                            <label class="flex items-center gap-3 cursor-pointer py-0.5 group" :style="categoryFilter.selection_type === 'tree' ? 'padding-left: ' + (cat.level * 14) + 'px' : ''">
                                <input :type="categoryFilter.selection_type === 'radio' ? 'radio' : 'checkbox'" name="preview_cat" class="w-5 h-5 rounded-[1.75px] border-[#818181] text-[#051650] focus:ring-0">
                                <span class="text-xs font-medium text-black uppercase group-hover:text-blue-900 transition-colors flex items-center gap-1.5">
                                    <span x-show="categoryFilter.selection_type === 'tree' && cat.level > 0" class="text-slate-400 font-mono">└</span>
                                    <span x-text="cat.name"></span>
                                </span>
                            </label>
                        </template>
                        <div x-show="getPreviewCategories().length === 0" class="text-xs text-slate-400 italic">
                            Chưa chọn danh mục nào để hiển thị.
                        </div>
                    </div>
                </div>

                <div x-show="categoryFilter.enabled" class="w-full border-t border-black"></div>

                {{-- GROUP 2: BRANDS --}}
                <div x-show="brandFilter.enabled" class="space-y-4">
                    <div class="flex items-center justify-between cursor-pointer py-0.5">
                        <h3 class="text-[13px] font-bold uppercase tracking-wider text-black" x-text="brandFilter.title || 'THƯƠNG HIỆU'"></h3>
                        <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                    </div>

                    <div class="space-y-3 pt-1">
                        <template x-for="brand in getPreviewBrands()" :key="brand.id">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" class="w-5 h-5 rounded-[1.75px] border-[#818181] text-[#051650] focus:ring-0">
                                <span class="text-xs font-bold text-slate-800 uppercase" x-text="brand.name"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <div x-show="brandFilter.enabled" class="w-full border-t border-black"></div>

                {{-- GROUP 3: PRICE RANGES OR SLIDER --}}
                <div x-show="priceFilter.enabled" class="space-y-4">
                    <div class="flex items-center justify-between cursor-pointer py-0.5">
                        <h3 class="text-[13px] font-bold uppercase tracking-wider text-black" x-text="priceFilter.title || 'KHOẢNG GIÁ'"></h3>
                        <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                    </div>

                    {{-- Option: Slider Range Live Mock --}}
                    <div x-show="priceFilter.type === 'slider'" class="space-y-3 pt-1">
                        <div class="flex items-center justify-between text-xs font-bold text-neutral-800">
                            <span x-text="formatCurrency(priceFilter.min_price || 0)"></span>
                            <span class="text-neutral-400 font-normal">-</span>
                            <span x-text="formatCurrency(priceFilter.max_price || 1000000)"></span>
                        </div>
                        <div class="relative w-full h-4 flex items-center">
                            <div class="w-full h-1.5 bg-neutral-200 rounded-full relative overflow-hidden">
                                <div class="absolute h-full bg-[#051650] rounded-full" style="left: 0%; right: 0%;"></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            <div class="flex-1 bg-neutral-100 rounded-lg px-2.5 py-1.5 border border-neutral-200">
                                <span class="block text-[9px] text-neutral-500 font-semibold uppercase">Từ</span>
                                <span class="text-xs font-bold text-black" x-text="formatCurrency(priceFilter.min_price || 0)"></span>
                            </div>
                            <span class="text-neutral-400 font-bold">-</span>
                            <div class="flex-1 bg-neutral-100 rounded-lg px-2.5 py-1.5 border border-neutral-200">
                                <span class="block text-[9px] text-neutral-500 font-semibold uppercase">Đến</span>
                                <span class="text-xs font-bold text-black" x-text="formatCurrency(priceFilter.max_price || 1000000)"></span>
                            </div>
                        </div>
                        <div x-show="priceFilter.allow_contact" class="pt-1">
                            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                <input type="checkbox" class="w-4 h-4 rounded border-[#818181] text-[#051650] focus:ring-0">
                                <span class="text-xs font-medium text-neutral-700">Bao gồm giá liên hệ</span>
                            </label>
                        </div>
                    </div>

                    {{-- Option: Milestones Checklist Live Mock --}}
                    <div x-show="priceFilter.type !== 'slider'" class="space-y-3 pt-1">
                        <template x-for="r in priceFilter.ranges" :key="r.id">
                            <label class="flex items-center gap-3 cursor-pointer py-0.5">
                                <input type="checkbox" class="w-5 h-5 rounded-[1.75px] border-[#818181] text-[#051650] focus:ring-0">
                                <span class="text-xs font-medium text-black" x-text="r.label"></span>
                            </label>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Bottom Floating Save Action Bar --}}
    <div class="sticky bottom-4 z-40 bg-white/95 backdrop-blur-md p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xl flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-600">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
            <span>Các thay đổi sẽ được áp dụng ngay lập tức cho trang sản phẩm ngoài website.</span>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.settings.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold text-xs sm:text-sm transition">
                Hủy bỏ
            </a>
            <button type="button" @click="saveSettings()" :disabled="isSaving" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-blue-500/20 transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50">
                <svg x-show="!isSaving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <svg x-show="isSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span x-text="isSaving ? 'Đang lưu...' : 'Lưu Thay Đổi Bộ Lọc'">Lưu Thay Đổi Bộ Lọc</span>
            </button>
        </div>
    </div>

</div>

<script>
function productFilterManager() {
    return {
        activeTab: 'category',
        isSaving: false,
        catSearch: '',
        
        // Data sources
        allCategories: @json($categoriesJson),
        allBrands: @json($brandsJson),

        // Settings Model
        categoryFilter: @json($categoryFilterData),
        brandFilter: @json($brandFilterData),
        priceFilter: @json($priceFilterData),
        featuredCategories: @json($featuredCategoriesData),

        toggleFeaturedCategory(id) {
            const idx = this.featuredCategories.indexOf(id);
            if (idx > -1) {
                this.featuredCategories.splice(idx, 1);
            } else {
                this.featuredCategories.push(id);
            }
        },

        init() {
            if (!Array.isArray(this.categoryFilter.selected_categories)) {
                this.categoryFilter.selected_categories = [];
            }
            if (!this.categoryFilter.selection_type) {
                this.categoryFilter.selection_type = 'checkbox';
            }
            if (!Array.isArray(this.brandFilter.selected_brands)) {
                this.brandFilter.selected_brands = [];
            }
            if (!this.priceFilter.type) {
                this.priceFilter.type = 'milestones';
            }
            if (this.priceFilter.min_price === undefined) {
                this.priceFilter.min_price = 0;
            }
            if (this.priceFilter.max_price === undefined) {
                this.priceFilter.max_price = 1000000;
            }
            if (this.priceFilter.step === undefined) {
                this.priceFilter.step = 50000;
            }
            if (this.priceFilter.allow_contact === undefined) {
                this.priceFilter.allow_contact = true;
            }
            if (!Array.isArray(this.priceFilter.ranges)) {
                this.priceFilter.ranges = [];
            }
            this.priceFilter.ranges.forEach(r => {
                if (r.show_advanced === undefined) r.show_advanced = false;
                if (!r.id) r.id = this.generateRangeId(r);
            });
        },

        formatCurrency(num) {
            return (Number(num) || 0).toLocaleString('vi-VN') + 'đ';
        },

        setPricePreset(min, max, step) {
            this.priceFilter.min_price = min;
            this.priceFilter.max_price = max;
            this.priceFilter.step = step;
        },

        // Category Helpers
        isCategorySelected(id) {
            return this.categoryFilter.selected_categories.includes(id);
        },

        toggleCategory(id) {
            const idx = this.categoryFilter.selected_categories.indexOf(id);
            if (idx > -1) {
                this.categoryFilter.selected_categories.splice(idx, 1);
            } else {
                this.categoryFilter.selected_categories.push(id);
            }
        },

        isAllCategoriesSelected() {
            return this.allCategories.length > 0 && this.allCategories.every(c => this.categoryFilter.selected_categories.includes(c.id));
        },

        toggleSelectAllCategories(event) {
            if (event.target.checked) {
                this.selectAllCategories();
            } else {
                this.deselectAllCategories();
            }
        },

        selectAllCategories() {
            this.categoryFilter.selected_categories = this.allCategories.map(c => c.id);
        },

        deselectAllCategories() {
            this.categoryFilter.selected_categories = [];
        },

        selectRootOnlyCategories() {
            this.categoryFilter.selected_categories = this.allCategories
                .filter(c => (c.level === 0 || !c.parent_id))
                .map(c => c.id);
        },

        selectedCatCountText() {
            if (this.categoryFilter.mode === 'all_root') return 'Tất cả Cấp 1';
            if (this.categoryFilter.mode === 'all') return 'Toàn bộ';
            return this.categoryFilter.selected_categories.length + ' đã chọn';
        },

        matchesCatSearch(name, slug) {
            if (!this.catSearch.trim()) return true;
            const q = this.catSearch.toLowerCase().trim();
            return name.includes(q) || slug.includes(q);
        },

        // Brand Helpers
        isBrandSelected(id) {
            return this.brandFilter.selected_brands.includes(id);
        },

        toggleBrand(id) {
            const idx = this.brandFilter.selected_brands.indexOf(id);
            if (idx > -1) {
                this.brandFilter.selected_brands.splice(idx, 1);
            } else {
                this.brandFilter.selected_brands.push(id);
            }
        },

        selectAllBrands() {
            this.brandFilter.selected_brands = this.allBrands.map(b => b.id);
        },

        deselectAllBrands() {
            this.brandFilter.selected_brands = [];
        },

        categoryBrandText() {
            if (this.brandFilter.mode === 'all') return 'Tất cả';
            return this.brandFilter.selected_brands.length + ' đã chọn';
        },

        // Price Helpers & Non-technical Auto-generation
        generateRangeId(range) {
            if (range.type === 'contact') return 'contact';
            const min = Number(range.min) || 0;
            const max = range.max !== null && range.max !== '' && range.max !== undefined ? Number(range.max) : null;
            
            const formatK = (val) => {
                if (val >= 1000000) {
                    const m = val / 1000000;
                    return (Number.isInteger(m) ? m : m.toFixed(1).replace('.', '_')) + 'm';
                }
                return Math.round(val / 1000) + 'k';
            };

            if (min === 0 && max !== null) {
                return 'under_' + formatK(max);
            } else if (min > 0 && max !== null) {
                return formatK(min) + '_' + formatK(max);
            } else if (min > 0 && max === null) {
                return 'over_' + formatK(min);
            }
            return 'p_range_' + Math.floor(Math.random() * 10000);
        },

        generateRangeLabel(range) {
            if (range.type === 'contact') return 'Giá liên hệ dự án';
            const min = Number(range.min) || 0;
            const max = range.max !== null && range.max !== '' && range.max !== undefined ? Number(range.max) : null;

            if (min === 0 && max !== null) {
                return 'Dưới ' + this.formatCurrency(max);
            } else if (min > 0 && max !== null) {
                return this.formatCurrency(min) + ' - ' + this.formatCurrency(max);
            } else if (min > 0 && max === null) {
                return 'Trên ' + this.formatCurrency(min);
            }
            return 'Mức giá tùy chỉnh';
        },

        onPriceRangeTypeChange(range) {
            if (range.type === 'contact') {
                range.min = null;
                range.max = null;
                range.label = 'Giá liên hệ dự án';
                range.id = 'contact';
            } else {
                if (range.min === null || range.min === undefined) range.min = 0;
                if (!range.max) range.max = 500000;
                range.label = this.generateRangeLabel(range);
                range.id = this.generateRangeId(range);
            }
        },

        onPriceRangeInputChange(range) {
            range.id = this.generateRangeId(range);
            if (!range.label || range.is_custom_label !== true) {
                range.label = this.generateRangeLabel(range);
            }
        },

        autoFormatRangeLabel(range) {
            range.label = this.generateRangeLabel(range);
            range.id = this.generateRangeId(range);
            range.is_custom_label = false;
        },

        addPriceRange() {
            let nextMin = 0;
            let nextMax = 500000;
            
            const numericRanges = (this.priceFilter.ranges || []).filter(r => r.type !== 'contact' && r.max);
            if (numericRanges.length > 0) {
                const last = numericRanges[numericRanges.length - 1];
                nextMin = Number(last.max) || 0;
                nextMax = nextMin >= 10000000 ? nextMin + 10000000 : (nextMin >= 1000000 ? nextMin + 2000000 : (nextMin >= 200000 ? nextMin + 300000 : 500000));
            }

            const newRange = {
                id: '',
                label: '',
                type: 'range',
                min: nextMin,
                max: nextMax,
                is_custom_label: false,
                show_advanced: false
            };
            newRange.id = this.generateRangeId(newRange);
            newRange.label = this.generateRangeLabel(newRange);

            const contactIdx = (this.priceFilter.ranges || []).findIndex(r => r.type === 'contact');
            if (contactIdx > -1) {
                this.priceFilter.ranges.splice(contactIdx, 0, newRange);
            } else {
                this.priceFilter.ranges.push(newRange);
            }
        },

        removePriceRange(index) {
            this.priceFilter.ranges.splice(index, 1);
        },

        applyPricePreset(presetKey) {
            if (presetKey === 'electrical') {
                this.priceFilter.ranges = [
                    { id: 'under_500k', label: 'Dưới 500.000đ', min: 0, max: 500000, type: 'range', show_advanced: false },
                    { id: '500k_2m', label: '500.000đ - 2.000.000đ', min: 500000, max: 2000000, type: 'range', show_advanced: false },
                    { id: '2m_10m', label: '2.000.000đ - 10.000.000đ', min: 2000000, max: 10000000, type: 'range', show_advanced: false },
                    { id: 'over_10m', label: 'Trên 10.000.000đ', min: 10000000, max: null, type: 'range', show_advanced: false },
                    { id: 'contact', label: 'Giá liên hệ dự án', min: null, max: null, type: 'contact', show_advanced: false },
                ];
            } else if (presetKey === 'popular') {
                this.priceFilter.ranges = [
                    { id: 'under_200k', label: 'Dưới 200.000đ', min: 0, max: 200000, type: 'range', show_advanced: false },
                    { id: '200k_500k', label: '200.000đ - 500.000đ', min: 200000, max: 500000, type: 'range', show_advanced: false },
                    { id: '500k_1m', label: '500.000đ - 1.000.000đ', min: 500000, max: 1000000, type: 'range', show_advanced: false },
                    { id: 'over_1m', label: 'Trên 1.000.000đ', min: 1000000, max: null, type: 'range', show_advanced: false },
                    { id: 'contact', label: 'Giá liên hệ dự án', min: null, max: null, type: 'contact', show_advanced: false },
                ];
            } else if (presetKey === 'heavy') {
                this.priceFilter.ranges = [
                    { id: 'under_5m', label: 'Dưới 5.000.000đ', min: 0, max: 5000000, type: 'range', show_advanced: false },
                    { id: '5m_20m', label: '5.000.000đ - 20.000.000đ', min: 5000000, max: 20000000, type: 'range', show_advanced: false },
                    { id: '20m_50m', label: '20.000.000đ - 50.000.000đ', min: 20000000, max: 50000000, type: 'range', show_advanced: false },
                    { id: 'over_50m', label: 'Trên 50.000.000đ', min: 50000000, max: null, type: 'range', show_advanced: false },
                    { id: 'contact', label: 'Giá liên hệ dự án', min: null, max: null, type: 'contact', show_advanced: false },
                ];
            }
        },

        // Preview Helpers
        getPreviewCategories() {
            if (this.categoryFilter.mode === 'all_root') {
                return this.allCategories.filter(c => c.level === 0 || !c.parent_id);
            }
            if (this.categoryFilter.mode === 'all') {
                return this.allCategories;
            }
            return this.allCategories.filter(c => this.categoryFilter.selected_categories.includes(c.id));
        },

        getPreviewBrands() {
            if (this.brandFilter.mode === 'all') {
                return this.allBrands;
            }
            return this.allBrands.filter(b => this.brandFilter.selected_brands.includes(b.id));
        },

        // Payload Serialization
        getDataPayload() {
            return {
                category_filter: this.categoryFilter,
                brand_filter: this.brandFilter,
                price_filter: this.priceFilter,
                featured_categories: this.featuredCategories,
            };
        },

        async saveSettings() {
            this.isSaving = true;
            const payload = this.getDataPayload();

            try {
                const response = await fetch('{{ $settingsSaveUrl }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_filter_settings: payload
                    })
                });

                const data = await response.json();
                if (data.success) {
                    alert('✓ ' + (data.message || 'Lưu cấu hình bộ lọc thành công!'));
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể lưu cấu hình.'));
                }
            } catch (err) {
                console.error(err);
                // Fallback to standard form submit
                document.getElementById('filterForm').submit();
            } finally {
                this.isSaving = false;
            }
        }
    };
}
</script>
@endsection
