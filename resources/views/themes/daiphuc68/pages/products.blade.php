@extends('themes.daiphuc68.layouts.app')

@section('title', 'Danh Mục Sản Phẩm & Vật Tư Thiết Bị Điện - CÔNG TY TNHH ĐẠI PHÚC 68')
@section('meta_description', 'Đại Phúc 68 chuyên cung ứng trọn gói vật tư thiết bị điện trung thế, hạ thế: Cầu chì tự rơi, chống sét van, máy biến áp, cáp điện chính hãng Vina Electric, Thăng Long, THC đạt chuẩn EVN.')

@section('content')
  {{-- Hero Banner --}}
  <section class="relative w-full h-[500px] md:h-[600px] overflow-hidden bg-brand-navy flex items-end justify-center pb-12 md:pb-16 text-white" aria-label="Hero Banner SẢN PHẨM">
    <div class="absolute inset-0 z-0">
      <img class="w-full h-full object-cover object-center filter brightness-95" src="{{ asset('storage/media/banners/hero-banner-main.jpg') }}" alt="SẢN PHẨM" />
      <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-black/40"></div>
    </div>
    <div class="relative z-10 w-full max-w-[1110px] px-4 text-center">
      <h1 class="text-[26px] sm:text-[34px] md:text-[40px] lg:text-[44px] font-bold uppercase tracking-wider text-white drop-shadow-md">
        SẢN PHẨM
      </h1>
    </div>
  </section>

  {{-- Breadcrumb --}}
  @include('themes.daiphuc68.components.breadcrumb', [
    'items' => [
      ['label' => 'SẢN PHẨM', 'active' => true]
    ]
  ])

  {{-- Danh mục thiết bị điện nổi bật --}}
  @if(isset($featuredCategories) && $featuredCategories->isNotEmpty())
  <section class="bg-[#F6F6F6] pt-10 md:pt-14 pb-4 md:pb-6 flex justify-center" aria-label="Danh mục thiết bị điện nổi bật">
    <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-[72px]">
      @php
        $catCount = $featuredCategories->count();
        $gridClass = match(true) {
          $catCount === 1 => 'grid-cols-1 max-w-xl justify-self-center',
          $catCount === 2 => 'grid-cols-1 md:grid-cols-2',
          $catCount === 4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
          default => 'grid-cols-1 sm:grid-cols-2 md:grid-cols-3',
        };
        $defaultCatImages = [
          'vat-tu-thiet-bi-dien' => 'storage/media/banners/hero-banner-main.jpg',
          'ong-tru-be-tong-ly-tam' => 'storage/media/projects/project-detail-hero.jpg',
          'thiet-bi-nang-luong-mat-troi' => 'storage/media/projects/project-item-1.png',
          'dong-cat-trung-the' => 'storage/media/projects/project-detail-hero.jpg',
          'may-bien-ap-tram' => 'storage/media/projects/project-item-1.png',
          'thiet-bi-do-luong' => 'storage/media/projects/project-item-2.jpg',
        ];
        $fallbackMediaList = [
          'storage/media/banners/hero-banner-main.jpg',
          'storage/media/projects/project-detail-hero.jpg',
          'storage/media/projects/project-item-1.png',
          'storage/media/projects/project-item-2.jpg',
          'storage/media/projects/project-item-3.jpg',
        ];
      @endphp
      <div class="relative w-full featured-categories-slider-wrap group/cats">
        <div class="swiper featured-categories-swiper w-full overflow-hidden">
          <div class="swiper-wrapper">
            @foreach($featuredCategories as $index => $cat)
              @php
                $catSlug = $cat->slug ?? \Illuminate\Support\Str::slug($cat->name ?? '');
                $catImage = !empty($cat->image) ? asset($cat->image) : asset($defaultCatImages[$catSlug] ?? $fallbackMediaList[$index % count($fallbackMediaList)]);
                $isActiveCat = request('category') === $catSlug || request('category') === ($cat->name ?? '');
              @endphp
              <div class="swiper-slide h-auto">
                <a class="relative h-[220px] md:h-[260px] lg:h-[290px] rounded-[12px] overflow-hidden group shadow-sm block transition-all duration-300 hover:shadow-lg focus:outline-none {{ $isActiveCat ? 'ring-2 ring-brand-navy shadow-md' : '' }}" 
                   href="{{ $isActiveCat ? route('products.index') : route('products.index', ['category' => $catSlug]) }}"
                   title="{{ $cat->name }}">
                  <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                       src="{{ $catImage }}" 
                       alt="{{ $cat->name }}"
                       loading="lazy"
                       onerror="this.onerror=null; this.src='{{ asset('storage/media/banners/hero-banner-main.jpg') }}';">
                  <div class="absolute inset-0 bg-black/40 group-hover:bg-black/30 transition-colors {{ $isActiveCat ? 'bg-black/55' : '' }}"></div>
                  <div class="absolute inset-0 flex items-center justify-center p-6 text-center">
                    <h2 class="text-[17px] sm:text-[19px] md:text-[21px] font-bold uppercase text-white tracking-wider drop-shadow-md group-hover:scale-[1.02] transition-transform duration-300">
                      {!! nl2br(e($cat->name)) !!}
                    </h2>
                  </div>
                  @if($isActiveCat)
                    <div class="absolute top-3.5 right-3.5 px-3 py-1 bg-brand-navy/95 backdrop-blur-xs text-white text-[11px] font-bold uppercase tracking-wider rounded-lg shadow-sm flex items-center gap-1.5">
                      <i class="fa-solid fa-check text-[10px]"></i>
                      <span>Đang chọn</span>
                    </div>
                  @endif
                </a>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Navigation arrows --}}
        <button class="featured-cat-prev absolute -left-3 sm:-left-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/95 hover:bg-brand-navy hover:text-white text-neutral-800 shadow-md backdrop-blur-xs flex items-center justify-center transition-all opacity-80 sm:opacity-0 group-hover/cats:opacity-100 disabled:opacity-0 disabled:pointer-events-none cursor-pointer border border-neutral-200 focus:outline-none" type="button" aria-label="Danh mục trước">
          <i class="fa-solid fa-chevron-left text-xs sm:text-sm"></i>
        </button>
        <button class="featured-cat-next absolute -right-3 sm:-right-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white/95 hover:bg-brand-navy hover:text-white text-neutral-800 shadow-md backdrop-blur-xs flex items-center justify-center transition-all opacity-80 sm:opacity-0 group-hover/cats:opacity-100 disabled:opacity-0 disabled:pointer-events-none cursor-pointer border border-neutral-200 focus:outline-none" type="button" aria-label="Danh mục sau">
          <i class="fa-solid fa-chevron-right text-xs sm:text-sm"></i>
        </button>
      </div>
    </div>
  </section>
  @endif

  {{-- Main Catalog & Sidebar Filter --}}
  <section class="pt-6 pb-16 md:pb-24 bg-[#F6F6F6] flex justify-center">
    <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-[72px] flex flex-col gap-6">
      
      {{-- Mobile filter bar trigger --}}
      <div class="lg:hidden flex items-center justify-between gap-4 bg-white p-4 rounded-xl border border-neutral-200 shadow-sm">
        <div class="text-xs font-bold text-neutral-800 uppercase flex items-center gap-2">
          <i class="fa-solid fa-filter text-brand-navy"></i>
          <span>Bộ Lọc &amp; Phân Loại</span>
        </div>
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-navy text-white text-xs font-bold uppercase tracking-wider hover:bg-[#0c2370] transition-colors" type="button" id="toggle-mobile-product-filters">
          <i class="fa-solid fa-sliders"></i>
          <span>Tùy Chọn Lọc</span>
        </button>
      </div>

      <div class="flex flex-col lg:flex-row items-start lg:gap-[36px] w-full" id="product-catalog-layout">
        
        {{-- Sidebar Filter --}}
        <style>
          .price-range-slider-thumb::-webkit-slider-thumb {
            pointer-events: auto;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #051650;
            border: 2px solid #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.35);
            cursor: pointer;
            transition: transform 0.15s ease, background-color 0.15s ease;
          }
          .price-range-slider-thumb::-webkit-slider-thumb:hover {
            transform: scale(1.15);
            background: #0c2370;
          }
          .price-range-slider-thumb::-moz-range-thumb {
            pointer-events: auto;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #051650;
            border: 2px solid #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.35);
            cursor: pointer;
          }
        </style>
        <aside class="w-full lg:w-[297px] shrink-0 flex flex-col gap-6 lg:sticky lg:top-[140px] self-start max-h-[calc(100vh-160px)] overflow-y-auto" id="product-sidebar-filter" aria-label="Bộ lọc danh mục sản phẩm">
          <div class="flex items-center justify-between h-[44px]">
            <div class="flex items-center gap-2.5">
              <svg class="w-4 h-4 text-black" viewBox="0 0 24 24" fill="currentColor">
                <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"></path>
              </svg>
              <span class="text-[14px] font-bold uppercase tracking-wider text-black">BỘ LỌC</span>
            </div>
            <div class="flex items-center gap-2">
              <button class="w-8 h-8 rounded-full border border-neutral-400 hover:border-black hover:bg-white text-neutral-700 hover:text-black flex items-center justify-center transition-all cursor-pointer" type="button" id="reset-all-product-filters" title="Đặt lại bộ lọc" aria-label="Đặt lại bộ lọc">
                <i class="fa-solid fa-arrows-rotate text-xs"></i>
              </button>
              <button class="w-8 h-8 rounded-full border border-neutral-300 hover:border-black hover:bg-white text-neutral-500 hover:text-black flex items-center justify-center transition-all cursor-pointer lg:hidden" type="button" id="close-sidebar-filter-btn" title="Đóng bộ lọc" aria-label="Đóng bộ lọc">
                <i class="fa-solid fa-xmark text-xs"></i>
              </button>
            </div>
          </div>
          
          <div class="w-full border-t border-black"></div>

          @php
            $catFilterConfig = $filterSettings['category'] ?? ['enabled' => true, 'title' => 'LOẠI SẢN PHẨM', 'selection_type' => 'checkbox', 'is_expanded' => true];
            $brandFilterConfig = $filterSettings['brand'] ?? ['enabled' => true, 'title' => 'THƯƠNG HIỆU', 'display_type' => 'logo', 'is_expanded' => true];
            $priceFilterConfig = $filterSettings['price'] ?? ['enabled' => true, 'title' => 'KHOẢNG GIÁ', 'type' => 'milestones', 'is_expanded' => true];

            $isExpandedFn = function($val) {
              if (is_bool($val)) return $val;
              if (is_numeric($val)) return (int)$val === 1;
              if (is_string($val)) {
                $v = strtolower(trim($val));
                if (in_array($v, ['false', '0', 'collapsed', 'thu gọn']) || str_contains($v, 'thu gọn') || str_contains($v, 'collapse')) {
                  return false;
                }
                return true;
              }
              return true;
            };
            $catExpanded = $isExpandedFn($catFilterConfig['is_expanded'] ?? true);
            $brandExpanded = $isExpandedFn($brandFilterConfig['is_expanded'] ?? true);
            $priceExpanded = $isExpandedFn($priceFilterConfig['is_expanded'] ?? true);
          @endphp

          {{-- Filter Group: Loại Sản Phẩm --}}
          @if($catFilterConfig['enabled'] ?? true)
          <div class="flex flex-col gap-4 filter-group">
            <div class="flex items-center justify-between cursor-pointer select-none py-0.5 group" data-filter-accordion-toggle aria-expanded="{{ $catExpanded ? 'true' : 'false' }}">
              <h3 class="text-[13px] font-bold uppercase tracking-wider text-black group-hover:text-brand-navy transition-colors">
                {{ $catFilterConfig['title'] ?: 'LOẠI SẢN PHẨM' }}
              </h3>
              <i class="fa-solid {{ $catExpanded ? 'fa-chevron-up' : 'fa-chevron-down' }} text-xs text-[#818181] group-hover:text-brand-navy transition-transform duration-300 ease-out"></i>
            </div>
            <div class="filter-content flex flex-col gap-3 pt-1 text-sm overflow-hidden transition-all duration-300 ease-in-out {{ $catExpanded ? '' : 'hidden' }}">
              @if(($catFilterConfig['selection_type'] ?? 'checkbox') === 'dropdown')
                <select id="cat-filter-select" class="w-full px-3 py-2 bg-neutral-100 border border-neutral-300 rounded-lg text-xs font-bold uppercase text-black focus:ring-1 focus:ring-brand-navy outline-none cursor-pointer">
                  <option value="">-- Tất cả danh mục --</option>
                  @foreach($filterCategories as $cat)
                    <option value="{{ $cat->slug }}" {{ (request('category') === $cat->slug || request('category') === $cat->name) ? 'selected' : '' }}>{{ $cat->name }}</option>
                  @endforeach
                </select>
              @else
                @forelse($filterCategories as $cat)
                  @php
                    $isCatSelected = request('category') === $cat->slug || request('category') === $cat->name;
                  @endphp
                  <label class="flex items-center gap-3 group cursor-pointer select-none py-0.5" @if(($catFilterConfig['selection_type'] ?? '') === 'tree' && isset($cat->tree_level) && $cat->tree_level > 0) style="padding-left: {{ $cat->tree_level * 14 }}px" @endif>
                    <input class="w-5 h-5 rounded-[1.75px] border-[#818181] text-brand-navy focus:ring-0 accent-brand-navy cursor-pointer product-filter-cb" 
                           type="{{ ($catFilterConfig['selection_type'] ?? 'checkbox') === 'radio' ? 'radio' : 'checkbox' }}" 
                           name="{{ ($catFilterConfig['selection_type'] ?? 'checkbox') === 'radio' ? 'cat_filter' : 'cat_' . $cat->slug }}" 
                           value="{{ $cat->slug }}"
                           data-cat-id="{{ $cat->id }}"
                           {{ $isCatSelected ? 'checked' : '' }}>
                    <span class="text-xs font-medium {{ $isCatSelected ? 'text-brand-navy font-bold' : 'text-black' }} group-hover:text-brand-navy transition-colors uppercase flex items-center gap-1.5">
                      @if(($catFilterConfig['selection_type'] ?? '') === 'tree' && isset($cat->tree_level) && $cat->tree_level > 0)
                        <span class="text-neutral-400 font-mono">└</span>
                      @endif
                      {{ $cat->name }}
                    </span>
                  </label>
                @empty
                  <p class="text-xs text-neutral-400 italic">Không có danh mục nào.</p>
                @endforelse
              @endif
            </div>
          </div>

          <div class="w-full border-t border-black"></div>
          @endif

          {{-- Filter Group: Thương Hiệu --}}
          @if($brandFilterConfig['enabled'] ?? true)
          <div class="flex flex-col gap-4 filter-group">
            <div class="flex items-center justify-between cursor-pointer select-none py-0.5 group" data-filter-accordion-toggle aria-expanded="{{ $brandExpanded ? 'true' : 'false' }}">
              <h3 class="text-[13px] font-bold uppercase tracking-wider text-black group-hover:text-brand-navy transition-colors">
                {{ $brandFilterConfig['title'] ?: 'THƯƠNG HIỆU' }}
              </h3>
              <i class="fa-solid {{ $brandExpanded ? 'fa-chevron-up' : 'fa-chevron-down' }} text-xs text-[#818181] group-hover:text-brand-navy transition-transform duration-300 ease-out"></i>
            </div>
            <div class="filter-content flex flex-col gap-4 pt-1 overflow-hidden transition-all duration-300 ease-in-out {{ $brandExpanded ? '' : 'hidden' }}">
              @forelse($filterBrands as $brand)
                @php
                  $bSlug = $brand->slug;
                  $bMeta = is_array($brand->meta_data) ? $brand->meta_data : json_decode($brand->meta_data ?? '[]', true);
                  $logoRel = $bMeta['logo'] ?? $brand->image ?? null;
                  if (!$logoRel && file_exists(public_path("storage/media/brands/brand-{$bSlug}.svg"))) {
                      $logoRel = "storage/media/brands/brand-{$bSlug}.svg";
                  }
                  $logoUrl = $logoRel ? (str_starts_with($logoRel, 'http') ? $logoRel : asset($logoRel)) : null;
                  $showLogo = ($brandFilterConfig['display_type'] ?? 'logo') === 'logo' && $logoUrl;
                  $isBrandSelected = request('brand') === $bSlug || request('brand') === $brand->name;
                @endphp
                <label class="flex items-center gap-3.5 group cursor-pointer select-none">
                  <input class="w-5 h-5 rounded-[1.75px] border-[#818181] text-brand-navy focus:ring-0 accent-brand-navy cursor-pointer product-filter-cb" 
                         type="checkbox" 
                         name="brand_{{ $bSlug }}" 
                         value="{{ $bSlug }}"
                         data-brand-id="{{ $brand->id }}"
                         {{ $isBrandSelected ? 'checked' : '' }}>
                  @if($showLogo)
                    <div class="flex items-center h-8">
                      <img class="h-6 max-w-[130px] w-auto object-contain" src="{{ $logoUrl }}" alt="{{ $brand->name }}">
                    </div>
                  @else
                    <span class="text-xs font-bold text-black uppercase tracking-wider group-hover:text-brand-navy transition-colors">
                      {{ $brand->name }}
                    </span>
                  @endif
                </label>
              @empty
                <p class="text-xs text-neutral-400 italic">Không có thương hiệu nào.</p>
              @endforelse
            </div>
          </div>

          <div class="w-full border-t border-black"></div>
          @endif

          {{-- Filter Group: Khoảng Giá --}}
          @if($priceFilterConfig['enabled'] ?? true)
          <div class="flex flex-col gap-4 filter-group">
            <div class="flex items-center justify-between cursor-pointer select-none py-0.5 group" data-filter-accordion-toggle aria-expanded="{{ $priceExpanded ? 'true' : 'false' }}">
              <h3 class="text-[13px] font-bold uppercase tracking-wider text-black group-hover:text-brand-navy transition-colors">
                {{ $priceFilterConfig['title'] ?: 'KHOẢNG GIÁ' }}
              </h3>
              <i class="fa-solid {{ $priceExpanded ? 'fa-chevron-up' : 'fa-chevron-down' }} text-xs text-[#818181] group-hover:text-brand-navy transition-transform duration-300 ease-out"></i>
            </div>
            <div class="filter-content flex flex-col gap-3 pt-1 text-sm overflow-hidden transition-all duration-300 ease-in-out {{ $priceExpanded ? '' : 'hidden' }}">
              @if(($priceFilterConfig['type'] ?? 'milestones') === 'slider')
                {{-- Range Slider Mode --}}
                <div class="price-slider-block flex flex-col gap-3 py-1"
                     id="price-filter-slider-box"
                     data-min="{{ $priceFilterConfig['min_price'] ?? 0 }}"
                     data-max="{{ $priceFilterConfig['max_price'] ?? 1000000 }}"
                     data-step="{{ $priceFilterConfig['step'] ?? 50000 }}">
                  <div class="flex items-center justify-between text-xs font-bold text-neutral-800">
                    <span id="price-slider-min-val">{{ number_format($priceFilterConfig['min_price'] ?? 0, 0, ',', '.') }}đ</span>
                    <span class="text-neutral-400 font-normal">-</span>
                    <span id="price-slider-max-val">{{ number_format($priceFilterConfig['max_price'] ?? 1000000, 0, ',', '.') }}đ</span>
                  </div>
                  <div class="relative w-full h-5 flex items-center select-none">
                    <div class="w-full h-1.5 bg-neutral-200 rounded-full relative overflow-hidden">
                      <div id="price-slider-progress" class="absolute h-full bg-brand-navy rounded-full transition-none" style="left: 0%; right: 0%;"></div>
                    </div>
                    <input type="range" id="price-range-min-input" 
                           min="{{ $priceFilterConfig['min_price'] ?? 0 }}" 
                           max="{{ $priceFilterConfig['max_price'] ?? 1000000 }}" 
                           step="{{ $priceFilterConfig['step'] ?? 50000 }}" 
                           value="{{ $priceFilterConfig['min_price'] ?? 0 }}" 
                           class="price-range-slider-thumb absolute w-full pointer-events-none appearance-none bg-transparent">
                    <input type="range" id="price-range-max-input" 
                           min="{{ $priceFilterConfig['min_price'] ?? 0 }}" 
                           max="{{ $priceFilterConfig['max_price'] ?? 1000000 }}" 
                           step="{{ $priceFilterConfig['step'] ?? 50000 }}" 
                           value="{{ $priceFilterConfig['max_price'] ?? 1000000 }}" 
                           class="price-range-slider-thumb absolute w-full pointer-events-none appearance-none bg-transparent">
                  </div>
                  <div class="flex items-center gap-2 pt-1">
                    <div class="flex-1 bg-neutral-100 rounded-lg px-2.5 py-1.5 border border-neutral-200">
                      <span class="block text-[9px] text-neutral-500 font-semibold uppercase">Từ</span>
                      <input type="number" id="price-box-min" 
                             value="{{ $priceFilterConfig['min_price'] ?? 0 }}" 
                             min="{{ $priceFilterConfig['min_price'] ?? 0 }}" 
                             max="{{ $priceFilterConfig['max_price'] ?? 1000000 }}" 
                             step="{{ $priceFilterConfig['step'] ?? 50000 }}" 
                             class="w-full text-xs font-bold text-black bg-transparent outline-none">
                    </div>
                    <span class="text-neutral-400 font-bold">-</span>
                    <div class="flex-1 bg-neutral-100 rounded-lg px-2.5 py-1.5 border border-neutral-200">
                      <span class="block text-[9px] text-neutral-500 font-semibold uppercase">Đến</span>
                      <input type="number" id="price-box-max" 
                             value="{{ $priceFilterConfig['max_price'] ?? 1000000 }}" 
                             min="{{ $priceFilterConfig['min_price'] ?? 0 }}" 
                             max="{{ $priceFilterConfig['max_price'] ?? 1000000 }}" 
                             step="{{ $priceFilterConfig['step'] ?? 50000 }}" 
                             class="w-full text-xs font-bold text-black bg-transparent outline-none">
                    </div>
                  </div>
                  @if($priceFilterConfig['allow_contact'] ?? true)
                  <label class="flex items-center gap-2.5 cursor-pointer pt-1 group select-none">
                    <input type="checkbox" id="price-slider-contact-cb" class="w-4 h-4 rounded text-brand-navy accent-brand-navy cursor-pointer product-filter-cb" name="price_contact" value="contact">
                    <span class="text-xs font-medium text-neutral-700 group-hover:text-brand-navy transition-colors">Bao gồm giá liên hệ</span>
                  </label>
                  @endif
                </div>
              @else
                @forelse($filterPriceRanges as $range)
                  <label class="flex items-center gap-3 group cursor-pointer select-none py-0.5">
                    <input class="w-5 h-5 rounded-[1.75px] border-[#818181] text-brand-navy focus:ring-0 accent-brand-navy cursor-pointer product-filter-cb" 
                           type="checkbox" 
                           name="price_{{ $range['id'] ?? 'range' }}" 
                           value="{{ $range['id'] ?? 'range' }}"
                           data-price-min="{{ $range['min'] ?? '' }}"
                           data-price-max="{{ $range['max'] ?? '' }}"
                           data-price-type="{{ $range['type'] ?? 'range' }}"
                           {{ request('price_range') === ($range['id'] ?? '') ? 'checked' : '' }}>
                    <span class="text-xs font-medium text-black group-hover:text-brand-navy transition-colors">
                      {{ $range['label'] }}
                    </span>
                  </label>
                @empty
                  <label class="flex items-center gap-3 group cursor-pointer select-none py-0.5">
                    <input class="w-5 h-5 rounded-[1.75px] border-[#818181] text-brand-navy focus:ring-0 accent-brand-navy cursor-pointer product-filter-cb" type="checkbox" name="price_under_200" value="under_200" {{ request('price_range') === 'under_200' ? 'checked' : '' }}>
                    <span class="text-xs font-medium text-black group-hover:text-brand-navy transition-colors">Dưới 200.000đ</span>
                  </label>
                  <label class="flex items-center gap-3 group cursor-pointer select-none py-0.5">
                    <input class="w-5 h-5 rounded-[1.75px] border-[#818181] text-brand-navy focus:ring-0 accent-brand-navy cursor-pointer product-filter-cb" type="checkbox" name="price_200_400" value="200_400" {{ request('price_range') === '200_400' ? 'checked' : '' }}>
                    <span class="text-xs font-medium text-black group-hover:text-brand-navy transition-colors">200.000đ - 400.000đ</span>
                  </label>
                  <label class="flex items-center gap-3 group cursor-pointer select-none py-0.5">
                    <input class="w-5 h-5 rounded-[1.75px] border-[#818181] text-brand-navy focus:ring-0 accent-brand-navy cursor-pointer product-filter-cb" type="checkbox" name="price_over_400" value="over_400" {{ request('price_range') === 'over_400' ? 'checked' : '' }}>
                    <span class="text-xs font-medium text-black group-hover:text-brand-navy transition-colors">Trên 400.000đ</span>
                  </label>
                  <label class="flex items-center gap-3 group cursor-pointer select-none py-0.5">
                    <input class="w-5 h-5 rounded-[1.75px] border-[#818181] text-brand-navy focus:ring-0 accent-brand-navy cursor-pointer product-filter-cb" type="checkbox" name="price_contact" value="contact" {{ request('price_range') === 'contact' ? 'checked' : '' }}>
                    <span class="text-xs font-medium text-black group-hover:text-brand-navy transition-colors">Giá liên hệ dự án</span>
                  </label>
                @endforelse
              @endif
            </div>
          </div>
          @endif
        </aside>

        {{-- Main Catalog Area --}}
        <div class="flex-1 min-w-0 w-full lg:max-w-[963px] flex flex-col gap-6 lg:gap-8">
          
          {{-- Toolbar --}}
          <div class="flex flex-wrap items-center justify-between gap-6 min-h-[24px] w-full" id="product-catalog-toolbar">
            <div class="flex items-center gap-3 sm:gap-4">
              {{-- Search Input box --}}
              <div class="relative flex items-center">
                <button class="w-[43px] h-[40px] rounded-full border border-[#323232]/60 hover:border-black bg-[#F6F6F6] hover:bg-white flex items-center justify-center text-[#818181] hover:text-black transition-all cursor-pointer shadow-2xs focus:outline-none" type="button" id="product-toolbar-search-btn" title="Tìm kiếm sản phẩm" aria-label="Tìm kiếm sản phẩm">
                  <svg class="w-[43px] h-[40px]" viewBox="59 0 44 44" fill="none">
                    <path d="M89.21 29.29L85.81 25.9C86.907 24.5025 87.5022 22.7767 87.5 21C87.5 19.4178 87.0308 17.871 86.1518 16.5554C85.2727 15.2398 84.0233 14.2145 82.5615 13.609C81.0997 13.0035 79.4911 12.845 77.9393 13.1537C76.3874 13.4624 74.962 14.2243 73.8431 15.3431C72.7243 16.462 71.9624 17.8874 71.6537 19.4393C71.345 20.9911 71.5035 22.5997 72.109 24.0615C72.7145 25.5233 73.7398 26.7727 75.0554 27.6518C76.371 28.5308 77.9178 29 79.5 29C81.2767 29.0022 83.0025 28.407 84.4 27.31L87.79 30.71C87.883 30.8037 87.9936 30.8781 88.1154 30.9289C88.2373 30.9797 88.368 31.0058 88.5 31.0058C88.632 31.0058 88.7627 30.9797 88.8846 30.9289C89.0064 30.8781 89.117 30.8037 89.21 30.71C89.3037 30.617 89.3781 30.5064 89.4289 30.3846C89.4797 30.2627 89.5058 30.132 89.5058 30C89.5058 29.868 89.4797 29.7373 89.4289 29.6154C89.3781 29.4936 89.3037 29.383 89.21 29.29ZM73.5 21C73.5 19.8133 73.8519 18.6533 74.5112 17.6666C75.1705 16.6799 76.1075 15.9109 77.2039 15.4567C78.3003 15.0026 79.5067 14.8838 80.6705 15.1153C81.8344 15.3468 82.9035 15.9182 83.7426 16.7574C84.5818 17.5965 85.1532 18.6656 85.3847 19.8295C85.6162 20.9933 85.4974 22.1997 85.0433 23.2961C84.5892 24.3925 83.8201 25.3295 82.8334 25.9888C81.8467 26.6481 80.6867 27 79.5 27C77.9087 27 76.3826 26.3679 75.2574 25.2426C74.1321 24.1174 73.5 22.5913 73.5 21Z" fill="currentColor"></path>
                  </svg>
                </button>
                <div class="hidden absolute left-0 z-20 w-[240px] sm:w-[280px] bg-white rounded-full border border-neutral-300 shadow-lg px-3 py-1.5 flex items-center gap-2" id="product-toolbar-search-box">
                  <i class="fa-solid fa-magnifying-glass text-xs text-neutral-400"></i>
                  <input class="w-full text-xs text-neutral-800 bg-transparent border-none focus:outline-none focus:ring-0" type="text" id="product-search-input" value="{{ $searchKeyword }}" placeholder="Tìm theo tên sản phẩm...">
                  <button class="text-neutral-400 hover:text-neutral-700 text-xs px-1 cursor-pointer" type="button" id="product-search-clear" aria-label="Xóa tìm kiếm">&times;</button>
                </div>
              </div>
            </div>

            {{-- Sorting dropdown --}}
            <div class="flex items-center gap-3 sm:gap-4">
              <span class="text-[13px] sm:text-[14px] font-normal text-black select-none whitespace-nowrap">Sắp xếp theo</span>
              <div class="relative select-none" id="product-sort-dropdown">
                <button class="w-[180px] sm:w-[220px] md:w-[258px] h-[44px] px-3.5 sm:px-4 rounded-[12px] bg-[#F6F6F6] hover:bg-white border border-[#051650] flex items-center justify-between text-[#818181] hover:text-black transition-all cursor-pointer shadow-2xs focus:outline-none" type="button" id="product-sort-trigger" aria-haspopup="listbox" aria-expanded="false">
                  <span class="text-[13px] sm:text-[14px] font-normal truncate text-[#818181]" id="product-sort-current-label">
                    @if($currentSort === 'name-desc') Theo tên Z-A
                    @elseif($currentSort === 'price-asc') Giá: Thấp đến Cao
                    @elseif($currentSort === 'price-desc') Giá: Cao đến Thấp
                    @elseif($currentSort === 'newest') Mới nhất
                    @else Theo tên A-Z
                    @endif
                  </span>
                  <div class="flex items-center gap-2 sm:gap-2.5 pl-2">
                    <span class="h-5 w-[0.5px] bg-[#323232]/40"></span>
                    <svg class="w-3.5 h-3.5 text-[#818181] transition-transform duration-200" id="product-sort-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </button>
                <div class="hidden absolute right-0 top-[calc(100%+6px)] w-full bg-white rounded-[12px] border border-neutral-200 shadow-xl z-30 py-1.5 overflow-hidden transition-all duration-200" id="product-sort-menu" role="listbox">
                  <button class="w-full text-left px-4 py-2.5 text-xs sm:text-[13px] text-neutral-700 hover:bg-neutral-100 hover:text-brand-navy font-medium transition-colors cursor-pointer flex items-center justify-between {{ $currentSort === 'name-asc' ? 'active-sort' : '' }}" type="button" data-sort-value="name-asc">
                    <span>Theo tên A-Z</span>
                    <i class="fa-solid fa-check text-[11px] text-brand-navy {{ $currentSort === 'name-asc' ? '' : 'opacity-0' }}"></i>
                  </button>
                  <button class="w-full text-left px-4 py-2.5 text-xs sm:text-[13px] text-neutral-700 hover:bg-neutral-100 hover:text-brand-navy font-medium transition-colors cursor-pointer flex items-center justify-between {{ $currentSort === 'name-desc' ? 'active-sort' : '' }}" type="button" data-sort-value="name-desc">
                    <span>Theo tên Z-A</span>
                    <i class="fa-solid fa-check text-[11px] text-brand-navy {{ $currentSort === 'name-desc' ? '' : 'opacity-0' }}"></i>
                  </button>
                  <button class="w-full text-left px-4 py-2.5 text-xs sm:text-[13px] text-neutral-700 hover:bg-neutral-100 hover:text-brand-navy font-medium transition-colors cursor-pointer flex items-center justify-between {{ $currentSort === 'price-asc' ? 'active-sort' : '' }}" type="button" data-sort-value="price-asc">
                    <span>Giá: Thấp đến Cao</span>
                    <i class="fa-solid fa-check text-[11px] text-brand-navy {{ $currentSort === 'price-asc' ? '' : 'opacity-0' }}"></i>
                  </button>
                  <button class="w-full text-left px-4 py-2.5 text-xs sm:text-[13px] text-neutral-700 hover:bg-neutral-100 hover:text-brand-navy font-medium transition-colors cursor-pointer flex items-center justify-between {{ $currentSort === 'price-desc' ? 'active-sort' : '' }}" type="button" data-sort-value="price-desc">
                    <span>Giá: Cao đến Thấp</span>
                    <i class="fa-solid fa-check text-[11px] text-brand-navy {{ $currentSort === 'price-desc' ? '' : 'opacity-0' }}"></i>
                  </button>
                  <button class="w-full text-left px-4 py-2.5 text-xs sm:text-[13px] text-neutral-700 hover:bg-neutral-100 hover:text-brand-navy font-medium transition-colors cursor-pointer flex items-center justify-between {{ $currentSort === 'newest' ? 'active-sort' : '' }}" type="button" data-sort-value="newest">
                    <span>Mới nhất</span>
                    <i class="fa-solid fa-check text-[11px] text-brand-navy {{ $currentSort === 'newest' ? '' : 'opacity-0' }}"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          {{-- Product Catalog Swiper Slider --}}
          <div class="swiper product-catalog-swiper w-full overflow-hidden" id="product-catalog-swiper">
            <div class="swiper-wrapper">
              @forelse($allProducts->chunk(6) as $productChunk)
                <div class="swiper-slide w-full">
                  <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-2.5 sm:gap-6 lg:gap-x-[36px] lg:gap-y-[36px] w-full">
                    @foreach($productChunk as $product)
                      @include('themes.daiphuc68.components.product-card', ['product' => $product])
                    @endforeach
                  </div>
                </div>
              @empty
                <div class="swiper-slide w-full py-16 text-center flex flex-col items-center gap-4 bg-white rounded-xl border border-neutral-200">
                  <i class="fa-solid fa-box-open text-4xl text-neutral-300"></i>
                  <h4 class="text-base font-bold text-neutral-800">Không tìm thấy sản phẩm phù hợp</h4>
                  <p class="text-xs text-neutral-500 max-w-md">Vui lòng thử tìm kiếm với từ khóa khác hoặc bỏ chọn một số tiêu chí bộ lọc.</p>
                  <a href="{{ route('products.index') }}" class="inline-block px-4 py-2 rounded-lg bg-brand-navy text-white text-xs font-bold uppercase tracking-wider hover:bg-[#0c2370] transition-colors">
                    Đặt lại bộ lọc
                  </a>
                </div>
              @endforelse
            </div>
          </div>

          {{-- Slider Navigation & Progress Bar --}}
          <div class="pt-4 sm:pt-6 flex items-center justify-between gap-6 sm:gap-8 select-none">
            <div class="flex-1 h-[2px] bg-black/20 relative rounded-full overflow-hidden">
              <div class="h-full bg-black rounded-full transition-all duration-300 origin-left" id="product-slider-progress" style="width: 0%;"></div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
              <button class="w-8 h-8 rounded-full border border-neutral-300 bg-white hover:bg-neutral-100 text-neutral-800 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition-all shadow-xs focus:outline-none cursor-pointer" type="button" id="product-slider-prev" aria-label="Sản phẩm trước" title="Trước">
                <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
                </svg>
              </button>
              <button class="w-8 h-8 rounded-full border border-neutral-300 bg-white hover:bg-neutral-100 text-neutral-800 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition-all shadow-xs focus:outline-none cursor-pointer" type="button" id="product-slider-next" aria-label="Sản phẩm tiếp theo" title="Tiếp theo">
                <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  {{-- Viewed Products Slider --}}
  <section class="pt-8 pb-16 md:pb-24 bg-[#F6F6F6] flex justify-center">
    <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-[72px]">
      <section class="viewed-products-section w-full pt-10 pb-8 md:pt-14 md:pb-12 select-none flex flex-col gap-6 sm:gap-8 items-center" id="viewed-products-slider">
        <h2 class="text-center text-lg sm:text-xl md:text-[22px] font-black uppercase text-black tracking-wider">
          SẢN PHẨM TIÊU BIỂU
        </h2>
        <div class="swiper viewed-product-swiper overflow-hidden w-full" data-swiper-id="viewed-products-slider">
          <div class="swiper-wrapper">
            @foreach($allProducts->take(8) as $featuredProd)
              <div class="swiper-slide h-auto">
                @include('themes.daiphuc68.components.product-card', ['product' => $featuredProd])
              </div>
            @endforeach
          </div>
        </div>
        <div class="pt-6 sm:pt-8 flex items-center justify-between gap-6 sm:gap-8">
          <div class="flex-1 h-[2px] bg-black/20 relative rounded-full overflow-hidden">
            <div class="viewed-products-slider-progress h-full bg-black rounded-full transition-all duration-300 origin-left" style="width: 33.33%;"></div>
          </div>
          <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <button class="viewed-products-slider-prev w-8 h-8 rounded-full border border-neutral-300 bg-white hover:bg-neutral-100 text-neutral-800 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition-all shadow-xs focus:outline-none cursor-pointer" type="button" aria-label="Slide trước" title="Trước">
              <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
              </svg>
            </button>
            <button class="viewed-products-slider-next w-8 h-8 rounded-full border border-neutral-300 bg-white hover:bg-neutral-100 text-neutral-800 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition-all shadow-xs focus:outline-none cursor-pointer" type="button" aria-label="Slide tiếp theo" title="Tiếp">
              <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>
      </section>
    </div>
  </section>

  @push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // 1. Featured Categories Swiper
      function initCatSwiper() {
        if (typeof Swiper === 'undefined') {
          setTimeout(initCatSwiper, 60);
          return;
        }
        const catEl = document.querySelector('.featured-categories-swiper');
        if (catEl && !catEl.swiper) {
          new Swiper(catEl, {
            slidesPerView: 1.25,
            spaceBetween: 16,
            speed: 500,
            grabCursor: true,
            watchSlidesProgress: true,
            navigation: {
              prevEl: '.featured-cat-prev',
              nextEl: '.featured-cat-next',
            },
            breakpoints: {
              640: { slidesPerView: 2.2, spaceBetween: 20 },
              768: { slidesPerView: 3, spaceBetween: 24 },
              1024: { slidesPerView: 3.5, spaceBetween: 24 },
              1280: { slidesPerView: 4, spaceBetween: 24 }
            }
          });
        }
      }
      initCatSwiper();

      // 1b. Product Catalog Swiper Slider (6 products per view)
      function initCatalogSwiper() {
        if (typeof Swiper === 'undefined') {
          setTimeout(initCatalogSwiper, 60);
          return;
        }
        const swiperEl = document.getElementById('product-catalog-swiper');
        const progressEl = document.getElementById('product-slider-progress');
        if (!swiperEl || swiperEl.swiper) return;

        function syncProgress(s) {
          if (!progressEl) return;
          const total = s.slides ? s.slides.length : 1;
          const current = s.activeIndex + 1;
          const pct = Math.min(100, Math.max(10, (current / total) * 100));
          progressEl.style.width = pct + '%';
        }

        new Swiper(swiperEl, {
          slidesPerView: 1,
          spaceBetween: 24,
          speed: 600,
          grabCursor: true,
          watchSlidesProgress: true,
          navigation: {
            prevEl: '#product-slider-prev',
            nextEl: '#product-slider-next',
          },
          on: {
            init: function () {
              syncProgress(this);
            },
            slideChange: function () {
              syncProgress(this);
            },
            resize: function () {
              syncProgress(this);
            }
          }
        });
      }
      initCatalogSwiper();

      // 2. Mobile Sidebar filter toggle (Sidebar is always visible on Desktop)
      const sidebarFilter = document.getElementById('product-sidebar-filter');
      const closeFilterBtn = document.getElementById('close-sidebar-filter-btn');
      const toggleMobileFilters = document.getElementById('toggle-mobile-product-filters');

      function toggleMobileSidebar(open) {
        if (!sidebarFilter || window.innerWidth >= 1024) return;
        const isOpen = typeof open === 'boolean' ? open : (sidebarFilter.classList.contains('hidden') || sidebarFilter.classList.contains('is-closed'));
        sidebarFilter.classList.remove('hidden');
        sidebarFilter.classList.toggle('is-open', isOpen);
        sidebarFilter.classList.toggle('is-closed', !isOpen);
        if (isOpen) {
          sidebarFilter.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }

      if (closeFilterBtn) closeFilterBtn.addEventListener('click', function(e) { e.preventDefault(); toggleMobileSidebar(false); });
      if (toggleMobileFilters) toggleMobileFilters.addEventListener('click', function(e) { e.preventDefault(); toggleMobileSidebar(); });

      // 3. Toolbar Search Box Toggle & Submit
      const searchBtn = document.getElementById('product-toolbar-search-btn');
      const searchBox = document.getElementById('product-toolbar-search-box');
      const searchInput = document.getElementById('product-search-input');
      const searchClear = document.getElementById('product-search-clear');

      if (searchBtn && searchBox && searchInput) {
        searchBtn.addEventListener('click', function(e) {
          e.preventDefault();
          const isHidden = searchBox.classList.contains('hidden');
          searchBox.classList.toggle('hidden');
          if (isHidden) {
            searchInput.focus();
          }
        });

        searchInput.addEventListener('keydown', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            const val = searchInput.value.trim();
            const url = new URL(window.location.href);
            if (val) {
              url.searchParams.set('search', val);
            } else {
              url.searchParams.delete('search');
            }
            url.searchParams.delete('page');
            window.location.href = url.toString();
          }
        });

        if (searchClear) {
          searchClear.addEventListener('click', function() {
            searchInput.value = '';
            const url = new URL(window.location.href);
            if (url.searchParams.has('search')) {
              url.searchParams.delete('search');
              url.searchParams.delete('page');
              window.location.href = url.toString();
            } else {
              searchBox.classList.add('hidden');
            }
          });
        }

        document.addEventListener('click', function(e) {
          if (searchBox && !searchBox.contains(e.target) && e.target !== searchBtn && !searchBtn.contains(e.target)) {
            if (!searchInput.value.trim()) {
              searchBox.classList.add('hidden');
            }
          }
        });
      }

      // 4. Sort Dropdown
      const sortTrigger = document.getElementById('product-sort-trigger');
      const sortMenu = document.getElementById('product-sort-menu');
      const sortChevron = document.getElementById('product-sort-chevron');
      const sortDropdown = document.getElementById('product-sort-dropdown');

      if (sortTrigger && sortMenu) {
        function toggleSort(open) {
          const isOpen = typeof open === 'boolean' ? open : sortMenu.classList.contains('hidden');
          sortMenu.classList.toggle('hidden', !isOpen);
          sortTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
          if (sortChevron) {
            sortChevron.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
          }
        }

        sortTrigger.addEventListener('click', function(e) {
          e.preventDefault();
          toggleSort();
        });

        document.addEventListener('click', function(e) {
          if (sortDropdown && !sortDropdown.contains(e.target)) {
            toggleSort(false);
          }
        });

        const sortItems = sortMenu.querySelectorAll('[data-sort-value]');
        sortItems.forEach(function(item) {
          item.addEventListener('click', function(e) {
            e.preventDefault();
            const sortVal = item.getAttribute('data-sort-value');
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sortVal);
            url.searchParams.delete('page');
            window.location.href = url.toString();
          });
        });
      }

      // 5. Sidebar Filter Selection (Category, Brand, Price)
      const filterInputs = document.querySelectorAll('.product-filter-cb');
      filterInputs.forEach(function(input) {
        input.addEventListener('change', function() {
          const url = new URL(window.location.href);
          if (input.name.startsWith('cat_') || input.name === 'cat_filter') {
            if (input.checked) {
              url.searchParams.set('category', input.value);
            } else {
              if (url.searchParams.get('category') === input.value) {
                url.searchParams.delete('category');
              }
            }
          } else if (input.name.startsWith('brand_')) {
            if (input.checked) {
              url.searchParams.set('brand', input.value);
            } else {
              if (url.searchParams.get('brand') === input.value) {
                url.searchParams.delete('brand');
              }
            }
          } else if (input.name.startsWith('price_')) {
            if (input.checked) {
              url.searchParams.set('price_range', input.value);
            } else {
              if (url.searchParams.get('price_range') === input.value) {
                url.searchParams.delete('price_range');
              }
            }
          }
          url.searchParams.delete('page');
          window.location.href = url.toString();
        });
      });

      const catSelect = document.getElementById('cat-filter-select');
      if (catSelect) {
        catSelect.addEventListener('change', function() {
          const url = new URL(window.location.href);
          if (catSelect.value) {
            url.searchParams.set('category', catSelect.value);
          } else {
            url.searchParams.delete('category');
          }
          url.searchParams.delete('page');
          window.location.href = url.toString();
        });
      }

      // 6. Reset Filters
      const resetBtn = document.getElementById('reset-all-product-filters');
      if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
          e.preventDefault();
          window.location.href = '{{ route("products.index") }}';
        });
      }
    });
  </script>
  @endpush
@endsection

