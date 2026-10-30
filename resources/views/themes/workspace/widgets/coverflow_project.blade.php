@php
  use App\Http\Controllers\CoverflowController;
  use Illuminate\Support\Str;

  $settings = CoverflowController::getServiceDetailSettings($settings ?? null);
  $services = $services ?? CoverflowController::getServicesData($settings);
  $activeSlug = session('active_service_slug') ?: (request()->query('service') ?: ($services[0]['slug'] ?? ''));
  
  $activeService = $activeService ?? null;
  if (!$activeService) {
      foreach ($services as $s) {
          if ($s['slug'] === $activeSlug) {
              $activeService = $s;
              break;
          }
      }
  }
  $activeService = $activeService ?? ($services[0] ?? []);
  $slug = $activeService['slug'] ?? '';
  
  $projects = $projects ?? ($activeService['projects'] ?? []);
  $specFields = \App\Http\Controllers\Admin\ServiceController::getSpecFields();
  
  $formatImg = function (?string $path) {
      if (empty($path)) return '';
      if (Str::startsWith($path, ['http://', 'https://', '/'])) {
          return $path;
      }
      return asset($path);
  };

  if (!isset($projectsFormatted)) {
      $projectsFormatted = array_map(function ($p) use ($formatImg, $specFields) {
          return [
              'title' => $p['title'] ?? 'PROJECT',
              'area' => $p['area'] ?? '',
              'location' => $p['location'] ?? '',
              'typology' => $p['typology'] ?? '',
              'style' => $p['style'] ?? '',
              'tech' => $p['tech'] ?? '',
              'specs' => \App\Http\Controllers\Admin\ServiceController::normalizeProjectSpecs($p, $specFields),
              'coverImg' => $formatImg($p['cover_image'] ?? ($p['coverImg'] ?? 'themes/workspace/coverflow/frame1.png')),
              'designImg' => $formatImg($p['design_image'] ?? ($p['designImg'] ?? 'themes/workspace/workflow/design.png')),
              'resultImg' => $formatImg($p['result_image'] ?? ($p['resultImg'] ?? 'themes/workspace/workflow/build.png')),
          ];
      }, $projects);
  }

  $firstProject = !empty($projectsFormatted[0]) ? $projectsFormatted[0] : [
      'title' => 'PROJECT 01',
      'area' => '125 m²',
      'location' => 'District 2, Ho Chi Minh City',
      'typology' => 'Office',
      'style' => 'Modern',
      'tech' => 'Integrated Smart-home system',
      'specs' => \App\Http\Controllers\Admin\ServiceController::normalizeProjectSpecs([
          'area' => '125 m²',
          'location' => 'District 2, Ho Chi Minh City',
          'typology' => 'Office',
          'style' => 'Modern',
          'tech' => 'Integrated Smart-home system',
      ], $specFields),
      'coverImg' => asset('themes/workspace/coverflow/frame1.png'),
      'designImg' => asset('themes/workspace/workflow/design.png'),
      'resultImg' => asset('themes/workspace/workflow/build.png')
  ];
@endphp

<style>
    /* ========================================================================= */
    /* SMOOTH BEFORE-AFTER SLIDER (CodePen YzmPrYQ Component)                    */
    /* ========================================================================= */
    .slider-container {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
        user-select: none;
        -webkit-user-select: none;
        cursor: ew-resize;
        background-color: #0b0f19;
        border-radius: 14px;
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
        -webkit-user-drag: none;
    }

    .before-image {
        z-index: 2;
        clip-path: polygon(0 0, 50% 0, 50% 100%, 0 100%);
        will-change: clip-path;
    }

    .after-image {
        z-index: 1;
    }

    .before-text,
    .after-text {
        position: absolute;
        bottom: 24px;
        padding: 9px 20px;
        background: rgba(15, 23, 42, 0.78);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        color: #ffffff;
        font-family: 'Roboto', sans-serif;
        font-size: 12px;
        font-weight: 700;
        border-radius: 999px;
        letter-spacing: 1px;
        z-index: 3;
        pointer-events: none;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.18);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
    }

    .before-text {
        left: 24px;
    }

    .after-text {
        right: 24px;
    }

    .dot-indicator {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background-color: #EB2026;
        display: inline-block;
    }

    .slider-handle {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 60px;
        transform: translateX(-50%);
        cursor: ew-resize;
        z-index: 5;
    }

    .slider-line {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 2px;
        background: #ffffff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        pointer-events: none;
        z-index: 4;
    }

    .pulse-container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        z-index: 6;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pulse-container svg {
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.35));
    }

    /* Modal Box Wrapper & Backdrop */
    .comparison-modal {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .comparison-modal.is-open {
        display: flex;
    }

    .comparison-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(10, 10, 12, 0.88);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .comparison-modal-wrapper {
        position: relative;
        z-index: 2;
        width: min(92vw, 1380px);
        height: min(88vh, 840px);
        max-width: 1400px;
        max-height: 860px;
        background: #0f131a;
        border-radius: 16px;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.12);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: modalScaleIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    @keyframes modalScaleIn {
        from {
            opacity: 0;
            transform: scale(0.94);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .comparison-modal-close {
        position: absolute;
        top: 18px;
        right: 18px;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #ffffff;
        border-radius: 50%;
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
        padding: 0;
        box-sizing: border-box;
    }
</style>

<div class="coverflowDetail-pane coverflowDetail-pane-project" id="coverflowProject" data-ui="light" aria-label="Service Projects">
    <div class="surface coverflowProject-surface">
        <div class="container">
            <div class="coverflowProject-stage">
                <!-- Cột trái: 1 hình ảnh đại diện dự án duy nhất -->
                <div class="coverflowProject-media">
                    <img src="{{ $firstProject['coverImg'] }}" class="coverflowProject-image" id="projectCoverImage" alt="{{ $firstProject['title'] }}">
                </div>

                <!-- Cột phải: Thông số chi tiết dự án -->
                <div class="coverflowProject-details">
                    <div class="coverflowProject-header">
                        <a href="#coverflowService" class="coverflowService-badge" id="coverflowProjectBackBtn" style="display:inline-flex; align-items:center; gap:6px; margin-bottom:12px; text-decoration:none;" title="Quay lại chi tiết dịch vụ">
                            <span class="coverflowService-badge-text" id="projectServiceBadge">{{ $activeService['title'] ?? 'Services' }}</span>
                        </a>
                        <div class="coverflowProject-heading">
                            <h2 class="coverflowProject-title" id="projectTitle">{{ $firstProject['title'] }}</h2>
                        </div>
                        <span class="coverflowProject-separator"></span>
                    </div>

                    <!-- Bảng thông tin dự án (Repeatable) -->
                    <div class="coverflowProject-info" id="coverflowProjectSpecsList">
                        @foreach(($firstProject['specs'] ?? []) as $specRow)
                            <div class="coverflowProject-info-row">
                                <span class="coverflowProject-info-label">{{ $specRow['label'] ?? '' }}</span>
                                <span class="coverflowProject-info-value">{{ $specRow['value'] ?? '' }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Nút Mở Modal So sánh Before-After -->
                    <div class="coverflowProject-action">
                        <a href="javascript:void(0)" class="coverflowProject-btn" id="openComparisonModalBtn" onclick="window.openComparisonModal && window.openComparisonModal(event)">
                            <span class="coverflowProject-btn-text">{{ $settings['compare_button_text'] ?? 'Comparison Design and Result' }}</span>
                        </a>
                    </div>

                    <!-- Điều khiển Slider (Progress Bar + 2 Nút Mũi Tên) -->
                    <div class="coverflowProject-controls">
                        <div class="coverflowProject-progress">
                            <div class="coverflowProject-progress-bar"></div>
                        </div>
                        <div class="coverflowProject-arrows">
                            <button type="button" class="coverflowProject-arrow prev" aria-label="Previous project">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                    <path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button type="button" class="coverflowProject-arrow next" aria-label="Next project">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                    <path d="M6 3L11 8L6 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thanh điều hướng đáy (Navigation Tabs) -->
            <div class="coverflowProject-nav">
                <a href="#coverflowService" class="coverflowService-nav-tab coverflowProject-nav-tab" data-pane="intro" title="Xem thông tin giới thiệu dịch vụ">
                    <span class="coverflowProject-nav-text">INTRODUCTIONS</span>
                </a>
                <a href="#coverflowProject" class="coverflowService-nav-tab coverflowProject-nav-tab is-active" data-pane="project" title="Xem các dự án thực tế của dịch vụ này">
                    <span class="coverflowProject-nav-text">PROJECTS</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- COMPARISON MODAL BOX Ở GIỮA (Chỉ có 2 hình Before-After Slider)            -->
<!-- ========================================================================= -->
<div id="comparisonModal" class="comparison-modal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="comparison-modal-backdrop" id="closeModalBackdrop" onclick="window.closeComparisonModal && window.closeComparisonModal(event)"></div>
    <div class="comparison-modal-wrapper">
        <!-- Nút đóng X -->
        <button type="button" class="comparison-modal-close" id="closeModalBtn" onclick="window.closeComparisonModal && window.closeComparisonModal(event)" aria-label="Close comparison">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <!-- Modal Box: Chỉ gồm 2 hình (Design vs Result) với thanh trượt ở giữa -->
        <div class="comparison-modal-box">
            <div class="slider-container" id="modalSlider">
                <!-- Hình 1: Design (Before) -->
                <div class="before-image">
                    <img src="{{ !empty($firstProject['designImg']) ? $firstProject['designImg'] : asset('themes/workspace/workflow/design.png') }}" id="modalDesignImg" alt="Design 3D">
                </div>

                <!-- Hình 2: Result (After) -->
                <div class="after-image">
                    <img src="{{ !empty($firstProject['resultImg']) ? $firstProject['resultImg'] : asset('themes/workspace/workflow/build.png') }}" id="modalResultImg" alt="Result Thực Tế">
                </div>

                <!-- Thanh trượt & Nút kéo -->
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
        function ensureModalInBody() {
            const modalEl = document.getElementById('comparisonModal');
            if (modalEl && modalEl.parentNode !== document.body) {
                document.body.appendChild(modalEl);
            }
            return modalEl;
        }

        const fallbackDesign = "{{ asset('themes/workspace/workflow/design.png') }}";
        const fallbackResult = "{{ asset('themes/workspace/workflow/build.png') }}";
        const fallbackCover = "{{ asset('themes/workspace/coverflow/frame1.png') }}";
        const defaultSpecFields = @json($specFields);

        const defaultProjects = [
            {
                title: 'PROJECT 01',
                area: '125 m²',
                location: 'District 2, Ho Chi Minh City',
                typology: 'Office',
                style: 'Modern',
                tech: 'Integrated Smart-home system',
                coverImg: fallbackCover,
                designImg: fallbackDesign,
                resultImg: fallbackResult
            },
            {
                title: 'PROJECT 02',
                area: '210 m²',
                location: 'District 1, Ho Chi Minh City',
                typology: 'Penthouse',
                style: 'Minimalist Luxury',
                tech: 'Smart Lighting & HVAC Control',
                coverImg: "{{ asset('themes/workspace/build2.png') }}",
                designImg: "{{ asset('themes/workspace/workflow/furniture-design.png') }}",
                resultImg: "{{ asset('themes/workspace/build2.png') }}"
            }
        ];

        let rawProjects = @json(!empty($projectsFormatted) ? $projectsFormatted : []);
        let projects = (Array.isArray(rawProjects) && rawProjects.length > 0) ? rawProjects : defaultProjects;
        let currentSlide = 0;
        let totalSlides = projects.length;

        const prevBtn = document.querySelector('#coverflowProject .coverflowProject-arrow.prev');
        const nextBtn = document.querySelector('#coverflowProject .coverflowProject-arrow.next');
        const progressBar = document.querySelector('#coverflowProject .coverflowProject-progress-bar');

        const elCoverImg = document.getElementById('projectCoverImage');
        const elTitle = document.getElementById('projectTitle');
        const elSpecsList = document.getElementById('coverflowProjectSpecsList');
        const elBadge = document.getElementById('projectServiceBadge');

        const elModalDesignImg = document.getElementById('modalDesignImg');
        const elModalResultImg = document.getElementById('modalResultImg');

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function buildSpecsList(p) {
            if (Array.isArray(p.specs) && p.specs.length) {
                return p.specs;
            }
            return (defaultSpecFields || []).map(function (sf) {
                return {
                    key: sf.key,
                    label: sf.label,
                    value: p[sf.key] || ''
                };
            });
        }

        function updateProject(index) {
            if (!projects || projects.length === 0) projects = defaultProjects;
            totalSlides = projects.length;
            currentSlide = (index + totalSlides) % totalSlides;
            const p = projects[currentSlide] || defaultProjects[0];

            if (progressBar) {
                const widthPercent = Math.max(25, ((currentSlide + 1) / totalSlides) * 100);
                progressBar.style.width = widthPercent + '%';
            }

            if (elTitle) elTitle.textContent = p.title || 'PROJECT';
            if (elSpecsList) {
                const specs = buildSpecsList(p);
                elSpecsList.innerHTML = specs.map(function (row) {
                    return '<div class="coverflowProject-info-row">' +
                        '<span class="coverflowProject-info-label">' + escapeHtml(row.label) + '</span>' +
                        '<span class="coverflowProject-info-value">' + escapeHtml(row.value) + '</span>' +
                    '</div>';
                }).join('');
            }

            if (elCoverImg) elCoverImg.src = p.coverImg || fallbackCover;
            if (elModalDesignImg) elModalDesignImg.src = p.designImg || fallbackDesign;
            if (elModalResultImg) elModalResultImg.src = p.resultImg || fallbackResult;

            if (window.sliderController) {
                window.sliderController.initCenter(false);
            }
        }

        if (prevBtn) prevBtn.addEventListener('click', () => updateProject(currentSlide - 1));
        if (nextBtn) nextBtn.addEventListener('click', () => updateProject(currentSlide + 1));

        window.addEventListener('workspace:service-changed', function (event) {
            const data = event.detail;
            if (!data || !data.service) return;
            const service = data.service;

            if (elBadge && service.title) {
                elBadge.textContent = service.title;
            }

            if (Array.isArray(service.projects) && service.projects.length > 0) {
                projects = service.projects.map(function (p) {
                    return {
                        title: p.title || 'PROJECT',
                        area: p.area || '',
                        location: p.location || '',
                        typology: p.typology || '',
                        style: p.style || '',
                        tech: p.tech || '',
                        specs: Array.isArray(p.specs) && p.specs.length ? p.specs : buildSpecsList(p),
                        coverImg: p.cover_image || p.coverImg || service.image || fallbackCover,
                        designImg: p.design_image || p.designImg || fallbackDesign,
                        resultImg: p.result_image || p.resultImg || fallbackResult
                    };
                });
                updateProject(0);
            }
        });

        function setupSlider() {
            const sliderContainer = document.getElementById('modalSlider');
            const sliderHandle = document.getElementById('sliderHandle');
            const sliderLine = document.getElementById('sliderLine') || (sliderContainer ? sliderContainer.querySelector('.slider-line') : null);
            const beforeImage = sliderContainer ? sliderContainer.querySelector('.before-image') : null;

            if (!sliderContainer || !sliderHandle || !sliderLine || !beforeImage) {
                return null;
            }

            let isDragging = false;

            function updateSliderPosition(clientX) {
                const rect = sliderContainer.getBoundingClientRect();
                if (rect.width === 0) return;
                let posX = clientX - rect.left;
                if (posX < 0) posX = 0;
                if (posX > rect.width) posX = rect.width;

                const percentage = (posX / rect.width) * 100;
                sliderHandle.style.left = percentage + '%';
                sliderLine.style.left = percentage + '%';
                beforeImage.style.clipPath = `polygon(0 0, ${percentage}% 0, ${percentage}% 100%, 0 100%)`;
            }

            function initCenter(animate = true) {
                const rect = sliderContainer.getBoundingClientRect();
                if (rect.width === 0) return;
                const centerPos = rect.left + rect.width / 2;

                if (animate) {
                    sliderHandle.style.transition = 'left 0.4s cubic-bezier(0.2, 0.8, 0.2, 1)';
                    sliderLine.style.transition = 'left 0.4s cubic-bezier(0.2, 0.8, 0.2, 1)';
                    beforeImage.style.transition = 'clip-path 0.4s cubic-bezier(0.2, 0.8, 0.2, 1)';

                    updateSliderPosition(centerPos);

                    setTimeout(() => {
                        sliderHandle.style.transition = 'none';
                        sliderLine.style.transition = 'none';
                        beforeImage.style.transition = 'none';
                    }, 420);
                } else {
                    sliderHandle.style.transition = 'none';
                    sliderLine.style.transition = 'none';
                    beforeImage.style.transition = 'none';
                    updateSliderPosition(centerPos);
                }
            }

            sliderHandle.addEventListener('mousedown', (e) => {
                isDragging = true;
                e.preventDefault();
            });

            sliderContainer.addEventListener('mousedown', (e) => {
                isDragging = true;
                updateSliderPosition(e.clientX);
                e.preventDefault();
            });

            window.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                updateSliderPosition(e.clientX);
            });

            window.addEventListener('mouseup', () => {
                isDragging = false;
            });

            sliderHandle.addEventListener('touchstart', () => { isDragging = true; }, { passive: true });
            sliderContainer.addEventListener('touchstart', (e) => {
                isDragging = true;
                if (e.touches && e.touches[0]) updateSliderPosition(e.touches[0].clientX);
            }, { passive: true });

            window.addEventListener('touchmove', (e) => {
                if (!isDragging) return;
                if (e.touches && e.touches[0]) updateSliderPosition(e.touches[0].clientX);
            }, { passive: true });

            window.addEventListener('touchend', () => { isDragging = false; });
            window.addEventListener('resize', () => { initCenter(false); });

            return { initCenter, updateSliderPosition };
        }

        window.openComparisonModal = function (e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const modalEl = ensureModalInBody();
            if (!modalEl) return;

            modalEl.classList.add('is-open');
            modalEl.setAttribute('aria-hidden', 'false');

            if (!window.sliderController) {
                window.sliderController = setupSlider();
            }

            requestAnimationFrame(() => {
                setTimeout(() => {
                    if (window.sliderController) {
                        window.sliderController.initCenter(true);
                    }
                }, 50);
            });
        };

        window.closeComparisonModal = function (e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const modalEl = document.getElementById('comparisonModal');
            if (!modalEl) return;
            modalEl.classList.remove('is-open');
            modalEl.setAttribute('aria-hidden', 'true');
        };

        const openBtn = document.getElementById('openComparisonModalBtn');
        if (openBtn) {
            openBtn.addEventListener('click', window.openComparisonModal);
        }

        const closeBtn = document.getElementById('closeModalBtn');
        if (closeBtn) {
            closeBtn.addEventListener('click', window.closeComparisonModal);
        }

        const backdrop = document.getElementById('closeModalBackdrop');
        if (backdrop) {
            backdrop.addEventListener('click', window.closeComparisonModal);
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const modalEl = document.getElementById('comparisonModal');
                if (modalEl && modalEl.classList.contains('is-open')) {
                    window.closeComparisonModal();
                }
            }
        });

        const projectBackBtn = document.getElementById('coverflowProjectBackBtn');
        if (projectBackBtn) {
            projectBackBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof window.setDetailPane === 'function') {
                    window.setDetailPane('intro', true);
                } else if (typeof window.goToSectionById === 'function') {
                    window.goToSectionById('coverflowService');
                } else {
                    window.location.hash = '#coverflowService';
                }
            });
        }

        ensureModalInBody();
        updateProject(0);
        window.sliderController = setupSlider();
    })();
</script>
