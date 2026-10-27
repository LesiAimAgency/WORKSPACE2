@php
  $titleLine1 = $title_line_1 ?? ($settings['title_line_1'] ?? null);
  $titleLine2 = $title_line_2 ?? ($settings['title_line_2'] ?? null);

  if (empty($titleLine1) && empty($titleLine2)) {
    $rawTitle = $title ?? ($settings['title'] ?? "NHỮNG DỰ ÁN\nMÀ CHÚNG TÔI TỰ HÀO");
    if (str_contains($rawTitle, "\n")) {
      $parts = explode("\n", str_replace("\r", "", $rawTitle), 2);
      $titleLine1 = trim($parts[0]);
      $titleLine2 = trim($parts[1] ?? '');
    } elseif (str_contains($rawTitle, "<br>")) {
      $parts = explode("<br>", $rawTitle, 2);
      $titleLine1 = trim(strip_tags($parts[0]));
      $titleLine2 = trim(strip_tags($parts[1] ?? ''));
    } elseif (str_contains(mb_strtoupper($rawTitle), 'MÀ CHÚNG TÔI')) {
      $pos = mb_stripos($rawTitle, 'MÀ CHÚNG TÔI');
      $titleLine1 = trim(mb_substr($rawTitle, 0, $pos));
      $titleLine2 = trim(mb_substr($rawTitle, $pos));
    } else {
      $titleLine1 = $rawTitle;
      $titleLine2 = '';
    }
  }

  $desc = $description ?? ($settings['description'] ?? 'Uy tín thương hiệu được gầy dựng theo năm tháng, qua từng dự án mà chúng tôi đã đặt vào cả tâm huyết.');
  $projects = $projects ?? collect([]);
  
  // Ensure we have at least 5 items for the 5-card staggered layout
  $displayProjects = $projects->values();
  if ($displayProjects->isEmpty()) {
      $baseProjects = [
          ['title' => 'Biệt Thự Đơn Lập Sinh Thái', 'img' => asset('storage/media/projects/project-detail-hero.jpg'), 'url' => '/projects'],
          ['title' => 'Tổ Hợp Năng Lượng & Cơ Điện', 'img' => asset('storage/media/projects/project-item-3.jpg'), 'url' => '/projects'],
          ['title' => 'Công Trình Công Cộng Hiện Đại', 'img' => asset('storage/media/projects/project-detail-2.jpg'), 'url' => '/projects'],
          ['title' => 'Khu Dân Cư Cao Cấp', 'img' => asset('storage/media/projects/project-detail-5.jpg'), 'url' => '/projects'],
          ['title' => 'Khu Biệt Thự Nghỉ Dưỡng', 'img' => asset('storage/media/projects/project-villa-park.jpg'), 'url' => '/projects'],
      ];
  } else {
      $baseProjects = $displayProjects->map(function($p) {
          return [
              'title' => $p->title ?? 'Dự án Đại Phúc 68',
              'img' => $p->featured_image ? asset($p->featured_image) : asset('storage/media/projects/project-detail-hero.jpg'),
              'url' => route('projects.show', $p->slug ?? 'du-an'),
          ];
      })->all();
  }

  // Lặp lại danh sách để đảm bảo vòng lặp vô tận (loop: true) không bị khuyết/trống khi kéo
  $carouselImages = $baseProjects;
  while (count($carouselImages) < 18) {
      $carouselImages = array_merge($carouselImages, $baseProjects);
  }
@endphp

<section class="proj-ellipse-section w-full select-none" id="projects-showcase-section" aria-label="Những Dự Án Tự Hào">
  {{-- SVG ClipPath Definition (Exact Figma Double-Ellipse Geometry) --}}
  <svg class="sr-only absolute pointer-events-none" width="0" height="0" aria-hidden="true" focusable="false">
    <defs>
      <clipPath id="projects-ellipse-mask" clipPathUnits="objectBoundingBox">
        <path d="M 0,0.13567 A 0.67014,0.20377 0 0,0 1,0.13567 L 1,0.86433 A 0.67014,0.20377 0 0,0 0,0.86433 Z" />
      </clipPath>
    </defs>
  </svg>

  <div class="proj-ellipse-inner-container relative z-10 flex flex-col justify-center items-center w-full max-w-none box-border h-full px-0">
    {{-- Top Title: Chia làm 2 dòng với class riêng biệt --}}
    <div class="proj-ellipse-title-wrapper w-full flex flex-col items-center justify-center z-30 shrink-0 reveal-item reveal-delay-1">
      <h2 class="proj-ellipse-title">
        @if(!empty($titleLine1))
          <span class="proj-ellipse-title-line proj-ellipse-title-line--1 block">{{ $titleLine1 }}</span>
        @endif
        @if(!empty($titleLine2))
          <span class="proj-ellipse-title-line proj-ellipse-title-line--2 block">{{ $titleLine2 }}</span>
        @endif
      </h2>
    </div>

    {{-- Middle Image Gallery Viewport with Ellipse Mask --}}
    <div class="proj-ellipse-viewport-wrapper w-full flex items-center justify-center relative reveal-item reveal-delay-2">
      {{-- Navigation Prev Button --}}
      <button class="proj-ellipse-nav-btn proj-ellipse-nav-btn--prev" id="projEllipsePrev" type="button" aria-label="Dự án trước">
        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
      </button>

      {{-- The Clipped Container with Swiper --}}
      <div class="proj-ellipse-viewport swiper" id="projEllipseViewport">
        <div class="proj-ellipse-track swiper-wrapper" id="projEllipseTrack">
          @foreach($carouselImages as $idx => $item)
            <div class="proj-ellipse-card swiper-slide group cursor-pointer" data-card-idx="{{ $idx % count($baseProjects) }}" title="{{ $item['title'] }}">
              <a class="block w-full h-full relative overflow-hidden" href="{{ $item['url'] }}" aria-label="{{ $item['title'] }}">
                <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="{{ $item['img'] }}" alt="{{ $item['title'] }}" loading="{{ $idx < 5 ? 'eager' : 'lazy' }}">
              </a>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Navigation Next Button --}}
      <button class="proj-ellipse-nav-btn proj-ellipse-nav-btn--next" id="projEllipseNext" type="button" aria-label="Dự án tiếp theo">
        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
      </button>
    </div>

    {{-- Bottom Subtitle --}}
    <div class="proj-ellipse-subtitle-wrapper w-full flex justify-center px-4 z-30 shrink-0 reveal-item reveal-delay-3">
      <p class="proj-ellipse-subtitle">
        {!! nl2br(e($desc)) !!}
      </p>
    </div>
  </div>
</section>

@push('scripts')
<script>
  (function() {
    function initEllipseShowcase() {
      if (typeof Swiper === 'undefined') {
        setTimeout(initEllipseShowcase, 60);
        return;
      }
      const viewport = document.getElementById('projEllipseViewport');
      if (!viewport || viewport.swiper) return;

      new Swiper(viewport, {
        slidesPerView: 3.4,
        centeredSlides: true,
        spaceBetween: 16,
        loop: true,
        loopAdditionalSlides: 6,
        speed: 650,
        grabCursor: true,
        watchSlidesProgress: true,
        navigation: {
          prevEl: '#projEllipsePrev',
          nextEl: '#projEllipseNext',
        },
        autoplay: {
          delay: 2800,
          disableOnInteraction: false,
          pauseOnMouseEnter: true,
        },
        keyboard: {
          enabled: true,
        },
        breakpoints: {
          320: {
            slidesPerView: 1.25,
            spaceBetween: 10,
          },
          640: {
            slidesPerView: 2.2,
            spaceBetween: 12,
          },
          768: {
            slidesPerView: 2.6,
            spaceBetween: 14,
          },
          1024: {
            slidesPerView: 3.4,
            spaceBetween: 16,
          },
          1440: {
            slidesPerView: 3.4,
            spaceBetween: 16,
          },
        },
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initEllipseShowcase);
    } else {
      initEllipseShowcase();
    }
  })();
</script>
@endpush
