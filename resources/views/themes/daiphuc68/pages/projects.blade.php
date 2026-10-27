@extends('themes.daiphuc68.layouts.app')

@section('title', 'Dự Án - CÔNG TY TNHH ĐẠI PHÚC 68')
@section('meta_description', 'Danh mục dự án công trình tiêu biểu, hạ tầng điện lưới và kiến trúc xây dựng từ CÔNG TY TNHH ĐẠI PHÚC 68.')

@section('content')
  {{-- Hero Banner --}}
  <section class="relative w-full h-[500px] md:h-[600px] overflow-hidden bg-brand-navy flex items-end justify-center pb-12 md:pb-16 text-white" aria-label="Hero Banner DỰ ÁN">
    <div class="absolute inset-0 z-0">
      <img class="w-full h-full object-cover object-center filter brightness-95" src="{{ asset('storage/media/projects/project-item-1.png') }}" alt="DỰ ÁN"/>
      <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-black/40"></div>
    </div>
    <div class="relative z-10 w-full max-w-[1110px] px-4 text-center">
      <h1 class="text-[26px] sm:text-[34px] md:text-[40px] lg:text-[44px] font-bold uppercase tracking-wider text-white drop-shadow-md">
        DỰ ÁN
      </h1>
    </div>
  </section>

  {{-- Breadcrumb --}}
  @include('themes.daiphuc68.components.breadcrumb', [
    'items' => [
      ['label' => 'DỰ ÁN', 'active' => true]
    ]
  ])

  {{-- Main Content --}}
  <main class="w-full max-w-[1440px] mx-auto bg-[#F6F6F6] py-10 md:py-16 flex flex-col gap-16 md:gap-24 overflow-hidden">
    
    {{-- Category Filter Navigation --}}
    <div class="w-full px-4 sm:px-8 lg:px-[72px]">
      <div class="flex flex-col md:flex-row md:items-center justify-between pb-3 text-[13px] sm:text-[14px] font-bold uppercase tracking-wider text-neutral-800 gap-3">
        <span class="text-neutral-900 font-extrabold cursor-pointer">LOẠI HÌNH DỰ ÁN</span>
        <div class="flex items-center gap-3 sm:gap-4 text-neutral-700 flex-wrap">
          <a class="hover:text-brand-navy transition-colors {{ empty($activeCategory) ? 'text-brand-navy font-bold' : '' }}" href="{{ route('projects.index') }}">TẤT CẢ</a>
          @foreach($projectCategories as $cat)
            <span class="text-neutral-300">|</span>
            <a class="hover:text-brand-navy transition-colors {{ ($activeCategory === $cat->slug) ? 'text-brand-navy font-bold' : '' }}" href="{{ route('projects.index', ['category' => $cat->slug]) }}#{{ $cat->slug }}">{{ mb_strtoupper($cat->name) }}</a>
          @endforeach
        </div>
      </div>
      <div class="w-full h-[1px] bg-neutral-300"></div>
    </div>

    @forelse($categoriesWithProjects as $categoryItem)
    {{-- Section: {{ $categoryItem->name }} --}}
    <section class="w-full" id="{{ $categoryItem->slug }}" aria-label="{{ $categoryItem->name }}">
      <div class="project-section-slider-block w-full flex flex-col gap-6" data-project-slider-id="slider-{{ $categoryItem->slug }}">
        <div class="w-full px-4 sm:px-8 lg:px-[72px]">
          <h2 class="text-[24px] sm:text-[28px] md:text-[32px] font-bold uppercase tracking-wide text-neutral-900">
            {{ mb_strtoupper($categoryItem->name) }}
          </h2>
        </div>
        <div class="swiper project-category-swiper w-full overflow-hidden" data-swiper-id="slider-{{ $categoryItem->slug }}">
          <div class="swiper-wrapper items-end">
            @forelse($categoryItem->projects as $index => $item)
              <div class="swiper-slide project-slide-item {{ $index === 0 ? 'swiper-slide-active is-active' : '' }}" data-project-id="{{ $item->slug }}" data-slide-index="{{ $index }}">
                <div class="project-card-dynamic group" data-figma-variant="Dynamic-Slider" data-project-id="{{ $item->slug }}">
                  <div class="project-image-box">
                    <img class="project-img" 
                         src="{{ $item->featured_image ? asset($item->featured_image) : asset('storage/media/projects/project-detail-hero.jpg') }}" 
                         alt="{{ $item->title }}" 
                         loading="lazy" 
                         onerror="this.onerror=null; this.src='{{ asset('storage/media/projects/project-detail-hero.jpg') }}'; this.classList.add('opacity-90');"/>
                    <span class="project-badge">{{ $item->category_name ?: $categoryItem->name }}</span>
                  </div>
                  <div class="project-info-row">
                    <h3 class="project-title">
                      <a class="hover:text-brand-navy transition-colors" href="{{ route('projects.show', $item->slug) }}">{{ $item->title }}</a>
                    </h3>
                    <a class="project-read-more" href="{{ route('projects.show', $item->slug) }}" title="Xem chi tiết dự án">
                      <span>Xem Thêm</span>
                      <i class="fa-solid fa-arrow-right text-[11px]"></i>
                    </a>
                  </div>
                </div>
              </div>
            @empty
              <div class="p-8 text-neutral-500">Đang cập nhật dự án trong danh mục này...</div>
            @endforelse
          </div>
        </div>
        <div class="w-full px-4 sm:px-8 lg:px-[72px]">
          <div class="project-slider-controls flex items-center justify-between gap-4 pt-3 sm:pt-4 select-none">
            <div class="project-progress-track relative flex-1 max-w-[958px] h-[3px] bg-neutral-200 rounded-full overflow-hidden">
              <div class="project-progress-bar slider-{{ $categoryItem->slug }}-progress w-[117px] h-full bg-neutral-900 rounded-full transition-all duration-300 ease-out" style="width: 25%;"></div>
            </div>
            <div class="project-nav-buttons flex items-center gap-3 shrink-0">
              <button class="project-btn-prev slider-{{ $categoryItem->slug }}-prev w-8 h-8 rounded-full bg-[#DADADA] hover:bg-neutral-800 text-neutral-600 hover:text-white flex items-center justify-center transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed shadow-xs cursor-pointer" type="button" aria-label="Dự án trước đó">
                <img class="w-auto h-3.5 object-contain" src="{{ asset('storage/media/icon/prev.svg') }}" alt="Prev"/>
              </button>
              <button class="project-btn-next slider-{{ $categoryItem->slug }}-next w-8 h-8 rounded-full bg-[#DADADA] hover:bg-neutral-800 text-neutral-600 hover:text-white flex items-center justify-center transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed shadow-xs cursor-pointer" type="button" aria-label="Dự án kế tiếp">
                <img class="w-auto h-3.5 object-contain" src="{{ asset('storage/media/icon/next.svg') }}" alt="Next"/>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
    @empty
      <div class="w-full text-center py-16 text-neutral-500 font-medium">Chưa có dự án nào được công bố.</div>
    @endforelse

  </main>
@endsection
