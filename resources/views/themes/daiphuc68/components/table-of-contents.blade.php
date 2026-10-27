@props([
    'tocData' => null,
])

@php
    if (!$tocData || empty($tocData['enabled']) || empty($tocData['headings'])) {
        return;
    }

    $headings = $tocData['headings'];
    $config = $tocData['config'] ?? [];
    $title = !empty($config['title']) ? $config['title'] : 'Mục lục bài viết';
    $showNumbers = !empty($config['show_numbers']);
    $collapsible = !empty($config['collapsible']);
    $initialCollapsed = !empty($config['initial_collapsed']);
    $stickyToc = !empty($config['sticky_toc']);
    $smoothScroll = !empty($config['smooth_scroll']);
    $highlightActive = !empty($config['highlight_active']);
    $position = $config['position'] ?? 'before_content';
    $scrollOffset = (int)($config['scroll_offset'] ?? 100);
    $style = $config['style'] ?? 'style-1';
@endphp

{{-- Main Article Table of Contents (TOC) --}}
@if($style === 'style-1')
  {{-- Style 1: Modern Card --}}
  <nav class="w-full p-5 sm:p-6 bg-gradient-to-br from-sky-50/80 to-blue-50/50 rounded-2xl border border-sky-200/80 shadow-sm toc-container toc-style-1 flex flex-col gap-4" id="article-toc" aria-label="Table of contents">
    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-sky-100">
      <div class="flex items-center gap-3">
        <h3 class="text-[20px] sm:text-[22px] font-bold text-sky-950 tracking-tight flex items-center gap-2">
          <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h7"/></svg>
          {{ $title }}
        </h3>
        @if($collapsible)
          <button type="button" id="toc-collapse-toggle-btn" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold text-sky-700 bg-white hover:bg-sky-50 border border-sky-200 shadow-xs transition-colors cursor-pointer" aria-expanded="{{ $initialCollapsed ? 'false' : 'true' }}">
            <span id="toc-collapse-text">{{ $initialCollapsed ? 'Mở rộng' : 'Thu gọn' }}</span>
            <span id="toc-collapse-arrow" class="transition-transform duration-200">{{ $initialCollapsed ? '▼' : '▲' }}</span>
          </button>
        @endif
      </div>
      <span class="inline-flex items-center gap-2 text-[12px] font-medium text-sky-700 bg-white/80 px-3 py-1 rounded-full border border-sky-200 shadow-xs">
        <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
        Dòng thời gian tự động theo dõi khi cuộn
      </span>
    </div>
    <div class="flex flex-col gap-2.5 text-[14px] sm:text-[15px] transition-all duration-300" id="article-toc-list" style="{{ $initialCollapsed ? 'display: none;' : '' }}">
      @foreach($headings as $item)
        <div class="transition-all duration-150" style="padding-left: {{ max(0, ($item['level'] - 2) * 16) }}px">
          <a class="flex items-center gap-2 py-1.5 px-3 rounded-lg text-slate-700 hover:bg-white/80 hover:text-sky-700 font-medium transition-all" 
             href="#{{ $item['id'] }}" 
             data-toc-link="{{ $item['id'] }}">
            <span class="w-2 h-2 rounded-full bg-sky-400 shrink-0 transition-all duration-200" data-toc-dot="{{ $item['id'] }}"></span>
            @if($showNumbers)<span class="text-sky-600 font-bold shrink-0">{{ $item['number'] }}.</span>@endif
            <span>{{ $item['clean_text'] }}</span>
          </a>
        </div>
      @endforeach
    </div>
  </nav>

@elseif($style === 'style-3')
  {{-- Style 3: Classic Boxed --}}
  <nav class="w-full p-5 sm:p-6 bg-amber-50/70 border border-amber-200/90 rounded-xl toc-container toc-style-3 flex flex-col gap-4" id="article-toc" aria-label="Table of contents">
    <div class="flex flex-wrap items-center justify-between gap-3 pb-2 border-b border-amber-200/70 font-serif">
      <div class="flex items-center gap-3">
        <h3 class="text-[20px] sm:text-[22px] font-bold text-amber-950">{{ $title }}</h3>
        @if($collapsible)
          <button type="button" id="toc-collapse-toggle-btn" class="text-xs font-serif text-amber-800 hover:text-amber-950 cursor-pointer underline flex items-center gap-1" aria-expanded="{{ $initialCollapsed ? 'false' : 'true' }}">
            [<span id="toc-collapse-text">{{ $initialCollapsed ? 'Hiện' : 'Ẩn' }}</span>]
            <span id="toc-collapse-arrow" class="transition-transform duration-200">{{ $initialCollapsed ? '▼' : '▲' }}</span>
          </button>
        @endif
      </div>
      <span class="text-[12px] font-serif text-amber-800 italic">Mục lục chi tiết</span>
    </div>
    <div class="flex flex-col gap-2 text-[14px] sm:text-[15px] font-serif transition-all duration-300" id="article-toc-list" style="{{ $initialCollapsed ? 'display: none;' : '' }}">
      @foreach($headings as $item)
        <div style="padding-left: {{ max(0, ($item['level'] - 2) * 16) }}px">
          <a class="text-amber-900 hover:text-amber-700 hover:underline transition-all flex items-center gap-2 py-0.5" 
             href="#{{ $item['id'] }}" 
             data-toc-link="{{ $item['id'] }}">
            <span class="inline-block w-2 h-2 rounded-full bg-amber-600 shrink-0 transition-all duration-200" data-toc-dot="{{ $item['id'] }}"></span>
            @if($showNumbers)<span class="font-bold shrink-0">{{ $item['number'] }}.</span>@endif
            <span>{{ $item['clean_text'] }}</span>
          </a>
        </div>
      @endforeach
    </div>
  </nav>

@elseif($style === 'style-4')
  {{-- Style 4: Floating 3D --}}
  <nav class="w-full p-6 bg-white border border-neutral-200/90 rounded-2xl shadow-xl shadow-sky-50/60 toc-container toc-style-4 flex flex-col gap-4" id="article-toc" aria-label="Table of contents">
    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-neutral-100">
      <div class="flex items-center gap-3">
        <h3 class="text-[20px] sm:text-[22px] font-bold text-neutral-900 tracking-tight flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-sky-600"></span>
          {{ $title }}
        </h3>
        @if($collapsible)
          <button type="button" id="toc-collapse-toggle-btn" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold text-neutral-600 bg-neutral-100 hover:bg-neutral-200 transition-colors cursor-pointer" aria-expanded="{{ $initialCollapsed ? 'false' : 'true' }}">
            <span id="toc-collapse-text">{{ $initialCollapsed ? 'Mở rộng' : 'Thu gọn' }}</span>
            <span id="toc-collapse-arrow" class="transition-transform duration-200">{{ $initialCollapsed ? '▼' : '▲' }}</span>
          </button>
        @endif
      </div>
      <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-sky-100 text-sky-800">TOC</span>
    </div>
    <div class="flex flex-col gap-2.5 text-[14px] sm:text-[15px] transition-all duration-300" id="article-toc-list" style="{{ $initialCollapsed ? 'display: none;' : '' }}">
      @foreach($headings as $item)
        <div style="padding-left: {{ max(0, ($item['level'] - 2) * 16) }}px">
          <a class="flex items-center gap-2 py-1.5 px-3 rounded-xl text-neutral-700 hover:bg-neutral-50 hover:text-sky-600 font-medium transition-all" 
             href="#{{ $item['id'] }}" 
             data-toc-link="{{ $item['id'] }}">
            <span class="inline-block w-2 h-2 rounded-full bg-sky-400 shrink-0 transition-all duration-200" data-toc-dot="{{ $item['id'] }}"></span>
            @if($showNumbers)<span class="text-sky-600 font-bold shrink-0">{{ $item['number'] }}.</span>@endif
            <span>{{ $item['clean_text'] }}</span>
          </a>
        </div>
      @endforeach
    </div>
  </nav>

@else
  {{-- Style 2 (Default / Minimalist Timeline Style) --}}
  <nav class="w-full py-4 toc-container toc-style-2 flex flex-col gap-6" id="article-toc" aria-label="Table of contents" data-figma-id="200:7984">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <h3 class="text-[22px] sm:text-[24px] font-bold text-[#051650] tracking-tight">{{ $title }}</h3>
        @if($collapsible)
          <button type="button" id="toc-collapse-toggle-btn" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 transition-colors cursor-pointer" aria-expanded="{{ $initialCollapsed ? 'false' : 'true' }}">
            <span id="toc-collapse-text">{{ $initialCollapsed ? 'Mở rộng' : 'Thu gọn' }}</span>
            <span id="toc-collapse-arrow" class="transition-transform duration-200">{{ $initialCollapsed ? '▼' : '▲' }}</span>
          </button>
        @endif
      </div>
      <span class="inline-flex items-center gap-2 text-[12px] font-medium text-[#0284c7] bg-sky-50 px-3 py-1 rounded-full border border-sky-200">
        <span class="w-2 h-2 rounded-full bg-[#0284c7] animate-pulse"></span>
        Dòng thời gian tự động theo dõi khi cuộn
      </span>
    </div>
    <div class="relative pl-6 border-l-2 border-[#38bdf8] flex flex-col gap-6 text-[14px] sm:text-[15px] transition-all duration-300" id="article-toc-list" style="{{ $initialCollapsed ? 'display: none;' : '' }}">
      @foreach($headings as $item)
        <div class="relative transition-all duration-150" style="padding-left: {{ max(0, ($item['level'] - 2) * 16) }}px">
          <span class="absolute -left-[30px] top-1.5 w-2.5 h-2.5 rounded-full bg-[#0284c7] ring-4 ring-white transition-all duration-200" data-toc-dot="{{ $item['id'] }}"></span>
          <a class="font-bold uppercase tracking-wide text-[#051650] hover:text-[#0284c7] transition-all inline-flex items-center gap-1.5" 
             href="#{{ $item['id'] }}" 
             data-toc-link="{{ $item['id'] }}">
            @if($showNumbers)<span class="text-[#0284c7] font-bold">{{ $item['number'] }}.</span>@endif
            <span>{{ $item['clean_text'] }}</span>
          </a>
        </div>
      @endforeach
    </div>
  </nav>
@endif

{{-- Sticky Floating TOC (Góc phải màn hình khi cuộn) --}}
@if($stickyToc)
  <aside class="fixed bottom-6 right-6 z-40 transition-all duration-300 opacity-0 pointer-events-none translate-y-4 flex flex-col items-end gap-3" id="sticky-toc-container" aria-label="Mục lục nổi và dòng thời gian">
    <div class="hidden w-[300px] sm:w-[340px] max-h-[70vh] overflow-y-auto bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-neutral-200/90 p-5 transition-all duration-300 origin-bottom-right flex-col gap-4" id="sticky-toc-panel">
      <div class="flex items-center justify-between pb-3 border-b border-neutral-100">
        <div class="flex items-center gap-2">
          <span class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#0284c7]"></span>
          </span>
          <span class="text-[13px] font-bold uppercase tracking-wider text-[#051650]">{{ $title }}</span>
        </div>
        <button class="text-neutral-400 hover:text-neutral-700 p-1 transition-colors rounded-lg hover:bg-neutral-100 cursor-pointer" id="sticky-toc-close-btn" aria-label="Đóng bảng mục lục">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="2" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      <div class="relative pl-5 border-l-2 border-[#38bdf8] flex flex-col gap-4 text-[13px]" id="sticky-toc-tree">
        @foreach($headings as $item)
          <div class="relative group" data-sticky-section="{{ $item['id'] }}" style="padding-left: {{ max(0, ($item['level'] - 2) * 12) }}px">
            <span class="absolute -left-[27px] top-1 w-2.5 h-2.5 rounded-full bg-[#0284c7] ring-4 ring-white transition-all duration-200" data-sticky-dot="{{ $item['id'] }}"></span>
            <a class="font-bold uppercase tracking-wide text-[#051650] hover:text-[#0284c7] transition-all inline-flex items-center gap-1 leading-tight" 
               href="#{{ $item['id'] }}" 
               data-sticky-link="{{ $item['id'] }}">
              @if($showNumbers)<span class="text-[#0284c7]">{{ $item['number'] }}.</span>@endif
              <span>{{ $item['clean_text'] }}</span>
            </a>
          </div>
        @endforeach
      </div>
      <div class="pt-3 border-t border-neutral-100 flex items-center justify-between text-[11px] text-neutral-500">
        <span>Tiến độ đọc:</span>
        <span class="font-bold text-[#0284c7]" id="sticky-toc-progress-pct">0%</span>
      </div>
      <div class="w-full h-1.5 bg-neutral-100 rounded-full overflow-hidden">
        <div class="h-full bg-gradient-to-r from-[#38bdf8] to-[#0284c7] transition-all duration-150 rounded-full" id="sticky-toc-progress-bar" style="width: 0%"></div>
      </div>
    </div>
 
  </aside>
@endif

{{-- Dynamic TOC Controller (ScrollSpy, SmoothScroll, Collapse, Sticky, Positioning) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  const tocNav = document.getElementById('article-toc');
  const stickyContainer = document.getElementById('sticky-toc-container');
  const stickyToggleBtn = document.getElementById('sticky-toc-toggle-btn');
  const stickyPanel = document.getElementById('sticky-toc-panel');
  const stickyCloseBtn = document.getElementById('sticky-toc-close-btn');
  const stickyToggleIcon = document.getElementById('sticky-toc-toggle-icon');
  const stickyActiveLabel = document.getElementById('sticky-toc-active-label');
  const stickyProgressBar = document.getElementById('sticky-toc-progress-bar');
  const stickyProgressPct = document.getElementById('sticky-toc-progress-pct');
  const collapseToggleBtn = document.getElementById('toc-collapse-toggle-btn');
  const tocList = document.getElementById('article-toc-list');
  const collapseText = document.getElementById('toc-collapse-text');
  const collapseArrow = document.getElementById('toc-collapse-arrow');

  const configPosition = @json($position);
  const isSmoothScroll = @json($smoothScroll);
  const isHighlightActive = @json($highlightActive);
  const scrollOffset = @json($scrollOffset);
  const currentStyle = @json($style);
  const isInitialCollapsed = @json($initialCollapsed);

  // 1. Dynamic In-Article TOC Positioning (before_content / after_first_heading / manual)
  if (tocNav) {
    const placeholder = document.getElementById('toc-placeholder');
    if (placeholder) {
      placeholder.parentNode.insertBefore(tocNav, placeholder);
      placeholder.remove();
    } else if (configPosition === 'after_first_heading') {
      const articleBody = document.querySelector('.prose') || document.querySelector('article') || document.querySelector('.page-main-content');
      if (articleBody) {
        const firstHeading = articleBody.querySelector('h1, h2, h3, h4, h5, h6');
        if (firstHeading) {
          firstHeading.insertAdjacentElement('afterend', tocNav);
        }
      }
    } else if (configPosition === 'manual') {
      // Manual shortcode was not found in content, hide in-article TOC
      tocNav.style.display = 'none';
    }
  }

  // 2. Collapse / Expand In-Article TOC
  if (collapseToggleBtn && tocList) {
    let isCollapsed = isInitialCollapsed;
    collapseToggleBtn.addEventListener('click', function(e) {
      e.preventDefault();
      isCollapsed = !isCollapsed;
      if (isCollapsed) {
        tocList.style.display = 'none';
        collapseToggleBtn.setAttribute('aria-expanded', 'false');
        if (collapseText) collapseText.textContent = (currentStyle === 'style-3') ? 'Hiện' : 'Mở rộng';
        if (collapseArrow) collapseArrow.textContent = '▼';
      } else {
        tocList.style.display = '';
        collapseToggleBtn.setAttribute('aria-expanded', 'true');
        if (collapseText) collapseText.textContent = (currentStyle === 'style-3') ? 'Ẩn' : 'Thu gọn';
        if (collapseArrow) collapseArrow.textContent = '▲';
      }
    });
  }

  // 3. Smooth Scroll / Standard Scroll To Heading on Click
  document.querySelectorAll('#article-toc a[href^="#"], #sticky-toc-tree a[href^="#"]').forEach(link => {
    link.addEventListener('click', function(e) {
      const targetId = this.getAttribute('href').substring(1);
      const targetEl = document.getElementById(targetId);
      if (targetEl) {
        e.preventDefault();
        const elementPosition = targetEl.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - (scrollOffset || 100);
        window.scrollTo({
          top: offsetPosition,
          behavior: isSmoothScroll ? 'smooth' : 'auto'
        });
        if (history.pushState) {
          history.pushState(null, null, '#' + targetId);
        }
        if (stickyPanel && !stickyPanel.classList.contains('hidden') && window.innerWidth < 768) {
          stickyPanel.classList.add('hidden');
          if (stickyToggleIcon) stickyToggleIcon.style.transform = 'rotate(0deg)';
          if (stickyToggleBtn) stickyToggleBtn.setAttribute('aria-expanded', 'false');
        }
      }
    });
  });

  // 4. Sticky Panel Toggle
  if (stickyToggleBtn && stickyPanel) {
    stickyToggleBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      const isHidden = stickyPanel.classList.contains('hidden');
      if (isHidden) {
        stickyPanel.classList.remove('hidden');
        if (stickyToggleIcon) stickyToggleIcon.style.transform = 'rotate(180deg)';
        stickyToggleBtn.setAttribute('aria-expanded', 'true');
      } else {
        stickyPanel.classList.add('hidden');
        if (stickyToggleIcon) stickyToggleIcon.style.transform = 'rotate(0deg)';
        stickyToggleBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }

  if (stickyCloseBtn && stickyPanel) {
    stickyCloseBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      stickyPanel.classList.add('hidden');
      if (stickyToggleIcon) stickyToggleIcon.style.transform = 'rotate(0deg)';
      if (stickyToggleBtn) stickyToggleBtn.setAttribute('aria-expanded', 'false');
    });
  }

  document.addEventListener('click', function(e) {
    if (stickyPanel && !stickyPanel.classList.contains('hidden')) {
      if (stickyContainer && !stickyContainer.contains(e.target)) {
        stickyPanel.classList.add('hidden');
        if (stickyToggleIcon) stickyToggleIcon.style.transform = 'rotate(0deg)';
        if (stickyToggleBtn) stickyToggleBtn.setAttribute('aria-expanded', 'false');
      }
    }
  });

  // 5. Scroll Spy & Progress Tracking
  const headingElements = Array.from(document.querySelectorAll('#article-toc a[href^="#"]'))
    .map(a => document.getElementById(a.getAttribute('href').substring(1)))
    .filter(Boolean);

  function onScroll() {
    // Show/hide floating sticky TOC
    if (stickyContainer && tocNav) {
      const tocRect = tocNav.getBoundingClientRect();
      if (tocRect.bottom < 50) {
        stickyContainer.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
        stickyContainer.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
      } else {
        stickyContainer.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
        stickyContainer.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
      }
    }

    // Reading progress
    const article = document.querySelector('article') || document.querySelector('.prose') || document.querySelector('main');
    if (article) {
      const rect = article.getBoundingClientRect();
      const totalHeight = rect.height - window.innerHeight;
      const progress = Math.min(100, Math.max(0, Math.round((-rect.top / Math.max(1, totalHeight)) * 100)));
      if (stickyProgressPct) stickyProgressPct.textContent = progress + '%';
      if (stickyProgressBar) stickyProgressBar.style.width = progress + '%';
    }

    // Scroll spy: active heading
    if (!isHighlightActive) return;

    let currentId = null;
    let currentTitle = '';
    const spyOffset = (scrollOffset || 100) + 30;
    for (let i = 0; i < headingElements.length; i++) {
      const el = headingElements[i];
      const top = el.getBoundingClientRect().top;
      if (top <= spyOffset) {
        currentId = el.id;
        currentTitle = el.textContent.trim();
      } else {
        break;
      }
    }

    if (!currentId && headingElements.length > 0) {
      currentId = headingElements[0].id;
      currentTitle = headingElements[0].textContent.trim();
    }

    if (currentId) {
      if (stickyActiveLabel) {
        const clean = currentTitle.replace(/^\d+([.\-]\d+)*[.\s\-:]+/g, '').trim();
        stickyActiveLabel.textContent = clean || currentTitle;
      }

      // Update in-page dots and links according to each specific style
      document.querySelectorAll('[data-toc-dot]').forEach(dot => {
        const isCurrent = (dot.getAttribute('data-toc-dot') === currentId);
        if (currentStyle === 'style-1') {
          if (isCurrent) {
            dot.className = 'w-2.5 h-2.5 rounded-full bg-sky-600 scale-125 ring-2 ring-sky-300 shrink-0 transition-all duration-200';
          } else {
            dot.className = 'w-2 h-2 rounded-full bg-sky-400 shrink-0 transition-all duration-200';
          }
        } else if (currentStyle === 'style-3') {
          if (isCurrent) {
            dot.className = 'inline-block w-2.5 h-2.5 rounded-full bg-amber-800 scale-125 ring-2 ring-amber-400 shrink-0 transition-all duration-200';
          } else {
            dot.className = 'inline-block w-2 h-2 rounded-full bg-amber-600 shrink-0 transition-all duration-200';
          }
        } else if (currentStyle === 'style-4') {
          if (isCurrent) {
            dot.className = 'inline-block w-2.5 h-2.5 rounded-full bg-sky-600 scale-125 ring-2 ring-sky-300 shrink-0 transition-all duration-200';
          } else {
            dot.className = 'inline-block w-2 h-2 rounded-full bg-sky-400 shrink-0 transition-all duration-200';
          }
        } else {
          // Style 2 (Timeline)
          if (isCurrent) {
            dot.className = 'absolute -left-[30px] top-1.5 w-3 h-3 rounded-full bg-[#0284c7] ring-4 ring-sky-300 scale-125 transition-all duration-300';
          } else {
            dot.className = 'absolute -left-[30px] top-1.5 w-2.5 h-2.5 rounded-full bg-[#0284c7] ring-4 ring-white transition-all duration-300';
          }
        }
      });

      document.querySelectorAll('[data-toc-link]').forEach(link => {
        const isCurrent = (link.getAttribute('data-toc-link') === currentId);
        if (currentStyle === 'style-1') {
          if (isCurrent) {
            link.classList.add('bg-white', 'text-sky-700', 'font-bold', 'shadow-xs', 'border', 'border-sky-100', 'ring-1', 'ring-sky-200');
            link.classList.remove('text-slate-700');
          } else {
            link.classList.remove('bg-white', 'text-sky-700', 'font-bold', 'shadow-xs', 'border', 'border-sky-100', 'ring-1', 'ring-sky-200');
            link.classList.add('text-slate-700');
          }
        } else if (currentStyle === 'style-3') {
          if (isCurrent) {
            link.classList.add('text-amber-950', 'font-bold', 'underline', 'decoration-amber-600', 'decoration-2');
            link.classList.remove('text-amber-900');
          } else {
            link.classList.remove('text-amber-950', 'font-bold', 'underline', 'decoration-amber-600', 'decoration-2');
            link.classList.add('text-amber-900');
          }
        } else if (currentStyle === 'style-4') {
          if (isCurrent) {
            link.classList.add('bg-sky-50', 'text-sky-700', 'font-bold', 'shadow-xs');
            link.classList.remove('text-neutral-700');
          } else {
            link.classList.remove('bg-sky-50', 'text-sky-700', 'font-bold', 'shadow-xs');
            link.classList.add('text-neutral-700');
          }
        } else {
          // Style 2 (Timeline)
          if (isCurrent) {
            link.classList.add('text-[#0284c7]');
            link.classList.remove('text-[#051650]');
          } else {
            link.classList.remove('text-[#0284c7]');
            link.classList.add('text-[#051650]');
          }
        }
      });

      // Update sticky dots and links
      document.querySelectorAll('[data-sticky-dot]').forEach(dot => {
        if (dot.getAttribute('data-sticky-dot') === currentId) {
          dot.className = 'absolute -left-[27px] top-1 w-3 h-3 rounded-full bg-[#0284c7] ring-4 ring-sky-300 scale-125 transition-all duration-300';
        } else {
          dot.className = 'absolute -left-[27px] top-1 w-2.5 h-2.5 rounded-full bg-[#0284c7] ring-4 ring-white transition-all duration-300';
        }
      });
      document.querySelectorAll('[data-sticky-link]').forEach(link => {
        if (link.getAttribute('data-sticky-link') === currentId) {
          link.classList.add('text-[#0284c7]');
          link.classList.remove('text-[#051650]');
        } else {
          link.classList.remove('text-[#0284c7]');
          link.classList.add('text-[#051650]');
        }
      });
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  setTimeout(onScroll, 200);
});
</script>
