{{-- MODIFIED: 2025-01-21 --}}
@extends('cms.layouts.app')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Sản phẩm')

@section('content')
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
    <div class="flex flex-col sm:flex-row flex-wrap items-start sm:items-center gap-4">
        <!-- Quick Status Tabs -->
        <div class="flex space-x-3 text-sm">
            <a href="{{ request()->url() }}" class="{{ !request('status') ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }} pb-1.5 transition">Tất cả</a>
            <a href="{{ request()->url() }}?status=published" class="{{ request('status') === 'published' ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }} pb-1.5 transition">Đã xuất bản</a>
            <a href="{{ request()->url() }}?status=draft" class="{{ request('status') === 'draft' ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }} pb-1.5 transition">Nháp</a>
            <a href="{{ request()->url() }}?status=trashed" class="{{ request('status') === 'trashed' ? 'text-red-600 font-semibold border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }} pb-1.5 transition">Thùng rác</a>
        </div>
        
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Tìm kiếm sản phẩm..." 
                   class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            
            <select name="category" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả danh mục</option>
                @foreach($parentCategories as $parent)
                    <optgroup label="{{ $parent->name }}">
                        <option value="{{ $parent->id }}" {{ request('category') == $parent->id ? 'selected' : '' }}>-- {{ $parent->name }} (Tất cả)</option>
                        @foreach($parent->children as $child)
                            <option value="{{ $child->id }}" {{ request('category') == $child->id ? 'selected' : '' }}>
                                {{ $child->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            
            <select name="status" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả trạng thái</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                <option value="trashed" {{ request('status') == 'trashed' ? 'selected' : '' }}>Thùng rác</option>
            </select>
            
            <button type="submit" class="px-4 py-1.5 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Lọc
            </button>
        </form>
    </div>
    
    <div class="flex items-center space-x-2 flex-shrink-0">
      
        <button id="bulkEditBtn" onclick="openBulkEdit()" class="px-3.5 py-1.5 text-sm font-medium bg-orange-600 text-white rounded-lg hover:bg-orange-700 disabled:opacity-50 transition-colors" disabled>
            Sửa nhanh (<span id="selectedCount">0</span>)
        </button>
        <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.products.create', $currentProject->code) : (Route::has('cms.products.create') ? route('cms.products.create') : '#') }}" 
           class="px-3.5 py-1.5 text-sm font-medium bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            Thêm sản phẩm
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">
    <div class="overflow-x-auto w-full">
        <table class="w-full divide-y divide-gray-200 text-left border-collapse table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-12 px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider min-w-[240px]">Sản phẩm</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">SKU</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Danh mục</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Giá</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Kho</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Trạng thái</th>
                    <th class="w-24 px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap" title="Số càng âm thì hiển thị càng lên cao">Thứ tự</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50/80 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        <input type="checkbox" class="product-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $product->id }}">
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-10 w-10 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center">
                                @if($product->featured_image)
                                    <img class="h-10 w-10 object-cover" src="{{ media_url($product->featured_image) }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.src='/assets/img/placeholder-images-image_large.webp';">
                                @else
                                    <div class="h-10 w-10 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-xs font-medium">IMG</span>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.products.edit', [$currentProject->code, $product]) : (Route::has('cms.products.edit') ? route('cms.products.edit', $product) : '#') }}" 
                                       class="text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors line-clamp-2" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </a>
                                    @if($product->product_type === 'variable' || $product->variations->isNotEmpty())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 shrink-0">
                                            Biến thể ({{ $product->variations->count() }})
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 font-mono">{{ $product->sku }}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                        {{ $product->category?->name ?? 'Chưa phân loại' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                        {{ $product->display_price }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        @if($product->product_type === 'variable' && $product->variations->isNotEmpty())
                            @php
                                $totalVarStock = $product->variations->sum('stock_quantity');
                            @endphp
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $totalVarStock > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                {{ $totalVarStock }}
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $product->stock_status === 'in_stock' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                {{ $product->stock_quantity }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        @if(request('status') === 'trashed' || (method_exists($product, 'trashed') && $product->trashed()))
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-50 text-red-700 border border-red-200">
                                Thùng rác
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $product->status === 'published' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                                   ($product->status === 'draft' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-center">
                        <div class="inline-flex items-center justify-center">
                            <input type="number" 
                                   class="order-input w-20 px-2 py-1 text-center text-xs font-semibold border rounded-md transition-all shadow-2xs {{ ($product->order ?? 0) < 0 ? 'bg-amber-50 text-amber-700 border-amber-300 font-bold ring-1 ring-amber-300' : (($product->order ?? 0) > 0 ? 'bg-gray-50 text-gray-700 border-gray-200' : 'bg-white text-gray-700 border-gray-200') }} focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   value="{{ $product->order ?? 0 }}" 
                                   title="Thứ tự (càng âm thì càng lên cao). Nhập số và bấm Enter hoặc click ra ngoài để lưu."
                                   data-id="{{ $product->id }}"
                                   data-original-value="{{ $product->order ?? 0 }}"
                                   onchange="quickUpdateProductOrder(this, {{ $product->id }})"
                                   onkeydown="if(event.key === 'Enter'){ this.blur(); }">
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                        <div class="inline-flex items-center space-x-1">
                            @if(request('status') === 'trashed')
                                <!-- Restore -->
                                <form method="POST" action="{{ isset($currentProject) && $currentProject ? route('project.admin.products.restore', [$currentProject->code, $product]) : route('cms.products.restore', $product) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors" title="Khôi phục">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                        </svg>
                                    </button>
                                </form>
                                
                                <!-- Force Delete -->
                                <form method="POST" action="{{ isset($currentProject) && $currentProject ? route('project.admin.products.force-delete', [$currentProject->code, $product]) : route('cms.products.force-delete', $product) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn sản phẩm này không? Hành động này không thể hoàn tác!');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors" title="Xóa vĩnh viễn">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <!-- Quick Edit -->
                                <button onclick="openSingleQuickEdit({{ $product->id }})" 
                                        class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors" 
                                        title="Sửa nhanh">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </button>
                                
                                <!-- Edit -->
                                <a href="{{ isset($currentProject) && $currentProject ? route('project.admin.products.edit', [$currentProject->code, $product]) : route('cms.products.edit', $product) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-200 transition-colors" 
                                   title="Chỉnh sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                
                                <!-- Delete -->
                                <form method="POST" action="{{ isset($currentProject) && $currentProject ? route('project.admin.products.destroy', [$currentProject->code, $product]) : route('cms.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors" title="Xóa">
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
                    <td colspan="9" class="px-6 py-8 text-center text-gray-500">Không tìm thấy sản phẩm nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>

<!-- Bulk Edit Modal -->
<div id="bulkEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-medium text-gray-900" id="bulkEditModalTitle">Sửa nhanh sản phẩm</h3>
            </div>
            <div class="grid grid-cols-12 gap-4 p-6">
                <!-- Product Selection - 3 columns -->
                <div class="col-span-3">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Sản phẩm sẽ cập nhật:</h4>
                    <div id="productList" class="space-y-2 max-h-96 overflow-y-auto border rounded-md p-3 bg-gray-50">
                        <!-- Dynamic product list -->
                    </div>
                </div>
                
                <!-- Bulk Edit Form - 9 columns -->
                <div class="col-span-9">
                    <form id="bulkEditForm">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                                <div id="categoryCheckboxes" class="max-h-40 overflow-y-auto border rounded-md p-3 bg-white">
                                    <!-- Category checkboxes will be populated -->
                                </div>
                                <small class="text-gray-500">Không chọn = không thay đổi</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Thương hiệu</label>
                                <div id="brandCheckboxes" class="max-h-40 overflow-y-auto border rounded-md p-3 bg-white">
                                    <!-- Brand checkboxes will be populated -->
                                </div>
                                <small class="text-gray-500">Không chọn = không thay đổi</small>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                                <select id="bulkStatus" name="status" class="w-full px-3 py-2 border rounded-md">
                                    <option value="">Không thay đổi</option>
                                    <option value="draft">Nháp</option>
                                    <option value="published">Đã xuất bản</option>
                                    <option value="archived">Lưu trữ</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Thứ tự sắp xếp (Càng âm càng lên cao)</label>
                                <input type="number" id="bulkOrder" name="order" placeholder="Để trống nếu không muốn thay đổi..." class="w-full px-3 py-2 border rounded-md text-sm">
                                <small class="text-gray-500">Số càng nhỏ / càng âm (ví dụ: -10, -5, -1) thì sản phẩm sẽ lên vị trí càng cao trên danh sách.</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Điều chỉnh Giá gốc</label>
                                <div class="grid grid-cols-12 gap-2">
                                    <select id="bulkPriceMode" name="price_mode" class="col-span-7 px-3 py-2 border rounded-md text-sm">
                                        <option value="no_change">-- Không thay đổi --</option>
                                        <option value="fixed">Đặt giá mới (VNĐ)</option>
                                        <option value="percent_increase">Tăng theo phần trăm (+%)</option>
                                        <option value="percent_decrease">Giảm theo phần trăm (-%)</option>
                                        <option value="amount_increase">Tăng số tiền cố định (+ VNĐ)</option>
                                        <option value="amount_decrease">Giảm số tiền cố định (- VNĐ)</option>
                                    </select>
                                    <input type="number" id="bulkPriceValue" name="price_value" placeholder="Giá trị" class="col-span-5 px-3 py-2 border rounded-md text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Điều chỉnh Giá khuyến mãi</label>
                                <div class="grid grid-cols-12 gap-2">
                                    <select id="bulkSalePriceMode" name="sale_price_mode" class="col-span-7 px-3 py-2 border rounded-md text-sm">
                                        <option value="no_change">-- Không thay đổi --</option>
                                        <option value="fixed">Đặt giá KM mới (VNĐ)</option>
                                        <option value="percent_decrease_regular">Giảm theo % so với Giá gốc (-%)</option>
                                        <option value="percent_increase">Tăng giá KM hiện tại (+%)</option>
                                        <option value="percent_decrease">Giảm giá KM hiện tại (-%)</option>
                                        <option value="amount_increase">Tăng giá KM (+ VNĐ)</option>
                                        <option value="amount_decrease">Giảm giá KM (- VNĐ)</option>
                                        <option value="clear">Xóa giá khuyến mãi</option>
                                    </select>
                                    <input type="number" id="bulkSalePriceValue" name="sale_price_value" placeholder="Giá trị" class="col-span-5 px-3 py-2 border rounded-md text-sm">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-2">
                <button type="button" onclick="closeBulkEdit()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Hủy</button>
                <button type="button" id="saveBulkEditBtn" onclick="saveBulkEdit()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Cập nhật tất cả</button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedProducts = [];

const bulkEditUrl = @if(Route::has('admin.products.bulk-edit'))
    '{{ route("admin.products.bulk-edit") }}'
@elseif(Route::has('cms.products.bulk-edit'))
    '{{ route("cms.products.bulk-edit") }}'
@else
    '/admin/products/bulk-edit'
@endif;

const bulkUpdateUrl = @if(Route::has('admin.products.bulk-update'))
    '{{ route("admin.products.bulk-update") }}'
@elseif(Route::has('cms.products.bulk-update'))
    '{{ route("cms.products.bulk-update") }}'
@else
    '/admin/products/bulk-update'
@endif;

const updateOrderUrl = @if(Route::has('admin.products.update-order'))
    '{{ route("admin.products.update-order") }}'
@elseif(Route::has('cms.products.update-order'))
    '{{ route("cms.products.update-order") }}'
@else
    '/admin/products/update-order'
@endif;

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
}

// Checkbox handling
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedProducts();
});

document.querySelectorAll('.product-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedProducts);
});

function updateSelectedProducts() {
    selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
    const countEl = document.getElementById('selectedCount');
    if (countEl) countEl.textContent = selectedProducts.length;
    const btn = document.getElementById('bulkEditBtn');
    if (btn) btn.disabled = selectedProducts.length === 0;
}

function openBulkEdit() {
    if (selectedProducts.length === 0) return;
    
    console.log('Opening bulk edit for products:', selectedProducts);
    
    fetch(bulkEditUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ ids: selectedProducts })
    })
    .then(async response => {
        const isJson = response.headers.get('content-type')?.includes('application/json');
        const data = isJson ? await response.json() : null;
        if (!response.ok) {
            throw new Error((data && data.message) ? data.message : 'Server error: ' + response.status);
        }
        return data;
    })
    .then(data => {
        if (data && data.success) {
            renderBulkEditTable(data.products, data.categories, data.brands);
            const modalTitle = document.getElementById('bulkEditModalTitle');
            if (modalTitle) {
                modalTitle.textContent = 'Sửa hàng loạt (' + data.products.length + ' sản phẩm)';
            }
            document.getElementById('bulkEditModal').classList.remove('hidden');
        } else {
            alert('Lỗi: ' + (data?.message || 'Không thể tải dữ liệu'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Có lỗi xảy ra khi tải dữ liệu: ' + error.message);
    });
}

function openSingleQuickEdit(productId) {
    console.log('Opening quick edit for product:', productId);
    
    fetch(bulkEditUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({ ids: [productId] })
    })
    .then(async response => {
        const isJson = response.headers.get('content-type')?.includes('application/json');
        const data = isJson ? await response.json() : null;
        if (!response.ok) {
            throw new Error((data && data.message) ? data.message : 'Server error: ' + response.status);
        }
        return data;
    })
    .then(data => {
        console.log('Response data:', data);
        if (data && data.success) {
            renderBulkEditTable(data.products, data.categories, data.brands);
            const modalTitle = document.getElementById('bulkEditModalTitle');
            if (modalTitle && data.products && data.products.length === 1) {
                modalTitle.textContent = 'Sửa nhanh: ' + data.products[0].name;
            } else if (modalTitle) {
                modalTitle.textContent = 'Sửa nhanh sản phẩm';
            }
            document.getElementById('bulkEditModal').classList.remove('hidden');
        } else {
            alert('Lỗi: ' + (data?.message || 'Không thể tải dữ liệu sản phẩm'));
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Có lỗi xảy ra khi tải dữ liệu sản phẩm: ' + error.message);
    });
}

let bulkProducts = [];

function renderBulkEditTable(products, categories, brands) {
    bulkProducts = products;
    
    // Render product list
    const productList = document.getElementById('productList');
    productList.innerHTML = products.map(product => `
        <div class="flex items-center justify-between p-2 bg-gray-50 rounded border border-gray-100">
            <span class="text-sm font-medium text-gray-800 truncate" title="${product.name}">${product.name}</span>
            <button type="button" onclick="removeProduct(${product.id})" class="text-red-500 hover:text-red-700 ml-2" title="Xóa khỏi danh sách sửa">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `).join('');
    
    // Get selected category and brand from products
    const selectedCategoryIds = products.length === 1 && products[0]?.category_ids 
        ? products[0].category_ids 
        : (products[0]?.category?.id ? [products[0]?.category?.id] : []);
    const selectedBrandIds = products.length === 1 && products[0]?.brand_ids 
        ? products[0].brand_ids 
        : (products[0]?.brand?.id ? [products[0]?.brand?.id] : []);
    
    // Populate category checkboxes with pre-selected
    const categoryCheckboxes = `
        <div class="flex items-center mb-2 pb-2 border-b">
            <input type="checkbox" id="selectAllCategories" class="mr-2 rounded" onchange="toggleAllCheckboxes('categories[]', this.checked)">
            <label for="selectAllCategories" class="text-sm font-semibold cursor-pointer">Chọn tất cả</label>
        </div>
    ` + categories.map(cat => `
        <div class="flex items-center mb-1.5 hover:bg-gray-50 px-1 py-0.5 rounded">
            <input type="checkbox" id="cat_${cat.id}" name="categories[]" value="${cat.id}" class="mr-2 rounded category-checkbox" ${selectedCategoryIds.includes(cat.id) ? 'checked' : ''}>
            <label for="cat_${cat.id}" class="text-sm cursor-pointer select-none">${cat.name}</label>
        </div>
    `).join('');
    
    // Populate brand checkboxes with pre-selected
    const brandCheckboxes = `
        <div class="flex items-center mb-2 pb-2 border-b">
            <input type="checkbox" id="selectAllBrands" class="mr-2 rounded" onchange="toggleAllCheckboxes('brands[]', this.checked)">
            <label for="selectAllBrands" class="text-sm font-semibold cursor-pointer">Chọn tất cả</label>
        </div>
    ` + brands.map(brand => `
        <div class="flex items-center mb-1.5 hover:bg-gray-50 px-1 py-0.5 rounded">
            <input type="checkbox" id="brand_${brand.id}" name="brands[]" value="${brand.id}" class="mr-2 rounded brand-checkbox" ${selectedBrandIds.includes(brand.id) ? 'checked' : ''}>
            <label for="brand_${brand.id}" class="text-sm cursor-pointer select-none">${brand.name}</label>
        </div>
    `).join('');
    
    document.getElementById('categoryCheckboxes').innerHTML = categoryCheckboxes;
    document.getElementById('brandCheckboxes').innerHTML = brandCheckboxes;

    const saveBtn = document.getElementById('saveBulkEditBtn');
    if (saveBtn) {
        saveBtn.textContent = products.length === 1 ? 'Lưu thay đổi' : 'Cập nhật tất cả';
    }

    // Prefill form values for single product quick edit
    if (products.length === 1) {
        const p = products[0];
        document.getElementById('bulkStatus').value = p.status || '';
        if (document.getElementById('bulkOrder')) {
            document.getElementById('bulkOrder').value = p.order !== undefined ? p.order : 0;
        }
        if (p.price !== undefined && p.price !== null && p.price > 0) {
            document.getElementById('bulkPriceMode').value = 'fixed';
            document.getElementById('bulkPriceValue').value = p.price;
        } else {
            document.getElementById('bulkPriceMode').value = 'no_change';
            document.getElementById('bulkPriceValue').value = '';
        }

        if (p.sale_price !== undefined && p.sale_price !== null && p.sale_price > 0) {
            document.getElementById('bulkSalePriceMode').value = 'fixed';
            document.getElementById('bulkSalePriceValue').value = p.sale_price;
        } else {
            document.getElementById('bulkSalePriceMode').value = 'no_change';
            document.getElementById('bulkSalePriceValue').value = '';
        }
    } else {
        document.getElementById('bulkStatus').value = '';
        if (document.getElementById('bulkOrder')) {
            document.getElementById('bulkOrder').value = '';
        }
        document.getElementById('bulkPriceMode').value = 'no_change';
        document.getElementById('bulkPriceValue').value = '';
        document.getElementById('bulkSalePriceMode').value = 'no_change';
        document.getElementById('bulkSalePriceValue').value = '';
    }
}

function removeProduct(productId) {
    bulkProducts = bulkProducts.filter(p => p.id !== productId);
    if (bulkProducts.length === 0) {
        closeBulkEdit();
        return;
    }
    const productList = document.getElementById('productList');
    productList.innerHTML = bulkProducts.map(product => `
        <div class="flex items-center justify-between p-2 bg-gray-50 rounded border border-gray-100">
            <span class="text-sm font-medium text-gray-800 truncate" title="${product.name}">${product.name}</span>
            <button type="button" onclick="removeProduct(${product.id})" class="text-red-500 hover:text-red-700 ml-2" title="Xóa khỏi danh sách sửa">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `).join('');

    const saveBtn = document.getElementById('saveBulkEditBtn');
    if (saveBtn) {
        saveBtn.textContent = bulkProducts.length === 1 ? 'Lưu thay đổi' : 'Cập nhật tất cả';
    }
}

function closeBulkEdit() {
    document.getElementById('bulkEditModal').classList.add('hidden');
}

function saveBulkEdit() {
    if (!bulkProducts || bulkProducts.length === 0) {
        alert('Vui lòng chọn ít nhất một sản phẩm');
        return;
    }

    const categoryCheckboxes = document.querySelectorAll('input[name="categories[]"]:checked');
    const brandCheckboxes = document.querySelectorAll('input[name="brands[]"]:checked');
    
    const formData = {
        ids: bulkProducts.map(p => p.id),
        categories: Array.from(categoryCheckboxes).map(cb => cb.value),
        brands: Array.from(brandCheckboxes).map(cb => cb.value),
        status: document.getElementById('bulkStatus').value,
        price_mode: document.getElementById('bulkPriceMode').value,
        price_value: document.getElementById('bulkPriceValue').value,
        sale_price_mode: document.getElementById('bulkSalePriceMode').value,
        sale_price_value: document.getElementById('bulkSalePriceValue').value
    };

    const bulkOrderVal = document.getElementById('bulkOrder')?.value;
    if (bulkOrderVal !== '' && bulkOrderVal !== null && !isNaN(bulkOrderVal)) {
        formData.order = parseInt(bulkOrderVal, 10);
    }
    
    const saveBtn = document.getElementById('saveBulkEditBtn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Đang lưu...';
    }

    fetch(bulkUpdateUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify(formData)
    })
    .then(async response => {
        const isJson = response.headers.get('content-type')?.includes('application/json');
        const data = isJson ? await response.json() : null;
        if (!response.ok) {
            throw new Error((data && data.message) ? data.message : 'Server error: ' + response.status);
        }
        return data;
    })
    .then(data => {
        if (data && data.success) {
            alert('Cập nhật thành công!');
            closeBulkEdit();
            location.reload();
        } else {
            alert(data?.message || 'Có lỗi xảy ra khi cập nhật');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật: ' + error.message);
    })
    .finally(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.textContent = bulkProducts.length === 1 ? 'Lưu thay đổi' : 'Cập nhật tất cả';
        }
    });
}

// Toggle all checkboxes
function toggleAllCheckboxes(name, checked) {
    document.querySelectorAll(`input[name="${name}"]`).forEach(cb => {
        cb.checked = checked;
    });
}

// Close modal when clicking outside
document.getElementById('bulkEditModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBulkEdit();
    }
});

// Quick update order for a product directly from table
function quickUpdateProductOrder(inputEl, productId) {
    const newOrder = inputEl.value.trim();
    if (newOrder === '' || isNaN(newOrder)) {
        inputEl.value = inputEl.getAttribute('data-original-value') || 0;
        return;
    }
    
    const prevVal = inputEl.getAttribute('data-original-value');
    if (String(prevVal) === String(newOrder)) {
        return;
    }
    
    inputEl.disabled = true;
    inputEl.classList.add('opacity-50');

    fetch(updateOrderUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: JSON.stringify({
            id: productId,
            order: parseInt(newOrder, 10)
        })
    })
    .then(async response => {
        const isJson = response.headers.get('content-type')?.includes('application/json');
        const data = isJson ? await response.json() : null;
        if (!response.ok) {
            throw new Error((data && data.message) ? data.message : 'Server error: ' + response.status);
        }
        return data;
    })
    .then(data => {
        inputEl.disabled = false;
        inputEl.classList.remove('opacity-50');
        if (data && data.success) {
            inputEl.setAttribute('data-original-value', data.order);
            // Dynamic styling based on negative or zero/positive
            if (data.order < 0) {
                inputEl.className = 'order-input w-20 px-2 py-1 text-center text-xs font-bold border rounded-md transition-all shadow-2xs bg-amber-50 text-amber-700 border-amber-300 ring-1 ring-amber-300 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
            } else if (data.order > 0) {
                inputEl.className = 'order-input w-20 px-2 py-1 text-center text-xs font-semibold border rounded-md transition-all shadow-2xs bg-gray-50 text-gray-700 border-gray-200 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
            } else {
                inputEl.className = 'order-input w-20 px-2 py-1 text-center text-xs font-semibold border rounded-md transition-all shadow-2xs bg-white text-gray-700 border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500';
            }
            showToast('Đã lưu thứ tự ' + data.order + ' (càng âm thì càng lên cao)', 'success');
        } else {
            inputEl.value = prevVal;
            showToast(data?.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        inputEl.disabled = false;
        inputEl.classList.remove('opacity-50');
        inputEl.value = prevVal;
        showToast('Lỗi: ' + error.message, 'error');
    });
}

function showToast(message, type = 'success') {
    let toast = document.getElementById('product-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'product-toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    if (type === 'success') {
        toast.className = 'fixed bottom-5 right-5 z-50 px-4 py-2.5 rounded-lg text-sm font-medium text-white bg-emerald-600 shadow-xl transition-all duration-300 transform translate-y-0 opacity-100 flex items-center gap-2';
    } else {
        toast.className = 'fixed bottom-5 right-5 z-50 px-4 py-2.5 rounded-lg text-sm font-medium text-white bg-red-600 shadow-xl transition-all duration-300 transform translate-y-0 opacity-100 flex items-center gap-2';
    }
    setTimeout(() => {
        toast.className = 'fixed bottom-5 right-5 z-50 px-4 py-2.5 rounded-lg text-sm font-medium text-white shadow-xl transition-all duration-300 transform translate-y-10 opacity-0 pointer-events-none';
    }, 2800);
}
</script>
@endsection
