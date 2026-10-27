@extends('themes.daiphuc68.layouts.app')

@section('title', 'Tin Tức & Hoạt Động - CÔNG TY TNHH ĐẠI PHÚC 68')
@section('meta_description', 'Danh mục tin tức, sự kiện và bài viết chuyên ngành vật tư thiết bị điện từ CÔNG TY TNHH ĐẠI PHÚC 68.')

@section('content')
  {{-- Hero Banner --}}
  <section class="relative w-full h-[500px] md:h-[600px] overflow-hidden bg-brand-navy flex items-end justify-center pb-12 md:pb-16 text-white" aria-label="Hero Banner TIN TỨC">
    <div class="absolute inset-0 z-0">
      <img class="w-full h-full object-cover object-center filter brightness-95" src="{{ asset('storage/media/news/news-item-1.jpg') }}" alt="TIN TỨC"/>
      <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-black/40"></div>
    </div>
    <div class="relative z-10 w-full max-w-[1110px] px-4 text-center">
      <h1 class="text-[26px] sm:text-[34px] md:text-[40px] lg:text-[44px] font-bold uppercase tracking-wider text-white drop-shadow-md">
        TIN TỨC
      </h1>
    </div>
  </section>

  {{-- Breadcrumb --}}
  @include('themes.daiphuc68.components.breadcrumb', [
    'items' => [
      ['label' => 'TIN TỨC ', 'active' => true]
    ]
  ])

  {{-- News Listing Section --}}
  <main class="w-full bg-[#F6F6F6] py-12 md:py-16 flex justify-center">
    <div class="w-full max-w-[925px] px-4 sm:px-6 lg:px-0 flex flex-col gap-8 md:gap-10">
      
      {{-- Category Filter Navigation --}}
      @if(isset($newsCategories) && $newsCategories->isNotEmpty())
      <div class="w-full pb-2">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-3 text-[13px] sm:text-[14px] font-bold uppercase tracking-wider text-neutral-800 gap-3">
          <span class="text-neutral-900 font-extrabold cursor-pointer">CHUYÊN MỤC BÀI VIẾT</span>
          <div class="flex items-center gap-3 sm:gap-4 text-neutral-700 flex-wrap">
            <a class="hover:text-brand-navy transition-colors {{ empty($activeCategory) ? 'text-brand-navy font-bold' : '' }}" href="{{ route('news.index') }}">TẤT CẢ</a>
            @foreach($newsCategories as $cat)
              <span class="text-neutral-300">|</span>
              <a class="hover:text-brand-navy transition-colors {{ ($activeCategory === $cat->slug) ? 'text-brand-navy font-bold' : '' }}" href="{{ route('news.index', ['category' => $cat->slug]) }}">{{ mb_strtoupper($cat->name) }}</a>
            @endforeach
          </div>
        </div>
        <div class="w-full h-[1px] bg-neutral-300"></div>
      </div>
      @endif

      @php
        $articles = $news->items();
        $getCategoryName = function($item) {
            if (!$item) return 'TIN TỨC';
            if (isset($item->taxonomies) && $item->taxonomies instanceof \Illuminate\Support\Collection) {
                $tax = $item->taxonomies->firstWhere('taxonomy', 'category');
                if ($tax) return $tax->name;
            }
            if (is_array($item->meta_data) && !empty($item->meta_data['category'])) {
                return $item->meta_data['category'];
            }
            return $item->category_name ?: 'TIN TỨC';
        };
        $firstPost = $articles[0] ?? null;
        $gridGroup1 = array_slice($articles, 1, 2);
        $fourthPost = $articles[3] ?? null;
        $gridGroup2 = array_slice($articles, 4);
      @endphp

      @if(empty($articles))
        <div class="bg-white rounded-xl p-12 text-center shadow-xs border border-neutral-200">
          <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
            <i class="fa-regular fa-newspaper text-xl"></i>
          </div>
          <h3 class="text-base font-bold text-neutral-800">Chưa có bài viết nào</h3>
          <p class="text-xs text-neutral-500 mt-1">Chưa có bài viết nào thuộc chuyên mục này. Vui lòng chọn chuyên mục khác.</p>
          <a href="{{ route('news.index') }}" class="inline-block mt-4 px-4 py-2 bg-brand-navy text-white text-xs font-bold uppercase rounded-lg hover:bg-neutral-800 transition-colors">Xem tất cả bài viết</a>
        </div>
      @endif

      {{-- Article 1: Large Horizontal Card --}}
      @if($firstPost)
        @php
          $firstDate = $firstPost->published_at ? \Carbon\Carbon::parse($firstPost->published_at)->format('d/m/Y') : date('d/m/Y');
          $firstRead = $firstPost->reading_time ?: '5 phút đọc';
          $firstCat = $getCategoryName($firstPost);
          $firstImg = $firstPost->featured_image ? asset($firstPost->featured_image) : asset('storage/media/news/news-item-2.png');
        @endphp
        <article class="post-card post-card-horizontal group" data-figma-variant="Frame 231" data-post-id="{{ $firstPost->slug }}">
          <figure class="post-image-box">
            <img class="post-img" src="{{ $firstImg }}" alt="{{ $firstPost->title }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('storage/media/banners/hero-banner-main.jpg') }}'; this.classList.add('opacity-90');"/>
          </figure>
          <div class="post-body">
            <div class="post-meta-row">
              <span class="post-category-tag">{{ $firstCat }}</span>
              <span class="post-meta-dot">&bull;</span>
              <span class="post-date">{{ $firstDate }}</span>
              <span class="post-meta-dot">&bull;</span>
              <span class="post-date">{{ $firstRead }}</span>
            </div>
            <div class="post-title-wrap">
              <h2 class="post-title" title="{{ $firstPost->title }}">
                <a class="focus:outline-none focus:underline" href="{{ route('news.show', $firstPost->slug) }}">{{ $firstPost->title }}</a>
              </h2>
            </div>
            <p class="post-excerpt">{{ $firstPost->excerpt ?: 'Cập nhật tin tức và phân tích chuyên sâu về thị trường thiết bị điện trung thế.' }}</p>
            <div class="post-action-wrap">
              <a class="post-read-more" href="{{ route('news.show', $firstPost->slug) }}" title="Xem chi tiết bài viết">
                <span>Xem Thêm</span>
                <i class="post-arrow-icon fa-solid fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </article>
      @endif

      {{-- Articles 2 & 3: 2-Column Grid --}}
      @if(!empty($gridGroup1))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-[32px]">
          @foreach($gridGroup1 as $p)
            @php
              $pDate = $p->published_at ? \Carbon\Carbon::parse($p->published_at)->format('d/m/Y') : date('d/m/Y');
              $pCat = $getCategoryName($p);
              $pImg = $p->featured_image ? asset($p->featured_image) : asset('storage/media/news/news-item-1.jpg');
            @endphp
            <article class="post-card post-card-grid group" data-figma-variant="Frame 232" data-post-id="{{ $p->slug }}">
              <figure class="post-image-box">
                <img class="post-img" src="{{ $pImg }}" alt="{{ $p->title }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('storage/media/banners/hero-banner-main.jpg') }}'; this.classList.add('opacity-90');"/>
              </figure>
              <div class="post-body">
                <div class="post-meta-row">
                  <span class="post-category-tag">{{ $pCat }}</span>
                  <span class="post-meta-dot">&bull;</span>
                  <span class="post-date">{{ $pDate }}</span>
                </div>
                <div class="post-title-wrap">
                  <h2 class="post-title" title="{{ $p->title }}">
                    <a class="focus:outline-none focus:underline" href="{{ route('news.show', $p->slug) }}">{{ $p->title }}</a>
                  </h2>
                </div>
                <p class="post-excerpt">{{ $p->excerpt ?: 'Thông tin phân tích và đánh giá giải pháp công nghệ kỹ thuật điện.' }}</p>
                <div class="post-action-wrap">
                  <a class="post-read-more" href="{{ route('news.show', $p->slug) }}" title="Xem chi tiết bài viết">
                    <span>Xem Thêm</span>
                    <i class="post-arrow-icon fa-solid fa-arrow-right"></i>
                  </a>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      @endif

      {{-- Article 4: Large Horizontal Card --}}
      @if($fourthPost)
        @php
          $fDate = $fourthPost->published_at ? \Carbon\Carbon::parse($fourthPost->published_at)->format('d/m/Y') : date('d/m/Y');
          $fRead = $fourthPost->reading_time ?: '6 phút đọc';
          $fCat = $getCategoryName($fourthPost);
          $fImg = $fourthPost->featured_image ? asset($fourthPost->featured_image) : asset('storage/media/news/news-item-4.jpg');
        @endphp
        <article class="post-card post-card-horizontal group" data-figma-variant="Frame 231" data-post-id="{{ $fourthPost->slug }}">
          <figure class="post-image-box">
            <img class="post-img" src="{{ $fImg }}" alt="{{ $fourthPost->title }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('storage/media/banners/hero-banner-main.jpg') }}'; this.classList.add('opacity-90');"/>
          </figure>
          <div class="post-body">
            <div class="post-meta-row">
              <span class="post-category-tag">{{ $fCat }}</span>
              <span class="post-meta-dot">&bull;</span>
              <span class="post-date">{{ $fDate }}</span>
              <span class="post-meta-dot">&bull;</span>
              <span class="post-date">{{ $fRead }}</span>
            </div>
            <div class="post-title-wrap">
              <h2 class="post-title" title="{{ $fourthPost->title }}">
                <a class="focus:outline-none focus:underline" href="{{ route('news.show', $fourthPost->slug) }}">{{ $fourthPost->title }}</a>
              </h2>
            </div>
            <p class="post-excerpt">{{ $fourthPost->excerpt ?: 'Quy chuẩn an toàn và biện pháp thi công lưới điện trung thế.' }}</p>
            <div class="post-action-wrap">
              <a class="post-read-more" href="{{ route('news.show', $fourthPost->slug) }}" title="Xem chi tiết bài viết">
                <span>Xem Thêm</span>
                <i class="post-arrow-icon fa-solid fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </article>
      @endif

      {{-- Articles 5+: 2-Column Grid --}}
      @if(!empty($gridGroup2))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-[32px]">
          @foreach($gridGroup2 as $p)
            @php
              $pDate = $p->published_at ? \Carbon\Carbon::parse($p->published_at)->format('d/m/Y') : date('d/m/Y');
              $pCat = $getCategoryName($p);
              $pImg = $p->featured_image ? asset($p->featured_image) : asset('storage/media/news/news-item-5.jpg');
            @endphp
            <article class="post-card post-card-grid group" data-figma-variant="Frame 232" data-post-id="{{ $p->slug }}">
              <figure class="post-image-box">
                <img class="post-img" src="{{ $pImg }}" alt="{{ $p->title }}" loading="lazy" onerror="this.onerror=null; this.src='{{ asset('storage/media/banners/hero-banner-main.jpg') }}'; this.classList.add('opacity-90');"/>
              </figure>
              <div class="post-body">
                <div class="post-meta-row">
                  <span class="post-category-tag">{{ $pCat }}</span>
                  <span class="post-meta-dot">&bull;</span>
                  <span class="post-date">{{ $pDate }}</span>
                </div>
                <div class="post-title-wrap">
                  <h2 class="post-title" title="{{ $p->title }}">
                    <a class="focus:outline-none focus:underline" href="{{ route('news.show', $p->slug) }}">{{ $p->title }}</a>
                  </h2>
                </div>
                <p class="post-excerpt">{{ $p->excerpt ?: 'Thông tin mới nhất về tiêu chuẩn IEC, TCVN và kiểm định EVN.' }}</p>
                <div class="post-action-wrap">
                  <a class="post-read-more" href="{{ route('news.show', $p->slug) }}" title="Xem chi tiết bài viết">
                    <span>Xem Thêm</span>
                    <i class="post-arrow-icon fa-solid fa-arrow-right"></i>
                  </a>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      @endif

      {{-- Pagination / View All --}}
      @if($news->hasPages())
        <div class="flex justify-center pt-6 pb-4">
          <div class="flex items-center gap-2">
            @if(!$news->onFirstPage())
              <a href="{{ $news->previousPageUrl() }}" class="h-[38px] px-4 rounded-[19px] bg-white border border-neutral-300 shadow-2xs hover:bg-neutral-100 flex items-center justify-center gap-2 text-neutral-900 transition-colors text-xs font-bold uppercase">
                <i class="fa-solid fa-arrow-left"></i> Trang Trước
              </a>
            @endif
            @if($news->hasMorePages())
              <a href="{{ $news->nextPageUrl() }}" class="h-[38px] px-6 rounded-[19px] bg-white border border-neutral-300 shadow-2xs hover:bg-neutral-100 flex items-center justify-center gap-2 text-neutral-900 transition-colors text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-arrow-down text-xs"></i> XEM THÊM
              </a>
            @endif
          </div>
        </div>
      @endif

    </div>
  </main>
@endsection
