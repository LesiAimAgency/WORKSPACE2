@php
  use App\Http\Controllers\CoverflowController;
  use Illuminate\Support\Str;

  $services = $services ?? CoverflowController::getServicesData();
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
              'slug' => $s['slug'] ?? '',
              'desc' => $s['desc'] ?? '',
              'image' => $formatImg($s['image'] ?? 'themes/workspace/coverflow/frame1.png'),
              'intro_items' => $s['intro_items'] ?? [],
              'projects' => $s['projects'] ?? [],
              'serviceUrl' => '#coverflowService',
              'projectUrl' => '#coverflowProject',
          ];
      }, $services);
  }
@endphp

<style>
    .coverflow-stage {
        overflow: hidden;
        border-radius: 12px;
    }
    .coverflow-media img {
        width: 100%;
        display: block;
        object-fit: cover;
        transition: opacity 0.3s ease, transform 0.4s ease;
    }
    .coverflow-chip {
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
    }
    .coverflow-chip.is-active {
        color: #0f0f10 !important;
        font-weight: 700 !important;
    }
    .coverflow-chip.is-active::after {
        content: '';
        display: block;
        width: 32px;
        height: 2px;
        background: #EB2026;
        margin-top: 6px;
        border-radius: 2px;
    }
    .view-more-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        cursor: pointer;
        transition: transform 0.2s ease, background 0.2s ease;
    }
    .view-more-btn:hover {
        transform: translateY(-2px);
    }
</style>

<section class="panel panel-coverflow" id="coverflow" data-ui="light">
  <div class="surface coverflow-surface">
    <div class="coverflow-layout">

      <!-- 1. Top Category Tabs (Chips Selector) -->
      <nav class="coverflow-chips" id="coverflowChips">
        @foreach($servicesFormatted as $index => $srv)
          <button type="button" 
                  class="coverflow-chip {{ $index === $activeServiceIndex ? 'is-active' : '' }}" 
                  data-index="{{ $index }}"
                  title="{{ $srv['title'] }}">
            {{ $srv['title'] }}
          </button>
        @endforeach
      </nav>

      <!-- 2. Main Visual Stage with Floating Red Badge -->
      <div class="coverflow-stage">
        <div class="coverflow-media">
          <img id="coverflowMediaImg" 
               src="{{ $servicesFormatted[$activeServiceIndex]['image'] ?? asset('themes/workspace/coverflow/frame1.png') }}" 
               alt="{{ $servicesFormatted[$activeServiceIndex]['title'] ?? 'Service' }}"/>
        </div>
         <div class="coverflow-controls">
          <button class="coverflow-arrow coverflow-prev" id="coverflowPrevBtn" type="button" aria-label="Previous service">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
          </button>
          <button class="coverflow-arrow coverflow-next" id="coverflowNextBtn" type="button" aria-label="Next service">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </button>
        </div>
        <!-- Floating Red Badge / Overlay Card -->
         <!-- 3. Bottom Description & Navigation Arrows -->
      <div class="coverflow">
      <div class="coverflow-footer">
        <div class="coverflow-badge">
          <div class="badge-header">
            <span class="badge-num" id="coverflowBadgeNum">{{ $servicesFormatted[$activeServiceIndex]['number'] ?? '01' }}</span>
            <a href="#coverflowService" 
               class="view-more-btn" 
               id="coverflowViewMoreBtn"
               title="Xem chi tiết dịch vụ">
              <span>View more</span>
            </a>
          </div>
          <h3 class="badge-title" id="coverflowBadgeTitle">
            {{ $servicesFormatted[$activeServiceIndex]['title'] ?? '' }}
          </h3>
        </div>
        <p class="coverflow-desc" id="coverflowDesc">
          {{ $servicesFormatted[$activeServiceIndex]['desc'] ?? '' }}
        </p>
      </div>
        
      </div>
      
      </div>
 
      <

    </div>
  </div>
</section>

<!-- Dữ liệu JSON động từ Backend -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const services = @json($servicesFormatted);
    let currentIndex = {{ (int) $activeServiceIndex }};
    const total = services.length;

    const chips = document.querySelectorAll('#coverflowChips .coverflow-chip');
    const elImg = document.getElementById('coverflowMediaImg');
    const elNum = document.getElementById('coverflowBadgeNum');
    const elTitle = document.getElementById('coverflowBadgeTitle');
    const elDesc = document.getElementById('coverflowDesc');
    const elViewMore = document.getElementById('coverflowViewMoreBtn');
    const prevBtn = document.getElementById('coverflowPrevBtn');
    const nextBtn = document.getElementById('coverflowNextBtn');

    function updateService(idx) {
      if (!services || services.length === 0) return;
      currentIndex = (idx + total) % total;
      const s = services[currentIndex];

      // 1. Cập nhật Chips active state
      chips.forEach((c, i) => {
        if (i === currentIndex) {
          c.classList.add('is-active');
        } else {
          c.classList.remove('is-active');
        }
      });

      // 2. Hiệu ứng mờ nhẹ và cập nhật Media
      if (elImg) {
        elImg.style.opacity = '0.6';
        setTimeout(() => {
          elImg.src = s.image;
          elImg.alt = s.title;
          elImg.style.opacity = '1';
        }, 120);
      }

      // 3. Cập nhật Badge & Nội dung
      if (elNum) elNum.textContent = s.number;
      if (elTitle) elTitle.textContent = s.title;
      if (elDesc) elDesc.textContent = s.desc;
      if (elViewMore) {
        elViewMore.href = '#coverflowService';
      }

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
        updateService(idx);
      }
    });

    // Bắt sự kiện click vào nút View More
    if (elViewMore) {
      elViewMore.addEventListener('click', function (e) {
        e.preventDefault();
        if (typeof window.openServiceDetail === 'function') {
          window.openServiceDetail();
        }
      });
    }

    // Bắt sự kiện click vào Chips
    chips.forEach((chip) => {
      chip.addEventListener('click', function () {
        const targetIdx = parseInt(this.getAttribute('data-index'), 10);
        updateService(targetIdx);
      });
    });

    // Bắt sự kiện Previous & Next
    if (prevBtn) {
      prevBtn.addEventListener('click', () => updateService(currentIndex - 1));
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', () => updateService(currentIndex + 1));
    }

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
