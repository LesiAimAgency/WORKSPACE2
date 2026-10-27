{{-- MODIFIED: 2025-01-21 - Modern Admin Products Dashboard --}}
@extends('admin.layouts.app')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Sản phẩm')

@section('content')
@include('cms.components.alert')

<!-- Multi-site Project Header -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-6 text-white mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold">Dự án: {{ strtoupper($currentProject->code ?? request()->route('projectCode')) }}</h1>
                <p class="text-blue-100">Quản lý sản phẩm multi-site</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <span class="text-blue-100 text-sm font-medium">Database:</span> 
                <span class="font-mono font-semibold">project_{{ strtolower($currentProject->code ?? request()->route('projectCode')) }}</span>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <span class="text-blue-100 text-sm font-medium">Ngôn ngữ:</span> 
                <span class="font-semibold">
                    @if(($languageId ?? 1) == 1) Tiếng Việt
                    @elseif(($languageId ?? 1) == 2) English  
                    @elseif(($languageId ?? 1) == 3) 中文
                    @else ID: {{ $languageId ?? 1 }}
                    @endif
                </span>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <span class="text-blue-100 text-sm font-medium">Sản phẩm:</span> 
                <span class="font-bold text-xl">{{ $products->total() }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-blue-500">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-blue-50 rounded-xl">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Tổng sản phẩm</p>
                <p class="text-2xl font-bold text-gray-900">{{ $products->total() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-green-500">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-green-50 rounded-xl">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Đã xuất bản</p>
                <p class="text-2xl font-bold text-gray-900">{{ $products->where('status', 'published')->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-yellow-500">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-yellow-50 rounded-xl">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Nháp</p>
                <p class="text-2xl font-bold text-gray-900">{{ $products->where('status', 'draft')->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-purple-500">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-purple-50 rounded-xl">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Danh mục</p>
                <p class="text-2xl font-bold text-gray-900">{{ $parentCategories->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Actions -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <form method="GET" class="flex-1 w-full lg:w-auto">
            <div class="flex flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Tìm kiếm sản phẩm..." 
                       class="flex-1 min-w-[200px] px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                
                <select name="language_id" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <option value="1" {{ ($languageId ?? 1) == 1 ? 'selected' : '' }}>Tiếng Việt</option>
                    <option value="2" {{ ($languageId ?? 1) == 2 ? 'selected' : '' }}>English</option>
                    <option value="3" {{ ($languageId ?? 1) == 3 ? 'selected' : '' }}>中文</option>
                </select>
                
                <select name="category" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả danh mục</option>
                    @foreach($parentCategories as $parent)
                        <optgroup label="{{ $parent->name }}">
                            <option value="{{ $parent->id }}" {{ request('category') == $parent->id ? 'selected' : '' }}>-- Tất cả</option>
                            @foreach($parent->children as $child)
                                <option value="{{ $child->id }}" {{ request('category') == $child->id ? 'selected' : '' }}>
                                    {{ $child->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                </select>
                
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </form>
        
        <div class="flex gap-2">
            <button id="bulkEditBtn" onclick="openBulkEdit()" 
                    class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-xl hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    disabled>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Sửa nhanh (<span id="selectedCount">0</span>)
            </button>
            <a href="{{ route('project.admin.categories.index', request()->route('projectCode')) }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Danh mục
            </a>
            <a href="{{ route('project.admin.products.create', request()->route('projectCode')) }}" 
               class="inline-flex items-center px-5 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Thêm sản phẩm
            </a>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600">
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Danh mục & Thương hiệu</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Giá</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kho</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="product-checkbox rounded border-gray-300 text-blue-600" value="{{ $product->id }}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12 rounded-lg overflow-hidden">
                                @if($product->featured_image)
                                    <img class="h-12 w-12 rounded-lg object-cover" src="{{ $product->featured_image }}" alt="">
                                @else
                                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-xs">IMG</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $product->name }}</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v4a2 2 0 01-2 2H5z"></path>
                                        </svg>
                                        {{ strtoupper($currentProject->code ?? request()->route('projectCode')) }}
                                    </span>
                                </div>
                                <!-- Badges -->
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-xs text-gray-500 mr-2">Badges:</span>
                                    <!-- Featured -->
                                    <button onclick="toggleBadge({{ $product->id }}, 'featured')" 
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg {{ $product->is_featured ? 'bg-yellow-200 text-yellow-800' : 'bg-gray-100 text-gray-400' }} hover:bg-yellow-300 transition-colors" 
                                            title="Nổi bật">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </button>
                                    <!-- Favorite -->
                                    <button onclick="toggleBadge({{ $product->id }}, 'favorite')" 
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg {{ $product->is_favorite ? 'bg-red-200 text-red-800' : 'bg-gray-100 text-gray-400' }} hover:bg-red-300 transition-colors" 
                                            title="Yêu thích">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    <!-- Bestseller -->
                                    <button onclick="toggleBadge({{ $product->id }}, 'bestseller')" 
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg {{ $product->is_bestseller ? 'bg-green-200 text-green-800' : 'bg-gray-100 text-gray-400' }} hover:bg-green-300 transition-colors" 
                                            title="Bán chạy">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $product->sku }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @if($product->categories && $product->categories->count() > 0)
                            <div class="mb-2">
                                <div class="text-xs font-medium text-gray-600 mb-1">Danh mục:</div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($product->categories->take(3) as $category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                    @if($product->categories->count() > 3)
                                        <span class="text-xs text-gray-400">+{{ $product->categories->count() - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        @if($product->brands && $product->brands->count() > 0)
                            <div>
                                <div class="text-xs font-medium text-gray-600 mb-1">Thương hiệu:</div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($product->brands->take(3) as $brand)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            {{ $brand->name }}
                                        </span>
                                    @endforeach
                                    @if($product->brands->count() > 3)
                                        <span class="text-xs text-gray-400">+{{ $product->brands->count() - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $product->display_price }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $product->stock_status === 'in_stock' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->stock_quantity }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $product->status === 'published' ? 'bg-green-100 text-green-800' : 
                               ($product->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openSingleQuickEdit({{ $product->id }})" 
                                    class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors" 
                                    title="Sửa nhanh">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                </svg>
                            </button>
                            <a href="{{ route('project.admin.products.edit', [request()->route('projectCode'), $product]) }}" 
                               class="p-2 bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-200 transition-colors" 
                               title="Chỉnh sửa">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('project.admin.products.destroy', [request()->route('projectCode'), $product]) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors" 
                                        title="Xóa" 
                                        onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <div class="bg-gray-100 rounded-full p-4">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="text-center max-w-md">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Chưa có sản phẩm nào</h3>
                                <p class="text-gray-500 mb-4">Dự án <strong class="text-gray-900">{{ strtoupper($currentProject->code ?? request()->route('projectCode')) }}</strong> chưa có sản phẩm nào được tạo.</p>
                                <a href="{{ route('project.admin.products.create', request()->route('projectCode')) }}" 
                                   class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tạo sản phẩm đầu tiên
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $products->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection