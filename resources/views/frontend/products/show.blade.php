@extends('frontend.layouts.product-layout')

@php
  $projectCode = request()->route('projectCode');
@endphp

@section('page-title', $product->name ?? 'Chi tiết sản phẩm')

@push('styles')
<style>
/* Style 1: Modern Card */
.toc-style-1 { background: linear-gradient(135deg, #f0f7ff, #eef2ff); border: 1px solid #c7d2fe; border-radius: 1rem; padding: 1.25rem; }
.toc-style-1 .toc-item { color: #1e40af; font-weight: 500; }
.toc-style-1 .toc-item:hover { text-decoration: underline; }

/* Style 2: Minimalist Clean */
.toc-style-2 { border-left: 4px solid #10b981; background-color: #ecfdf5; padding: 1rem 1.25rem; border-radius: 0 0.75rem 0.75rem 0; }
.toc-style-2 .toc-item { color: #047857; }
.toc-style-2 .toc-item:hover { color: #059669; }

/* Style 3: Classic Boxed */
.toc-style-3 { background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 0.5rem; padding: 1rem; font-family: serif; }
.toc-style-3 .toc-item { color: #92400e; font-weight: bold; }
.toc-style-3 .toc-item:hover { text-decoration: underline; }

/* Style 4: Floating Shadow */
.toc-style-4 { background: #ffffff; border-radius: 1rem; padding: 1.25rem; box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.1); }
.toc-style-4 .toc-item { color: #6b21a8; font-weight: 600; }
.toc-style-4 .toc-item:hover { background-color: #f3e8ff; border-radius: 0.5rem; }

html { scroll-behavior: smooth; }
</style>
@endpush

@section('product-content')
<!-- Breadcrumb -->
<nav class="mb-6">
  <ol class="flex items-center space-x-2 text-sm text-gray-500">
    <li><a href="/{{ $projectCode }}" class="hover:text-blue-600 transition-colors">Trang chủ</a></li>
    @if($product->category)
    <li><span class="mx-2">/</span></li>
    <li><a href="/{{ $projectCode }}/danh-muc/{{ $product->category->slug }}" class="hover:text-blue-600 transition-colors">{{ $product->category->name }}</a></li>
    @endif
    <li><span class="mx-2">/</span></li>
    <li class="text-gray-900 font-medium">{{ $product->name }}</li>
  </ol>
</nav>

<div class="bg-white rounded-2xl shadow-sm p-8 mb-8 border border-gray-100">
  <div class="grid lg:grid-cols-2 gap-10">
    <!-- Product Images -->
    <div class="product-gallery" x-data="{ mainImage: '{{ $product->featured_image ?? '/assets/img/placeholder-images-image_large.webp' }}' }">
      <!-- Main Image -->
      <div class="relative rounded-2xl overflow-hidden shadow-lg mb-4 bg-gray-50 aspect-[4/3]">
        <img :src="mainImage" alt="{{ $product->name }}" class="w-full h-full object-cover">
        
        <!-- Badges -->
        <div class="absolute top-4 left-4 flex flex-col gap-2">
          @if($product->is_featured)
          <span class="bg-yellow-400 text-yellow-900 text-sm px-4 py-2 rounded-full font-bold shadow-lg">⭐ Nổi bật</span>
          @endif
          @if($product->is_bestseller)
          <span class="bg-green-500 text-white text-sm px-4 py-2 rounded-full font-bold shadow-lg"> Bán chạy</span>
          @endif
          @if($product->sale_price && $product->sale_price < $product->price)
          @php $discount = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
          <span class="bg-red-500 text-white text-sm px-4 py-2 rounded-full font-bold shadow-lg">-{{ $discount }}%</span>
          @endif
        </div>
      </div>
      
      <!-- Gallery Thumbnails -->
      @php
        $gallery = $product->gallery;
        if (is_string($gallery)) {
          $gallery = json_decode($gallery, true) ?? [];
        }
        $galleryImages = !empty($gallery) && is_array($gallery) ? $gallery : [];
        if ($product->featured_image && !in_array($product->featured_image, $galleryImages)) {
          array_unshift($galleryImages, $product->featured_image);
        }
      @endphp
      
      @if(!empty($galleryImages) && is_array($galleryImages))
      <div class="grid grid-cols-5 gap-3">
        <button @click="mainImage = '{{ $product->featured_image }}'"
            class="relative rounded-lg overflow-hidden border-2 transition-all hover:border-blue-500"
            :class="mainImage === '{{ $product->featured_image }}' ? 'border-blue-500' : 'border-transparent'">
          <img src="{{ $product->featured_image }}" class="w-full h-24 object-cover">
        </button>
        @foreach($galleryImages as $img)
        @if($img !== $product->featured_image)
        <button @click="mainImage = '{{ $img }}'"
            class="relative rounded-lg overflow-hidden border-2 transition-all hover:border-blue-500"
            :class="mainImage === '{{ $img }}' ? 'border-blue-500' : 'border-transparent'">
          <img src="{{ $img }}" class="w-full h-24 object-cover">
        </button>
        @endif
        @endforeach
      </div>
      @endif
    </div>
    
    <!-- Product Info -->
    <div>
      <h1 class="text-3xl md:text-4xl font-bold mb-4 text-gray-900 leading-tight">{{ $product->name }}</h1>
      
      <!-- SKU & Meta Info -->
      <div class="flex items-center gap-6 text-sm text-gray-600 mb-6">
        @if($product->sku)
        <span class="text-gray-500">SKU: <strong class="text-gray-900">{{ $product->sku }}</strong></span>
        @endif
        @if($product->stock_quantity !== null)
        <span class="{{ $product->stock_quantity > 0 ? 'text-green-600 font-medium' : 'text-red-600 font-medium' }}">
          {{ $product->stock_quantity > 0 ? ' Còn hàng' : ' Hết hàng' }}
        </span>
        @endif
        @if($product->views)
        <span class="text-gray-500"> {{ number_format($product->views) }} lượt xem</span>
        @endif
      </div>
      
      <!-- Price Box -->
      <div class="mb-8 p-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl border border-gray-200">
        @if($product->sale_price && $product->sale_price < $product->price)
        <div class="flex items-center gap-4">
          <span class="text-4xl font-bold text-red-600">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
          <span class="text-2xl text-gray-400 line-through">{{ number_format($product->price, 0, ',', '.') }}đ</span>
          <span class="px-4 py-2 bg-red-100 text-red-600 font-bold rounded-full">
            Tiết kiệm {{ number_format($product->price - $product->sale_price, 0, ',', '.') }}đ
          </span>
        </div>
        @else
        <span class="text-4xl font-bold text-blue-600">{{ number_format($product->price ?? 0, 0, ',', '.') }}đ</span>
        @endif
      </div>
      
      <!-- Short Description -->
      @if($product->short_description)
      <div class="mb-6 text-gray-700 leading-relaxed text-lg">
        {!! $product->short_description !!}
      </div>
      @endif
      
      <!-- Quantity Selector -->
      <div class="mb-6">
        <label class="block font-medium text-gray-700 mb-3">Số lượng:</label>
        <div class="flex items-center border-2 border-gray-200 rounded-lg w-40">
          <button type="button" onclick="decreaseQuantity()" class="px-5 py-3 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
            </svg>
          </button>
          <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock_quantity ?? 99 }}" 
              class="w-16 text-center border-0 py-3 focus:outline-none font-medium" readonly>
          <button type="button" onclick="increaseQuantity()" class="px-5 py-3 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
          </button>
        </div>
      </div>
      
      <!-- Action Buttons -->
      <div class="flex gap-4 mb-8">
        <button type="submit" form="productForm" 
            class="flex-1 bg-blue-600 text-white py-4 rounded-xl hover:bg-blue-700 font-bold text-lg transition-all transform hover:scale-105 flex items-center justify-center gap-2 shadow-lg"
            {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
          </svg>
          Thêm vào giỏ hàng
        </button>
        <button type="submit" form="productForm" formaction="/{{ $projectCode }}/checkout" 
            class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white py-4 rounded-xl hover:from-orange-600 hover:to-orange-700 font-bold text-lg transition-all transform hover:scale-105 shadow-lg">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
          </svg>
          Mua ngay
        </button>
      </div>
      
      <!-- Contact Info -->
      <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
        <div class="flex items-center gap-3 text-blue-700">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
          </svg>
          <span class="font-medium">
            Cần hỗ trợ? Gọi ngay: 
            <a href="tel:{{ setting_string('hotline', '1900 1234') }}" class="font-bold underline decoration-blue-400">
              {{ setting_string('hotline', '1900 1234') }}
            </a>
          </span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Product Tabs -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ activeTab: 'description' }">
  <!-- Tabs Navigation -->
  <div class="border-b">
    <nav class="flex">
      <button @click="activeTab = 'description'" 
          :class="activeTab === 'description' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
          class="px-8 py-5 font-medium text-lg transition-colors">
        Mô tả sản phẩm
      </button>
      <button @click="activeTab = 'specs'" 
          :class="activeTab === 'specs' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
          class="px-8 py-5 font-medium text-lg transition-colors">
        Thông số kỹ thuật
      </button>
      <button @click="activeTab = 'reviews'" 
          :class="activeTab === 'reviews' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
          class="px-8 py-5 font-medium text-lg transition-colors">
        Đánh giá
      </button>
    </nav>
  </div>
  
  <!-- Tab Content -->
  <div class="p-8">
    <!-- Description Tab -->
    <div x-show="activeTab === 'description'" x-cloak class="prose max-w-none text-gray-700 leading-relaxed">
      @php
        $tocConfig = setting('toc', []);
        $tocEnabled = !empty($tocConfig['enabled']);
        $minHeadings = $tocConfig['min_headings'] ?? 3;
        $tocStyle = $tocConfig['style'] ?? 'style-1';
        $tocPosition = $tocConfig['position'] ?? 'before_content';
      @endphp

      @if($tocEnabled && isset($product->toc) && count($product->toc) >= $minHeadings && $tocPosition === 'before_content')
        <div class="toc-wrapper toc-{{ $tocStyle }} my-6">
          <div class="toc-header flex items-center justify-between mb-2">
            <h4 class="font-bold text-lg">{{ $tocConfig['title'] ?? 'Mục lục' }}</h4>
            @if(!empty($tocConfig['collapsible']))
              <button type="button" onclick="this.closest('.toc-wrapper').querySelector('.toc-body').classList.toggle('hidden')" class="text-xs opacity-75 hover:opacity-100">[Ẩn/Hiện]</button>
            @endif
          </div>
          <nav class="toc-body space-y-1">
            @foreach($product->toc as $index => $item)
              <a href="#{{ $item['id'] }}" class="toc-item block text-sm transition py-1" style="padding-left: {{ ($item['level'] - 2) }}rem">
                @if(!empty($tocConfig['show_numbers']))
                  <span class="toc-num">{{ $item['number'] ?? ($index + 1) }}.</span>
                @endif
                {{ $item['text'] }}
              </a>
            @endforeach
          </nav>
        </div>
      @endif

      {!! $product->description ?? '<p class="text-gray-500 italic">Chưa có mô tả chi tiết cho sản phẩm này.</p>' !!}
    </div>
    
    <!-- Specs Tab -->
    <div x-show="activeTab === 'specs'" x-cloak>
      @if($product->specifications)
      <div class="prose max-w-none text-gray-700">
        {!! $product->specifications !!}
      </div>
      @else
      <div class="text-center py-12">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-gray-500">Chưa có thông số kỹ thuật</p>
      </div>
      @endif
    </div>
    
    <!-- Reviews Tab -->
    <div x-show="activeTab === 'reviews'" x-cloak>
      <livewire:frontend.product-reviews :product="$product" />
    </div>
  </div>
</div>

<!-- Related Products -->
@if(isset($relatedProducts) && $relatedProducts->count() > 0)
<div class="mt-16">
  <h2 class="text-3xl font-bold mb-8 text-gray-900">Sản phẩm liên quan</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach($relatedProducts->take(4) as $related)
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
      <a href="/{{ $projectCode }}/{{ $related->slug }}" class="block">
        <div class="relative overflow-hidden">
          <img src="{{ $related->featured_image ?? '/assets/img/placeholder-images-image_large.webp' }}" 
             alt="{{ $related->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500">
          @if($related->sale_price && $related->sale_price < ($related->meta_data['price'] ?? 0))
          @php $discount = round((($related->meta_data['price'] - $related->sale_price) / $related->meta_data['price']) * 100); @endphp
          <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">-{{ $discount }}%</span>
          @endif
        </div>
      </a>
      <div class="p-5">
        <a href="/{{ $projectCode }}/{{ $related->slug }}" class="font-bold hover:text-blue-600 line-clamp-2 mb-2 block">
          {{ $related->title }}
        </a>
        <div class="flex items-center justify-between">
          @if(!empty($related->meta_data['sale_price']) && $related->meta_data['sale_price'] < ($related->meta_data['price'] ?? 0))
          <div>
            <span class="text-red-600 font-bold text-lg">{{ number_format($related->meta_data['sale_price']) }}đ</span>
            <span class="text-gray-400 text-sm line-through">{{ number_format($related->meta_data['price'] ?? 0) }}đ</span>
          </div>
          @else
          <span class="text-blue-600 font-bold text-lg">{{ number_format($related->meta_data['price'] ?? 0) }}đ</span>
          @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

<script>
function increaseQuantity() {
  const input = document.getElementById('quantity');
  const max = {{ $product->stock_quantity ?? 99 }};
  if (input.value < max) {
    input.value = parseInt(input.value) + 1;
  }
}

function decreaseQuantity() {
  const input = document.getElementById('quantity');
  if (input.value > 1) {
    input.value = parseInt(input.value) - 1;
  }
}
</script>
@endsection

@section('sidebar')
<div class="space-y-6">
  <!-- Categories Widget -->
  <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
    <h3 class="font-bold mb-4 text-lg text-gray-900">Danh mục sản phẩm</h3>
    <ul class="space-y-2">
      @php
        $categories = \App\Models\Taxonomy::where('taxonomy', 'product_cat')->where('status', 'published')->orderBy('order')->get();
      @endphp
      @foreach($categories as $cat)
      <li>
        <a href="/{{ $projectCode }}/danh-muc/{{ $cat->slug }}" 
          class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors {{ isset($product) && $product->taxonomies->contains('id', $cat->id) ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700' }}">
          <span>{{ $cat->name }}</span>
        </a>
      </li>
      @endforeach
    </ul>
  </div>
  
  <!-- Hot Products Widget -->
  <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
    <h3 class="font-bold mb-4 text-lg text-gray-900 flex items-center gap-2">
      <span></span> Sản phẩm hot
    </h3>
    <div class="space-y-3">
      @php
        $hotProducts = \App\Models\Post::where('post_type', 'product')
          ->where('status', 'published')
          ->where('meta_data->is_bestseller', true)
          ->limit(5)
          ->get();
      @endphp
      @foreach($hotProducts as $hot)
      <a href="/{{ $projectCode }}/{{ $hot->slug }}" class="flex gap-3 hover:bg-gray-50 p-2 rounded-lg transition-colors">
        <img src="{{ $hot->featured_image ?? '/assets/img/placeholder-images-image_large.webp' }}" 
           alt="{{ $hot->title }}" class="w-16 h-16 object-cover rounded-lg">
        <div class="flex-1">
          <h4 class="font-medium text-sm line-clamp-2 mb-1">{{ $hot->title }}</h4>
          @if($hot->meta_data['sale_price'] ?? 0)
          <span class="text-red-600 font-bold text-sm">{{ number_format($hot->meta_data['sale_price']) }}đ</span>
          <span class="text-gray-400 text-xs line-through ml-1">{{ number_format($hot->meta_data['price'] ?? 0) }}đ</span>
          @else
          <span class="text-blue-600 font-bold text-sm">{{ number_format($hot->meta_data['price'] ?? 0) }}đ</span>
          @endif
        </div>
      </a>
      @endforeach
    </div>
  </div>
  
  <!-- Shipping Info Widget -->
  <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-sm p-5 border border-green-100">
    <h3 class="font-bold mb-3 text-lg text-gray-900 flex items-center gap-2">
      <span></span> Giao hàng
    </h3>
    <p class="text-sm text-gray-700 mb-2">
      Miễn phí vận chuyển cho đơn hàng từ {{ setting_string('free_shipping_threshold', '500,000') }}đ
    </p>
    <div class="text-xs text-gray-600 space-y-1">
      <p> Giao hàng toàn quốc</p>
      <p> Kiểm tra hàng trước khi thanh toán</p>
      <p> Hoàn tiền 100% nếu sản phẩm không đúng</p>
    </div>
  </div>
</div>
@endsection