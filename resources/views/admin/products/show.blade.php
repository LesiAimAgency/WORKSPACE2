{{-- MODIFIED: 2025-01-22 Professional Product Show Layout --}}
@extends('cms.layouts.app')

@section('title', $product->name)
@section('page-title', $product->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="text-gray-600 text-sm mt-1">SKU: {{ $product->sku }} • Xem & quản lý chi tiết sản phẩm</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.products.edit', [$currentProject->code, $product]) : route('cms.products.edit', $product) }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:shadow-lg transition font-medium flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Chỉnh sửa
                </a>
                <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.products.index', $currentProject->code) : route('cms.products.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- 2-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Details (2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Featured Image -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Ảnh đại diện
                </h2>
                
                <div class="border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50 aspect-square flex items-center justify-center">
                    @if($product->featured_image)
                        <img src="{{ $product->featured_image }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm">Chưa có ảnh đại diện</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Description -->
            <div class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <h2 class="font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"/></svg>
                    Mô tả sản phẩm
                </h2>

                @if($product->short_description)
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-1">Mô tả ngắn</p>
                        <p class="text-gray-700 text-sm">{{ $product->short_description }}</p>
                    </div>
                @endif

                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase mb-2">Mô tả đầy đủ</p>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1C6.48 1 2 5.48 2 11s4.48 10 10 10 10-4.48 10-10S17.52 1 12 1zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-13c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5z"/></svg>
                    Giá
                </h2>

                <div class="space-y-4">
                    @if($product->price)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-gray-600">Giá gốc:</span>
                            <span class="font-semibold text-lg text-gray-900">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
                        </div>
                    @endif

                    @if($product->sale_price)
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg border border-red-200">
                            <span class="text-gray-600">Giá khuyến mãi:</span>
                            <span class="font-semibold text-lg text-red-600">{{ number_format($product->sale_price, 0, ',', '.') }} ₫</span>
                        </div>
                        @if($product->price > $product->sale_price)
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <span class="text-gray-600">Tiết kiệm:</span>
                                <span class="font-semibold text-green-600">{{ round(((($product->price - $product->sale_price) / $product->price) * 100)) }}%</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Metadata (Sticky) -->
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-6">
                <!-- Product Info -->
                <div class="bg-white rounded-lg shadow-sm p-6 space-y-3">
                    <h2 class="font-semibold text-gray-900 flex items-center mb-4">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        Chi tiết cơ bản
                    </h2>

                    <div class="border-t pt-3">
                        <p class="text-xs font-medium text-gray-500 uppercase">SKU</p>
                        <p class="text-sm font-mono text-gray-900 mt-1">{{ $product->sku }}</p>
                    </div>

                    <div class="border-t pt-3">
                        <p class="text-xs font-medium text-gray-500 uppercase">Slug</p>
                        <p class="text-sm font-mono text-gray-900 mt-1">{{ $product->slug }}</p>
                    </div>

                    <div class="border-t pt-3">
                        <p class="text-xs font-medium text-gray-500 uppercase">Danh mục</p>
                        <p class="text-sm text-gray-900 mt-1">
                            @if($product->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-gray-500">Chưa phân loại</span>
                            @endif
                        </p>
                    </div>

                    <div class="border-t pt-3">
                        <p class="text-xs font-medium text-gray-500 uppercase">Thương hiệu</p>
                        <p class="text-sm text-gray-900 mt-1">
                            @if($product->brand)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $product->brand->name }}
                                </span>
                            @else
                                <span class="text-gray-500">Không có</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-sm p-4 border border-blue-100">
                    <h3 class="font-semibold text-gray-900 mb-3 flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-13c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5z"/></svg>
                        Thời gian
                    </h3>
                    <div class="text-xs text-gray-600 space-y-2">
                        <div class="flex justify-between">
                            <span>Được tạo:</span>
                            <span class="font-medium">{{ $product->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cập nhật:</span>
                            <span class="font-medium">{{ $product->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase mb-3">Trạng thái</p>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span class="text-sm font-medium text-green-800 bg-green-50 px-3 py-1 rounded-full">Hoạt động</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
