@props(['categories', 'attrs' => []])

@php
    $columns = (int) ($attrs['columns'] ?? 3);
    $gridCols = match($columns) {
        2 => 'grid-cols-2',
        3 => 'grid-cols-2 md:grid-cols-3',
        4 => 'grid-cols-2 md:grid-cols-4',
        6 => 'grid-cols-2 md:grid-cols-3 lg:grid-cols-6',
        default => 'grid-cols-2 md:grid-cols-3',
    };
    $showCount = ($attrs['show_count'] ?? 'true') !== 'false';
@endphp

<div class="shortcode-categories grid {{ $gridCols }} gap-4">
    @forelse($categories as $category)
        <a href="{{ route('frontend.category', $category->slug) }}" 
           class="category-card group relative overflow-hidden rounded-xl aspect-square">
            {{-- Background Image --}}
            @if($category->image)
                <img src="{{ $category->image }}" 
                     alt="{{ $category->name }}" 
                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-600"></div>
            @endif
            
            {{-- Overlay --}}
            <div class="absolute inset-0 bg-black/40 group-hover:bg-black/50 transition"></div>
            
            {{-- Content --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center text-white p-4">
                <h3 class="font-bold text-lg text-center mb-1">{{ $category->name }}</h3>
                @if($showCount)
                    <span class="text-sm opacity-80">{{ $category->products_count }} sản phẩm</span>
                @endif
            </div>
        </a>
    @empty
        <div class="col-span-full text-center py-12 text-gray-500">
            Không có danh mục nào
        </div>
    @endforelse
</div>
