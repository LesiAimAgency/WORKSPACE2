@props(['products', 'columns' => 1, 'attrs' => []])

<div class="shortcode-products-list space-y-4">
    @forelse($products as $product)
        <div class="product-item flex gap-4 bg-white rounded-lg shadow-sm hover:shadow-md transition p-4">
            {{-- Image --}}
            <div class="w-32 h-32 shrink-0 rounded-lg overflow-hidden">
                @if($product->featured_image)
                    <img src="{{ $product->featured_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>
            
            {{-- Content --}}
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800 mb-1 hover:text-blue-600">
                    <a href="{{ locale_route('shop.show', $product->slug) }}">{{ $product->name }}</a>
                </h3>
                
                @if($product->short_description)
                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ $product->short_description }}</p>
                @endif
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @if($product->sale_price && $product->sale_price < $product->price)
                            <span class="font-bold text-red-600">{{ number_format($product->sale_price) }}đ</span>
                            <span class="text-sm text-gray-400 line-through">{{ number_format($product->price) }}đ</span>
                        @else
                            <span class="font-bold text-gray-800">{{ number_format($product->price) }}đ</span>
                        @endif
                    </div>
                    
                    <a href="{{ locale_route('shop.show', $product->slug) }}" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-12 text-gray-500">Không có sản phẩm nào</div>
    @endforelse
</div>
