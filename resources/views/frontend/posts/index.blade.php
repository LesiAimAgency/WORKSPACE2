@extends('frontend.layouts.post-layout')

@section('page-title', 'Tin tức')

@php
    $layoutOpts = $postCategorySettings ?? [];
    $layoutType = data_get($layoutOpts, 'post_category_layout', 'grid');
    
    // Khởi tạo dynamic grid classes (Sử dụng Bootstrap)
    $colsDesktop = data_get($layoutOpts, 'responsive_columns.desktop', 3);
    $colsTablet = data_get($layoutOpts, 'responsive_columns.tablet', 2);
    $colsMobile = data_get($layoutOpts, 'responsive_columns.mobile', 1);
    
    if ($layoutType === 'classic') {
        $colsDesktop = 1;
        $colsTablet = 1;
        $colsMobile = 1;
    }
    
    $colDesktopClass = $colsDesktop == 4 ? 'col-lg-3' : ($colsDesktop == 2 ? 'col-lg-6' : ($colsDesktop == 1 ? 'col-lg-12' : 'col-lg-4'));
    $colTabletClass = $colsTablet == 3 ? 'col-md-4' : ($colsTablet == 2 ? 'col-md-6' : 'col-md-12');
    $colMobileClass = $colsMobile == 2 ? 'col-6' : 'col-12';
    
    $colClass = "{$colMobileClass} {$colTabletClass} {$colDesktopClass} mb-4";
@endphp

@section('post-content')
    <div class="row kalles-blog-grid my-4 g-4">
        @forelse($posts ?? [] as $post)
            <div class="{{ $colClass }} slideshow__slide" style="height:max-content   ;">
                <x-post-item :post="$post" :options="$layoutOpts" layout="{{ $layoutType }}" />
            </div>
        @empty
            <div class="col-12 bg-light rounded p-5 text-center text-muted border">
                <i class="las la-newspaper fs-1 mb-3 text-secondary" style="font-size: 3rem;"></i>
                <p class="fs-5">Chưa có bài viết nào.</p>
            </div>
        @endforelse
    </div>

    {{-- Phân trang --}}
    @if(isset($posts) && $posts->hasPages())
        <div class="filter-pagination mt-4">
            <div class="d-flex justify-content-center">
                {{ $posts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
@endsection

@section('sidebar')
    @php
        $categories = \App\Models\Taxonomy::categories()->where('status', 'published')->withCount('posts')->get();
        $recentPosts = \App\Models\Post::posts()->where('status', 'published')->latest()->take(5)->get();
    @endphp

    <div>
        {{-- Widget: Chuyên mục --}}
        @if($categories->count() > 0)
        <div>
            <h5 class="mb-2 fw-medium"> Chuyên mục </h5>
            <div class="filter-title mb-4"></div>
            <ul class="list-unstyled mb-4">
                @foreach($categories as $cat)
                <li class="nav-item mb-2">
                    <a class="nav-link pt-0 pe-3 text-secondary" href="/{{ request()->route('projectCode') }}/blog?category={{ $cat->slug }}">
                        <span class="d-inline-flex align-items-center">
                            {{ $cat->name }} ({{ $cat->posts_count }})
                        </span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        
        {{-- Widget: Bài viết mới --}}
        @if($recentPosts->count() > 0)
        <div>
            <h5 class="mb-2 mt-3 mt-lg-4 fw-medium">Bài viết mới</h5>
            <div class="filter-title mb-4"></div>
            @foreach($recentPosts as $recent)
            @php
                $recentImage = $recent->featured_image ?? $recent->image ?? 'https://ui-avatars.com/api/?name='.urlencode($recent->title).'&background=random&size=100x100';
            @endphp
            <div class="row mb-3">
                <div class="col-4">
                    <a href="/{{ request()->route('projectCode') }}/blog/{{ $recent->slug }}">
                        <img src="{{ $recentImage }}" alt="{{ $recent->title }}" class="img-fluid">
                    </a>
                </div>
                <div class="col-8 ps-0">
                    <h6 class="fw-medium mb-1">
                        <a href="/{{ request()->route('projectCode') }}/blog/{{ $recent->slug }}" class="text-dark">{{ $recent->title }}</a>
                    </h6>
                    <p class="text-muted mb-0">{{ $recent->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
@endsection
