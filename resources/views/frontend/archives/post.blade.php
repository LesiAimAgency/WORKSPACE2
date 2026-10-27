@extends('frontend.layouts.app')

@section('title', $title ?? 'Tin tức & Bài viết')

@php
  $themePostCat = setting('theme_option_post-category', []);
  $themeLayout = setting('theme_option_layout', []);
  
  // Post Category Layout (full-width, sidebar-left, sidebar-right)
  $layoutMode = $themeLayout['post_category_layout'] ?? 'full-width';
  if (in_array($layoutMode, ['sidebar-left-1', 'sidebar-left-2'])) $layoutMode = 'sidebar-left';
  if (in_array($layoutMode, ['sidebar-right-1', 'sidebar-right-2'])) $layoutMode = 'sidebar-right';

  // Post Category Options
  $postCategoryStyle = $themePostCat['post_category_style'] ?? 'grid';
  $excerptLength = (int)($themePostCat['post_excerpt_length'] ?? 150);
  $showPostDate = (bool)($themePostCat['show_post_date'] ?? true);
  $showPostAuthor = (bool)($themePostCat['show_post_author'] ?? true);
  $showPostCategory = (bool)($themePostCat['show_post_category'] ?? true);
  $showPostComments = (bool)($themePostCat['show_post_comments'] ?? false);
  
  $desktopCols = $themePostCat['desktop_columns'] ?? 3;
  $tabletCols = $themePostCat['tablet_columns'] ?? 2;
  $mobileCols = $themePostCat['mobile_columns'] ?? 1;

  $gridColsClass = "grid grid-cols-{$mobileCols} md:grid-cols-{$tabletCols} lg:grid-cols-{$desktopCols} gap-6";
@endphp

@section('content')

{{-- Banner Đầu Trang --}}
@include('frontend.partials.page-banner', [
  'title' => $title ?? 'Tin tức & Bài viết',
  'description' => $description ?? 'Cập nhật những thông tin, tin tức và bài viết mới nhất',
  'type' => 'post_category'
])

<div class="container mx-auto px-4 py-6">
  
  {{-- Main Layout Container --}}
  <div class="flex flex-col lg:flex-row gap-8 {{ $layoutMode === 'sidebar-left' ? 'lg:flex-row-reverse' : '' }}">
    
    {{-- Main Content Area --}}
    <div class="{{ in_array($layoutMode, ['sidebar-left', 'sidebar-right']) ? 'w-full lg:w-3/4' : 'w-full' }}">
      
      {{-- Categories Navigation Bar --}}
      @if(isset($categories) && $categories->count())
      <div class="flex flex-wrap items-center gap-2 mb-8 bg-gray-50 p-3 rounded-2xl border border-gray-100">
        <a href="{{ url()->current() }}" 
          class="px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('category') ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-gray-200 border border-gray-200' }}">
          Tất cả
        </a>
        @foreach($categories as $cat)
          <a href="{{ url()->current() }}?category={{ $cat->slug }}" 
            class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('category') == $cat->slug ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-gray-200 border border-gray-200' }}">
            {{ $cat->name }}
          </a>
        @endforeach
      </div>
      @endif

      {{-- Post Category Styles --}}
      @if($postCategoryStyle === 'classic')
        {{-- CLASSIC LIST STYLE --}}
        <div class="space-y-6">
          @forelse($posts as $post)
          <article class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs hover:shadow-md transition flex flex-col md:flex-row gap-6 group">
            <div class="w-full md:w-1/3 aspect-video rounded-xl overflow-hidden bg-gray-100 relative">
              @if($post->thumbnail)
                <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
              @else
            <div class="w-full md:w-1/3 aspect-video rounded-xl overflow-hidden bg-gray-100 relative">
              @if($post->thumbnail)
                <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
              @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                  <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
              @endif
            </div>
            <div class="flex-1 flex flex-col justify-between py-1">
              <div>
                <div class="flex items-center gap-3 text-xs text-gray-500 mb-2">
                  @if($showPostCategory && $post->categories && $post->categories->first())
                    <span class="bg-blue-50 text-blue-600 font-bold px-2.5 py-0.5 rounded-full text-[11px]">{{ $post->categories->first()->name }}</span>
                  @endif
                  @if($showPostDate)
                    <span class="flex items-center gap-1">
                      <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                      {{ $post->created_at->format('d/m/Y') }}
                    </span>
                  @endif
                  @if($showPostAuthor && $post->author)
                    <span class="flex items-center gap-1">
                      <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                      {{ $post->author->name }}
                    </span>
                  @endif
                </div>
                <h3 class="font-extrabold text-gray-900 text-lg group-hover:text-blue-600 transition mb-2">
                  <a href="{{ route('frontend.page', $post->slug) }}">{{ $post->title }}</a>
                </h3>
                @if($post->excerpt)
                  <p class="text-gray-600 text-xs leading-relaxed line-clamp-2">
                    {{ Str::limit(strip_tags($post->excerpt), $excerptLength) }}
                  </p>
                @endif
              </div>
              <div class="mt-4">
                <a href="{{ route('frontend.page', $post->slug) }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                  <span>Đọc chi tiết</span> &rarr;
                </a>
              </div>
            </div>
          </article>
          @empty
          <p class="text-center py-12 text-gray-500 text-sm">Chưa có bài viết nào.</p>
          @endforelse
        </div>

      @elseif(in_array($postCategoryStyle, ['masonry', 'masonry-tiles']))
        {{-- MASONRY STYLE --}}
        <div class="columns-1 md:columns-2 lg:columns-3 gap-6 space-y-6">
          @forelse($posts as $post)
          <article class="break-inside-avoid bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition overflow-hidden group">
            <div class="relative overflow-hidden">
              @if($post->thumbnail)
                <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}" class="w-full object-cover group-hover:scale-105 transition duration-300">
              @endif
            </div>
            <div class="p-5">
              <div class="flex items-center gap-2 text-[11px] text-gray-500 mb-2">
                @if($showPostDate)
                  <span>{{ $post->created_at->format('d/m/Y') }}</span>
                @endif
                @if($showPostAuthor && $post->author)
                  <span>• {{ $post->author->name }}</span>
                @endif
              </div>
              <h3 class="font-bold text-gray-900 text-base mb-2 group-hover:text-blue-600 transition">
                <a href="{{ route('frontend.page', $post->slug) }}">{{ $post->title }}</a>
              </h3>
              @if($post->excerpt)
                <p class="text-gray-600 text-xs line-clamp-3 mb-3">{{ Str::limit(strip_tags($post->excerpt), $excerptLength) }}</p>
              @endif
            </div>
          </article>
          @empty
          <p class="text-center py-12 text-gray-500 text-sm">Chưa có bài viết nào.</p>
          @endforelse
        </div>

      @elseif($postCategoryStyle === 'photo2')
        {{-- PHOTO FOCUS STYLE --}}
        <div class="{{ $gridColsClass }}">
          @forelse($posts as $post)
          <article class="relative rounded-2xl overflow-hidden aspect-[4/5] group shadow-md">
            @if($post->thumbnail)
              <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            @else
              <div class="w-full h-full bg-slate-800"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 p-5 text-white flex flex-col justify-end">
              @if($showPostCategory && $post->categories && $post->categories->first())
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-400 mb-1">{{ $post->categories->first()->name }}</span>
              @endif
              <h3 class="font-bold text-white text-base mb-2 group-hover:text-blue-200 transition">
                <a href="{{ route('frontend.page', $post->slug) }}">{{ $post->title }}</a>
              </h3>
              @if($showPostDate)
                <span class="text-[11px] text-gray-300 flex items-center gap-1">
                  <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  {{ $post->created_at->format('d/m/Y') }}
                </span>
              @endif
            </div>
          </article>
          @empty
          <p class="text-center py-12 text-gray-500 text-sm">Chưa có bài viết nào.</p>
          @endforelse
        </div>

      @else
        {{-- DEFAULT GRID STYLE --}}
        <div class="{{ $gridColsClass }}">
          @forelse($posts as $post)
          <article class="bg-white rounded-2xl border border-gray-100 shadow-xs hover:shadow-md transition overflow-hidden group flex flex-col justify-between">
            <div>
              <div class="aspect-video relative overflow-hidden bg-gray-100">
                @if($post->thumbnail)
                  <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                @else
                  <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  </div>
                @endif
                @if($showPostCategory && $post->categories && $post->categories->first())
                  <span class="absolute top-3 left-3 bg-blue-600 text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full shadow-xs">
                    {{ $post->categories->first()->name }}
                  </span>
                @endif
              </div>
              <div class="p-5">
                <div class="flex items-center gap-3 text-[11px] text-gray-400 mb-2">
                  @if($showPostDate)
                    <span class="flex items-center gap-1">
                      <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                      {{ $post->created_at->format('d/m/Y') }}
                    </span>
                  @endif
                  @if($showPostAuthor && $post->author)
                    <span class="flex items-center gap-1">
                      <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                      {{ $post->author->name }}
                    </span>
                  @endif
                  @if($showPostComments)
                    <span class="flex items-center gap-1">
                      <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                      {{ $post->comments_count ?? 0 }}
                    </span>
                  @endif
                </div>
                <h3 class="font-extrabold text-gray-900 text-base mb-2 group-hover:text-blue-600 transition line-clamp-2">
                  <a href="{{ route('frontend.page', $post->slug) }}">{{ $post->title }}</a>
                </h3>
                @if($post->excerpt)
                  <p class="text-gray-600 text-xs line-clamp-3 leading-relaxed mb-4">
                    {{ Str::limit(strip_tags($post->excerpt), $excerptLength) }}
                  </p>
                @endif
              </div>
            </div>
            <div class="px-5 pb-5 pt-0">
              <a href="{{ route('frontend.page', $post->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700">
                <span>Xem tiếp</span> &rarr;
              </a>
            </div>
          </article>
          @empty
          <div class="col-span-full text-center py-16">
            <p class="text-gray-500 text-sm">Chưa có bài viết nào.</p>
          </div>
          @endforelse
        </div>
      @endif
                <h3 class="font-extrabold text-gray-900 text-base mb-2 group-hover:text-blue-600 transition line-clamp-2">
                  <a href="{{ route('frontend.page', $post->slug) }}">{{ $post->title }}</a>
                </h3>
                @if($post->excerpt)
                  <p class="text-gray-600 text-xs line-clamp-3 leading-relaxed mb-4">
                    {{ Str::limit(strip_tags($post->excerpt), $excerptLength) }}
                  </p>
                @endif
              </div>
            </div>
            <div class="px-5 pb-5 pt-0">
              <a href="{{ route('frontend.page', $post->slug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700">
                <span>Xem tiếp</span> &rarr;
              </a>
            </div>
          </article>
          @empty
          <div class="col-span-full text-center py-16">
            <p class="text-gray-500 text-sm">Chưa có bài viết nào.</p>
          </div>
          @endforelse
        </div>
      @endif

      {{-- Phân Trang --}}
      @if(method_exists($posts, 'hasPages') && $posts->hasPages())
        <div class="mt-12 flex justify-center">
          {{ $posts->withQueryString()->links() }}
        </div>
      @endif
    </div>

    {{-- Sidebar Area --}}
    @if(in_array($layoutMode, ['sidebar-left', 'sidebar-right']))
    <aside class="w-full lg:w-1/4 space-y-6">
      {{-- Search Widget --}}
      <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs">
        <h4 class="font-bold text-gray-800 text-sm mb-3">Tìm kiếm bài viết</h4>
        <form action="/{{ request()->segment(1) }}/search" method="GET" class="relative">
          <input type="text" name="q" placeholder="Nhập từ khóa..." class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
          <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600"></button>
        </form>
      </div>

      {{-- Categories Widget --}}
      @if(isset($categories) && $categories->count())
      <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-xs">
        <h4 class="font-bold text-gray-800 text-sm mb-3 border-b pb-2">Danh mục tin tức</h4>
        <ul class="space-y-2 text-xs font-medium">
          @foreach($categories as $cat)
          <li>
            <a href="?category={{ $cat->slug }}" class="flex items-center justify-between py-1 text-gray-600 hover:text-blue-600 transition">
              <span>{{ $cat->name }}</span>
              <span class="bg-gray-100 text-gray-500 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $cat->posts_count ?? 0 }}</span>
            </a>
          </li>
          @endforeach
        </ul>
      </div>
      @endif
    </aside>
    @endif

  </div>
</div>
@endsection
