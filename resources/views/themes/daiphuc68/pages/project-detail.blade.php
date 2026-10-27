@extends('themes.daiphuc68.layouts.app')

@php
  $meta = is_array($project->meta_data) ? $project->meta_data : json_decode($project->meta_data ?? '[]', true);
  $heroImage = $project->featured_image ? asset($project->featured_image) : asset('storage/media/projects/project-detail-hero.jpg');
  $scale = !empty($meta['scale']) ? $meta['scale'] : ($project->scale ?: 'Tiêu chuẩn EVN');
  $location = !empty($meta['location']) ? $meta['location'] : ($project->location ?: 'TP. Hồ Chí Minh');
  $category = !empty($meta['category']) ? $meta['category'] : ($project->category_name ?: 'Dự Án Tiêu Biểu');
  $client = !empty($meta['client']) ? $meta['client'] : ($project->client ?: 'Đại Phúc 68 & Đối Tác');
  $year = !empty($meta['year']) ? $meta['year'] : (!empty($meta['completion_year']) ? $meta['completion_year'] : ($project->year ?: date('Y')));

  $gallery = $project->gallery;
  if ($gallery === null) {
      $gallery = [
          asset('storage/media/projects/project-detail-1.jpg'),
          asset('storage/media/projects/project-detail-hero.jpg'),
          asset('storage/media/projects/project-detail-2.jpg'),
          asset('storage/media/projects/project-detail-3.jpg'),
          asset('storage/media/projects/project-detail-4.jpg'),
      ];
  }

  $seoTitle = !empty(trim((string)$project->meta_title)) ? trim($project->meta_title) : ($project->title . ' - CÔNG TY TNHH ĐẠI PHÚC 68');
  $rawSeoDesc = !empty(trim((string)$project->meta_description)) ? trim($project->meta_description) : (!empty($project->excerpt) ? $project->excerpt : 'Chi tiết công trình dự án ' . $project->title . ' do Công ty TNHH Đại Phúc 68 thực hiện.');
  $seoDesc = Str::limit(strip_tags(html_entity_decode((string)$rawSeoDesc, ENT_QUOTES, 'UTF-8')), 200);
@endphp

@section('title', $seoTitle)
@section('meta_description', $seoDesc)

@section('content')
  {{-- Hero Banner --}}
  <section class="relative w-full h-[500px] md:h-[600px] overflow-hidden bg-brand-navy flex items-end justify-center pb-12 md:pb-16 text-white" aria-label="Hero Banner {{ $project->title }}">
    <div class="absolute inset-0 z-0">
      <img class="w-full h-full object-cover object-center filter brightness-95" src="{{ $heroImage }}" alt="{{ $project->title }}"/>
      <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/30 to-black/40"></div>
    </div>
    <div class="relative z-10 w-full max-w-[1110px] px-4 text-center">
      <h1 class="text-[26px] sm:text-[34px] md:text-[40px] lg:text-[44px] font-bold uppercase tracking-wider text-white drop-shadow-md">
        {{ $project->title }}
      </h1>
    </div>
  </section>

  {{-- Breadcrumb --}}
  @include('themes.daiphuc68.components.breadcrumb', [
    'items' => [
      ['label' => 'DỰ ÁN', 'url' => route('projects.index')],
      ['label' => mb_strtoupper($project->title), 'active' => true]
    ]
  ])

  {{-- Project Overview Header --}}
  <main class="w-full bg-white pt-10 md:pt-14 pb-14 flex justify-center">
    <div class="w-full max-w-[1296px] flex flex-col gap-8 px-4 sm:px-8">
      
      <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
        <div class="md:col-span-3 pt-2">
          <a class="inline-flex items-center gap-2 text-[14px] font-bold text-neutral-900 hover:text-brand-navy transition-colors" href="{{ route('projects.index') }}">
            <span class="text-base">&nwarr;</span>
            <span>Quay về Trang Dự Án</span>
          </a>
        </div>
        <div class="md:col-span-9 flex flex-col gap-2">
          <h2 class="text-[32px] sm:text-[40px] md:text-[46px] font-bold uppercase text-neutral-900 tracking-tight leading-tight">
            {{ $project->title }}
          </h2>
          <p class="text-[18px] sm:text-[22px] md:text-[24px] text-neutral-800 font-medium leading-snug">
            {{ $project->excerpt ?: 'Giải pháp kỹ thuật tối ưu. Kiến tạo giá trị bền vững.' }}
          </p>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-6 pb-4">
            <div class="flex flex-col gap-1">
              <div class="text-[12px] font-bold uppercase text-neutral-900 tracking-wider">QUY MÔ / DIỆN TÍCH</div>
              <div class="text-[13px] sm:text-[14px] text-neutral-700 uppercase">{{ $scale }}</div>
            </div>
            <div class="flex flex-col gap-1">
              <div class="text-[12px] font-bold uppercase text-neutral-900 tracking-wider">VỊ TRÍ</div>
              <div class="text-[13px] sm:text-[14px] text-neutral-700 uppercase">{{ $location }}</div>
            </div>
            <div class="flex flex-col gap-1">
              <div class="text-[12px] font-bold uppercase text-neutral-900 tracking-wider">LOẠI HÌNH DỰ ÁN</div>
              <div class="text-[13px] sm:text-[14px] text-neutral-700 uppercase">{{ $category }}</div>
            </div>
            <div class="flex flex-col gap-1">
              <div class="text-[12px] font-bold uppercase text-neutral-900 tracking-wider">CHỦ ĐẦU TƯ / ĐỐI TÁC</div>
              <div class="text-[13px] sm:text-[14px] text-neutral-700 uppercase">{{ $client }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="w-full h-[1px] bg-neutral-200"></div>

      {{-- Wide Feature Image --}}
      <figure class="w-full aspect-[1296/574] rounded-[12px] overflow-hidden bg-neutral-100 shadow-sm">
        <img class="w-full h-full object-cover object-center" src="{{ $heroImage }}" alt="{{ $project->title }} - Toàn cảnh">
      </figure>

      {{-- Detail Content Section --}}
      <div class="w-full max-w-[925px] self-center flex flex-col gap-4 pt-8 text-center sm:text-left">
        <!-- <h3 class="text-[24px] sm:text-[28px] md:text-[32px] font-bold uppercase tracking-wide text-neutral-900">
          NỘI DUNG CHI TIẾT
        </h3> -->

        {{-- Dynamic Table of Contents (TOC) --}}
        @if(!empty($tocData) && !empty($tocData['enabled']))
          @include('themes.daiphuc68.components.table-of-contents', ['tocData' => $tocData])
        @endif

        @if(!empty($project->content))
          <div class="prose max-w-none text-[14px] sm:text-[15px] md:text-[16px] text-neutral-800 leading-relaxed text-justify ck-content page-main-content">
            {!! function_exists('sanitize_editor_html') ? sanitize_editor_html($tocData['content'] ?? $project->content) : ($tocData['content'] ?? $project->content) !!}
          </div>
        @else
          <p class="text-[14px] sm:text-[15px] md:text-[16px] text-neutral-500 italic leading-relaxed text-justify">
            Nội dung chi tiết của dự án đang được cập nhật.
          </p>
        @endif
      </div>

    </div>
  </main>

  {{-- Photo Gallery Section --}}
  @if(!empty($gallery) && count($gallery) > 0)
  <section class="w-full bg-[#EEEEEE] py-14 md:py-20 flex justify-center" aria-label="Hình ảnh thực tế dự án">
    <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-18 flex flex-col gap-12">
      <div class="flex items-center gap-6">
        <h2 class="text-[26px] sm:text-[32px] md:text-[36px] font-bold uppercase text-neutral-900 tracking-wide shrink-0">
          HÌNH ẢNH THỰC TẾ
        </h2>
        <div class="flex-grow h-[1px] bg-neutral-400"></div>
      </div>
      <div class="flex flex-col gap-12 sm:gap-16" style="gap: 20px;">
        @foreach($gallery as $idx => $photo)
          <div class="flex {{ $idx % 2 === 0 ? 'justify-start' : 'justify-end' }}">
            <figure class="w-full max-w-[888px] aspect-[888/666] rounded-[12px] overflow-hidden bg-neutral-200 shadow-sm">
              <img class="w-full h-full object-cover object-center transition-transform duration-500 hover:scale-105" 
                   src="{{ $photo }}" 
                   alt="{{ $project->title }} - Hình ảnh thực tế {{ $idx + 1 }}">
            </figure>
          </div>
        @endforeach
      </div>
    </div>
  </section>
  @endif

  {{-- Related Projects --}}
  @if($relatedProjects->isNotEmpty())
    <section class="w-full bg-[#EEEEEE] py-14 md:py-16 border-t border-neutral-300 flex justify-center" aria-label="Dự án liên quan">
      <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-18 flex flex-col gap-10 items-center">
        <h2 class="text-[26px] sm:text-[32px] md:text-[36px] font-bold uppercase text-neutral-900 tracking-wide text-center">
          DỰ ÁN LIÊN QUAN
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 w-full max-w-[1296px]">
          @foreach($relatedProjects->take(3) as $relProj)
            <article class="project-card project-card-grid group" data-figma-variant="Grid-408x422" data-project-id="{{ $relProj->slug }}">
              <figure class="project-image-box">
                <img class="project-img" 
                     src="{{ $relProj->featured_image ? asset($relProj->featured_image) : asset('storage/media/projects/project-detail-hero.jpg') }}" 
                     alt="{{ $relProj->title }}" 
                     loading="lazy" 
                     onerror="this.onerror=null; this.src='{{ asset('storage/media/projects/project-detail-hero.jpg') }}'; this.classList.add('opacity-90');"/>
                <span class="project-badge">{{ $relProj->category_name ?: 'DỰ ÁN TIÊU BIỂU' }}</span>
              </figure>
              <div class="project-body">
                <div class="project-title-wrap">
                  <h3 class="project-title" title="{{ $relProj->title }}">
                    <a class="focus:outline-none focus:underline" href="{{ route('projects.show', $relProj->slug) }}">{{ $relProj->title }}</a>
                  </h3>
                </div>
                <p class="project-excerpt">{{ $relProj->excerpt ?: 'Hạng mục thi công hệ thống điện và giải pháp xây lắp trọn gói.' }}</p>
                <div class="project-action-wrap">
                  <a class="project-read-more" href="{{ route('projects.show', $relProj->slug) }}" title="Xem chi tiết dự án">
                    <span>Xem Thêm</span>
                    <i class="project-arrow-icon fa-solid fa-arrow-right"></i>
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
