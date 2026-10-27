@props(['products', 'columns' => 3, 'attrs' => []])

@php
    $gridCols = match((int)$columns) {
        2 => 'grid-cols-1 sm:grid-cols-2',
        3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
        5 => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-5',
        6 => 'grid-cols-2 sm:grid-cols-3 lg:grid-cols-6',
        default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    };
    $showPrice = ($attrs['show_price'] ?? 'true') !== 'false';
    $showButton = ($attrs['show_button'] ?? 'true') !== 'false';
@endphp

<div class="shortcode-products grid {{ $gridCols }} gap-6">
    @forelse($products as $product)
        <div class="product-card bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
            {{-- Image --}}
            <div class="relative aspect-square overflow-hidden">
                @if($product->featured_image)
                    <img src="{{ $product->featured_image }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                
                {{-- Badges --}}
                @if($product->sale_price && $product->sale_price < $product->price)
                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                        -{{ round((1 - $product->sale_price / $product->price) * 100) }}%
                    </span>
                @endif
            </div>
            
            {{-- Content --}}
            <div class="p-4">
                <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2 group-hover:text-blue-600 transition">
                    <a href="{{ locale_route('shop.show', $product->slug) }}">{{ $product->name }}</a>
                </h3>
                
                @if($showPrice)
                    <div class="flex items-center gap-2 mb-3">
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <span class="text-lg font-bold text-red-600">{{ number_format($product->sale_price) }}đ</span>
                            <span class="text-sm text-gray-400 line-through">{{ number_format($product->price) }}đ</span>
                        @else
                            <span class="text-lg font-bold text-gray-800">{{ number_format($product->price) }}đ</span>
                        @endif
                    </div>
                @endif
                
                @if($showButton)
                    <a href="{{ locale_route('shop.show', $product->slug) }}" 
                       class="block w-full text-center py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Xem chi tiết
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12 text-gray-500">
            Không có sản phẩm nào
        </div>
    @endforelse
</div>
