@extends('themes.daiphuc68.layouts.app')

@php
  $heroImage = $page->featured_image ? asset($page->featured_image) : asset('storage/media/banners/hero-banner-main.jpg');
  $category = $page->template ? mb_strtoupper($page->template) : 'THÔNG TIN & CHÍNH SÁCH';
  $publishedDate = $page->published_at ? \Carbon\Carbon::parse($page->published_at)->format('d/m/Y') : ($page->created_at ? $page->created_at->format('d/m/Y') : date('d/m/Y'));
  $readingTime = $page->reading_time ?: '5 phút đọc';

  $seoTitle = !empty(trim((string)$page->meta_title)) ? trim($page->meta_title) : ($page->title . ' - CÔNG TY TNHH ĐẠI PHÚC 68');
  $rawSeoDesc = !empty(trim((string)$page->meta_description)) ? trim($page->meta_description) : (!empty($page->excerpt) ? $page->excerpt : 'Chi tiết bài viết: ' . $page->title . '. Cập nhật thông tin chuyên ngành từ Đại Phúc 68.');
  $seoDesc = Str::limit(strip_tags(html_entity_decode((string)$rawSeoDesc, ENT_QUOTES, 'UTF-8')), 200);
@endphp

@section('title', $seoTitle)
@section('meta_description', $seoDesc)

@section('content')
  {{-- Hero Banner --}}
  <section class="relative w-full h-[500px] md:h-[600px] overflow-hidden bg-brand-navy flex items-end justify-center pb-12 md:pb-16 text-white" aria-label="Hero Banner {{ $page->title }}">
    <div class="absolute inset-0 z-0">
      <img class="w-full h-full object-cover object-center filter brightness-95" src="{{ $heroImage }}" alt="{{ $page->title }}" onerror="this.onerror=null; this.src='{{ asset('storage/media/banners/hero-banner-main.jpg') }}';"/>
      <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-black/40"></div>
    </div>
    <div class="relative z-10 w-full max-w-[1110px] px-4 text-center">
      <h1 class="text-[26px] sm:text-[34px] md:text-[40px] lg:text-[44px] font-bold uppercase tracking-wider text-white drop-shadow-md">
        {{ $page->title }}
      </h1>
    </div>
  </section>

  {{-- Breadcrumb --}}
  @include('themes.daiphuc68.components.breadcrumb', [
    'items' => [
      ['label' => 'TRANG CHỦ', 'url' => route('home')],
      ['label' => mb_strtoupper($page->title), 'active' => true]
    ]
  ])

  @push('styles')
    @include('themes.daiphuc68.components.ckeditor-content-styles')
  @endpush

  {{-- Main Article Content --}}
  <main class="w-full bg-white py-12 md:py-16 flow-root clear-both flex justify-center">
    <article class="w-full max-w-[925px] px-4 sm:px-6 flow-root clear-both flex flex-col gap-8">

      {{-- Article Header (Meta & Title) --}}
      <div class="flex flex-col gap-4 text-center">
        <div class="inline-flex items-center gap-2 text-xs font-semibold uppercase text-brand-navy bg-neutral-100 px-3 py-1 rounded-full justify-self-center">
          <span>{{ $category }}</span>
          <span>&bull;</span>
          <span>{{ $publishedDate }}</span>
          <span>&bull;</span>
          <span>{{ $readingTime }}</span>
        </div>
        <h2 class="text-[28px] sm:text-[36px] md:text-[42px] font-bold uppercase text-neutral-900 tracking-wide text-center leading-tight">
          {{ $page->title }}
        </h2>
      </div>

      @if($page->excerpt)
        <div>
          <p class="text-[14px] sm:text-[15px] md:text-[16px] text-neutral-800 italic font-normal leading-relaxed text-justify bg-neutral-50 p-4 sm:p-6 rounded-xl border border-neutral-200">
            {{ $page->excerpt }}
          </p>
        </div>
      @endif

      {{-- Dynamic Table of Contents (TOC) --}}
      @if(!empty($tocData) && !empty($tocData['enabled']))
        @include('themes.daiphuc68.components.table-of-contents', ['tocData' => $tocData])
      @endif

      {{-- Article Sections / Content --}}
      <div class="prose max-w-none text-[14px] sm:text-[15px] md:text-[16px] text-neutral-800 leading-relaxed text-justify page-main-content ck-content flow-root clear-both">
        {!! function_exists('sanitize_editor_html') ? sanitize_editor_html($tocData['content'] ?? $page->content) : ($tocData['content'] ?? $page->content) !!}
        <div class="clear-both" style="clear: both; display: block; height: 0;"></div>
      </div>

    </article>
  </main>

  {{-- Related Articles / Items Section --}}
  @php
    $displayItems = (isset($otherPages) && $otherPages->isNotEmpty()) ? $otherPages->take(3) : ($relatedNews ?? collect());
    $sectionTitle = (isset($otherPages) && $otherPages->isNotEmpty()) ? 'THÔNG TIN & CHÍNH SÁCH KHÁC' : 'TIN TỨC LIÊN QUAN';
  @endphp

  @if($displayItems->isNotEmpty())
    <section class="w-full bg-[#EEEEEE] py-14 md:py-16 clear-both relative z-10 flex justify-center" id="tin-tuc-lien-quan" aria-label="{{ $sectionTitle }}">
      <div class="w-full max-w-[1440px] px-6 sm:px-12 lg:px-18 flex flex-col gap-10 items-center">
        <h2 class="text-[26px] sm:text-[32px] md:text-[36px] font-bold uppercase text-neutral-900 tracking-wide text-center">
          {{ $sectionTitle }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 w-full max-w-[1296px]">
          @foreach($displayItems as $relItem)
            @php
              $relUrl = $relItem->post_type === 'page' ? url('/' . $relItem->slug) : route('news.show', $relItem->slug);
              $relImg = $relItem->featured_image ? asset($relItem->featured_image) : asset('storage/media/banners/hero-banner-main.jpg');
              $relTag = $relItem->post_type === 'page' ? 'CHÍNH SÁCH' : ($relItem->category_name ?: 'TIN TỨC');
            @endphp
            <article class="post-card post-card-related group" data-figma-variant="Variant4" data-post-id="{{ $relItem->slug }}">
              <figure class="post-image-box">
                <img class="post-img" 
                     src="{{ $relImg }}" 
                     alt="{{ $relItem->title }}" 
                     loading="lazy" 
                     onerror="this.onerror=null; this.src='{{ asset('storage/media/banners/hero-banner-main.jpg') }}'; this.classList.add('opacity-90');"/>
              </figure>
              <div class="post-body">
                <div class="post-meta-row">
                  <span class="post-category-tag">{{ $relTag }}</span>
                </div>
                <div class="post-title-wrap">
                  <h3 class="post-title" title="{{ $relItem->title }}">
                    <a class="focus:outline-none focus:underline" href="{{ $relUrl }}">{{ $relItem->title }}</a>
                  </h3>
                </div>
                <p class="post-excerpt">{{ $relItem->excerpt ?: 'Cập nhật thông tin chi tiết và quy định chính thức từ Đại Phúc 68.' }}</p>
                <div class="post-action-wrap">
                  <a class="post-read-more" href="{{ $relUrl }}" title="Xem chi tiết">
                    <span>Xem Thêm</span>
                    <i class="post-arrow-icon fa-solid fa-arrow-right"></i>
                  </a>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </section>
  @endif
@endsection
