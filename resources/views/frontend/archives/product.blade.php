@extends('frontend.layouts.app')

@section('title', $title ?? 'Sản phẩm')

@section('content')

{{-- Banner Đầu Trang --}}
@include('frontend.partials.page-banner', [
    'title' => $title ?? 'Tất cả sản phẩm',
    'description' => isset($category) ? $category->description : 'Khám phá các sản phẩm chất lượng hàng đầu',
    'type' => 'product_category'
])

<div class="container mx-auto px-4 py-6">

    <div class="flex flex-col lg:flex-row gap-8">
        {{-- Sidebar Filters --}}
        <aside class="w-full lg:w-64 shrink-0">
            <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                <h3 class="font-semibold text-gray-800 mb-4">Bộ lọc</h3>
                
                {{-- Categories --}}
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-3">Danh mục</h4>
                    <div class="space-y-2">
                        @foreach($categories ?? [] as $cat)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" 
                                       name="categories[]" 
                                       value="{{ $cat->id }}"
                                       {{ in_array($cat->id, request('categories', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-600">{{ $cat->name }}</span>
                                <span class="text-xs text-gray-400">({{ $cat->products_count }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Price Range --}}
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-3">Khoảng giá</h4>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="price" value="0-500000" class="text-blue-600">
                            <span class="text-sm text-gray-600">Dưới 500.000đ</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="price" value="500000-1000000" class="text-blue-600">
                            <span class="text-sm text-gray-600">500.000đ - 1.000.000đ</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="price" value="1000000-5000000" class="text-blue-600">
                            <span class="text-sm text-gray-600">1.000.000đ - 5.000.000đ</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="price" value="5000000+" class="text-blue-600">
                            <span class="text-sm text-gray-600">Trên 5.000.000đ</span>
                        </label>
                    </div>
                </div>

                {{-- Brands --}}
                @if(isset($brands) && $brands->count())
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-3">Thương hiệu</h4>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($brands as $brand)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="brands[]" value="{{ $brand->id }}" class="rounded border-gray-300 text-blue-600">
                                <span class="text-sm text-gray-600">{{ $brand->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <button type="button" onclick="applyFilters()" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Áp dụng
                </button>
            </div>
        </aside>

        {{-- Products Grid --}}
        <main class="flex-1">
            {{-- Toolbar --}}
            <div class="flex items-center justify-between mb-6 bg-white rounded-lg p-4 shadow-sm">
                <p class="text-gray-600">
                    Hiển thị <span class="font-medium">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span> 
                    trong <span class="font-medium">{{ $products->total() }}</span> sản phẩm
                </p>
                
                <div class="flex items-center gap-4">
                    {{-- Sort --}}
                    <select name="sort" onchange="sortProducts(this.value)" class="px-3 py-2 border rounded-lg text-sm">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp đến cao</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao đến thấp</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                        <option value="bestseller" {{ request('sort') == 'bestseller' ? 'selected' : '' }}>Bán chạy</option>
                    </select>

                    {{-- View Mode --}}
                    <div class="flex border rounded-lg overflow-hidden">
                        <button type="button" onclick="setViewMode('grid')" class="p-2 hover:bg-gray-100" title="Lưới">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button type="button" onclick="setViewMode('list')" class="p-2 hover:bg-gray-100" title="Danh sách">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Products --}}
            <div id="products-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                    @include('frontend.partials.product-card', ['product' => $product])
                @empty
                    <div class="col-span-full text-center py-16">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-500 text-lg">Không tìm thấy sản phẩm nào</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        </main>
    </div>
</div>

@push('scripts')
<script>
function sortProducts(value) {
    const url = new URL(window.location);
    url.searchParams.set('sort', value);
    window.location = url;
}

function setViewMode(mode) {
    localStorage.setItem('product_view_mode', mode);
    const container = document.getElementById('products-container');
    if (mode === 'list') {
        container.className = 'space-y-4';
    } else {
        container.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6';
    }
}

function applyFilters() {
    const form = document.querySelector('aside form') || document.querySelector('aside');
    const url = new URL(window.location);
    
    // Get all checked categories
    const categories = [...document.querySelectorAll('input[name="categories[]"]:checked')].map(el => el.value);
    if (categories.length) {
        url.searchParams.set('categories', categories.join(','));
    } else {
        url.searchParams.delete('categories');
    }
    
    // Get price range
    const price = document.querySelector('input[name="price"]:checked');
    if (price) {
        url.searchParams.set('price', price.value);
    }
    
    window.location = url;
}

// Restore view mode on load
document.addEventListener('DOMContentLoaded', function() {
    const mode = localStorage.getItem('product_view_mode');
    if (mode) setViewMode(mode);
});
</script>
@endpush
@endsection
