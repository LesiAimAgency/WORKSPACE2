@php
  use App\Http\Controllers\CoverflowController;
  use Illuminate\Support\Str;

  $services = $services ?? CoverflowController::getServicesData();
  $activeSlug = session('active_service_slug') ?: (request()->query('service') ?: ($services[0]['slug'] ?? ''));

  $activeService = $activeService ?? null;
  $activeIndex = 0;
  foreach ($services as $i => $s) {
      if (($s['slug'] ?? '') === $activeSlug) {
          $activeIndex = $i;
          if (!$activeService) {
              $activeService = $s;
          }
          break;
      }
  }
  $activeService = $activeService ?? ($services[0] ?? []);

  $formatImg = function (?string $path) {
      if (empty($path)) return '';
      if (Str::startsWith($path, ['http://', 'https://', '/'])) {
          return $path;
      }
      return asset($path);
  };

  $projects = $projects ?? ($activeService['projects'] ?? []);

  if (!isset($projectsFormatted)) {
      $projectsFormatted = array_map(function ($p) use ($formatImg) {
          return [
              'title' => $p['title'] ?? 'PROJECT 01',
              'area' => $p['area'] ?? '',
              'location' => $p['location'] ?? '',
              'typology' => $p['typology'] ?? '',
              'style' => $p['style'] ?? '',
              'tech' => $p['tech'] ?? '',
              'coverImg' => $formatImg($p['cover_image'] ?? ($p['coverImg'] ?? 'themes/workspace/coverflow/frame1.png')),
              'designImg' => $formatImg($p['design_image'] ?? ($p['designImg'] ?? 'themes/workspace/workflow/design.png')),
              'resultImg' => $formatImg($p['result_image'] ?? ($p['resultImg'] ?? 'themes/workspace/workflow/build.png')),
          ];
      }, $projects);
  }

  $firstProject = !empty($projectsFormatted[0]) ? $projectsFormatted[0] : [
      'title' => 'PROJECT 01',
      'area' => '125m²',
      'location' => 'District 2, Ho Chi Minh City',
      'typology' => 'Office',
      'style' => 'Modern',
      'tech' => 'Integrated Smart-home system',
      'coverImg' => asset('themes/workspace/coverflow/frame1.png'),
      'designImg' => asset('themes/workspace/workflow/design.png'),
      'resultImg' => asset('themes/workspace/workflow/build.png'),
  ];

  $srvImg = $activeService['image'] ?? 'themes/workspace/coverflow/frame1.png';
  $srvImg = $formatImg($srvImg);

  $serviceTitles = array_values(array_map(fn ($s) => $s['title'] ?? '', $services));
  $nextServiceIndex = count($serviceTitles) ? (($activeIndex + 1) % count($serviceTitles)) : 0;
  $nextServiceTitle = $serviceTitles[$nextServiceIndex] ?? '';
@endphp

<style>
    /* ========================================================================= */
    /* SERVICE DETAIL - 2 STEP ONEPAGE (Service_D1 -> Frame 128)                 */
    /* Thiết kế gốc 1440 x 900. --u = 1 đơn vị thiết kế, co giãn theo màn hình.  */
    /* Frame tổng rộng 2269 (overflow): [Intro text | Ảnh | Thông số dự án].     */
    /* Step 1: thấy Intro + một phần ảnh. Step 2: trượt frame sang trái.         */
    /* ========================================================================= */
    #coverflowService {
        --u: min(0.069444vw, 0.111111vh);
        --svc-red: #EB2026;
        --svc-shift: 972;
        background-color: #F2F2F7;
        overflow: hidden !important;
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Surface = vùng nội dung 80vh (padding 10vh trên/dưới, 72px trái/phải do .panel).
       overflow visible để ảnh ở step 1 tràn ra mép phải màn hình; .panel sẽ cắt ở mép viewport. */
    #coverflowService .coverflowService-surface {
        padding: 0 !important;
        background: transparent !important;
        overflow: visible !important;
        display: block !important;
    }

    #coverflowService .svc-stage {
        position: relative;
        width: 100%;
        height: 100%;
        color: #000;
    }

    /* ---- Frame tổng (overflow): cao 532, cộng tabs + back/next bên dưới = tổng 638 đơn vị trong hộp 720 (80vh) ---- */
    #coverflowService .svc-frame {
        position: absolute;
        left: 0;
        top: calc((100% - var(--u) * 638) / 2);
        width: calc(var(--u) * 2269);
        height: calc(var(--u) * 532);
        transition: transform 850ms cubic-bezier(0.76, 0, 0.24, 1);
        will-change: transform;
    }

    #coverflowService .svc-stage.is-step-project .svc-frame {
        transform: translate3d(calc(var(--u) * var(--svc-shift) * -1), 0, 0);
    }

    /* ---- Cột trái: giới thiệu dịch vụ ---- */
    #coverflowService .svc-intro {
        position: absolute;
        left: 0;
        top: 0;
        width: calc(var(--u) * 940);
        height: 100%;
        transition: opacity 600ms ease;
    }

    #coverflowService .svc-stage.is-step-project .svc-intro {
        opacity: 0;
        pointer-events: none;
    }

    #coverflowService .svc-pill {
        position: absolute;
        left: 0;
        top: calc(var(--u) * 85.875);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: calc(var(--u) * 94);
        height: calc(var(--u) * 28);
        padding: 0 calc(var(--u) * 16);
        box-sizing: border-box;
        border: 1px solid var(--svc-red);
        border-radius: calc(var(--u) * 14);
        color: var(--svc-red);
        font-size: calc(var(--u) * 16);
        line-height: 1;
        text-decoration: none;
        transition: background 0.25s ease, color 0.25s ease;
    }

    #coverflowService .svc-pill:hover {
        background: var(--svc-red);
        color: #fff;
    }

    #coverflowService .svc-title {
        position: absolute;
        left: 0;
        top: calc(var(--u) * 131);
        width: calc(var(--u) * 440);
        margin: 0;
        font-size: calc(var(--u) * 50.5);
        font-weight: 500;
        line-height: calc(var(--u) * 59);
        letter-spacing: 0;
        color: #000;
    }

    #coverflowService .svc-underline {
        position: absolute;
        left: 0;
        display: block;
        top: calc(var(--u) * 264);
        width: calc(var(--u) * 205);
        height: calc(var(--u) * 4);
        border-radius: calc(var(--u) * 2);
        background: linear-gradient(90deg, var(--svc-red) 0%, rgba(235, 32, 38, 0) 100%);
    }

    #coverflowService .svc-items {
        position: absolute;
        left: 0;
        top: calc(var(--u) * 327);
        display: grid;
        grid-template-columns: calc(var(--u) * 400) calc(var(--u) * 400);
        column-gap: calc(var(--u) * 104);
        row-gap: calc(var(--u) * 24);
    }

    #coverflowService .svc-item-title {
        margin: 0 0 calc(var(--u) * 8);
        color: var(--svc-red);
        font-size: calc(var(--u) * 28);
        font-weight: 700;
        line-height: calc(var(--u) * 36);
        letter-spacing: 0;
    }

    #coverflowService .svc-item-desc {
        margin: 0;
        color: #000;
        font-size: calc(var(--u) * 16);
        font-weight: 400;
        line-height: calc(var(--u) * 19);
    }

    /* ---- Ảnh ---- */
    #coverflowService .svc-image {
        position: absolute;
        left: calc(var(--u) * 972);
        top: 0;
        width: calc(var(--u) * 945);
        height: 100%;
        margin: 0;
        overflow: hidden;
        border-radius: calc(var(--u) * 12);
        background: #d9d9de;
        cursor: pointer;
    }

    #coverflowService .svc-stage.is-step-project .svc-image {
        cursor: default;
    }

    #coverflowService .svc-image img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.25s ease;
    }

    /* ---- Cột phải: thông số dự án ---- */
    #coverflowService .svc-info {
        position: absolute;
        left: calc(var(--u) * 1944);
        top: 0;
        width: calc(var(--u) * 324);
        height: 100%;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
        padding-top: calc(var(--u) * 46);
        padding-bottom: calc(var(--u) * 47);
    }

    #coverflowService .svc-project-title {
        margin: 0;
        font-size: calc(var(--u) * 28);
        font-weight: 700;
        line-height: calc(var(--u) * 34);
        letter-spacing: 0;
        color: #000;
    }

    #coverflowService .svc-info .svc-underline {
        position: relative;
        top: auto;
        margin-top: calc(var(--u) * 10);
    }

    #coverflowService .svc-specs {
        margin-top: calc(var(--u) * 19);
    }

    #coverflowService .svc-spec-row {
        display: flex;
        align-items: flex-start;
        padding: calc(var(--u) * 12) 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.28);
        font-size: calc(var(--u) * 16);
        line-height: calc(var(--u) * 19);
    }

    #coverflowService .svc-spec-label {
        flex: 0 0 calc(var(--u) * 150);
        font-weight: 700;
    }

    #coverflowService .svc-spec-value {
        flex: 1 1 auto;
        font-weight: 400;
    }

    #coverflowService .svc-compare {
        align-self: flex-start;
        margin-top: calc(var(--u) * 21);
        display: inline-flex;
        align-items: center;
        height: calc(var(--u) * 22);
        padding: 0 calc(var(--u) * 10);
        border: 0;
        border-radius: calc(var(--u) * 11);
        background: #000;
        color: #fff;
        font-family: inherit;
        font-size: calc(var(--u) * 12);
        font-weight: 400;
        line-height: 1;
        cursor: pointer;
        transition: background 0.25s ease;
    }

    #coverflowService .svc-compare:hover {
        background: var(--svc-red);
    }

    #coverflowService .svc-pager {
        margin-top: auto;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
    }

    #coverflowService .svc-pager-left {
        width: calc(var(--u) * 214);
    }

    #coverflowService .svc-pager-label {
        display: block;
        margin-bottom: calc(var(--u) * 10);
        font-size: calc(var(--u) * 16);
        font-weight: 500;
        line-height: calc(var(--u) * 19);
    }

    #coverflowService .svc-pager-line {
        position: relative;
        height: 1px;
        background: rgba(0, 0, 0, 0.5);
    }

    #coverflowService .svc-pager-bar {
        position: absolute;
        left: 0;
        top: -0.5px;
        height: 2px;
        width: 25%;
        background: #000;
        transition: width 0.35s ease;
    }

    #coverflowService .svc-arrows {
        display: flex;
        gap: calc(var(--u) * 16);
    }

    #coverflowService .svc-arrow {
        width: calc(var(--u) * 32);
        height: calc(var(--u) * 32);
        padding: 0;
        border: 0;
        border-radius: 50%;
        background: #BDBDBD;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.25s ease;
    }

    #coverflowService .svc-arrow:hover {
        background: #000;
    }

    #coverflowService .svc-arrow svg {
        width: 45%;
        height: 45%;
    }

    /* ---- Tabs đáy + liên kết đáy (nằm gọn trong vùng nội dung 80vh) ---- */
    #coverflowService .svc-tabs {
        position: absolute;
        left: calc(var(--u) * 18);
        top: calc((100% - var(--u) * 638) / 2 + var(--u) * 560);
        display: flex;
        gap: calc(var(--u) * 67);
        z-index: 3;
    }

    #coverflowService .svc-tab {
        padding: 0;
        border: 0;
        border-bottom: 1px solid transparent;
        background: none;
        font-family: inherit;
        font-size: calc(var(--u) * 16);
        font-weight: 400;
        line-height: calc(var(--u) * 19);
        text-transform: uppercase;
        color: #9a9a9f;
        cursor: pointer;
        transition: color 0.25s ease, border-color 0.25s ease;
    }

    #coverflowService .svc-tab:hover {
        color: #000;
    }

    #coverflowService .svc-tab.is-active {
        color: #000;
        border-bottom-color: #000;
    }

    #coverflowService .svc-back,
    #coverflowService .svc-next {
        position: absolute;
        top: calc((100% - var(--u) * 638) / 2 + var(--u) * 619);
        z-index: 3;
        font-size: calc(var(--u) * 16);
        font-style: italic;
        line-height: calc(var(--u) * 19);
        color: #000;
        text-decoration: none;
        background: none;
        border: 0;
        padding: 0;
        font-family: inherit;
        cursor: pointer;
    }

    #coverflowService .svc-back { left: calc(var(--u) * 18); }
    #coverflowService .svc-next { right: calc(var(--u) * 17); text-align: right; }

    #coverflowService .svc-back:hover,
    #coverflowService .svc-next:hover {
        color: var(--svc-red);
    }

    body.is-service-detail .pdf-profile-link {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    /* ---- Mobile: xếp dọc, chuyển bước bằng cách ẩn/hiện ---- */
    @media (max-width: 768px) {
        #coverflowService { --u: 1px; }
        #coverflowService .svc-stage {
            width: 100%;
            height: 100%;
            padding: 0 0 70px;
            box-sizing: border-box;
            overflow-y: auto;
        }
        #coverflowService .svc-frame,
        #coverflowService .svc-intro,
        #coverflowService .svc-image,
        #coverflowService .svc-info,
        #coverflowService .svc-pill,
        #coverflowService .svc-title,
        #coverflowService .svc-underline,
        #coverflowService .svc-items {
            position: relative;
            left: auto;
            top: auto;
            width: 100%;
            height: auto;
            transform: none !important;
        }
        #coverflowService .svc-intro { opacity: 1 !important; pointer-events: auto !important; }
        #coverflowService .svc-pill { width: auto; margin-bottom: 14px; display: inline-flex; }
        #coverflowService .svc-title { font-size: 28px; line-height: 34px; margin-bottom: 12px; }
        #coverflowService .svc-underline { width: 140px; height: 3px; margin: 0 0 20px; }
        #coverflowService .svc-items { grid-template-columns: 1fr; row-gap: 18px; }
        #coverflowService .svc-item-title { font-size: 18px; line-height: 24px; }
        #coverflowService .svc-item-desc { font-size: 14px; line-height: 20px; }
        #coverflowService .svc-image { height: 220px; margin-top: 22px; }
        #coverflowService .svc-info { display: none; padding: 0; }
        #coverflowService .svc-stage.is-step-project .svc-intro,
        #coverflowService .svc-stage.is-step-project .svc-image--intro { display: none; }
        #coverflowService .svc-stage.is-step-project .svc-info { display: flex; margin-top: 18px; }
        #coverflowService .svc-project-title { font-size: 22px; line-height: 28px; }
        #coverflowService .svc-spec-row { font-size: 14px; line-height: 20px; }
        #coverflowService .svc-spec-label { flex-basis: 110px; }
        #coverflowService .svc-compare { height: 30px; font-size: 13px; padding: 0 14px; border-radius: 15px; }
        #coverflowService .svc-pager { margin-top: 22px; }
        #coverflowService .svc-pager-label { font-size: 14px; }
        #coverflowService .svc-tabs { position: absolute; left: 20px; top: auto; bottom: 74px; gap: 28px; }
        #coverflowService .svc-tab { font-size: 13px; line-height: 18px; }
        #coverflowService .svc-back { left: 20px; top: auto; bottom: 20px; font-size: 13px; }
        #coverflowService .svc-next { right: 20px; top: auto; bottom: 20px; font-size: 13px; max-width: 55%; }
    }

    /* ========================================================================= */
    /* COMPARISON MODAL (Before / After)                                         */
    /* ========================================================================= */
    .comparison-modal {
        position: fixed;
        inset: 0;
        z-index: 999999;
        display: none;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    .comparison-modal.is-open {
        display: flex !important;
    }

    .comparison-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 1;
    }

    .comparison-modal-wrapper {
        position: relative;
        z-index: 2;
        width: min(1080px, 92vw);
        height: min(680px, 86vh);
        background: #0f121d;
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .comparison-modal-close {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.2s ease;
    }

    .comparison-modal-close:hover {
        background: #EB2026;
        border-color: #EB2026;
        transform: rotate(90deg);
    }

    .comparison-modal-box {
        flex: 1;
        width: 100%;
        height: 100%;
        padding: 12px;
        box-sizing: border-box;
    }

    .slider-container {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
        user-select: none;
        -webkit-user-select: none;
        cursor: ew-resize;
        background-color: #0b0f19;
        border-radius: 12px;
    }

    .before-image,
    .after-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .before-image img,
    .after-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        pointer-events: none;
        user-select: none;
    }

    .before-image {
        width: 50%;
        z-index: 2;
    }

    .before-text,
    .after-text {
        position: absolute;
        top: 20px;
        padding: 6px 14px;
        font-family: 'Roboto', sans-serif;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 1px;
        color: #ffffff;
        background: rgba(0, 0, 0, 0.65);
        backdrop-filter: blur(4px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        z-index: 5;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .before-text { left: 20px; }
    .after-text { right: 20px; }

    .dot-indicator {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #EB2026;
        display: inline-block;
    }

    .slider-handle {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 4px;
        background: #ffffff;
        z-index: 3;
        transform: translateX(-50%);
        pointer-events: none;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }

    .slider-line {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 4;
        pointer-events: none;
    }

    .pulse-container {
        display: flex;
        align-items: center;
        justify-content: center;
        filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.4));
    }
</style>

<section class="panel panel-coverflow-service panel-detail-flow is-hidden" id="coverflowService" data-ui="light" aria-label="{{ $activeService['title'] ?? 'Service' }} Introductions &amp; Projects">
    <div class="surface coverflowService-surface">
        <div class="svc-stage" id="svcStage">

            {{-- FRAME TỔNG (overflow): [Intro | Ảnh | Thông số dự án] --}}
            <div class="svc-frame" id="svcFrame">

                <div class="svc-intro">
                    <a href="#coverflow" class="svc-pill" id="coverflowServiceBackBtn" title="Quay lại bộ sưu tập Services">Services</a>
                    <h2 class="svc-title" id="srvActiveTitle">{{ $activeService['title'] ?? 'Interior Advisory and Project Setup' }}</h2>
                    <span class="svc-underline" aria-hidden="true"></span>

                    <div class="svc-items" id="srvActiveList">
                        @if(!empty($activeService['intro_items']) && is_array($activeService['intro_items']))
                            @foreach($activeService['intro_items'] as $item)
                                <div class="svc-item">
                                    <h3 class="svc-item-title">{{ $item['title'] ?? '' }}</h3>
                                    <p class="svc-item-desc">{{ $item['desc'] ?? '' }}</p>
                                </div>
                            @endforeach
                        @else
                            <div class="svc-item">
                                <h3 class="svc-item-title">{{ $activeService['title'] ?? '' }}</h3>
                                <p class="svc-item-desc">{{ $activeService['desc'] ?? '' }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <figure class="svc-image" id="svcImage" title="Xem dự án">
                    <img src="{{ $srvImg }}" id="srvActiveImg" alt="{{ $activeService['title'] ?? 'Service' }}">
                </figure>

                <div class="svc-info">
                    <h3 class="svc-project-title" id="projectTitle">{{ $firstProject['title'] }}</h3>
                    <span class="svc-underline" aria-hidden="true"></span>

                    <div class="svc-specs">
                        <div class="svc-spec-row">
                            <span class="svc-spec-label">Total Area</span>
                            <span class="svc-spec-value" id="projectArea">{{ $firstProject['area'] }}</span>
                        </div>
                        <div class="svc-spec-row">
                            <span class="svc-spec-label">Location</span>
                            <span class="svc-spec-value" id="projectLocation">{{ $firstProject['location'] }}</span>
                        </div>
                        <div class="svc-spec-row">
                            <span class="svc-spec-label">Typology</span>
                            <span class="svc-spec-value" id="projectTypology">{{ $firstProject['typology'] }}</span>
                        </div>
                        <div class="svc-spec-row">
                            <span class="svc-spec-label">Style</span>
                            <span class="svc-spec-value" id="projectStyle">{{ $firstProject['style'] }}</span>
                        </div>
                        <div class="svc-spec-row">
                            <span class="svc-spec-label">Tech Solution</span>
                            <span class="svc-spec-value" id="projectTech">{{ $firstProject['tech'] }}</span>
                        </div>
                    </div>

                    <button type="button" class="svc-compare" id="openComparisonModalBtn">Comparison Design and Result</button>

                    <div class="svc-pager">
                        <div class="svc-pager-left">
                            <span class="svc-pager-label" id="projectCurrentNum">{{ $firstProject['title'] }}</span>
                            <div class="svc-pager-line"><span class="svc-pager-bar" id="projectProgressBar"></span></div>
                        </div>
                        <div class="svc-arrows">
                            <button type="button" class="svc-arrow" id="projectPrevBtn" aria-label="Previous project">
                                <svg viewBox="0 0 16 16" fill="none"><path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button type="button" class="svc-arrow" id="projectNextBtn" aria-label="Next project">
                                <svg viewBox="0 0 16 16" fill="none"><path d="M6 3L11 8L6 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabs + liên kết đáy (nằm gọn trong vùng nội dung 80vh) --}}
            <div class="svc-tabs">
                <button type="button" class="svc-tab is-active" data-pane="intro" id="tabServiceBtn">Introductions</button>
                <button type="button" class="svc-tab" data-pane="project" id="tabProjectBtn">Projects</button>
            </div>
            <a href="#coverflow" class="svc-back" id="svcBackToServices">Back to Services</a>
            <button type="button" class="svc-next" id="svcNextService" data-next-index="{{ $nextServiceIndex }}">{{ $nextServiceTitle }}</button>

        </div>
    </div>
</section>

{{-- Modal so sánh Before / After --}}
<div id="comparisonModal" class="comparison-modal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="comparison-modal-backdrop" id="closeModalBackdrop"></div>
    <div class="comparison-modal-wrapper">
        <button type="button" class="comparison-modal-close" id="closeModalBtn" aria-label="Close comparison">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="comparison-modal-box">
            <div class="slider-container" id="modalSlider">
                <div class="before-image" id="modalBeforeImage">
                    <img src="{{ !empty($firstProject['designImg']) ? $firstProject['designImg'] : asset('themes/workspace/workflow/design.png') }}" id="modalDesignImg" alt="Design 3D">
                    <span class="before-text"><span class="dot-indicator"></span> DESIGN 3D</span>
                </div>

                <div class="after-image">
                    <img src="{{ !empty($firstProject['resultImg']) ? $firstProject['resultImg'] : asset('themes/workspace/workflow/build.png') }}" id="modalResultImg" alt="Result">
                    <span class="after-text"><span class="dot-indicator"></span> RESULT</span>
                </div>

                <div class="slider-handle" id="sliderHandle"></div>
                <div class="slider-line" id="sliderLine">
                    <div class="pulse-container">
                        <svg role="presentation" focusable="false" fill="none" width="46" height="46" viewBox="0 0 50 50">
                            <g>
                                <rect width="50" height="50" rx="25" fill="#ffffff"></rect>
                                <path d="m19.25 19-6 6 6 6m11.5 0 6-6-6-6" stroke="#000000" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const serviceTitles = @json($serviceTitles);
        const baseUrl = @json(url('/')) + '/';

        let currentProjects = @json($projectsFormatted);
        let currentProjectIndex = 0;
        let serviceImage = @json($srvImg);
        let step = 'intro';

        const stage = document.getElementById('svcStage');
        const imgEl = document.getElementById('srvActiveImg');
        const imageBox = document.getElementById('svcImage');
        const tabIntro = document.getElementById('tabServiceBtn');
        const tabProject = document.getElementById('tabProjectBtn');

        function resolveUrl(path, fallback) {
            let p = path || fallback;
            if (!/^(https?:)?\/\//.test(p) && p.charAt(0) !== '/') {
                p = baseUrl + p;
            }
            return p;
        }

        function swapImage(src) {
            if (!imgEl || !src || imgEl.getAttribute('src') === src) return;
            imgEl.style.opacity = '0.35';
            setTimeout(function () {
                imgEl.src = src;
                imgEl.style.opacity = '1';
            }, 180);
        }

        function setStep(next) {
            step = next === 'project' ? 'project' : 'intro';
            if (stage) stage.classList.toggle('is-step-project', step === 'project');
            if (tabIntro) tabIntro.classList.toggle('is-active', step === 'intro');
            if (tabProject) tabProject.classList.toggle('is-active', step === 'project');

            if (step === 'project') {
                const p = currentProjects[currentProjectIndex];
                if (p && p.coverImg) swapImage(p.coverImg);
            } else {
                swapImage(serviceImage);
            }
        }

        // main.js (setDetailPane) và các nơi khác gọi 2 hàm này
        window.expandProjectWing = function () { setStep('project'); };
        window.collapseProjectWing = function () { setStep('intro'); };

        function requestStep(pane) {
            if (typeof window.setDetailPane === 'function') {
                window.setDetailPane(pane, true);
            } else {
                setStep(pane);
            }
        }

        if (tabIntro) tabIntro.addEventListener('click', function (e) { e.preventDefault(); requestStep('intro'); });
        if (tabProject) tabProject.addEventListener('click', function (e) { e.preventDefault(); requestStep('project'); });

        // Bấm vào ảnh (step 1) => next sang frame dự án
        if (imageBox) {
            imageBox.addEventListener('click', function () {
                if (step === 'intro') requestStep('project');
            });
        }

        function goBackToServices(e) {
            e.preventDefault();
            e.stopPropagation();
            if (typeof window.closeServiceDetail === 'function') {
                window.closeServiceDetail();
            } else if (typeof window.goToSectionById === 'function') {
                window.goToSectionById('coverflow');
            } else {
                window.location.hash = '#coverflow';
            }
        }
        ['coverflowServiceBackBtn', 'svcBackToServices'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.addEventListener('click', goBackToServices);
        });

        // Link góc phải dưới: sang dịch vụ kế tiếp
        const nextServiceBtn = document.getElementById('svcNextService');
        if (nextServiceBtn) {
            nextServiceBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const idx = Number(nextServiceBtn.getAttribute('data-next-index'));
                window.dispatchEvent(new CustomEvent('workspace:service-goto', { detail: { index: idx } }));
            });
        }

        // ---------------- Dự án ----------------
        function renderProject(idx) {
            if (!currentProjects || !currentProjects.length) return;
            currentProjectIndex = (idx + currentProjects.length) % currentProjects.length;
            const p = currentProjects[currentProjectIndex];

            const set = function (id, value) {
                const el = document.getElementById(id);
                if (el) el.textContent = value || '';
            };
            set('projectTitle', p.title || 'PROJECT');
            set('projectCurrentNum', p.title || 'PROJECT');
            set('projectArea', p.area);
            set('projectLocation', p.location);
            set('projectTypology', p.typology);
            set('projectStyle', p.style);
            set('projectTech', p.tech);

            const bar = document.getElementById('projectProgressBar');
            if (bar) bar.style.width = (((currentProjectIndex + 1) / currentProjects.length) * 100) + '%';

            if (step === 'project' && p.coverImg) swapImage(p.coverImg);

            const d = document.getElementById('modalDesignImg');
            const r = document.getElementById('modalResultImg');
            if (d && p.designImg) d.src = p.designImg;
            if (r && p.resultImg) r.src = p.resultImg;
        }

        const prevBtn = document.getElementById('projectPrevBtn');
        const nextBtn = document.getElementById('projectNextBtn');
        if (prevBtn) prevBtn.addEventListener('click', function (e) { e.preventDefault(); renderProject(currentProjectIndex - 1); });
        if (nextBtn) nextBtn.addEventListener('click', function (e) { e.preventDefault(); renderProject(currentProjectIndex + 1); });

        renderProject(0);

        // ---------------- Đồng bộ khi đổi dịch vụ ở Coverflow ----------------
        window.addEventListener('workspace:service-changed', function (event) {
            const data = event.detail;
            if (!data || !data.service) return;
            const service = data.service;

            const elTitle = document.getElementById('srvActiveTitle');
            const elList = document.getElementById('srvActiveList');
            if (elTitle && service.title) elTitle.textContent = service.title;

            if (elList && Array.isArray(service.intro_items) && service.intro_items.length) {
                elList.innerHTML = service.intro_items.map(function (item) {
                    return '<div class="svc-item">' +
                        '<h3 class="svc-item-title">' + (item.title || '') + '</h3>' +
                        '<p class="svc-item-desc">' + (item.desc || '') + '</p>' +
                    '</div>';
                }).join('');
            }

            if (service.image) {
                serviceImage = resolveUrl(service.image, 'themes/workspace/coverflow/frame1.png');
            }

            if (Array.isArray(service.projects) && service.projects.length) {
                currentProjects = service.projects.map(function (p) {
                    return {
                        title: p.title || 'PROJECT',
                        area: p.area || '',
                        location: p.location || '',
                        typology: p.typology || '',
                        style: p.style || '',
                        tech: p.tech || '',
                        coverImg: resolveUrl(p.cover_image || p.coverImg, 'themes/workspace/coverflow/frame1.png'),
                        designImg: resolveUrl(p.design_image || p.designImg, 'themes/workspace/workflow/design.png'),
                        resultImg: resolveUrl(p.result_image || p.resultImg, 'themes/workspace/workflow/build.png')
                    };
                });
                currentProjectIndex = 0;
            }

            // Link "dịch vụ kế tiếp" ở góc phải dưới
            if (nextServiceBtn && typeof data.index === 'number' && serviceTitles.length) {
                const nextIdx = (data.index + 1) % serviceTitles.length;
                nextServiceBtn.setAttribute('data-next-index', String(nextIdx));
                nextServiceBtn.textContent = serviceTitles[nextIdx] || '';
            }

            // Dịch vụ mới luôn bắt đầu từ step 1 (Introductions)
            setStep('intro');
            renderProject(0);
        });

        // ---------------- Modal Before / After ----------------
        function ensureModalInBody() {
            const modal = document.getElementById('comparisonModal');
            if (modal && modal.parentElement !== document.body) document.body.appendChild(modal);
        }

        window.openComparisonModal = function (e) {
            if (e) { e.preventDefault(); e.stopPropagation(); }
            ensureModalInBody();
            const modal = document.getElementById('comparisonModal');
            if (!modal) return;

            const p = currentProjects[currentProjectIndex] || {};
            const d = document.getElementById('modalDesignImg');
            const r = document.getElementById('modalResultImg');
            if (d && p.designImg) d.src = p.designImg;
            if (r && p.resultImg) r.src = p.resultImg;

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            initModalSlider();
        };

        window.closeComparisonModal = function (e) {
            if (e) { e.preventDefault(); e.stopPropagation(); }
            const modal = document.getElementById('comparisonModal');
            if (!modal) return;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        };

        const openBtn = document.getElementById('openComparisonModalBtn');
        const closeBtn = document.getElementById('closeModalBtn');
        const backdrop = document.getElementById('closeModalBackdrop');
        if (openBtn) openBtn.addEventListener('click', window.openComparisonModal);
        if (closeBtn) closeBtn.addEventListener('click', window.closeComparisonModal);
        if (backdrop) backdrop.addEventListener('click', window.closeComparisonModal);

        let isSliderInit = false;
        function initModalSlider() {
            const container = document.getElementById('modalSlider');
            const beforeImage = document.getElementById('modalBeforeImage');
            const sliderHandle = document.getElementById('sliderHandle');
            const sliderLine = document.getElementById('sliderLine');
            if (!container || !beforeImage || !sliderHandle || !sliderLine) return;

            beforeImage.style.width = '50%';
            sliderHandle.style.left = '50%';
            sliderLine.style.left = '50%';

            if (isSliderInit) return;
            isSliderInit = true;

            let dragging = false;

            function update(clientX) {
                const rect = container.getBoundingClientRect();
                let x = Math.max(0, Math.min(clientX - rect.left, rect.width));
                const pct = (x / rect.width) * 100;
                beforeImage.style.width = pct + '%';
                sliderHandle.style.left = pct + '%';
                sliderLine.style.left = pct + '%';
            }

            container.addEventListener('mousedown', function (e) { dragging = true; update(e.clientX); });
            window.addEventListener('mousemove', function (e) { if (dragging) update(e.clientX); });
            window.addEventListener('mouseup', function () { dragging = false; });

            container.addEventListener('touchstart', function (e) {
                dragging = true;
                if (e.touches && e.touches[0]) update(e.touches[0].clientX);
            }, { passive: true });
            window.addEventListener('touchmove', function (e) {
                if (dragging && e.touches && e.touches[0]) update(e.touches[0].clientX);
            }, { passive: true });
            window.addEventListener('touchend', function () { dragging = false; });
        }
    })();
</script>
