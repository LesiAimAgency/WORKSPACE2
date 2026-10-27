{{-- Figma Layout Grid Overlay (2 Cấu hình chuẩn từ Figma: Grid_Laptop 12 Columns & Grid 6 Rows) --}}
{{-- 
  CẤU HÌNH 1: Grid_Laptop (Columns)
  - Type: Columns | Count: 12 | Color: #FF0000 (10% - 50%)
  - Stretch: Margin 72px (trái & phải) | Gutter 36px (giữa các cột) | Khung chuẩn: 1440px
  
  CẤU HÌNH 2: Grid (Rows)
  - Type: Rows | Count: 6 | Color: #FF0000 (10% - 50%)
  - Stretch: Margin 72px (trên & dưới) | Gutter 40px (giữa các hàng) | Chiều cao: 100vh / 900px
--}}

@if(config('app.figma_layout_guide', env('FIGMA_LAYOUT_GUIDE', false)))
{{-- 1. LỚP OVERLAY TOÀN MÀN HÌNH VỚI Z-INDEX CAO NHẤT (2147483640) ĐÈ LÊN MỌI SECTION (KỂ CẢ HERO) --}}
<div id="figma-grid-root" 
     class="figma-grid-root-container" 
     style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100%; max-width: 100%; height: 100vh; pointer-events: none; z-index: 2147483640; overflow: hidden; margin: 0; padding: 0;" 
     aria-hidden="true">
  
  {{-- 1.1 Lớp Cột Dọc (12 CỘT: Grid_Laptop) --}}
  <div id="figma-cols-overlay" class="fgo-layer fgo-cols-layer">
    <div class="fgo-cols-container">
      @for ($i = 1; $i <= 12; $i++)
        <div class="fgo-col-track">
          <span class="fgo-col-badge">{{ $i }}</span>
        </div>
      @endfor
    </div>
  </div>

  {{-- 1.2 Lớp Hàng Ngang (6 HÀNG: Grid) --}}
  <div id="figma-rows-overlay" class="fgo-layer fgo-rows-layer fgo-hidden">
    <div class="fgo-rows-container">
      @for ($r = 1; $r <= 6; $r++)
        <div class="fgo-row-track">
          <span class="fgo-row-badge">R{{ $r }}</span>
        </div>
      @endfor
    </div>
  </div>

</div>

{{-- 2. BẢNG ĐIỀU KHIỂN NỔI (CONTROL PANEL) VỚI Z-INDEX 2147483647 --}}
<div id="figma-grid-panel" 
     class="figma-panel-wrapper" 
     style="position: fixed; bottom: 24px; left: 24px; z-index: 2147483647; pointer-events: auto;">
  
  {{-- 2.1 Hộp điều khiển chi tiết --}}
  <div id="figma-panel-card" class="fgo-panel-box">
    
    {{-- Header --}}
    <div class="fgo-panel-header">
      <div class="fgo-header-left">
        <span class="fgo-live-dot"></span>
        <span class="fgo-title">Figma Layout Guide</span>
        <span class="fgo-badge-dim">1440×900</span>
      </div>
      <div class="fgo-header-right">
        <button id="figma-btn-minimize" class="fgo-icon-btn" type="button" title="Thu nhỏ panel (Shift+M)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </button>
      </div>
    </div>

    {{-- Cấu hình 1: 12 Columns (Grid_Laptop) --}}
    <div class="fgo-config-row">
      <div class="fgo-config-info">
        <div class="fgo-config-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <rect x="3" y="3" width="18" height="18" rx="3"></rect>
            <line x1="9" y1="3" x2="9" y2="21"></line>
            <line x1="15" y1="3" x2="15" y2="21"></line>
          </svg>
        </div>
        <div>
          <div class="fgo-config-title">
            <span>Grid_Laptop</span>
            <span class="fgo-tag-accent">12 Cột</span>
          </div>
          <div class="fgo-config-sub">Margin 72px • Gutter 36px</div>
        </div>
      </div>
      <button id="toggle-cols-btn" class="fgo-toggle-btn fgo-btn-active" type="button" title="Phím tắt: Shift + C">
        <span class="fgo-btn-dot"></span>
        <span class="fgo-btn-text">BẬT</span>
      </button>
    </div>

    {{-- Cấu hình 2: 6 Rows (Grid) --}}
    <div class="fgo-config-row">
      <div class="fgo-config-info">
        <div class="fgo-config-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <rect x="3" y="3" width="18" height="18" rx="3"></rect>
            <line x1="3" y1="9" x2="21" y2="9"></line>
            <line x1="3" y1="15" x2="21" y2="15"></line>
          </svg>
        </div>
        <div>
          <div class="fgo-config-title">
            <span>Grid (Rows)</span>
            <span class="fgo-tag-accent">6 Hàng</span>
          </div>
          <div class="fgo-config-sub">Margin 72px • Gutter 40px</div>
        </div>
      </div>
      <button id="toggle-rows-btn" class="fgo-toggle-btn fgo-btn-inactive" type="button" title="Phím tắt: Shift + R">
        <span class="fgo-btn-dot"></span>
        <span class="fgo-btn-text">TẮT</span>
      </button>
    </div>

    {{-- Tuỳ chỉnh nâng cao: Độ mờ & Tương phản cao cho Hero Dark Section --}}
    <div class="fgo-tools-row">
      <div class="fgo-opacity-group">
        <span class="fgo-tools-label">Độ mờ:</span>
        <button data-opacity="0.10" class="fgo-chip-btn fgo-chip-active" type="button">10%</button>
        <button data-opacity="0.20" class="fgo-chip-btn" type="button">20%</button>
        <button data-opacity="0.35" class="fgo-chip-btn" type="button">35%</button>
        <button data-opacity="0.50" class="fgo-chip-btn" type="button">50%</button>
      </div>
      <div class="fgo-tools-actions">
        <button id="toggle-contrast-btn" class="fgo-text-action" type="button" title="Tăng độ nét cho nền tối Hero">
          Tương phản cao
        </button>
        <span class="fgo-divider">|</span>
        <button id="toggle-all-btn" class="fgo-text-action fgo-text-highlight" type="button" title="Phím tắt: Shift + G">
          Bật cả 2
        </button>
      </div>
    </div>

    {{-- Gợi ý phím tắt --}}
    <div class="fgo-shortcuts-hint">
      <span>Phím tắt: <b>Shift+C</b> (Cột) • <b>Shift+R</b> (Hàng) • <b>Shift+G</b> (Tất cả)</span>
    </div>

  </div>

  {{-- 2.2 Nút thu nhỏ gọn (Pill) --}}
  <button id="figma-panel-minimized" class="fgo-minimized-pill" type="button" title="Mở bảng điều khiển Figma Grid (Shift+M)">
    <span class="fgo-live-dot"></span>
    <span class="fgo-pill-label">Figma Grid: <strong id="mini-status-text">12 Cột</strong></span>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
  </button>

</div>

{{-- 3. SCOPED CSS HOÀN TOÀN ĐỘC LẬP (KHÔNG PHỤ THUỘC VÀO TAILWIND HOẶC CSS NGOÀI) --}}
<style id="figma-grid-scoped-styles">
  :root {
    --fgo-grid-color: rgba(255, 0, 0, 0.10);
    --fgo-grid-border: rgba(255, 40, 40, 0.38);
    --fgo-grid-badge-bg: rgba(255, 255, 255, 0.95);
    --fgo-grid-badge-text: #dc2626;
  }

  /* Chế độ tương phản cao cho nền tối Hero */
  .fgo-high-contrast {
    --fgo-grid-color: rgba(255, 0, 60, 0.22);
    --fgo-grid-border: rgba(255, 50, 80, 0.75);
    --fgo-grid-badge-bg: #dc2626;
    --fgo-grid-badge-text: #ffffff;
  }

  /* Root Container */
  .figma-grid-root-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    max-width: 100%;
    height: 100vh;
    z-index: 2147483640;
    pointer-events: none;
    overflow: hidden;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  .fgo-layer {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    box-sizing: border-box;
  }

  .fgo-layer.fgo-hidden {
    display: none;
  }

  /* 3.1 GRID COLUMNS (Grid_Laptop: 12 CỘT CHUẨN FIGMA) */
  .fgo-cols-container {
    display: grid;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    column-gap: 36px;
    row-gap: 0;
    width: 100%;
    max-width: 1440px;
    height: 100%;
    margin-left: auto;
    margin-right: auto;
    padding-left: 72px;
    padding-right: 72px;
    padding-top: 0;
    padding-bottom: 0;
    box-sizing: border-box;
    pointer-events: none;
  }

  .fgo-col-track {
    height: 100%;
    width: 100%;
    background-color: var(--fgo-grid-color);
    border-left: 1px solid var(--fgo-grid-border);
    border-right: 1px solid var(--fgo-grid-border);
    position: relative;
    box-sizing: border-box;
    pointer-events: none;
  }

  .fgo-col-badge {
    position: absolute;
    top: 6px;
    left: 50%;
    transform: translateX(-50%);
    color: var(--fgo-grid-badge-text);
    background: var(--fgo-grid-badge-bg);
    border: 1px solid var(--fgo-grid-border);
    font-size: 10px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-weight: 700;
    line-height: 1;
    padding: 2px 5px;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
    user-select: none;
  }

  /* 3.2 GRID ROWS (Grid: 6 HÀNG CHUẨN FIGMA) */
  .fgo-rows-container {
    display: grid;
    grid-template-rows: repeat(6, minmax(0, 1fr));
    row-gap: 40px;
    column-gap: 0;
    width: 100%;
    max-width: 1440px;
    height: 100%;
    margin-left: auto;
    margin-right: auto;
    padding-top: 72px;
    padding-bottom: 72px;
    padding-left: 72px;
    padding-right: 72px;
    box-sizing: border-box;
    pointer-events: none;
  }

  .fgo-row-track {
    width: 100%;
    height: 100%;
    background-color: var(--fgo-grid-color);
    border-top: 1px solid var(--fgo-grid-border);
    border-bottom: 1px solid var(--fgo-grid-border);
    position: relative;
    box-sizing: border-box;
    pointer-events: none;
  }

  .fgo-row-badge {
    position: absolute;
    left: 6px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--fgo-grid-badge-text);
    background: var(--fgo-grid-badge-bg);
    border: 1px solid var(--fgo-grid-border);
    font-size: 10px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-weight: 700;
    line-height: 1;
    padding: 2px 5px;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
    user-select: none;
  }

  /* Responsive screen adjustment */
  @media (max-width: 1024px) {
    .fgo-cols-container {
      padding-left: 24px;
      padding-right: 24px;
      column-gap: 16px;
    }
    .fgo-rows-container {
      padding-top: 24px;
      padding-bottom: 24px;
      padding-left: 24px;
      padding-right: 24px;
      row-gap: 20px;
    }
  }

  /* 3.3 STYLING BẢNG ĐIỀU KHIỂN NỔI (CONTROL PANEL) */
  .figma-panel-wrapper {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    user-select: none;
    -webkit-user-select: none;
  }

  .fgo-panel-box {
    width: 326px;
    background: rgba(18, 18, 22, 0.96);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 16px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    padding: 14px;
    box-sizing: border-box;
  }

  .fgo-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 10px;
    margin-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
  }

  .fgo-header-left {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .fgo-live-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #ef4444;
    box-shadow: 0 0 8px #ef4444;
    animation: fgoPulse 2s infinite;
  }

  @keyframes fgoPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.85); }
  }

  .fgo-title {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #f4f4f5;
  }

  .fgo-badge-dim {
    font-size: 10px;
    font-family: ui-monospace, monospace;
    color: #a1a1aa;
    background: rgba(255, 255, 255, 0.08);
    padding: 1px 5px;
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }

  .fgo-icon-btn {
    background: transparent;
    border: none;
    color: #a1a1aa;
    padding: 4px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s, color 0.15s;
  }

  .fgo-icon-btn:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.12);
  }

  /* Hàng cấu hình toggle */
  .fgo-config-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    padding: 8px 10px;
    margin-bottom: 8px;
    transition: background 0.15s;
  }

  .fgo-config-row:hover {
    background: rgba(255, 255, 255, 0.08);
  }

  .fgo-config-info {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .fgo-config-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(239, 68, 68, 0.15);
    color: #f87171;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .fgo-config-title {
    font-size: 12px;
    font-weight: 600;
    color: #f4f4f5;
    display: flex;
    align-items: center;
    gap: 6px;
    line-height: 1.2;
  }

  .fgo-tag-accent {
    font-size: 10px;
    font-family: ui-monospace, monospace;
    font-weight: 500;
    color: #f87171;
  }

  .fgo-config-sub {
    font-size: 10px;
    font-family: ui-monospace, monospace;
    color: #a1a1aa;
    margin-top: 2px;
    line-height: 1;
  }

  /* Nút Toggle BẬT / TẮT */
  .fgo-toggle-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    outline: none;
    line-height: 1;
  }

  .fgo-btn-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
  }

  .fgo-toggle-btn.fgo-btn-active {
    background-color: #15803d;
    color: #ffffff;
    border: 1px solid #22c55e;
    box-shadow: 0 0 12px rgba(34, 197, 94, 0.4);
  }

  .fgo-toggle-btn.fgo-btn-active .fgo-btn-dot {
    background-color: #86efac;
  }

  .fgo-toggle-btn.fgo-btn-inactive {
    background-color: #27272a;
    color: #a1a1aa;
    border: 1px solid #3f3f46;
    box-shadow: none;
  }

  .fgo-toggle-btn.fgo-btn-inactive .fgo-btn-dot {
    background-color: #323232;
  }

  /* Toolbar công cụ phụ */
  .fgo-tools-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 8px;
    margin-top: 6px;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
    font-size: 10px;
  }

  .fgo-opacity-group {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .fgo-tools-label {
    color: #a1a1aa;
    margin-right: 2px;
  }

  .fgo-chip-btn {
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    font-family: ui-monospace, monospace;
    cursor: pointer;
    background: rgba(255, 255, 255, 0.08);
    color: #a1a1aa;
    border: 1px solid transparent;
    transition: all 0.15s;
    line-height: 1.2;
  }

  .fgo-chip-btn:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.15);
  }

  .fgo-chip-btn.fgo-chip-active {
    background: rgba(244, 63, 94, 0.28);
    color: #fecdd3;
    border-color: rgba(244, 63, 94, 0.6);
    font-weight: 700;
  }

  .fgo-tools-actions {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .fgo-divider {
    color: rgba(255, 255, 255, 0.2);
  }

  .fgo-text-action {
    background: transparent;
    border: none;
    color: #a1a1aa;
    font-size: 10px;
    font-weight: 600;
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 4px;
    transition: color 0.15s;
  }

  .fgo-text-action:hover {
    color: #ffffff;
  }

  .fgo-text-action.fgo-text-highlight {
    color: #f87171;
  }

  .fgo-text-action.fgo-action-active {
    color: #facc15;
    text-decoration: underline;
  }

  .fgo-shortcuts-hint {
    margin-top: 8px;
    padding-top: 6px;
    border-top: 1px dashed rgba(255, 255, 255, 0.08);
    font-size: 9px;
    color: #323232;
    text-align: center;
    line-height: 1.3;
  }

  .fgo-shortcuts-hint b {
    color: #a1a1aa;
  }

  /* Minimized Pill */
  .fgo-minimized-pill {
    display: none;
    align-items: center;
    gap: 8px;
    background: rgba(18, 18, 22, 0.96);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 9999px;
    padding: 7px 14px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    transition: all 0.2s ease;
  }

  .fgo-minimized-pill:hover {
    background: rgba(28, 28, 34, 1);
    border-color: rgba(255, 255, 255, 0.35);
    transform: translateY(-2px);
  }

  .fgo-pill-label {
    color: #d4d4d8;
  }

  .fgo-pill-label strong {
    color: #f87171;
    margin-left: 2px;
  }
</style>

{{-- 4. JAVASCRIPT ĐIỀU KHIỂN ĐỘC LẬP VÀ LƯU TRẠNG THÁI LOCALSTORAGE --}}
<script id="figma-grid-script">
  (function() {
    const rootContainer = document.getElementById('figma-grid-root');
    const colsOverlay = document.getElementById('figma-cols-overlay');
    const rowsOverlay = document.getElementById('figma-rows-overlay');
    const toggleColsBtn = document.getElementById('toggle-cols-btn');
    const toggleRowsBtn = document.getElementById('toggle-rows-btn');
    const toggleAllBtn = document.getElementById('toggle-all-btn');
    const toggleContrastBtn = document.getElementById('toggle-contrast-btn');
    const panelCard = document.getElementById('figma-panel-card');
    const panelMinimized = document.getElementById('figma-panel-minimized');
    const btnMinimize = document.getElementById('figma-btn-minimize');
    const miniStatusText = document.getElementById('mini-status-text');
    const opacityBtns = document.querySelectorAll('.fgo-chip-btn');

    if (!colsOverlay || !rowsOverlay || !toggleColsBtn || !toggleRowsBtn) return;

    // Khởi tạo trạng thái từ localStorage (Mặc định 12 Cột BẬT, Hàng TẮT để giao diện thoáng)
    let colsActive = localStorage.getItem('figma_guide_cols_v3') !== null 
      ? localStorage.getItem('figma_guide_cols_v3') === 'true' 
      : true;

    let rowsActive = localStorage.getItem('figma_guide_rows_v3') !== null 
      ? localStorage.getItem('figma_guide_rows_v3') === 'true' 
      : false;

    let highContrast = localStorage.getItem('figma_guide_contrast_v3') === 'true';
    let isMinimized = localStorage.getItem('figma_guide_mini_v3') === 'true';
    let savedOpacity = localStorage.getItem('figma_guide_opacity_v3') || '0.10';

    function renderToggleButton(btn, active) {
      if (active) {
        btn.classList.add('fgo-btn-active');
        btn.classList.remove('fgo-btn-inactive');
        btn.querySelector('.fgo-btn-text').textContent = 'BẬT';
      } else {
        btn.classList.remove('fgo-btn-active');
        btn.classList.add('fgo-btn-inactive');
        btn.querySelector('.fgo-btn-text').textContent = 'TẮT';
      }
    }

    function applyOpacity(val) {
      savedOpacity = val;
      localStorage.setItem('figma_guide_opacity_v3', val);
      document.documentElement.style.setProperty('--fgo-grid-color', `rgba(255, 0, 0, ${val})`);
      document.documentElement.style.setProperty('--fgo-grid-border', `rgba(255, 40, 40, ${Math.min(parseFloat(val) * 2.5 + 0.15, 0.85)})`);
      opacityBtns.forEach(b => {
        if (b.dataset.opacity === val) {
          b.className = 'fgo-chip-btn fgo-chip-active';
        } else {
          b.className = 'fgo-chip-btn';
        }
      });
    }

    function applyHighContrast(enabled) {
      highContrast = enabled;
      localStorage.setItem('figma_guide_contrast_v3', enabled ? 'true' : 'false');
      if (enabled) {
        rootContainer.classList.add('fgo-high-contrast');
        if (toggleContrastBtn) toggleContrastBtn.classList.add('fgo-action-active');
      } else {
        rootContainer.classList.remove('fgo-high-contrast');
        if (toggleContrastBtn) toggleContrastBtn.classList.remove('fgo-action-active');
        applyOpacity(savedOpacity);
      }
    }

    function updateState() {
      // 1. Cột dọc (12 Cột)
      if (colsActive) {
        colsOverlay.classList.remove('fgo-hidden');
      } else {
        colsOverlay.classList.add('fgo-hidden');
      }
      renderToggleButton(toggleColsBtn, colsActive);
      localStorage.setItem('figma_guide_cols_v3', colsActive ? 'true' : 'false');

      // 2. Hàng ngang (6 Hàng)
      if (rowsActive) {
        rowsOverlay.classList.remove('fgo-hidden');
      } else {
        rowsOverlay.classList.add('fgo-hidden');
      }
      renderToggleButton(toggleRowsBtn, rowsActive);
      localStorage.setItem('figma_guide_rows_v3', rowsActive ? 'true' : 'false');

      // 3. Nút Toggle All
      if (colsActive && rowsActive) {
        toggleAllBtn.textContent = 'Tắt cả 2';
      } else {
        toggleAllBtn.textContent = 'Bật cả 2';
      }

      // 4. Status text trên nút thu nhỏ
      if (colsActive && rowsActive) {
        miniStatusText.textContent = '12 Cột + 6 Hàng';
      } else if (colsActive) {
        miniStatusText.textContent = '12 Cột';
      } else if (rowsActive) {
        miniStatusText.textContent = '6 Hàng';
      } else {
        miniStatusText.textContent = 'TẮT';
      }
    }

    function setMinimized(minimized) {
      isMinimized = minimized;
      if (minimized) {
        panelCard.style.display = 'none';
        panelMinimized.style.display = 'inline-flex';
      } else {
        panelCard.style.display = 'block';
        panelMinimized.style.display = 'none';
      }
      localStorage.setItem('figma_guide_mini_v3', minimized ? 'true' : 'false');
    }

    // Gán sự kiện click
    toggleColsBtn.addEventListener('click', function(e) {
      e.preventDefault();
      colsActive = !colsActive;
      updateState();
    });

    toggleRowsBtn.addEventListener('click', function(e) {
      e.preventDefault();
      rowsActive = !rowsActive;
      updateState();
    });

    toggleAllBtn.addEventListener('click', function(e) {
      e.preventDefault();
      const bothActive = colsActive && rowsActive;
      colsActive = !bothActive;
      rowsActive = !bothActive;
      updateState();
    });

    if (toggleContrastBtn) {
      toggleContrastBtn.addEventListener('click', function(e) {
        e.preventDefault();
        applyHighContrast(!highContrast);
      });
    }

    btnMinimize.addEventListener('click', function(e) {
      e.preventDefault();
      setMinimized(true);
    });

    panelMinimized.addEventListener('click', function(e) {
      e.preventDefault();
      setMinimized(false);
    });

    // Thay đổi độ mờ
    opacityBtns.forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        if (highContrast) applyHighContrast(false);
        applyOpacity(this.dataset.opacity);
      });
    });

    // Phím tắt toàn cục: Shift + C (Cột), Shift + R (Hàng), Shift + G (Tất cả), Shift + M (Thu nhỏ/Mở rộng)
    window.addEventListener('keydown', function(e) {
      if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;
      if (e.shiftKey && (e.key === 'C' || e.key === 'c')) {
        e.preventDefault();
        colsActive = !colsActive;
        updateState();
      } else if (e.shiftKey && (e.key === 'R' || e.key === 'r')) {
        e.preventDefault();
        rowsActive = !rowsActive;
        updateState();
      } else if (e.shiftKey && (e.key === 'G' || e.key === 'g')) {
        e.preventDefault();
        const bothActive = colsActive && rowsActive;
        colsActive = !bothActive;
        rowsActive = !bothActive;
        updateState();
      } else if (e.shiftKey && (e.key === 'M' || e.key === 'm')) {
        e.preventDefault();
        setMinimized(!isMinimized);
      }
    });

    // Khởi tạo hiển thị ban đầu
    updateState();
    applyOpacity(savedOpacity);
    if (highContrast) applyHighContrast(true);
    setMinimized(isMinimized);
  })();
</script>
@endif
