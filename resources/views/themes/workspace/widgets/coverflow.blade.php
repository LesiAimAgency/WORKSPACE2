@php
  use App\Http\Controllers\CoverflowController;
  use Illuminate\Support\Str;

  $settings = CoverflowController::getCoverflowSettings($settings ?? null);
  $services = $services ?? CoverflowController::getServicesData($settings);
  $viewMoreText = $settings['view_more_text'] ?? 'View more';
  $activeSlug = request()->query('service') ?: ($services[0]['slug'] ?? '');
  $activeServiceIndex = $activeServiceIndex ?? 0;

  foreach ($services as $idx => $s) {
      if ($s['slug'] === $activeSlug) {
          $activeServiceIndex = $idx;
          break;
      }
  }

  $activeService = $activeService ?? ($services[$activeServiceIndex] ?? $services[0]);

  $formatImg = function (?string $path) {
      if (empty($path)) return '';
      if (Str::startsWith($path, ['http://', 'https://', '/'])) {
          return $path;
      }
      return asset($path);
  };

  if (!isset($servicesFormatted)) {
      $servicesFormatted = array_map(function ($s) use ($formatImg) {
          return [
              'number' => $s['number'] ?? '01',
              'title' => $s['title'] ?? '',
              'chip_line_1' => $s['chip_line_1'] ?? '',
              'chip_line_2' => $s['chip_line_2'] ?? '',
              'slug' => $s['slug'] ?? '',
              'desc' => $s['desc'] ?? '',
              'image' => $formatImg($s['image'] ?? 'themes/workspace/coverflow/frame1.png'),
              'detail_image' => $formatImg($s['detail_image'] ?? ($s['image'] ?? 'themes/workspace/coverflow/frame1.png')),
              'intro_items' => $s['intro_items'] ?? [],
              'projects' => $s['projects'] ?? [],
              'serviceUrl' => '#coverflowService',
              'projectUrl' => '#coverflowProject',
          ];
      }, $services);
  }

  $totalServices = count($servicesFormatted);

  // Format title thành 2 dòng chuẩn cho tất cả button chips theo Service (1).svg
  $formatChipTitle = function (string $title): array {
      $customMap = [
          // Tiêu đề đầy đủ từ Database / CoverflowController
          'Interior Advisory and Project Setup' => ['Interior Advisory', 'and Project Setup'],
          'Interior Design and FF&E Consultancy' => ['Interior Design and', 'FF&E Consultancy'],
          'Fit-Out and Finishing Works' => ['Fit-Out and', 'Finishing Works'],
          'Joinery Manufacturer and FF&E Installation' => ['Joinery Manufacturer and', 'FF&E Installation'],
          'Material Library and Competency Management' => ['Material Library and', 'Competency Management'],

          // Tiêu đề rút gọn dự phòng
          'Design and FF&E Consultancy' => ['Design', 'and FF&E Consultancy'],
          'Finishing Works' => ['Finishing', 'Works'],
          'Procurement and FF&E Installation' => ['Procurement', 'and FF&E Installation'],
          'Competency Management' => ['Competency', 'Management'],
      ];
      if (isset($customMap[$title])) {
          return $customMap[$title];
      }
      $words = explode(' ', trim($title));
      if (count($words) <= 1) {
          return [$title, ''];
      }
      $mid = (int) ceil(count($words) / 2);
      return [
          implode(' ', array_slice($words, 0, $mid)),
          implode(' ', array_slice($words, $mid))
      ];
  };
@endphp

<style>
    /* ==========================================================================
       1. OVERRIDE PANEL / SURFACE CHO SLIDER FULL-WIDTH 3 SLIDES THEO Service (1).svg
       Chỉ tác động trong phạm vi .panel-coverflow, không đụng vào style.css gốc.
       ========================================================================== */
    .fullpage > .panel.panel-coverflow {
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-top: clamp(10px, 2vh, 25px) !important;
        padding-bottom: clamp(10px, 2vh, 25px) !important;
        overflow: hidden !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-sizing: border-box !important;
    }

    .fullpage .panel-coverflow .coverflow-surface {
        overflow: visible !important;
        width: 100% !important;
        will-change: transform, opacity;
    }

    .fullpage > .panel.panel-coverflow.is-sliding-out-left {
        z-index: 5 !important;
        background: transparent !important;
        pointer-events: none !important;
    }

    .fullpage > .panel.panel-coverflow.is-sliding-out-left::before {
        display: none !important;
    }

    .fullpage .panel-coverflow .coverflow-layout {
        width: 1440px !important;
        max-width: 100vw;
        margin: 0 auto;
        position: relative;
    }

    /* ==========================================================================
       2. CHIPS NAVIGATION: 2 hàng, căn giữa, font nhỏ hơn, active indicator bar
       ========================================================================== */
    .panel-coverflow .coverflow-chips {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        width: 100%;
        max-width: 1100px;
        margin: 0 auto 16px;
        justify-content: center;
        align-items: center;
        text-align: center;
        position: relative;
        z-index: 20;
        font-size:1rem;
    }
    .panel-coverflow .coverflow-chip {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: transparent;
        border: none;
        padding: 4px 6px;
        font-family: var(--font-main, inherit);
        font-size: var(--p_S, 12px); /* Root Token --p_S: 12px / --p_S-rem: 0.75rem */
        line-height: 1.35;
        color: #32323280;
        font-weight: 500;
        cursor: pointer;
        transition: color 0.22s ease, transform 0.2s ease;
        min-height: 50px; /* Cố định chiều cao 2 dòng đều nhau */
        font-size:1rem;
    }
    .panel-coverflow .coverflow-chip .chip-line {
        display: block;
        white-space: nowrap;
    }
    .panel-coverflow .coverflow-chip:hover {
        color: #18181b;
    }
    .panel-coverflow .coverflow-chip.is-active {
        color: #1B1B1B !important;
       
    }
    /* Thanh gạch đỏ active indicator theo Figma Service (1).svg */
    /* .panel-coverflow .coverflow-chip.is-active::after {
        content: '';
        display: block;
        width: 28px;
        height: 2px;
        background: #930C10;
        margin: 5px auto 0;
        border-radius: 2px;
    } */

    /* ==========================================================================
       3. REAL CAROUSEL SLIDER STAGE: perPage = 3 slides (Theo Service (1).svg)
       Chiều cao chuẩn 551px theo Figma Service (1).svg
       ========================================================================== */
    .panel-coverflow .coverflow-stage {
        overflow: visible !important;
        position: relative;
        width: 100%;
        height: 551px !important; /* 551px chuẩn Figma Service (1).svg */
        min-height: 551px !important;
        max-height: 551px !important;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 45px;
    }

    .panel-coverflow .coverflow-track {
        position: relative;
        width: 100%;
        height: 100%;
        z-index: 5 !important;
    }

    /* Từng Slide trong track */
    .panel-coverflow .coverflow-slide {
        position: absolute;
        top: 50%;
        left: 50%;
        width: calc(100% - 180px); /* 1260px trên 1440px chuẩn Service (1).svg */
        height: 100%;
        border-radius: 12px;
        overflow: hidden;
        background-color: #e5e5ea;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.08);
        transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1), 
                    opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1),
                    box-shadow 0.55s ease;
        user-select: none;
        will-change: transform, opacity;
        pointer-events: none;
        opacity: 0;
        z-index: 1;
    }

    .panel-coverflow .coverflow-slide.no-transition {
        transition: none !important;
    }

    .panel-coverflow .coverflow-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border-radius: 12px;
    }

    /* Lớp phủ tối 20% (#1B1B1B fill-opacity="0.2") cho slide preview 2 bên */
    .panel-coverflow .coverflow-slide-overlay {
        position: absolute;
        inset: 0;
        background: rgba(27, 27, 27, 0.2);
        opacity: 0;
        transition: opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1), background 0.3s ease;
        pointer-events: none;
        border-radius: 12px;
    }

    /* Trạng thái 1: Slide chính ở giữa (Active Service) */
    .panel-coverflow .coverflow-slide.is-active {
        transform: translate(-50%, -50%) scale(1);
        z-index: 10 !important;
        opacity: 1;
        pointer-events: auto;
        cursor: default;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
    }
    .panel-coverflow .coverflow-slide.is-active .coverflow-slide-overlay {
        opacity: 0;
    }

    /* Trạng thái 2: Slide preview bên trái (Prev) */
    .panel-coverflow .coverflow-slide.is-prev {
        /* Dịch tâm sang trái đúng -1250px theo tọa độ chuẩn Figma Service (1).svg */
        transform: translate(calc(-50% - 1250px), calc(-50% + 18px)) scale(0.93);
        z-index: 5 !important;
        opacity: 1;
        pointer-events: auto;
        cursor: pointer;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }
    .panel-coverflow .coverflow-slide.is-prev .coverflow-slide-overlay {
        opacity: 1;
    }
    .panel-coverflow .coverflow-slide.is-prev:hover .coverflow-slide-overlay {
        background: rgba(27, 27, 27, 0.05);
    }

    /* Trạng thái 3: Slide preview bên phải (Next) */
    .panel-coverflow .coverflow-slide.is-next {
        /* Dịch tâm sang phải đúng +1250px theo tọa độ chuẩn Figma Service (1).svg */
        transform: translate(calc(-50% + 1250px), calc(-50% + 18px)) scale(0.93);
        z-index: 5 !important;
        opacity: 1;
        pointer-events: auto;
        cursor: pointer;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }
    .panel-coverflow .coverflow-slide.is-next .coverflow-slide-overlay {
        opacity: 1;
    }
    .panel-coverflow .coverflow-slide.is-next:hover .coverflow-slide-overlay {
        background: rgba(27, 27, 27, 0.05);
    }

    /* Trạng thái 4: Slide ẩn ngoài tầm nhìn bên trái */
    .panel-coverflow .coverflow-slide.is-hidden-left {
        transform: translate(calc(-50% - 2400px), calc(-50% + 18px)) scale(0.85);
        z-index: 1 !important;
        opacity: 0;
        pointer-events: none;
    }

    /* Trạng thái 5: Slide ẩn ngoài tầm nhìn bên phải */
    .panel-coverflow .coverflow-slide.is-hidden-right {
        transform: translate(calc(-50% + 2400px), calc(-50% + 18px)) scale(0.85);
        z-index: 1 !important;
        opacity: 0;
        pointer-events: none;
    }

    /* Mũi tên controls (32x32px nằm ngay trên đường viền mép slide giữa: x=90 và x=1350) */
    .panel-coverflow .coverflow-controls {
        position: absolute;
        top: 50%;
        left: calc((100% - 1260px) / 2 - 16px);
        right: calc((100% - 1260px) / 2 - 16px);
        width: auto;
        transform: translateY(-50%);
        display: flex;
        justify-content: space-between;
        align-items: center;
        pointer-events: none;
        z-index: 25 !important;
    }
    .panel-coverflow .coverflow-arrow {
        width: 32px;
        height: 32px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(0, 0, 0, 0.08);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #1B1B1B;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        pointer-events: auto;
        padding: 0;
    }
    .panel-coverflow .coverflow-arrow:hover {
        background: #111111;
        color: #ffffff;
        transform: scale(1.08);
    }

    /* ==========================================================================
       4. FIT HEIGHT CHO BOTTOM FOOTER & BADGE (Theo chuẩn Figma Service (1).svg)
       Badge (319x211px) đè lên ảnh 122px (58%), nhô xuống dưới 89px (42%), x=72px
       ========================================================================== */
    .panel-coverflow .coverflow {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        transform: translateY(42.18%); /* 89px / 211px = 42.18% chuẩn Service (1).svg */
        padding: 0 max(16px, calc((100% - 1260px) / 2 - 18px)); /* x=72px cho badge bên trái */
        box-sizing: border-box;
        z-index: 30 !important; /* Luôn nổi trên slide image (z-index 10) */
        pointer-events: none;
    }

    .panel-coverflow .coverflow-footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        height: 211px; /* Chiều cao cố định chuẩn 211px theo Figma Service (1).svg */
        box-sizing: border-box;
        gap: 32px;
        pointer-events: auto;
    }

    .panel-coverflow .coverflow-badge {
        width: 319px; /* Chuẩn 319px Figma Service (1).svg */
        height: 211px; /* Chuẩn 211px Figma Service (1).svg */
        min-height: 211px;
        max-height: 211px;
        padding: 24px 28px;
        border-radius: 12px; /* rx=12 theo Figma Service (1).svg */
        background: #930C10 !important; /* Màu đỏ chuẩn #930C10 theo Figma Service (1).svg */
        color: #ffffff !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between; /* Phân bố đều số '01' ở trên và tiêu đề ở dưới */
        box-sizing: border-box;
        flex-shrink: 0;
        position: relative !important;
        z-index: 35 !important; /* Đảm bảo không bao giờ bị ảnh che khuất */
        box-shadow: 0 14px 35px rgba(147, 12, 16, 0.28);
    }

    .panel-coverflow .badge-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0;
        line-height: 1;
    }

    .panel-coverflow .badge-num {
        font-family: var(--font-main, inherit);
        font-size: var(--h3, 50.5px); /* Root Token --h3: 50.5px / --h3-rem: 3.15625rem */
        font-weight: 700;
        line-height: 1;
        letter-spacing: -0.02em;
        color: #ffffff !important;
        display: block;
    }

    .panel-coverflow .badge-title {
        font-family: var(--font-main, inherit);
        font-size: var(--h6, 21.3px); /* Root Token --h6: 21.3px / --h6-rem: 1.33125rem */
        font-weight: 700;
        line-height: 1.25;
        color: #ffffff !important;
        margin: 0;
        width: 100%;
        max-width: 100%;
        word-break: break-word;
        text-transform: none !important; /* Chuẩn Figma Service (1).svg: 'and Project Setup' */
    }

    .panel-coverflow .coverflow-desc {
        flex: 1;
        max-width: 530px;
        font-family: var(--font-main, inherit);
        font-size: var(--p_S, 12px); /* Root Token --p_S: 12px / --p_S-rem: 0.75rem */
        line-height: 1.55;
        color: #333333;
        margin: 0 0 6px 0;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 1rem;
    }

    .panel-coverflow .view-more-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #1B1B1B ;
        color: #ffffff ;
        padding: 8px 24px;
        border-radius: 999px;
        font-family: var(--font-main, inherit);
        font-size: var(--p);
        font-weight: 600;
        line-height: 1;
        text-decoration: none ;
        transition: background-color 0.2s ease, transform 0.2s ease;
        white-space: nowrap;
        margin-bottom: 6px;
        flex-shrink: 0;
    }
    .panel-coverflow .view-more-btn:hover {
        background: #333333 ;
        color: #ffffff ;
        transform: translateY(-1px);
    }

    @media (max-width: 900px) {
        .panel-coverflow .coverflow-slide.is-prev,
        .panel-coverflow .coverflow-slide.is-next {
            display: none;
        }
        .panel-coverflow .coverflow-slide.is-active {
            width: 100%;
        }
        .panel-coverflow .coverflow-stage {
            height: 380px;
            margin-bottom: 30px;
        }
        .panel-coverflow .coverflow {
            position: relative;
            transform: none;
            padding: 0 16px;
            margin-top: 16px;
        }
        .panel-coverflow .coverflow-footer {
            height: auto;
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
        .panel-coverflow .coverflow-badge {
            width: 100%;
            height: auto;
            min-height: 0;
            padding: 18px 20px;
        }
        .panel-coverflow .coverflow-controls {
            left: 10px;
            right: 10px;
        }
        .panel-coverflow .coverflow-chips {
            gap: 8px;
        }
        .panel-coverflow .coverflow-chip {
            font-size: var(--p_S, 12px); /* Root Token --p_S: 12px */
        }
    }
</style>

<section class="panel panel-coverflow" id="coverflow" data-ui="light">
  <div class="surface coverflow-surface">
    <div class="coverflow-layout">

      <!-- 1. Top Category Tabs (Chips Selector - 2 hàng, căn giữa, font nhỏ hơn) -->
      <nav class="coverflow-chips" id="coverflowChips">
        @foreach($servicesFormatted as $index => $srv)
          @php
            $lines = (!empty($srv['chip_line_1']) || !empty($srv['chip_line_2']))
                ? [$srv['chip_line_1'] ?? '', $srv['chip_line_2'] ?? '']
                : $formatChipTitle($srv['title'] ?? '');
          @endphp
          <button type="button" 
                  class="coverflow-chip {{ $index === $activeServiceIndex ? 'is-active' : '' }}" 
                  data-index="{{ $index }}"
                  title="{{ $srv['title'] }}">
            <span class="chip-line">{{ $lines[0] }}</span>
            <span class="chip-line">{{ $lines[1] }}</span>
          </button>
        @endforeach
      </nav>

      <!-- 2. Real Carousel Slider Stage with perPage = 3 (Prev + Active + Next) -->
      <div class="coverflow-stage">
        <!-- Track chứa toàn bộ các slides vật lý -->
        <div class="coverflow-track" id="coverflowTrack">
          @foreach($servicesFormatted as $index => $srv)
            @php
              $slideInitialClass = 'is-hidden-right';
              $prevIndex = ($activeServiceIndex - 1 + $totalServices) % $totalServices;
              $nextIndex = ($activeServiceIndex + 1) % $totalServices;

              if ($index === $activeServiceIndex) {
                  $slideInitialClass = 'is-active';
              } elseif ($index === $prevIndex) {
                  $slideInitialClass = 'is-prev';
              } elseif ($index === $nextIndex) {
                  $slideInitialClass = 'is-next';
              } else {
                  $diff = ($index - $activeServiceIndex + $totalServices) % $totalServices;
                  if ($diff > $totalServices / 2) $diff -= $totalServices;
                  $slideInitialClass = $diff < 0 ? 'is-hidden-left' : 'is-hidden-right';
              }
            @endphp
            <div class="coverflow-slide {{ $slideInitialClass }}" 
                 data-index="{{ $index }}" 
                 role="button" 
                 tabindex="0"
                 aria-label="{{ $srv['title'] }}"
                 title="{{ $srv['title'] }}">
              <img src="{{ $srv['image'] }}" alt="{{ $srv['title'] }}"/>
              <div class="coverflow-slide-overlay"></div>
            </div>
          @endforeach
        </div>

        <!-- Navigation Controls: Mũi tên nằm trên đường viền mép slide giữa -->
        <div class="coverflow-controls">
          <button class="coverflow-arrow coverflow-prev" id="coverflowPrevBtn" type="button" aria-label="Previous service">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>
          <button class="coverflow-arrow coverflow-next" id="coverflowNextBtn" type="button" aria-label="Next service">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>

        <!-- 3. Bottom Description & Navigation Arrows -->
        <div class="coverflow">
          <div class="coverflow-footer">
        
            <div class="coverflow-badge">
              <div class="badge-header">
                <span class="badge-num" id="coverflowBadgeNum">{{ $servicesFormatted[$activeServiceIndex]['number'] ?? '01' }}</span>
              </div>
              <h3 class="badge-title" id="coverflowBadgeTitle">
                {{ $servicesFormatted[$activeServiceIndex]['title'] ?? '' }}
              </h3>
            </div>
            <p class="coverflow-desc" id="coverflowDesc">
              {{ $servicesFormatted[$activeServiceIndex]['desc'] ?? '' }}
            </p>
            <a href="#coverflowService" 
               class="view-more-btn" 
               id="coverflowViewMoreBtn"
               title="Xem chi tiết dịch vụ">
              <span>{{ $viewMoreText }}</span>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Dữ liệu JSON động và Javascript điều khiển Real Horizontal Slider -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const services = @json($servicesFormatted);
    let currentIndex = {{ (int) $activeServiceIndex }};
    const total = services.length;

    const chips = Array.from(document.querySelectorAll('#coverflowChips .coverflow-chip'));
    const slides = Array.from(document.querySelectorAll('.panel-coverflow .coverflow-slide'));
    const elNum = document.getElementById('coverflowBadgeNum');
    const elTitle = document.getElementById('coverflowBadgeTitle');
    const elDesc = document.getElementById('coverflowDesc');
    const elViewMore = document.getElementById('coverflowViewMoreBtn');
    const prevBtn = document.getElementById('coverflowPrevBtn');
    const nextBtn = document.getElementById('coverflowNextBtn');

    function updateSlider(targetIdx) {
      if (!services || services.length === 0) return;
      currentIndex = (targetIdx + total) % total;
      const s = services[currentIndex];

      // 1. Cập nhật Chips active state
      chips.forEach((c, i) => {
        c.classList.toggle('is-active', i === currentIndex);
      });

      // 2. Cập nhật Badge & Nội dung
      if (elNum) elNum.textContent = s.number;
      if (elTitle) elTitle.textContent = s.title;
      if (elDesc) elDesc.textContent = s.desc;
      if (elViewMore) {
        elViewMore.href = '#coverflowService';
      }

      // 3. Trượt các Slide trong Track theo đúng thứ tự không gian
      const activeIdx = currentIndex;
      const prevIdx = (activeIdx - 1 + total) % total;
      const nextIdx = (activeIdx + 1) % total;

      slides.forEach((slide, idx) => {
        let targetClass = '';
        if (idx === activeIdx) {
          targetClass = 'is-active';
        } else if (idx === prevIdx) {
          targetClass = 'is-prev';
        } else if (idx === nextIdx) {
          targetClass = 'is-next';
        } else {
          // Tính toán vị trí ngoài màn hình (trái hoặc phải)
          let diff = (idx - activeIdx + total) % total;
          if (diff > total / 2) diff -= total;
          targetClass = diff < 0 ? 'is-hidden-left' : 'is-hidden-right';
        }

        const wasHidden = slide.classList.contains('is-hidden-left') || slide.classList.contains('is-hidden-right');
        const willBeHidden = targetClass === 'is-hidden-left' || targetClass === 'is-hidden-right';

        // Nếu nhảy giữa 2 vị trí ẩn offscreen thì tắt transition tạm thời để không bay ngang màn hình
        if (wasHidden && willBeHidden) {
          slide.classList.add('no-transition');
        } else {
          slide.classList.remove('no-transition');
        }

        slide.classList.remove('is-active', 'is-prev', 'is-next', 'is-hidden-left', 'is-hidden-right');
        slide.classList.add(targetClass);

        if (slide.classList.contains('no-transition')) {
          requestAnimationFrame(() => {
            slide.classList.remove('no-transition');
          });
        }
      });

      // 4. Phát sự kiện đổi service cho coverflowService & coverflowProject cùng cập nhật
      window.dispatchEvent(new CustomEvent('workspace:service-changed', {
        detail: {
          index: currentIndex,
          service: s
        }
      }));

      // 5. Cập nhật URL query mà không reload trang
      if (history.replaceState && s.slug) {
        const url = new URL(window.location);
        url.searchParams.set('service', s.slug);
        window.history.replaceState({}, '', url);
      }
    }

    // Cho phép widget chi tiết (coverflowService) yêu cầu đổi sang dịch vụ khác
    window.addEventListener('workspace:service-goto', function (event) {
      const idx = event && event.detail ? Number(event.detail.index) : NaN;
      if (Number.isFinite(idx)) {
        updateSlider(idx);
      }
    });

    // Bắt sự kiện click vào nút View More
    if (elViewMore) {
      elViewMore.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (typeof window.openServiceDetail === 'function') {
          window.openServiceDetail();
        }
      });
    }

    // Bắt sự kiện click vào Chips
    chips.forEach((chip) => {
      chip.addEventListener('click', function () {
        const targetIdx = parseInt(this.getAttribute('data-index'), 10);
        updateSlider(targetIdx);
      });
    });

    // Bắt sự kiện Previous & Next trên nút mũi tên
    if (prevBtn) {
      prevBtn.addEventListener('click', () => updateSlider(currentIndex - 1));
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', () => updateSlider(currentIndex + 1));
    }

    // Bắt sự kiện click trực tiếp vào slide preview (trái hoặc phải) để chuyển slide
    slides.forEach((slide) => {
      slide.addEventListener('click', function () {
        if (this.classList.contains('is-prev')) {
          updateSlider(currentIndex - 1);
        } else if (this.classList.contains('is-next')) {
          updateSlider(currentIndex + 1);
        }
      });
      slide.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          if (this.classList.contains('is-prev')) {
            e.preventDefault();
            updateSlider(currentIndex - 1);
          } else if (this.classList.contains('is-next')) {
            e.preventDefault();
            updateSlider(currentIndex + 1);
          }
        }
      });
    });

    // Phát lần đầu để các widget bên dưới nhận dữ liệu service ban đầu
    if (services[currentIndex]) {
      window.dispatchEvent(new CustomEvent('workspace:service-changed', {
        detail: {
          index: currentIndex,
          service: services[currentIndex]
        }
      }));
    }
  });
</script>
