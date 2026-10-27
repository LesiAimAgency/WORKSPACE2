@extends('frontend.layouts.product-layout')

@php
  $projectCode = request()->route('projectCode');
  $pageTitle = isset($category) ? $category->name : 'Sản phẩm';
@endphp

@section('page-title', $pageTitle)

@section('product-content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-8 mb-8 text-white">
  <div class="max-w-4xl mx-auto text-center">
    <h1 class="text-4xl md:text-5xl font-bold mb-4">Khám phá {{ $pageTitle }}</h1>
    <p class="text-blue-100 text-lg md:text-xl">
      {{ $products->total() ?? 0 }} sản phẩm chất lượng đang chờ bạn
    </p>
  </div>
</div>

<!-- Filter & Sort Bar -->
<div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
  <div class="flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3 overflow-x-auto pb-2">
      <span class="text-gray-600 font-medium whitespace-nowrap">Sắp xếp theo:</span>
      <select onchange="window.location.href=this.value" 
          class="border-2 border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500 transition-colors">
        <option value="?sort=newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
        <option value="?sort=price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp → Cao</option>
        <option value="?sort=price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao → Thấp</option>
        <option value="?sort=popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Phổ biến</option>
        <option value="?sort=name" {{ request('sort') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
      </select>
    </div>
    <div class="text-gray-500 text-sm whitespace-nowrap">
      Hiển thị <span class="font-semibold text-gray-900">{{ $products->count() }}</span> / {{ $products->total() }} sản phẩm
    </div>
  </div>
</div>

<!-- Products Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
  @forelse($products ?? [] as $product)
  <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
    <!-- Image Container -->
    <div class="relative overflow-hidden">
      <a href="/{{ $projectCode }}/{{ $product->slug }}" class="block">
        <img src="{{ $product->featured_image ?? '/assets/img/placeholder-images-image_large.webp' }}" 
           alt="{{ $product->name }}" 
           class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
      </a>
      
      <!-- Badges -->
      <div class="absolute top-3 left-3 flex flex-col gap-2">
        @if($product->is_featured)
        <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full shadow-md">⭐ Nổi bật</span>
        @endif
        @if($product->is_bestseller)
        <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">� Bán chạy</span>
        @endif
        @if($product->is_favorite)
        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">️ Yêu thích</span>
        @endif
      </div>
      
      <!-- Discount Badge -->
      @if($product->sale_price && $product->sale_price < $product->price)
      @php $discount = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
      <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">-{{ $discount }}%</span>
      @endif
    </div>
    
    <!-- Content -->
    <div class="p-5">
      <a href="/{{ $projectCode }}/{{ $product->slug }}" class="block">
        <h3 class="font-bold mb-2 text-lg hover:text-blue-600 transition-colors line-clamp-2">
          {{ $product->name }}
        </h3>
      </a>
      
      @if($product->short_description)
      <p class="text-gray-500 text-sm mb-4 line-clamp-2">
        {{ Str::limit(strip_tags($product->short_description), 100) }}
      </p>
      @endif
      
      <div class="flex items-center justify-between pt-4 border-t border-gray-100">
        <!-- Price -->
        <div>
          @if($product->sale_price && $product->sale_price < $product->price)
          <div class="flex items-center gap-2">
            <span class="text-xl font-bold text-red-600">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
            <span class="text-sm text-gray-400 line-through">{{ number_format($product->price, 0, ',', '.') }}đ</span>
          </div>
          @else
          <span class="text-xl font-bold text-blue-600">{{ number_format($product->price ?? 0, 0, ',', '.') }}đ</span>
          @endif
        </div>
        
        <!-- Add to Cart -->
        <form action="/{{ $projectCode }}/cart/add" method="POST" class="inline">
          @csrf
          <input type="hidden" name="id" value="{{ $product->id }}">
          <input type="hidden" name="name" value="{{ $product->name }}">
          <input type="hidden" name="slug" value="{{ $product->slug }}">
          <input type="hidden" name="price" value="{{ $product->sale_price ?? $product->price }}">
          <input type="hidden" name="image" value="{{ $product->featured_image }}">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors flex items-center gap-2"
              {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            {{ $product->stock_quantity > 0 ? 'Thêm' : 'Hết hàng' }}
          </button>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="col-span-3 text-center py-16 bg-white rounded-2xl">
    <div class="flex flex-col items-center justify-center">
      <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
      </svg>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">Chưa có sản phẩm nào</h3>
      <p class="text-gray-500">Danh mục này hiện chưa có sản phẩm</p>
    </div>
  </div>
  @endforelse
</div>

<!-- Pagination -->
@if(isset($products) && method_exists($products, 'links'))
<div class="mt-10">
  {{ $products->withQueryString()->links('vendor.pagination.tailwind') }}
</div>
@endif
@endsection

@section('sidebar')
<div class="space-y-6">
  <!-- Search Widget -->
  <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
    <h3 class="font-bold mb-4 text-lg text-gray-900">Tìm kiếm</h3>
    <form action="{{ locale_route('shop.index') }}" method="GET">
      <div class="flex rounded-lg overflow-hidden">
        <input type="text" name="q" value="{{ request('q') }}" 
            placeholder="Tìm sản phẩm..." 
            class="flex-1 px-4 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button type="submit" class="bg-blue-600 text-white px-4 hover:bg-blue-700 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </button>
      </div>
    </form>
  </div>

  <!-- Categories Widget -->
  <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
    <h3 class="font-bold mb-4 text-lg text-gray-900">Danh mục</h3>
    <ul class="space-y-2">
      <li>
        <a href="{{ locale_route('shop.index') }}" 
          class="flex items-center justify-between p-3 rounded-lg {{ !isset($category) ? 'bg-blue-50 text-blue-600 font-medium' : 'hover:bg-gray-50 text-gray-700' }} transition-colors">
          <span> tất cả sản phẩm</span>
          <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $products->total() ?? 0 }}</span>
        </a>
      </li>
      @foreach($categories ?? [] as $cat)
      <li>
        <a href="{{ locale_route('shop.category', $cat->slug) }}" 
          class="flex items-center justify-between p-3 rounded-lg {{ isset($category) && $category->id == $cat->id ? 'bg-blue-50 text-blue-600 font-medium' : 'hover:bg-gray-50 text-gray-700' }} transition-colors">
          <span>{{ $cat->name }}</span>
          @if(isset($cat->products_count))
          <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $cat->products_count }}</span>
          @endif
        </a>
      </li>
      @endforeach
    </ul>
  </div>
  
  <!-- Price Filter Widget -->
  <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
    <h3 class="font-bold mb-4 text-lg text-gray-900">Lọc theo giá</h3>
    <form action="{{ locale_route('shop.index') }}" method="GET">
      <div class="space-y-3">
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Từ (₫)</label>
          <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" 
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Đến (₫)</label>
          <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="10,000,000" 
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded-lg hover:bg-gray-900 font-medium transition-colors">
          Áp dụng bộ lọc
        </button>
      </div>
    </form>
  </div>
  
  <!-- Featured Products Widget -->
  @if(isset($featuredProducts) && $featuredProducts->count() > 0)
  <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl shadow-sm p-5 border border-purple-100">
    <h3 class="font-bold mb-4 text-lg text-gray-900">⭐ Sản phẩm nổi bật</h3>
    <div class="space-y-3">
      @foreach($featuredProducts->take(3) as $product)
      <a href="/{{ $projectCode }}/{{ $product->slug }}" class="flex gap-3 p-2 rounded-lg hover:bg-purple-100 transition-colors">
        <img src="{{ $product->featured_image ?? '/assets/img/placeholder-images-image_large.webp' }}" 
           alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-lg">
        <div class="flex-1">
          <h4 class="font-medium text-sm text-gray-900 line-clamp-2">{{ $product->name }}</h4>
          @if($product->sale_price)
          <span class="text-red-600 font-bold text-sm">{{ number_format($product->sale_price) }}đ</span>
          @else
          <span class="text-blue-600 font-bold text-sm">{{ number_format($product->price) }}đ</span>
          @endif
        </div>
      </a>
      @endforeach
    </div>
  </div>
  @endif
</div>
@endsection