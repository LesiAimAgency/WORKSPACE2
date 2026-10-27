@props(['posts', 'columns' => 2, 'attrs' => []])

@php
    $gridCols = match((int)$columns) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        default => 'grid-cols-1 md:grid-cols-2',
    };
    $showExcerpt = ($attrs['show_excerpt'] ?? 'true') !== 'false';
    $showDate = ($attrs['show_date'] ?? 'true') !== 'false';
@endphp

<div class="shortcode-posts grid {{ $gridCols }} gap-6">
    @forelse($posts as $post)
        <article class="post-card bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
            {{-- Thumbnail --}}
            <div class="relative aspect-video overflow-hidden">
                @if($post->thumbnail)
                    <img src="{{ $post->thumbnail }}" 
                         alt="{{ $post->title }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full bg-gradient-to-br from-blue-100 to-blue-100 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                @endif
            </div>
            
            {{-- Content --}}
            <div class="p-5">
                @if($showDate)
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $post->created_at->format('d/m/Y') }}
                    </div>
                @endif
                
                <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2 group-hover:text-blue-600 transition">
                    <a href="{{ route('frontend.page', $post->slug) }}">{{ $post->title }}</a>
                </h3>
                
                @if($showExcerpt && $post->excerpt)
                    <p class="text-gray-600 text-sm line-clamp-3 mb-3">{{ $post->excerpt }}</p>
                @endif
                
                <a href="{{ route('frontend.page', $post->slug) }}" 
                   class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Đọc tiếp
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </article>
    @empty
        <div class="col-span-full text-center py-12 text-gray-500">
            Không có bài viết nào
        </div>
    @endforelse
</div>
