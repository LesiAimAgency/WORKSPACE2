@extends('frontend.layouts.post-layout')

@section('page-title', $post->title ?? 'Chi tiết bài viết')

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

@section('post-content')
<article>
    @php
        $postImage = $post->featured_image ?? $post->image;
        if (!$postImage) {
            $postImage = 'https://ui-avatars.com/api/?name='.urlencode($post->title ?? 'Blog').'&background=random&size=800x400';
        }
    @endphp
    <img src="{{ $postImage }}" alt="{{ $post->title ?? '' }}" class="w-full md:h-[400px] object-cover rounded-xl mb-8 shadow-sm">
    
    <header class="mb-6">
        <h1 class="text-3xl font-bold mb-3">{{ $post->title ?? '' }}</h1>
        <div class="flex items-center gap-4 text-gray-500 text-sm">
            @if($post->created_at)
            <span><i class="far fa-calendar mr-1"></i> {{ $post->created_at->format('d/m/Y') }}</span>
            @endif
            @if($post->author)
            <span><i class="far fa-user mr-1"></i> {{ $post->author->name ?? 'Admin' }}</span>
            @endif
            @if($post->views)
            <span><i class="far fa-eye mr-1"></i> {{ number_format($post->views) }} lượt xem</span>
            @endif
        </div>
    </header>

    <!-- Nhúng Mục Lục Bài Viết -->
    @php
        $tocConfig = setting('toc', []);
        $tocEnabled = !empty($tocConfig['enabled']);
        $minHeadings = $tocConfig['min_headings'] ?? 3;
        $tocStyle = $tocConfig['style'] ?? 'style-1';
    @endphp

    @if($tocEnabled && isset($post->toc) && count($post->toc) >= $minHeadings)
        <div class="toc-wrapper toc-{{ $tocStyle }} my-6">
            <div class="toc-header flex items-center justify-between">
                <h4 class="font-bold text-lg">{{ $tocConfig['title'] ?? 'Mục lục' }}</h4>
                @if(!empty($tocConfig['collapsible']))
                    <button type="button" onclick="this.closest('.toc-wrapper').classList.toggle('collapsed')" class="text-xs opacity-75 hover:opacity-100">[Ẩn/Hiện]</button>
                @endif
            </div>
            <nav class="toc-body mt-3 space-y-1">
                @foreach($post->toc as $index => $item)
                    <a href="#{{ $item['id'] }}" class="toc-item toc-{{ $item['level'] }} block text-sm transition py-1">
                        @if(!empty($tocConfig['show_numbers']))
                            <span class="toc-num">{{ $index + 1 }}.</span>
                        @endif
                        {{ $item['text'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    @endif
    
    <div class="prose max-w-none">
        {!! $post->content ?? '' !!}
    </div>
    
    {{-- Tags temporarily disabled because tags table is missing
    @if($post->tags && count($post->tags) > 0)
    <div class="mt-8 pt-6 border-t">
        <span class="font-medium">Tags:</span>
        @foreach($post->tags as $tag)
        <a href="/{{ request()->route('projectCode') }}/blog?tag={{ $tag->slug }}" class="inline-block bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-gray-200 ml-2">{{ $tag->name }}</a>
        @endforeach
    </div>
    @endif
    --}}
</article>

@if(isset($relatedPosts) && count($relatedPosts) > 0)
<div class="mt-10 pt-8 border-t">
    <h3 class="text-2xl font-bold mb-6">Bài viết liên quan</h3>
    <div class="grid md:grid-cols-3 gap-6">
        @foreach($relatedPosts as $related)
        <a href="/{{ request()->route('projectCode') }}/blog/{{ $related->slug }}" class="group block bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-gray-100">
            @php
                $relatedImage = $related->featured_image ?? $related->image;
                if (!$relatedImage) {
                    $relatedImage = 'https://ui-avatars.com/api/?name='.urlencode($related->title ?? 'Blog').'&background=random&size=400x300';
                }
            @endphp
            <img src="{{ $relatedImage }}" alt="{{ $related->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
            <div class="p-4">
                <h4 class="font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2">{{ $related->title }}</h4>
                <div class="mt-2 text-xs text-gray-500">{{ $related->created_at->format('d/m/Y') }}</div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

@if(isset($products) && count($products) > 0)
<div class="mt-10 pt-8 border-t border-gray-200">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-900">Sản phẩm nổi bật</h3>
        <a href="{{ locale_route('shop.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Xem tất cả &rarr;</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($products as $product)
        <a href="/{{ request()->route('projectCode') }}/{{ $product->slug }}" class="group block bg-white rounded-xl p-3 border border-gray-100 hover:border-blue-200 hover:shadow-lg transition">
            @php
                $productImage = $product->image ?? $product->featured_image;
                if (!$productImage) {
                    $productImage = 'https://ui-avatars.com/api/?name='.urlencode($product->name ?? 'Product').'&background=random&size=400x400';
                }
            @endphp
            <div class="relative rounded-lg overflow-hidden mb-3 aspect-square bg-gray-50">
                <img src="{{ $productImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
            </div>
            <h4 class="font-medium text-sm text-gray-900 line-clamp-2 group-hover:text-blue-600 transition min-h-[40px]">{{ $product->name }}</h4>
            @if($product->price)
            <div class="mt-2 flex items-center gap-2">
                <span class="text-red-600 font-bold">{{ number_format($product->price) }}đ</span>
                @if($product->old_price)
                <span class="text-xs text-gray-400 line-through">{{ number_format($product->old_price) }}đ</span>
                @endif
            </div>
            @endif
        </a>
        @endforeach
    </div>
</div>
@endif
@endsection

@section('sidebar')
<div class="space-y-6">
    <div class="widget">
        <h3 class="font-bold mb-4 text-lg">Chuyên mục</h3>
        <ul class="space-y-2">
            @foreach($categories ?? [] as $cat)
            <li><a href="/{{ request()->route('projectCode') }}/blog?category={{ $cat->slug }}" class="text-gray-700 hover:text-green-600 flex justify-between">
                <span>{{ $cat->name }}</span>
                <span class="text-gray-400">({{ $cat->posts_count ?? 0 }})</span>
            </a></li>
            @endforeach
        </ul>
    </div>
    
    @if(isset($recentPosts) && count($recentPosts) > 0)
    <div class="widget">
        <h3 class="font-bold mb-4 text-lg">Bài viết mới</h3>
        <div class="space-y-3">
            @foreach($recentPosts as $recent)
            <a href="/{{ request()->route('projectCode') }}/blog/{{ $recent->slug }}" class="flex gap-4 hover:bg-gray-50 p-2 -mx-2 rounded-lg transition">
                @php
                    $recentImage = $recent->featured_image ?? $recent->image;
                    if (!$recentImage) {
                        $recentImage = 'https://ui-avatars.com/api/?name='.urlencode($recent->title ?? 'Blog').'&background=random&size=100x100';
                    }
                @endphp
                <img src="{{ $recentImage }}" alt="{{ $recent->title }}" class="w-20 h-20 object-cover rounded-md shadow-sm">
                <div class="flex flex-col justify-center">
                    <h4 class="font-medium text-sm text-gray-900 line-clamp-2 group-hover:text-blue-600 mb-1">{{ $recent->title }}</h4>
                    <span class="text-gray-500 text-xs flex items-center gap-1"><i class="far fa-clock"></i> {{ $recent->created_at->format('d/m/Y') }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
