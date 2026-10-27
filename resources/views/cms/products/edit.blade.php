@extends('cms.layouts.app')

@section('title', 'Chỉnh sửa sản phẩm: ' . $product->name)
@section('page-title', 'Chỉnh sửa sản phẩm')

@section('content')
<x-media-picker-modal />

<form action="{{ isset($currentProject) && $currentProject ? route('project.admin.products.update', [$currentProject->code, $product]) : (Route::has('admin.products.update') ? route('admin.products.update', $product->id) : route('cms.products.update', $product->id)) }}" 
      method="POST" 
      x-data="productEditForm()" 
      @media-selected.window="handleMediaSelected($event.detail)"
      novalidate
      class="max-w-7xl mx-auto pb-16">
    @csrf
    @method('PUT')

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sticky top-0 bg-gray-100 py-3 z-20 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-gray-900">Sửa: {{ $product->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $product->product_type === 'variable' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $product->product_type === 'variable' ? 'Sản phẩm biến thể' : 'Sản phẩm đơn giản' }}
                </span>
            </div>
            <p class="text-xs text-gray-500">Slug: /products/{{ $product->slug }} • <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="text-blue-600 hover:underline">Xem ngoài web</a></p>
        </div>
        <div class="flex items-center gap-3">
            @include('cms.components.ai-modal', ['postType' => 'product', 'currentLang' => 'vi'])
            <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.products.index', $currentProject->code) : (Route::has('admin.products.index') ? route('admin.products.index') : route('cms.products.index')) }}" 
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                Quay lại
            </a>
            <button type="submit" 
                    class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Cập nhật sản phẩm
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg text-red-700 text-sm">
            <p class="font-bold mb-1">Vui lòng kiểm tra lại các lỗi sau:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Column (Left: 2 Cols) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Basic Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-1">Tên sản phẩm <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="productName" value="{{ old('name', $product->name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-base font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Đường dẫn tĩnh (Slug)</label>
                    <div class="flex items-center">
                        <input type="text" name="slug" value="{{ old('slug', $product->slug) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-r-lg text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-1">Mô tả ngắn</label>
                    <textarea name="short_description" id="short_description" rows="4"
                              placeholder="Mô tả tóm tắt sản phẩm hiển thị cạnh nút mua hàng..."
                              class="ckeditor w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                              data-height="160px">{{ old('short_description', $product->short_description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-800 mb-1">Mô tả chi tiết sản phẩm</label>
                    <textarea name="description" id="description" rows="10"
                              placeholder="Nội dung giới thiệu chi tiết, tính năng nổi bật..."
                              class="ckeditor w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                              data-height="380px">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <!-- Dữ liệu sản phẩm (WooCommerce Style Tabs Box) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50/75 px-6 py-3 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-gray-900">Dữ liệu sản phẩm:</span>
                        <select name="product_type" x-model="productType" class="px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-blue-700 focus:ring-2 focus:ring-blue-500">
                            <option value="simple"> Sản phẩm đơn giản (Simple Product)</option>
                            <option value="variable"> Sản phẩm có biến thể (Variable Product)</option>
                        </select>
                    </div>

                    <nav class="flex space-x-1 sm:space-x-2 text-sm font-medium">
                        <button type="button" @click="activeTab = 'general'"
                                :class="activeTab === 'general' ? 'bg-white text-blue-600 shadow-xs border-b-2 border-blue-600 font-bold' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded-t-lg transition">
                            Chung
                        </button>
                        <button type="button" @click="activeTab = 'inventory'"
                                :class="activeTab === 'inventory' ? 'bg-white text-blue-600 shadow-xs border-b-2 border-blue-600 font-bold' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded-t-lg transition">
                            Kho hàng
                        </button>
                        <button type="button" @click="activeTab = 'attributes'"
                                :class="activeTab === 'attributes' ? 'bg-white text-blue-600 shadow-xs border-b-2 border-blue-600 font-bold' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded-t-lg transition">
                            Các thuộc tính
                        </button>
                        <button type="button" @click="activeTab = 'variations'" x-show="productType === 'variable'"
                                :class="activeTab === 'variations' ? 'bg-white text-blue-600 shadow-xs border-b-2 border-blue-600 font-bold' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded-t-lg transition">
                            Biến thể (<span x-text="variations.length"></span>)
                        </button>
                        <button type="button" @click="activeTab = 'guarantees'"
                                :class="activeTab === 'guarantees' ? 'bg-white text-emerald-600 shadow-xs border-b-2 border-emerald-600 font-bold' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1.5 rounded-t-lg transition flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Cam kết &amp; Bảo hành</span>
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- 1. TAB CHUNG (General) -->
                    <div x-show="activeTab === 'general'" class="space-y-4">
                        <template x-if="productType === 'variable'">
                            <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800 flex items-start gap-2">
                                <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Đây là sản phẩm có biến thể. Giá bán và giá khuyến mãi của từng biến thể được thiết lập chi tiết trong tab <strong>"Biến thể"</strong>.</span>
                            </div>
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mã SKU (Sản phẩm chính)</label>
                                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="VD: DP-68001"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                            <div x-show="productType === 'simple'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán thường (₫)</label>
                                <input type="number" name="price" value="{{ old('price', $product->price) }}" min="0" step="any" placeholder="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="productType === 'simple'">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Giá khuyến mãi (₫)</label>
                                <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="any" placeholder="Để trống nếu không giảm giá"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Khuyến mãi từ</label>
                                    <input type="date" name="date_on_sale_from" value="{{ old('date_on_sale_from', $product->date_on_sale_from) }}" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Đến ngày</label>
                                    <input type="date" name="date_on_sale_to" value="{{ old('date_on_sale_to', $product->date_on_sale_to) }}" class="w-full px-2 py-2 border border-gray-300 rounded-lg text-xs">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. TAB KHO HÀNG (Inventory) -->
                    <div x-show="activeTab === 'inventory'" class="space-y-4">
                        <template x-if="productType === 'variable'">
                            <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800 flex items-start gap-2">
                                <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Với sản phẩm biến thể, số lượng tồn kho được quản lý chi tiết cho từng biến thể ở tab <strong>"Biến thể"</strong>.</span>
                            </div>
                        </template>

                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="manage_stock" id="manage_stock" value="1" x-model="manageStock"
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="manage_stock" class="text-sm font-medium text-gray-800 cursor-pointer">Bật quản lý tồn kho cấp sản phẩm này</label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div x-show="manageStock">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Số lượng trong kho</label>
                                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái kho hàng</label>
                                <select name="stock_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="in_stock" {{ old('stock_status', $product->stock_status) === 'in_stock' ? 'selected' : '' }}>Còn hàng (In Stock)</option>
                                    <option value="out_of_stock" {{ old('stock_status', $product->stock_status) === 'out_of_stock' ? 'selected' : '' }}>Hết hàng (Out of Stock)</option>
                                    <option value="on_backorder" {{ old('stock_status', $product->stock_status) === 'on_backorder' ? 'selected' : '' }}>Chờ nhập hàng (On Backorder)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 3. TAB THUỘC TÍNH (Attributes) -->
                    <div x-show="activeTab === 'attributes'" class="space-y-6">
                        <!-- Nhóm A: Thuộc tính tạo biến thể (Variable Product) -->
                        <div class="border rounded-xl p-4 bg-gray-50/70 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                        Bộ thuộc tính sinh biến thể (Cấp điện áp, Dòng điện, Tiết diện, Màu sắc...)
                                    </h3>
                                    <p class="text-xs text-gray-500">Chọn thuộc tính từ hệ thống hoặc thêm tùy chỉnh để sinh các tổ hợp sản phẩm biến thể.</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="addVariationAttribute()"
                                            class="px-3 py-1.5 text-xs font-semibold bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition flex items-center gap-1 shadow-xs">
                                        + Thêm thuộc tính tùy chỉnh
                                    </button>
                                </div>
                            </div>

                            <!-- Selector: Chọn từ bộ thuộc tính có sẵn của hệ thống -->
                            <div class="p-3.5 bg-indigo-50/70 border border-indigo-200 rounded-xl space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-indigo-900 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        Chọn nhanh từ bộ thuộc tính có sẵn của hệ thống:
                                    </label>
                                    <a href="{{ route('admin.attributes.index') }}" target="_blank" class="text-[11px] text-indigo-600 hover:text-indigo-800 hover:underline font-medium">
                                        Quản lý bộ thuộc tính
                                    </a>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <select x-model="selectedGlobalAttrId" class="flex-1 min-w-[220px] px-3 py-1.5 text-xs bg-white border border-indigo-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                        <option value="">-- Chọn thuộc tính hệ thống --</option>
                                        <template x-for="gAttr in availableGlobalAttributes" :key="gAttr.id">
                                            <option :value="gAttr.id" x-text="`${gAttr.name} (${gAttr.values ? gAttr.values.length : 0} giá trị)`"></option>
                                        </template>
                                    </select>
                                    <button type="button" @click="addPredefinedAttribute()" :disabled="!selectedGlobalAttrId"
                                            class="px-4 py-1.5 text-xs font-bold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition shadow-xs">
                                        + Thêm vào sản phẩm
                                    </button>
                                </div>
                            </div>

                            <!-- List of product variation attributes -->
                            <div class="space-y-3">
                                <template x-for="(attr, aIndex) in variationAttributes" :key="aIndex">
                                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs space-y-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="w-1/3">
                                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tên thuộc tính</label>
                                                <input type="text" :name="`variation_attributes[${aIndex}][name]`" x-model="attr.name" 
                                                       placeholder="VD: Màu sắc"
                                                       class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-800">
                                                <input type="hidden" :name="`variation_attributes[${aIndex}][slug]`" :value="attr.slug || slugify(attr.name)">
                                            </div>

                                            <div class="flex-1 space-y-2">
                                                <label class="block text-xs font-semibold text-gray-700 mb-1">Các giá trị áp dụng</label>
                                                
                                                <!-- Clickable pills if availableValues exist -->
                                                <template x-if="attr.availableValues && attr.availableValues.length > 0">
                                                    <div class="space-y-1.5 p-2.5 bg-gray-50 rounded-lg border border-gray-200">
                                                        <div class="flex items-center justify-between text-[11px] text-gray-500 mb-1">
                                                            <span>Bấm vào từng giá trị để bật/tắt:</span>
                                                            <div class="flex items-center gap-2">
                                                                <button type="button" @click="selectAllPills(aIndex)" class="text-blue-600 hover:underline font-medium">Chọn tất cả</button>
                                                                <span>•</span>
                                                                <button type="button" @click="deselectAllPills(aIndex)" class="text-red-500 hover:underline font-medium">Bỏ chọn</button>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-wrap gap-1.5">
                                                            <template x-for="pill in attr.availableValues" :key="pill.id || pill.name">
                                                                <button type="button" @click="togglePill(aIndex, pill.name)"
                                                                        :class="isPillSelected(aIndex, pill.name) ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                                                                        class="px-2.5 py-1 text-xs rounded-md border font-medium transition flex items-center gap-1.5 select-none">
                                                                    <template x-if="pill.color_code">
                                                                        <span class="w-2.5 h-2.5 rounded-full border border-white" :style="`background-color: ${pill.color_code}`"></span>
                                                                    </template>
                                                                    <span x-text="pill.name"></span>
                                                                    <svg x-show="isPillSelected(aIndex, pill.name)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Text representation input -->
                                                <div>
                                                    <input type="text" :name="`variation_attributes[${aIndex}][values]`" x-model="attr.valuesString"
                                                           placeholder="Phân tách các giá trị bằng dấu phẩy: VD: 24kV, 35kV, 110kV"
                                                           class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-xs bg-white text-gray-700">
                                                </div>
                                            </div>

                                            <button type="button" @click="removeVariationAttribute(aIndex)" class="mt-6 text-red-500 hover:text-red-700 p-1.5 rounded hover:bg-red-50 transition" title="Xóa thuộc tính này">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="variationAttributes.length === 0" class="text-center py-6 text-xs text-gray-400 bg-white border border-dashed rounded-xl">
                                    Chưa có thuộc tính biến thể nào. Chọn từ bộ thuộc tính có sẵn ở trên hoặc bấm "+ Thêm thuộc tính tùy chỉnh".
                                </div>
                            </div>
                        </div>

                        <!-- Nhóm B: Thuộc tính mô tả / Thông số kỹ thuật (Specs) -->
                        <div class="border rounded-xl p-4 bg-gray-50/70 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Thuộc tính mô tả / Thông số kỹ thuật cố định (Specs)
                                    </h3>
                                    <p class="text-xs text-gray-500">Hiển thị trong bảng thông số kỹ thuật (không tạo biến thể)</p>
                                </div>
                                <button type="button" @click="addSpec()"
                                        class="px-3 py-1.5 text-xs font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center gap-1 shadow-xs">
                                    + Thêm thông số
                                </button>
                            </div>

                            <div class="space-y-2">
                                <template x-for="(spec, sIndex) in specs" :key="sIndex">
                                    <div class="flex items-center gap-3 bg-white p-2.5 rounded-lg border border-gray-200">
                                        <input type="text" :name="`specs[${sIndex}][name]`" x-model="spec.name" placeholder="Tên thông số (VD: Cấp điện áp)"
                                               class="w-1/3 px-2.5 py-1.5 border border-gray-300 rounded text-xs font-medium">
                                        <input type="text" :name="`specs[${sIndex}][value]`" x-model="spec.value" placeholder="Giá trị (VD: 24kV - 100A)"
                                               class="flex-1 px-2.5 py-1.5 border border-gray-300 rounded text-xs">
                                        <button type="button" @click="removeSpec(sIndex)" class="text-red-500 hover:text-red-700 p-1" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </template>

                                <div x-show="specs.length === 0" class="text-center py-4 text-xs text-gray-400 bg-white border border-dashed rounded-lg">
                                    Chưa có thông số kỹ thuật nào. Bấm nút "+ Thêm thông số" ở trên.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. TAB BIẾN THỂ (Variations) -->
                    <div x-show="activeTab === 'variations' && productType === 'variable'" class="space-y-4">
                        <!-- Action Bar -->
                        <div class="p-4 bg-gray-50 border rounded-xl space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-2">
                                  
                                    <button type="button" @click="addManualVariation()"
                                            class="px-3 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-100 transition">
                                        + Thêm thủ công
                                    </button>
                                </div>
                                <button type="button" @click="if(confirm('Bạn có chắc muốn xóa toàn bộ biến thể?')) variations = []" x-show="variations.length > 0"
                                        class="text-xs text-red-600 hover:text-red-800 font-medium">
                                    Xóa tất cả biến thể
                                </button>
                            </div>

                            <!-- Bulk Actions Bar -->
                            <div x-show="variations.length > 0" class="pt-3 border-t border-gray-200 flex flex-wrap items-center gap-3 text-xs">
                                <span class="font-bold text-gray-700">Thao tác hàng loạt:</span>
                                
                                <div class="flex items-center gap-1">
                                    <input type="number" x-model="bulkPrice" placeholder="Giá đồng loạt (₫)" class="w-32 px-2 py-1 border border-gray-300 rounded text-xs bg-white">
                                    <button type="button" @click="applyBulkPrice()" class="px-2.5 py-1 bg-gray-200 hover:bg-gray-300 rounded text-gray-800 font-medium">
                                        Áp giá
                                    </button>
                                </div>

                                <div class="flex items-center gap-1">
                                    <input type="number" x-model="bulkStock" placeholder="Số lượng kho" class="w-24 px-2 py-1 border border-gray-300 rounded text-xs bg-white">
                                    <button type="button" @click="applyBulkStock()" class="px-2.5 py-1 bg-gray-200 hover:bg-gray-300 rounded text-gray-800 font-medium">
                                        Áp kho
                                    </button>
                                </div>

                                <button type="button" @click="applyStockStatus('in_stock')" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 rounded font-medium">
                                    Tất cả còn hàng
                                </button>
                            </div>
                        </div>

                        <!-- Variations list -->
                        <div class="space-y-3">
                            <template x-for="(v, vIdx) in variations" :key="vIdx">
                                <div class="border rounded-xl bg-white shadow-2xs overflow-hidden" x-data="{ expanded: false }">
                                    <input type="hidden" :name="`variations[${vIdx}][id]`" :value="v.id || ''">
                                    <!-- Header -->
                                    <div class="flex items-center justify-between p-3.5 bg-gray-50 border-b border-gray-200 cursor-pointer select-none" @click="expanded = !expanded">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-800 text-xs font-bold flex items-center justify-center" x-text="vIdx + 1"></span>
                                            <div class="font-bold text-sm text-gray-900" x-text="getVariationTitle(v)"></div>
                                            <span class="text-xs text-gray-500 font-mono" x-show="v.sku" x-text="`[SKU: ${v.sku}]`"></span>
                                            <span class="text-xs font-semibold text-emerald-600" x-show="v.price > 0" x-text="`${Number(v.price).toLocaleString('vi-VN')} ₫`"></span>
                                            <span class="text-xs px-2 py-0.5 rounded" :class="v.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'" x-text="v.status === 'active' ? 'Hoạt động' : 'Tạm ẩn'"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click.stop="removeVariation(vIdx)" class="text-red-500 hover:text-red-700 p-1 text-xs">
                                                Xóa
                                            </button>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>

                                    <!-- Body (Expanded) -->
                                    <div class="p-4 space-y-4" x-show="expanded">
                                        <!-- Attribute selection for this variant -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase">Tổ hợp thuộc tính của biến thể</label>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                                <template x-for="(attr, aI) in variationAttributes" :key="aI">
                                                    <div>
                                                        <label class="block text-xs text-gray-600 mb-1" x-text="attr.name"></label>
                                                        <select :name="`variations[${vIdx}][attributes][${attr.name}]`" x-model="v.attributes[attr.name]"
                                                                @change="checkDuplicateCombinations()"
                                                                class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs bg-white">
                                                            <option value="">-- Chọn --</option>
                                                            <template x-for="val in (attr.valuesString ? attr.valuesString.split(',').map(s => s.trim()).filter(Boolean) : [])" :key="val">
                                                                <option :value="val" x-text="val"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Mã SKU riêng</label>
                                                <input type="text" :name="`variations[${vIdx}][sku]`" x-model="v.sku" placeholder="VD: DP-68001-RED"
                                                       class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs font-mono">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Giá bán thường (₫)</label>
                                                <input type="number" :name="`variations[${vIdx}][price]`" x-model="v.price" min="0" step="any" placeholder="0"
                                                       class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs font-semibold text-gray-900">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Giá khuyến mãi (₫)</label>
                                                <input type="number" :name="`variations[${vIdx}][sale_price]`" x-model="v.sale_price" min="0" step="any" placeholder="Để trống nếu không có"
                                                       class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs text-red-600">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-center pt-2 border-t">
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox" :name="`variations[${vIdx}][manage_stock]`" value="1" x-model="v.manage_stock" :id="`vm_${vIdx}`" class="rounded text-blue-600">
                                                <label :for="`vm_${vIdx}`" class="text-xs text-gray-700 cursor-pointer">Quản lý kho</label>
                                            </div>
                                            <div x-show="v.manage_stock">
                                                <label class="block text-xs text-gray-600 mb-1">Số lượng tồn kho</label>
                                                <input type="number" :name="`variations[${vIdx}][stock_quantity]`" x-model="v.stock_quantity" min="0"
                                                       class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">Trạng thái kho</label>
                                                <select :name="`variations[${vIdx}][stock_status]`" x-model="v.stock_status" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs">
                                                    <option value="in_stock">Còn hàng</option>
                                                    <option value="out_of_stock">Hết hàng</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">Trạng thái bán</label>
                                                <select :name="`variations[${vIdx}][status]`" x-model="v.status" class="w-full px-2.5 py-1.5 border border-gray-300 rounded text-xs">
                                                    <option value="active">Đang bán (Active)</option>
                                                    <option value="inactive">Tạm ẩn (Inactive)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="pt-2 border-t flex items-center gap-3">
                                            <div class="flex-1">
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Ảnh riêng của biến thể (URL)</label>
                                                <div class="flex gap-2">
                                                    <input type="text" :name="`variations[${vIdx}][image]`" x-model="v.image" placeholder="https://... hoặc /storage/..."
                                                           class="flex-1 px-2.5 py-1.5 border border-gray-300 rounded text-xs">
                                                    <button type="button" @click="openMediaPickerFor('variation', vIdx)"
                                                            class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded text-xs text-gray-700 flex items-center gap-1 shrink-0 font-medium transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        Chọn ảnh
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="w-12 h-12 rounded border bg-gray-50 overflow-hidden flex items-center justify-center shrink-0 mt-5 cursor-pointer relative group"
                                                 @click="openMediaPickerFor('variation', vIdx)" title="Bấm để chọn/đổi ảnh">
                                                <template x-if="v.image">
                                                    <img :src="v.image" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!v.image">
                                                    <span class="text-[10px] text-gray-400">Không ảnh</span>
                                                </template>
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                                    <span class="text-[9px] text-white font-bold">Đổi</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="variations.length === 0" class="text-center py-8 bg-gray-50 border-2 border-dashed rounded-xl">
                                <p class="text-sm text-gray-500 font-medium">Chưa có biến thể nào được tạo.</p>
                                <p class="text-xs text-gray-400 mt-1">Khai báo thuộc tính ở tab "Các thuộc tính" rồi bấm <strong>"Tự động tạo biến thể từ thuộc tính"</strong>.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 5. TAB CAM KẾT & BẢO HÀNH (Guarantees) -->
                    <div x-show="activeTab === 'guarantees'" class="space-y-5">
                        <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-xl space-y-2">
                            <h4 class="text-sm font-bold text-emerald-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Cấu hình cam kết dịch vụ cho sản phẩm này
                            </h4>
                            <p class="text-xs text-emerald-800">
                                Mặc định, sản phẩm sẽ áp dụng 3 tiêu chuẩn cam kết chung của hệ thống. Bạn có thể tùy biến riêng cho sản phẩm này (ví dụ: thời hạn bảo hành 12 tháng, 24 tháng) hoặc ẩn khối cam kết nếu không muốn hiển thị.
                            </p>
                        </div>

                        <!-- 3 Radio Modes -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Chế độ áp dụng:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="flex items-start gap-3 p-3.5 border rounded-xl cursor-pointer transition"
                                       :class="guaranteeOverride === 'default' ? 'border-blue-500 bg-blue-50/50 shadow-xs' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="radio" name="guarantee_override" value="default" x-model="guaranteeOverride" class="mt-0.5 text-blue-600 focus:ring-blue-500">
                                    <div class="text-xs">
                                        <div class="font-bold text-gray-900">Mặc định hệ thống</div>
                                        <div class="text-gray-500 mt-0.5">Dùng 3 cam kết chung cấu hình trong Cài đặt</div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-3.5 border rounded-xl cursor-pointer transition"
                                       :class="guaranteeOverride === 'custom' ? 'border-blue-500 bg-blue-50/50 shadow-xs' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="radio" name="guarantee_override" value="custom" x-model="guaranteeOverride" class="mt-0.5 text-blue-600 focus:ring-blue-500">
                                    <div class="text-xs">
                                        <div class="font-bold text-gray-900">Tùy chỉnh riêng</div>
                                        <div class="text-gray-500 mt-0.5">Chỉnh sửa nội dung cam kết riêng cho sản phẩm này</div>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-3.5 border rounded-xl cursor-pointer transition"
                                       :class="guaranteeOverride === 'hide' ? 'border-red-500 bg-red-50/50 shadow-xs' : 'border-gray-200 bg-white hover:bg-gray-50'">
                                    <input type="radio" name="guarantee_override" value="hide" x-model="guaranteeOverride" class="mt-0.5 text-red-600 focus:ring-red-500">
                                    <div class="text-xs">
                                        <div class="font-bold text-gray-900">Ẩn khối cam kết</div>
                                        <div class="text-gray-500 mt-0.5">Không hiển thị cam kết trên sản phẩm này</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Custom Guarantees Repeater (Shown only when custom) -->
                        <div x-show="guaranteeOverride === 'custom'" class="space-y-4 pt-3 border-t">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-700">Các mục cam kết riêng của sản phẩm:</span>
                                <button type="button" @click="customGuarantees.push({ icon: 'themes/daiphuc68/assets/icons/guarantee.svg', title: '', description: '', link: '' })"
                                        class="px-2.5 py-1 text-xs font-semibold bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition flex items-center gap-1 shadow-2xs">
                                    + Thêm cam kết
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(cItem, cIdx) in customGuarantees" :key="cIdx">
                                    <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-gray-700" x-text="'Mục #' + (cIdx + 1)"></span>
                                            <button type="button" @click="customGuarantees.splice(cIdx, 1)" class="text-xs text-red-500 hover:text-red-700 font-medium">Xóa</button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Đường dẫn Icon</label>
                                                <input type="text" :name="`custom_guarantees[${cIdx}][icon]`" x-model="cItem.icon" class="w-full px-2.5 py-1.5 text-xs bg-white border border-gray-300 rounded-lg">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Tiêu đề cam kết</label>
                                                <input type="text" :name="`custom_guarantees[${cIdx}][title]`" x-model="cItem.title" placeholder="VD: BẢO HÀNH CHÍNH HÃNG 24 THÁNG" class="w-full px-2.5 py-1.5 text-xs bg-white border border-gray-300 rounded-lg font-bold">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-gray-600 mb-1">Nội dung mô tả</label>
                                            <textarea :name="`custom_guarantees[${cIdx}][description]`" x-model="cItem.description" rows="2" class="w-full px-2.5 py-1.5 text-xs bg-white border border-gray-300 rounded-lg"></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-medium text-gray-500 mb-1">Link liên kết (nếu có)</label>
                                            <input type="text" :name="`custom_guarantees[${cIdx}][link]`" x-model="cItem.link" placeholder="/chinh-sach-bao-hanh" class="w-full px-2.5 py-1.5 text-xs bg-white border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Note when default is active -->
                        <div x-show="guaranteeOverride === 'default'" class="p-3 bg-gray-50 border rounded-xl text-xs text-gray-600 flex items-center justify-between">
                            <span>Sản phẩm này đang thừa hưởng cấu hình cam kết từ Cài đặt chung hệ thống.</span>
                            <a href="{{ route('admin.settings.group', 'guarantees') }}" target="_blank" class="text-blue-600 hover:underline font-semibold flex items-center gap-1">
                                <span>Chỉnh sửa cấu hình chung</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Analyzer Checklist Component (Chuẩn SEO như Post/Project/Page) -->
            @include('cms.components.seo-analyzer', ['contentType' => 'sản phẩm', 'model' => $product])
        </div>

        <!-- Sidebar Column (Right: 1 Col) -->
        <div class="space-y-6">
            
            <!-- Publish Status Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h3 class="text-sm font-bold text-gray-900 border-b pb-2">Xuất bản</h3>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Trạng thái</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium bg-white">
                        <option value="published" {{ old('status', $product->status) === 'published' ? 'selected' : '' }}> Đã xuất bản (Công khai)</option>
                        <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Bản nháp (Draft)</option>
                        <option value="archived" {{ old('status', $product->status) === 'archived' ? 'selected' : '' }}> Lưu trữ (Archived)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Thứ tự sắp xếp</label>
                    <input type="number" 
                           name="order" 
                           value="{{ old('order', $product->order ?? 0) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-[11px] text-gray-500 mt-1">Số càng nhỏ / càng âm (ví dụ: -10, -5, -1) thì sản phẩm sẽ lên vị trí càng cao.</p>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded text-blue-600">
                    <label for="is_featured" class="text-xs font-medium text-gray-700 cursor-pointer">Sản phẩm nổi bật (Featured)</label>
                </div>
            </div>

            <!-- Categories Card (Taxonomies: product_cat) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-3">
                <div class="flex items-center justify-between border-b pb-2">
                    <h3 class="text-sm font-bold text-gray-900">Danh mục sản phẩm</h3>
                    <a href="{{ route('admin.categories.index') }}" target="_blank" class="text-xs text-blue-600 hover:underline">+ Quản lý</a>
                </div>
                @php
                    $selectedCatIds = old('categories', $product->categories->pluck('id')->toArray());
                @endphp
                <div class="max-h-56 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                    @forelse($categoriesTree as $catOption)
                        <label class="flex items-center gap-2 p-1.5 hover:bg-gray-50 rounded cursor-pointer text-xs">
                            <input type="checkbox" name="categories[]" value="{{ $catOption->id }}" 
                                   {{ in_array($catOption->id, $selectedCatIds) ? 'checked' : '' }}
                                   class="rounded text-blue-600 border-gray-300">
                            <span class="text-gray-800">{{ $catOption->name }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-gray-400 py-2">Chưa có danh mục sản phẩm nào.</p>
                    @endforelse
                </div>
            </div>

            <!-- Brands Card (Taxonomies: brand) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-3">
                <div class="flex items-center justify-between border-b pb-2">
                    <h3 class="text-sm font-bold text-gray-900">Thương hiệu</h3>
                </div>
                @php
                    $selectedBrandIds = old('brands', $product->brands->pluck('id')->toArray());
                @endphp
                <div class="max-h-48 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                    @forelse($brands as $brand)
                        <label class="flex items-center gap-2 p-1.5 hover:bg-gray-50 rounded cursor-pointer text-xs">
                            <input type="checkbox" name="brands[]" value="{{ $brand->id }}" 
                                   {{ in_array($brand->id, $selectedBrandIds) ? 'checked' : '' }}
                                   class="rounded text-blue-600 border-gray-300">
                            <span class="text-gray-800 font-medium">{{ $brand->name }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-gray-400 py-2">Chưa có thương hiệu nào.</p>
                    @endforelse
                </div>
            </div>

            <!-- Featured Image Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-3">
                <div class="flex items-center justify-between border-b pb-2">
                    <h3 class="text-sm font-bold text-gray-900">Ảnh đại diện</h3>
                    <button type="button" x-show="featuredImage" @click="featuredImage = ''" class="text-xs text-red-500 hover:text-red-700 hover:underline">Xóa ảnh</button>
                </div>
                <div>
                    <div class="flex gap-2 mb-3">
                        <input type="text" name="featured_image" x-model="featuredImage" placeholder="URL ảnh hoặc chọn từ thư viện..."
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
                        <button type="button" @click="openMediaPickerFor('featured_image')"
                                class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5 transition shrink-0 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Chọn ảnh
                        </button>
                    </div>

                    <div class="w-full h-44 rounded-lg border-2 border-dashed border-gray-300 hover:border-blue-400 bg-gray-50 overflow-hidden flex items-center justify-center relative group cursor-pointer transition"
                         @click="openMediaPickerFor('featured_image')">
                        <template x-if="featuredImage">
                            <div class="w-full h-full relative">
                                <img :src="featuredImage" class="w-full h-full object-contain p-2"
                                     onerror="this.onerror=null; this.src='/assets/img/placeholder-images-image_large.webp';">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <span class="px-3 py-1.5 bg-white text-gray-800 rounded-md text-xs font-semibold shadow">Đổi ảnh khác</span>
                                </div>
                            </div>
                        </template>
                        <template x-if="!featuredImage">
                            <div class="text-center p-4">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-xs text-blue-600 font-medium block">Bấm để chọn ảnh từ thư viện</span>
                                <span class="text-[11px] text-gray-400 block mt-0.5">JPG, PNG, WEBP, GIF</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Gallery Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <div class="flex items-center justify-between border-b pb-2">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-gray-900">Album ảnh (Gallery)</h3>
                        <span x-show="gallery.length > 0" 
                              class="text-xs px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded-full font-bold border border-blue-200" 
                              x-text="`${gallery.length} ảnh`"></span>
                    </div>
                    <button type="button" x-show="gallery.length > 0" 
                            @click="if (confirm('Bạn có chắc muốn xóa tất cả ảnh trong album?')) gallery = []" 
                            class="text-xs text-red-500 hover:text-red-700 hover:underline">
                        Xóa tất cả
                    </button>
                </div>

                <!-- Fallback input when gallery is empty to ensure empty array is sent to server -->
                <input type="hidden" name="gallery" value="" x-show="gallery.length === 0">

                <!-- Hidden inputs sending gallery array -->
                <template x-for="(imgUrl, gIdx) in gallery" :key="gIdx">
                    <input type="hidden" name="gallery[]" :value="imgUrl">
                </template>

                <!-- Grid thumbnails of gallery images -->
                <div x-show="gallery.length > 0" class="grid grid-cols-3 sm:grid-cols-4 gap-2.5">
                    <template x-for="(imgUrl, gIdx) in gallery" :key="gIdx">
                        <div class="group relative aspect-square rounded-xl border border-gray-200 bg-gray-50 overflow-hidden shadow-xs hover:shadow-md transition">
                            <img :src="imgUrl" 
                                 class="w-full h-full object-cover" 
                                 onerror="this.onerror=null; this.src='/assets/img/placeholder-images-image_large.webp';">
                            
                            <!-- Index Badge -->
                            <div class="absolute top-1 left-1 w-5 h-5 bg-black/60 backdrop-blur-xs text-white text-[10px] font-bold rounded flex items-center justify-center pointer-events-none"
                                 x-text="gIdx + 1"></div>

                            <!-- Overlay on hover with action buttons -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex flex-col justify-between p-1.5">
                                <div class="flex justify-end gap-1">
                                    <!-- Remove Button -->
                                    <button type="button" @click="gallery.splice(gIdx, 1)" 
                                            title="Xóa khỏi album"
                                            class="w-6 h-6 bg-red-600 hover:bg-red-700 text-white rounded-md flex items-center justify-center shadow">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <div class="flex items-center justify-between gap-1 bg-black/60 p-1 rounded-md backdrop-blur-xs">
                                    <!-- Move Left -->
                                    <button type="button" @click="moveGalleryItem(gIdx, -1)" :disabled="gIdx === 0"
                                            :class="gIdx === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-white/20'"
                                            title="Di chuyển sang trái"
                                            class="text-white p-0.5 rounded">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    
                                    <!-- Set as Featured Image -->
                                    <button type="button" @click="featuredImage = imgUrl"
                                            title="Đặt làm ảnh đại diện chính"
                                            class="text-[9px] text-white hover:text-yellow-300 font-bold px-1 uppercase tracking-tight truncate">
                                        Đại diện
                                    </button>

                                    <!-- Move Right -->
                                    <button type="button" @click="moveGalleryItem(gIdx, 1)" :disabled="gIdx === gallery.length - 1"
                                            :class="gIdx === gallery.length - 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-white/20'"
                                            title="Di chuyển sang phải"
                                            class="text-white p-0.5 rounded">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Primary Action: Open Media Modal with multiple: true -->
                <div class="space-y-2">
                    <button type="button" 
                            @click="openMediaPickerFor('gallery')"
                            class="w-full py-3.5 border-2 border-dashed border-blue-300 hover:border-blue-500 rounded-xl text-blue-600 bg-blue-50/60 hover:bg-blue-50 font-semibold text-xs transition flex items-center justify-center gap-2 group shadow-xs">
                        <svg class="w-5 h-5 text-blue-600 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Chọn nhiều ảnh từ thư viện Media</span>
                    </button>

                    <!-- Manual URL Add Option -->
                    <div x-data="{ showUrlInput: false, manualUrl: '' }" class="text-center pt-1">
                        <button type="button" @click="showUrlInput = !showUrlInput" class="text-[11px] text-gray-500 hover:text-gray-700 underline">
                            <span x-text="showUrlInput ? 'Đóng nhập link URL' : '+ Hoặc thêm bằng link URL ngoài'"></span>
                        </button>
                        <div x-show="showUrlInput" x-cloak class="mt-2 flex gap-2">
                            <input type="text" x-model="manualUrl" 
                                   @keydown.enter.prevent="if (manualUrl.trim()) { if (!gallery.includes(manualUrl.trim())) gallery.push(manualUrl.trim()); manualUrl = ''; }" 
                                   placeholder="Dán URL ảnh (https://...)..." 
                                   class="flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-xs">
                            <button type="button" 
                                    @click="if (manualUrl.trim()) { if (!gallery.includes(manualUrl.trim())) gallery.push(manualUrl.trim()); manualUrl = ''; }" 
                                    class="px-3 py-1.5 bg-gray-800 hover:bg-black text-white rounded-lg text-xs font-semibold">
                                Thêm
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<script>
function productEditForm() {
    @php
        $availableGlobalAttributes = ($globalAttributes ?? collect())->map(function($attr) {
            return [
                'id' => $attr->id,
                'name' => $attr->name,
                'slug' => $attr->slug,
                'values' => ($attr->children ?? collect())->map(function($c) {
                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'slug' => $c->slug,
                        'color_code' => $c->color_code ?? null,
                    ];
                })->values(),
            ];
        })->values();

        $initVariationAttrs = [];
        $existingVarAttrs = !empty($product->variation_attributes) 
            ? $product->variation_attributes 
            : (method_exists($product, 'getVariationAttributesList') ? $product->getVariationAttributesList() : []);

        foreach ($existingVarAttrs as $attr) {
            $vals = is_array($attr['values'] ?? []) ? implode(', ', $attr['values']) : ($attr['valuesString'] ?? '');
            $initVariationAttrs[] = [
                'id' => $attr['id'] ?? null,
                'name' => $attr['name'] ?? '',
                'slug' => $attr['slug'] ?? '',
                'valuesString' => $vals,
                'availableValues' => [],
            ];
        }

        $initVariations = [];
        foreach ($product->variations as $var) {
            $initVariations[] = [
                'id' => $var->id,
                'sku' => $var->sku,
                'price' => (float)$var->price,
                'sale_price' => $var->sale_price !== null ? (float)$var->sale_price : '',
                'manage_stock' => (bool)$var->manage_stock,
                'stock_quantity' => (int)$var->stock_quantity,
                'stock_status' => $var->stock_status ?: 'in_stock',
                'image' => $var->image,
                'attributes' => $var->attributes ?: [],
                'status' => $var->status ?: 'active',
            ];
        }

        $rawGallery = old('gallery', $product->gallery ?? []);
        if (is_string($rawGallery)) {
            $rawGallery = json_decode($rawGallery, true) ?: array_filter(explode(',', $rawGallery));
        }
        $initGallery = is_array($rawGallery) ? array_values(array_filter($rawGallery)) : [];

        $guaranteeOverride = old('guarantee_override', $product->guarantee_override ?? 'default');
        $customGuarantees = old('custom_guarantees', $product->custom_guarantees ?? []);
        if (is_string($customGuarantees)) {
            $customGuarantees = json_decode($customGuarantees, true) ?: [];
        }
        if (empty($customGuarantees)) {
            $customGuarantees = [
                [
                    'icon' => 'themes/daiphuc68/assets/icons/guarantee.svg',
                    'title' => 'HÀNG CHẤT LƯỢNG CHÍNH HÃNG',
                    'description' => 'Cam kết xuất xứ rõ ràng, đầy đủ chứng chỉ CO/CQ & tiêu chuẩn EVN.',
                    'link' => '',
                ],
                [
                    'icon' => 'themes/daiphuc68/assets/icons/rocket.svg',
                    'title' => 'GIAO HÀNG THẦN TỐC TRONG VÒNG 24H',
                    'description' => 'Giao hàng trong vòng 24h, hỗ trợ vận chuyển toàn quốc tận công trình.',
                    'link' => '',
                ],
                [
                    'icon' => 'themes/daiphuc68/assets/icons/preferences.svg',
                    'title' => 'BẢO HÀNH DÀI HẠN LÊN ĐẾN 36 THÁNG',
                    'description' => 'Bảo hành chính hãng lên đến 36 tháng, hỗ trợ kỹ thuật bảo trì tận tâm.',
                    'link' => '',
                ],
            ];
        }
    @endphp

    return {
        activeTab: 'general',
        productType: '{{ old('product_type', $product->product_type ?? 'simple') }}',
        productName: '{{ old('name', $product->name) }}',
        manageStock: {{ old('manage_stock', $product->manage_stock ? 1 : 0) ? 'true' : 'false' }},
        featuredImage: '{{ old('featured_image', $product->featured_image ?? '') }}',
        gallery: @json($initGallery),
        specs: @json(old('specs', $product->specs ?? [])),
        variationAttributes: @json(old('variation_attributes', $initVariationAttrs)),
        variations: @json(old('variations', $initVariations)),
        availableGlobalAttributes: @json($availableGlobalAttributes),
        selectedGlobalAttrId: '',
        bulkPrice: '',
        bulkStock: '',
        guaranteeOverride: '{{ $guaranteeOverride }}',
        customGuarantees: @json($customGuarantees),

        init() {
            // Auto connect availableValues for existing or pre-filled attributes
            this.variationAttributes.forEach(attr => {
                if (!attr.availableValues || attr.availableValues.length === 0) {
                    const match = this.availableGlobalAttributes.find(g => 
                        g.name.toLowerCase().trim() === (attr.name || '').toLowerCase().trim() ||
                        (attr.slug && g.slug.toLowerCase().trim() === attr.slug.toLowerCase().trim())
                    );
                    if (match) {
                        attr.id = match.id;
                        attr.availableValues = match.values || [];
                        if (!attr.slug) attr.slug = match.slug;
                    }
                }
            });
        },

        slugify(text) {
            if (!text) return '';
            return text.toString().toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[đĐ]/g, 'd')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-');
        },

        openMediaPickerFor(field, extra = null) {
            const isMultiple = (field === 'gallery');
            const targetField = (field === 'variation' && extra !== null) ? ('variation_' + extra) : field;

            // 1. If global openMediaPicker helper exists
            if (typeof window.openMediaPicker === 'function') {
                window.openMediaPicker(null, (urls, items) => {
                    this.handleMediaSelected({
                        field: targetField,
                        urls: urls,
                        files: Array.isArray(items) ? items : [items]
                    });
                }, isMultiple);
                return;
            }

            // 2. Dispatch Alpine event to open x-media-picker-modal
            window.dispatchEvent(new CustomEvent('open-media-picker', {
                detail: {
                    field: targetField,
                    multiple: isMultiple,
                    mediaType: 'image'
                }
            }));
        },

        handleMediaSelected(detail) {
            if (!detail || !detail.field) return;

            if (detail.field === 'gallery') {
                const newUrls = Array.isArray(detail.urls) ? detail.urls : [detail.urls];
                newUrls.filter(Boolean).forEach(url => {
                    if (!this.gallery.includes(url)) {
                        this.gallery.push(url);
                    }
                });
            } else if (detail.field === 'featured_image') {
                const url = Array.isArray(detail.urls) ? detail.urls[0] : detail.urls;
                if (url) {
                    this.featuredImage = url;
                }
            } else if (typeof detail.field === 'string' && detail.field.startsWith('variation_')) {
                const idx = parseInt(detail.field.replace('variation_', ''), 10);
                const url = Array.isArray(detail.urls) ? detail.urls[0] : detail.urls;
                if (url && this.variations[idx]) {
                    this.variations[idx].image = url;
                }
            }
        },

        moveGalleryItem(index, direction) {
            const newIndex = index + direction;
            if (newIndex < 0 || newIndex >= this.gallery.length) return;
            const item = this.gallery.splice(index, 1)[0];
            this.gallery.splice(newIndex, 0, item);
        },

        addGalleryImage(url = '') {
            if (url) {
                if (!this.gallery.includes(url)) this.gallery.push(url);
            } else {
                this.openMediaPickerFor('gallery');
            }
        },

        addSpec() {
            this.specs.push({ name: '', value: '' });
        },

        removeSpec(idx) {
            this.specs.splice(idx, 1);
        },

        addVariationAttribute() {
            this.variationAttributes.push({ name: '', slug: '', valuesString: '', availableValues: [] });
        },

        removeVariationAttribute(idx) {
            this.variationAttributes.splice(idx, 1);
        },

        addPredefinedAttribute() {
            if (!this.selectedGlobalAttrId) return;
            const gAttr = this.availableGlobalAttributes.find(a => a.id == this.selectedGlobalAttrId);
            if (!gAttr) return;

            const existing = this.variationAttributes.find(a => a.name.toLowerCase().trim() === gAttr.name.toLowerCase().trim());
            if (existing) {
                alert(`Thuộc tính "${gAttr.name}" đã có trong danh sách!`);
                this.selectedGlobalAttrId = '';
                return;
            }

            this.variationAttributes.push({
                id: gAttr.id,
                name: gAttr.name,
                slug: gAttr.slug,
                valuesString: '',
                availableValues: gAttr.values || []
            });
            this.selectedGlobalAttrId = '';
        },

        isPillSelected(aIndex, valName) {
            const attr = this.variationAttributes[aIndex];
            if (!attr || !attr.valuesString) return false;
            const current = attr.valuesString.split(',').map(s => s.trim().toLowerCase());
            return current.includes(valName.trim().toLowerCase());
        },

        togglePill(aIndex, valName) {
            const attr = this.variationAttributes[aIndex];
            if (!attr) return;
            let list = attr.valuesString ? attr.valuesString.split(',').map(s => s.trim()).filter(Boolean) : [];
            const lowerTarget = valName.trim().toLowerCase();
            const idx = list.findIndex(s => s.toLowerCase() === lowerTarget);
            if (idx >= 0) {
                list.splice(idx, 1);
            } else {
                list.push(valName.trim());
            }
            attr.valuesString = list.join(', ');
        },

        selectAllPills(aIndex) {
            const attr = this.variationAttributes[aIndex];
            if (!attr || !attr.availableValues) return;
            attr.valuesString = attr.availableValues.map(v => v.name).join(', ');
        },

        deselectAllPills(aIndex) {
            const attr = this.variationAttributes[aIndex];
            if (!attr) return;
            attr.valuesString = '';
        },

        getVariationTitle(v) {
            if (!v.attributes || Object.keys(v.attributes).length === 0) {
                return v.sku ? `Biến thể [${v.sku}]` : 'Biến thể tùy chỉnh';
            }
            return Object.entries(v.attributes).map(([k, val]) => `${k}: ${val}`).join(' | ');
        },

        addManualVariation() {
            const initialAttrs = {};
            this.variationAttributes.forEach(attr => {
                if (attr.name) initialAttrs[attr.name] = '';
            });

            this.variations.push({
                id: null,
                sku: '',
                price: '',
                sale_price: '',
                manage_stock: false,
                stock_quantity: 0,
                stock_status: 'in_stock',
                image: '',
                attributes: initialAttrs,
                status: 'active'
            });
        },

        removeVariation(idx) {
            this.variations.splice(idx, 1);
        },

        applyBulkPrice() {
            if (this.bulkPrice === '' || this.bulkPrice === null) return;
            const val = parseFloat(this.bulkPrice);
            if (isNaN(val) || val < 0) {
                alert('Vui lòng nhập giá bán hợp lệ!');
                return;
            }
            this.variations.forEach(v => {
                v.price = val;
            });
        },

        applyBulkStock() {
            if (this.bulkStock === '' || this.bulkStock === null) return;
            const qty = parseInt(this.bulkStock, 10);
            if (isNaN(qty) || qty < 0) {
                alert('Vui lòng nhập số lượng hợp lệ!');
                return;
            }
            this.variations.forEach(v => {
                v.manage_stock = true;
                v.stock_quantity = qty;
                v.stock_status = qty > 0 ? 'in_stock' : 'out_of_stock';
            });
        },

        applyStockStatus(status) {
            this.variations.forEach(v => {
                v.stock_status = status;
            });
        },

        generateVariationsFromAttributes() {
            const validAttrs = this.variationAttributes
                .filter(a => a.name && a.valuesString)
                .map(a => ({
                    name: a.name.trim(),
                    values: a.valuesString.split(',').map(s => s.trim()).filter(Boolean)
                }))
                .filter(a => a.values.length > 0);

            if (validAttrs.length === 0) {
                alert('Vui lòng khai báo ít nhất một thuộc tính và chọn/nhập giá trị ở tab "Các thuộc tính" trước khi tạo biến thể!');
                this.activeTab = 'attributes';
                return;
            }

            const cartesian = (arrays) => arrays.reduce((a, b) => a.flatMap(d => b.map(e => [d, e].flat())));
            const valueArrays = validAttrs.map(a => a.values.map(val => ({ attrName: a.name, value: val })));
            
            let combinations = [];
            if (valueArrays.length === 1) {
                combinations = valueArrays[0].map(item => [item]);
            } else {
                combinations = cartesian(valueArrays);
            }

            const baseSku = (document.querySelector('input[name="sku"]')?.value || 'VAR').trim();

            const existingHashes = new Set(this.variations.map(v => this.computeHash(v.attributes)));
            let addedCount = 0;

            combinations.forEach((combo, cIdx) => {
                const attrs = {};
                combo.forEach(item => {
                    attrs[item.attrName] = item.value;
                });

                const hash = this.computeHash(attrs);
                if (!existingHashes.has(hash)) {
                    existingHashes.add(hash);
                    this.variations.push({
                        id: null,
                        sku: `${baseSku}-${cIdx + 1}`,
                        price: '',
                        sale_price: '',
                        manage_stock: false,
                        stock_quantity: 0,
                        stock_status: 'in_stock',
                        image: '',
                        attributes: attrs,
                        status: 'active'
                    });
                    addedCount++;
                }
            });

            alert(`Đã bổ sung ${addedCount} biến thể mới mà không làm mất các biến thể đã có!`);
        },

        computeHash(attrs) {
            if (!attrs) return '';
            const sortedKeys = Object.keys(attrs).sort();
            return sortedKeys.map(k => `${k}:${attrs[k]}`.toLowerCase().trim()).join('|');
        },

        checkDuplicateCombinations() {
            const seen = new Set();
            for (let v of this.variations) {
                const h = this.computeHash(v.attributes);
                if (h && seen.has(h)) {
                    alert(`Cảnh báo: Đang có biến thể bị trùng tổ hợp thuộc tính (${this.getVariationTitle(v)})! Vui lòng chọn lại.`);
                    return;
                }
                if (h) seen.add(h);
            }
        }
    };
}
</script>
@endsection
