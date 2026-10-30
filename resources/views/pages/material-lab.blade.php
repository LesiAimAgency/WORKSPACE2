<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $pageTitle ?? ($title ?? 'Thư viện vật liệu') }} • {{ $pageSubtitle ?? ($subtitle ?? 'Material Lab • V1.0') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    *,:after,:before{box-sizing:border-box}
    body {
      margin: 0;
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      line-height: 1.6;
      background-color: #F9F6F0;
      color: #1F1E1C;
    }
    .sans { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    
    /* Range Slider Styling */
    input[type=range] { -webkit-appearance: none; appearance: none; background: transparent; width: 100%; }
    input[type=range]::-webkit-slider-runnable-track { height: 6px; background: #E8E2D8; border-radius: 999px; }
    input[type=range]::-webkit-slider-thumb { 
      -webkit-appearance: none; 
      width: 24px; 
      height: 24px; 
      background: #C1272D; 
      border: 2px solid #F9F6F0; 
      border-radius: 50%; 
      margin-top: -9px; 
      box-shadow: 0 2px 8px rgba(193,39,45,0.3); 
      cursor: pointer; 
    }
    input[type=range]::-moz-range-track { height: 6px; background: #E8E2D8; border-radius: 999px; }
    input[type=range]::-moz-range-thumb { 
      width: 24px; 
      height: 24px; 
      background: #C1272D; 
      border: 2px solid #F9F6F0; 
      border-radius: 50%; 
      cursor: pointer; 
    }
    
    @keyframes slideInRight { from { transform: translateX(16px); opacity: 0; } to { transform: none; opacity: 1; } }
    @keyframes slideInLeft { from { transform: translateX(-16px); opacity: 0; } to { transform: none; opacity: 1; } }
    @keyframes popIn { from { transform: translateY(6px) scale(0.98); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
    
    .scrollbar-thin { scrollbar-width: thin; scrollbar-color: #C1272D #E8E2D8; }
    .scrollbar-thin::-webkit-scrollbar { width: 5px; height: 5px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #C1272D; border-radius: 999px; }
    
    /* Popover */
    [x-cloak] { display: none !important; }
  </style>
</head>
<body class="min-h-screen bg-[#F9F6F0] text-[#1F1E1C] selection:bg-[#C1272D] selection:text-white"
      x-data="materialLabApp()"
      x-init="initApp()">

  <!-- HEADER -->
  <header class="sticky top-0 z-40 border-b border-[#1F1E1C]/10 bg-[#F9F6F0]/95 backdrop-blur-xl">
    <div class="mx-auto flex max-w-[1280px] items-center justify-between px-5 py-3 md:px-8">
      <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
          <img src="{{ file_exists(public_path('images/workspace-logo-mat.png')) ? asset('images/workspace-logo-mat.png') : asset('Logo.png') }}"
               alt="Workspace Logo"
               class="h-8 w-auto object-contain">
        </a>
        <div class="hidden h-5 w-px bg-[#1F1E1C]/15 md:block"></div>
        <div>
          <div class="sans text-[13px] font-bold leading-none tracking-tight">{{ $pageTitle ?? ($title ?? 'Thư viện vật liệu') }}</div>
          <div class="sans text-[11px] text-[#1F1E1C]/50 leading-none mt-0.5">{{ $pageSubtitle ?? ($subtitle ?? 'Material Lab • V1.0') }}</div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <!-- Step progress indicators -->
        <div class="hidden md:flex items-center gap-1.5">
          <template x-for="stepNum in [1, 2, 3, 4, 5]" :key="stepNum">
            <div class="h-[3px] rounded-full transition-all duration-200"
                 :class="step >= stepNum ? 'w-6 bg-[#C1272D]' : 'w-3 bg-[#1F1E1C]/15'"></div>
          </template>
        </div>

        <span class="sans rounded-full border border-[#1F1E1C]/10 bg-white px-2.5 py-1 text-[10px] font-medium"
              x-text="'Bước ' + step + '/5'"></span>

        <span x-show="statusMsg" x-text="statusMsg"
              class="sans hidden lg:inline rounded-full bg-[#C1272D]/10 px-2.5 py-1 text-[10px] font-medium text-[#C1272D] max-w-[180px] truncate"></span>

    
      </div>
    </div>
  </header>

  <!-- MAIN CONTAINER -->
  <main class="mx-auto max-w-[1280px] px-5 md:px-8 pb-16">
    <div class="relative min-h-[60vh]">
      <div id="wizard-scroll-container"
           class="mx-auto w-full max-w-[1120px] min-h-[60vh] px-1 py-6 md:py-8 pb-10">
        
        <div class="w-full max-w-5xl mx-auto px-2 md:px-4 py-2"
             :style="'animation: ' + (slideDir > 0 ? 'slideInRight 0.2s ease' : 'slideInLeft 0.2s ease')">

          <!-- ============================================== -->
          <!-- STEP 1: CHỌN LOẠI HÌNH CÔNG TRÌNH              -->
          <!-- ============================================== -->
          <div x-show="step === 1" class="w-full" x-transition.opacity>
            <div class="mx-auto max-w-[680px] text-center">
              <h1 class="sans text-[26px] font-bold leading-[1.15] tracking-tight md:text-[36px]">
                {{ $step1Title ?? 'Công trình của bạn là gì?' }}
              </h1>
              <p class="sans mt-3 text-[13px] text-[#1F1E1C]/60">
                {{ $step1Desc ?? 'Chọn loại hình để gợi ý vật liệu phù hợp.' }}
              </p>
            </div>

            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              <template x-for="item in typologies" :key="item.en">
                <button type="button"
                        @click="selectTypology(item)"
                        class="group text-left rounded-[16px] border bg-white p-[18px] min-h-[96px] shadow-sm transition-all duration-200 hover:-translate-y-[1px] hover:shadow-md"
                        :class="selectedTypology && selectedTypology.en === item.en ? 'border-[#C1272D] ring-2 ring-[#C1272D]/20' : 'border-[#1F1E1C]/10 hover:border-[#1F1E1C]/20'">
                  <div class="flex items-start justify-between">
                    <span class="text-[18px]" x-text="item.icon"></span>
                    <span class="h-6 w-6 rounded-full border flex items-center justify-center text-[11px] transition-colors"
                          :class="selectedTypology && selectedTypology.en === item.en ? 'bg-[#C1272D] border-[#C1272D] text-white' : 'border-[#1F1E1C]/15 text-[#1F1E1C]/40'"
                          x-text="selectedTypology && selectedTypology.en === item.en ? '✓' : ''"></span>
                  </div>
                  <div class="sans mt-3 text-[16px] font-semibold leading-tight" x-text="item.vi"></div>
                  <div class="sans mt-1 text-[12px] italic text-[#1F1E1C]/50 leading-snug" x-text="item.en"></div>
                  <div class="sans mt-2 text-[11px] leading-[1.5] text-[#1F1E1C]/60 line-clamp-2" x-text="item.desc"></div>
                </button>
              </template>
            </div>
          </div>

          <!-- ============================================== -->
          <!-- STEP 2: CHỌN BỀ MẶT / VỊ TRÍ                   -->
          <!-- ============================================== -->
          <div x-show="step === 2" class="w-full" x-transition.opacity>
            <div class="mb-6 flex justify-center">
              <button type="button"
                      @click="goToStep(1, -1)"
                      class="sans inline-flex items-center gap-2 rounded-full bg-white border border-[#1F1E1C]/10 px-4 py-2 text-[12px] font-medium hover:border-[#1F1E1C]/20 transition-colors">
                <span>←</span> <span x-text="selectedTypology ? selectedTypology.vi : 'Loại hình'"></span>
              </button>
            </div>

            <div class="mx-auto max-w-[680px] text-center">
              <h2 class="sans text-[26px] font-bold leading-[1.15] tracking-tight md:text-[34px]">
                {{ $step2Title ?? 'Vị trí nào cần vật liệu?' }}
              </h2>
              <p class="sans mt-3 text-[13px] text-[#1F1E1C]/60">
                {{ $step2Desc ?? 'Chọn bề mặt để lọc vật liệu phù hợp.' }}
              </p>
            </div>

            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
              <template x-for="item in surfaces" :key="item.en">
                <button type="button"
                        @click="selectSurface(item)"
                        class="rounded-[16px] border bg-white p-5 text-left min-h-[118px] shadow-sm transition-all duration-200 hover:-translate-y-[1px] hover:shadow-md"
                        :class="selectedSurface && selectedSurface.en === item.en ? 'border-[#1F1E1C] bg-[#1F1E1C] text-white shadow-lg' : 'border-[#1F1E1C]/10 hover:border-[#1F1E1C]/20'">
                  <div class="sans text-[10px] font-bold uppercase tracking-widest"
                       :class="selectedSurface && selectedSurface.en === item.en ? 'text-white/50' : 'text-[#1F1E1C]/40'"
                       x-text="item.enLabel"></div>
                  <div class="sans mt-2 text-[15px] font-semibold leading-tight" x-text="item.vi"></div>
                  <div class="sans mt-1 text-[12px] italic leading-snug"
                       :class="selectedSurface && selectedSurface.en === item.en ? 'text-white/60' : 'text-[#1F1E1C]/50'"
                       x-text="item.enLabel"></div>
                </button>
              </template>
            </div>
          </div>

          <!-- ============================================== -->
          <!-- STEP 3: CHỌN KHU VỰC CHỨC NĂNG                 -->
          <!-- ============================================== -->
          <div x-show="step === 3" class="w-full" x-transition.opacity>
            <div class="mb-6 flex justify-center gap-2">
              <button type="button"
                      @click="goToStep(1, -1)"
                      class="sans rounded-full bg-white border px-3 py-1.5 text-[11px] hover:bg-[#1F1E1C] hover:text-white transition-colors"
                      x-text="selectedTypology ? selectedTypology.vi : ''"></button>
              <button type="button"
                      @click="goToStep(2, -1)"
                      class="sans rounded-full bg-[#1F1E1C] text-white px-3 py-1.5 text-[11px]"
                      x-text="selectedSurface ? selectedSurface.vi : ''"></button>
            </div>

            <div class="mx-auto max-w-[680px] text-center">
              <h2 class="sans text-[26px] font-bold leading-[1.15] tracking-tight md:text-[34px]">
                {{ $step3Title ?? 'Khu vực chức năng nào?' }}
              </h2>
              <p class="sans mt-3 text-[12px] text-[#1F1E1C]/60">
                <span x-text="selectedTypology ? selectedTypology.vi : ''"></span> •
                <span x-text="selectedSurface ? selectedSurface.vi : ''"></span> •
                <span x-text="availableAreas.length"></span> lựa chọn
              </p>
            </div>

            <div class="mx-auto mt-8 flex max-w-[900px] flex-wrap gap-2.5 justify-center">
              <template x-for="area in availableAreas" :key="area.en">
                <button type="button"
                        @click="selectArea(area)"
                        class="rounded-[12px] border bg-white px-4 py-3 text-left min-w-[130px] shadow-sm transition-all duration-200 hover:-translate-y-[0.5px]"
                        :class="selectedArea && selectedArea.en === area.en ? 'border-[#C1272D] bg-[#C1272D] text-white shadow' : 'border-[#1F1E1C]/10 hover:border-[#1F1E1C]/20'">
                  <div class="sans text-[14px] font-semibold leading-tight" x-text="area.vi"></div>
                  <div class="sans mt-0.5 text-[11px] italic leading-snug"
                       :class="selectedArea && selectedArea.en === area.en ? 'text-white/70' : 'text-[#1F1E1C]/50'"
                       x-text="area.en"></div>
                </button>
              </template>
            </div>
          </div>

          <!-- ============================================== -->
          <!-- STEP 4: NHU CẦU THỰC TẾ (4 BỘ LỌC)             -->
          <!-- ============================================== -->
          <div x-show="step === 4" class="w-full" x-transition.opacity>
            <div class="mb-5 flex flex-wrap justify-center gap-2">
              <button type="button" @click="goToStep(1, -1)" class="sans rounded-full bg-white border px-3 py-1 text-[10px]" x-text="selectedTypology ? selectedTypology.vi : ''"></button>
              <button type="button" @click="goToStep(2, -1)" class="sans rounded-full bg-white border px-3 py-1 text-[10px]" x-text="selectedSurface ? selectedSurface.vi : ''"></button>
              <button type="button" @click="goToStep(3, -1)" class="sans rounded-full bg-[#1F1E1C] text-white px-3 py-1 text-[10px]" x-text="selectedArea ? selectedArea.vi : ''"></button>
            </div>

            <div class="mx-auto max-w-[680px] text-center">
              <h2 class="sans text-[26px] font-bold leading-[1.15] md:text-[32px]">
                {{ $step4Title ?? 'Nhu cầu thực tế?' }}
              </h2>
              <p class="sans mt-2 text-[12px] text-[#1F1E1C]/60">
                {{ $step4Desc ?? 'Chọn độ bền, ngân sách, nguồn gốc và tồn kho.' }}
              </p>
            </div>

            <div class="mt-6 flex flex-col gap-4 max-w-[720px] mx-auto">
              <!-- 1. ĐỘ BỀN -->
              <div class="rounded-[16px] border bg-white p-4 shadow-sm transition-all duration-200"
                   :class="activeFilterFocus === 'durability' ? 'border-[#C1272D] shadow-[0_0_0_3px_rgba(193,39,45,0.10)]' : 'border-[#1F1E1C]/10'">
                <div class="flex items-center justify-between">
                  <div class="sans text-[11px] font-bold uppercase tracking-widest text-[#1F1E1C]/60">Độ bền</div>
                  <div class="sans text-[10px] rounded-full bg-[#1F1E1C] px-2 py-0.5 font-medium text-white"
                       x-text="selectedDurability ? selectedDurability.years + ' năm' : 'Chưa chọn'"></div>
                </div>

                <div class="mt-3 flex items-center gap-2">
                  <span class="sans text-[10px] text-[#1F1E1C]/40">Ngắn</span>
                  <input type="range" min="0" max="2" step="1"
                         :value="selectedDurability ? lifeCycles.findIndex(c => c.id === selectedDurability.id) : 1"
                         @input="onDurabilitySlider($event.target.value)"
                         class="flex-1">
                  <span class="sans text-[10px] text-[#1F1E1C]/40">Dài</span>
                </div>

                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                  <template x-for="item in lifeCycles" :key="item.id">
                    <button type="button"
                            @click="setDurability(item)"
                            class="rounded-[10px] border px-3 py-2.5 text-left transition-all duration-200"
                            :class="selectedDurability && selectedDurability.id === item.id ? 'border-[#1F1E1C] bg-[#1F1E1C] text-white' : 'border-[#1F1E1C]/10 bg-[#F9F6F0] hover:border-[#1F1E1C]/20'">
                      <div class="sans text-[13px] font-semibold" x-text="item.vi"></div>
                      <div class="sans text-[11px] italic"
                           :class="selectedDurability && selectedDurability.id === item.id ? 'text-white/60' : 'text-[#1F1E1C]/50'"
                           x-text="item.en + ' • ' + item.note"></div>
                    </button>
                  </template>
                </div>
              </div>

              <!-- 2. NGÂN SÁCH -->
              <div class="rounded-[16px] border bg-white p-4 shadow-sm transition-all duration-200"
                   :class="activeFilterFocus === 'budget' ? 'border-[#C1272D] shadow-[0_0_0_3px_rgba(193,39,45,0.10)]' : 'border-[#1F1E1C]/10'">
                <div class="flex items-center justify-between">
                  <div class="sans text-[11px] font-bold uppercase tracking-widest text-[#1F1E1C]/60">Ngân sách</div>
                  <div class="sans text-[10px] rounded-full bg-[#C1272D] px-2 py-0.5 font-medium text-white"
                       x-text="selectedBudget ? selectedBudget.vi + ' • ' + selectedBudget.vnd : 'Chưa chọn'"></div>
                </div>

                <input type="range" min="1" max="4" step="1"
                       :value="selectedBudget ? selectedBudget.level : 2"
                       @input="onBudgetSlider($event.target.value)"
                       class="mt-3 w-full">

                <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-2">
                  <template x-for="item in budgetLevels" :key="item.level">
                    <button type="button"
                            @click="setBudget(item)"
                            class="rounded-[10px] border px-3 py-2.5 text-left duration-200"
                            :class="[
                              item.isSkip ? 'border-dashed' : '',
                              selectedBudget && selectedBudget.level === item.level ? 'border-[#1F1E1C] bg-[#1F1E1C] text-white' : 'border-[#1F1E1C]/10 bg-[#F9F6F0]'
                            ]">
                      <div class="sans text-[13px] font-semibold" x-text="item.vi"></div>
                      <div class="sans text-[10px]"
                           :class="selectedBudget && selectedBudget.level === item.level ? 'text-white/60' : 'text-[#1F1E1C]/50'"
                           x-text="item.vnd"></div>
                      <div class="sans text-[11px] italic"
                           :class="selectedBudget && selectedBudget.level === item.level ? 'text-white/50' : 'text-[#1F1E1C]/40'"
                           x-text="item.en"></div>
                    </button>
                  </template>
                </div>
                <div class="sans mt-3 text-[11px] text-[#1F1E1C]/50">Giá & tồn kho tham khảo - Vui lòng xác nhận với đại lý</div>
              </div>

              <!-- 3. NGUỒN GỐC -->
              <div class="rounded-[16px] border bg-white p-4 shadow-sm transition-all duration-200"
                   :class="activeFilterFocus === 'origin' ? 'border-[#C1272D] shadow-[0_0_0_3px_rgba(193,39,45,0.10)]' : 'border-[#1F1E1C]/10'">
                <div class="sans text-[11px] font-bold uppercase tracking-widest text-[#1F1E1C]/60">Nguồn gốc</div>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2">
                  <template x-for="item in [{id: 'Vietnam', vi: 'Hàng Việt'}, {id: 'Import', vi: 'Nhập khẩu'}, {id: 'Any', vi: 'Bỏ qua'}]" :key="item.id">
                    <button type="button"
                            @click="setOrigin(item.id)"
                            class="rounded-[10px] border px-3 py-2.5 text-left duration-200"
                            :class="selectedOrigin === item.id ? 'border-[#1F1E1C] bg-[#1F1E1C] text-white' : 'border-[#1F1E1C]/10 bg-[#F9F6F0]'">
                      <span class="sans text-[13px] font-semibold" x-text="item.vi"></span>
                    </button>
                  </template>
                </div>
              </div>

              <!-- 4. TỒN KHO -->
              <div class="rounded-[16px] border bg-white p-4 shadow-sm transition-all duration-200"
                   :class="activeFilterFocus === 'availability' ? 'border-[#C1272D] shadow-[0_0_0_3px_rgba(193,39,45,0.10)]' : 'border-[#1F1E1C]/10'">
                <div class="sans text-[11px] font-bold uppercase tracking-widest text-[#1F1E1C]/60">Tồn kho</div>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                  <template x-for="item in [
                    {id: 'Stock', vi: 'Có sẵn', en: 'Stock'},
                    {id: '2-3 weeks', vi: 'Đặt 2–3 tuần', en: '2-3 weeks'},
                    {id: '6-8 weeks', vi: 'Chấp nhận 6–8 tuần', en: '6-8 weeks'},
                    {id: 'Any', vi: 'Bỏ qua', en: 'Any'}
                  ]" :key="item.id">
                    <button type="button"
                            @click="setAvailability(item.id)"
                            class="flex items-center justify-between rounded-[10px] border px-3 py-2.5 text-left duration-200"
                            :class="[
                              item.id === 'Any' ? 'border-dashed' : '',
                              selectedAvailability === item.id ? 'border-[#C1272D] bg-[#C1272D] text-white' : 'border-[#1F1E1C]/10 bg-[#F9F6F0]'
                            ]">
                      <span class="sans text-[13px] font-semibold" x-text="item.vi"></span>
                      <span class="sans text-[10px]"
                            :class="selectedAvailability === item.id ? 'text-white/70' : 'text-[#1F1E1C]/50'"
                            x-text="item.en"></span>
                    </button>
                  </template>
                </div>

                <div class="mt-4 flex flex-col sm:flex-row items-center justify-end gap-3 pt-3 border-t border-[#1F1E1C]/10">
                  <button type="button"
                          @click="skipStep4()"
                          class="w-full sm:w-auto sans rounded-full border border-[#1F1E1C]/20 bg-white px-5 py-2.5 text-[13px] font-medium text-[#1F1E1C]/80 hover:border-[#1F1E1C] hover:text-[#1F1E1C] transition-all">
                    Bỏ qua bước này
                  </button>
                  <button type="button"
                          @click="goToStep(5, 1)"
                          class="w-full sm:w-auto sans inline-flex items-center justify-center gap-2 rounded-full bg-[#C1272D] px-6 py-2.5 text-[13px] font-semibold text-white shadow-sm hover:bg-[#a81f25] transition-all">
                    <span>Xem kết quả</span>
                    <span>→</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- ============================================== -->
          <!-- STEP 5: KẾT QUẢ GỢI Ý VẬT LIỆU                  -->
          <!-- ============================================== -->
          <div id="results-section"
               x-show="step === 5"
               class="transition-all duration-300"
               x-transition.opacity>
            
            <!-- Section Header -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#1F1E1C]/10 pb-4">
              <h2 class="sans text-[20px] font-bold tracking-tight">
                Kết quả • <span x-text="filteredMaterials.length"></span> vật liệu
              </h2>
              <button type="button"
                      @click="goToStep(4, -1)"
                      class="sans rounded-full border bg-white px-4 py-1.5 text-[11px] font-medium hover:bg-[#1F1E1C] hover:text-white transition-colors">
                Chỉnh nhu cầu
              </button>
            </div>

            <!-- Quick Adjust Filters Bar with Popovers -->
            <div class="mt-4 rounded-[16px] border border-[#1F1E1C]/10 bg-white p-3 shadow-sm relative">
              <div class="flex flex-wrap items-center gap-2">
                <span class="sans text-[10px] uppercase tracking-widest text-[#1F1E1C]/40 mr-1">Điều chỉnh nhanh:</span>

                <!-- 1. Durability Chip -->
                <div class="relative">
                  <button type="button"
                          @click="togglePopover('durability')"
                          class="sans inline-flex items-center gap-1.5 rounded-full border px-3.5 py-2 text-[12px] font-medium transition-all"
                          :class="activePopover === 'durability' ? 'border-[#C1272D] bg-[#1F1E1C] text-white' : 'border-[#1F1E1C]/15 bg-[#F9F6F0] hover:border-[#1F1E1C]/20'">
                    <span x-text="selectedDurability ? selectedDurability.vi : 'Độ bền'"></span>
                    <span class="text-[10px] opacity-60">›</span>
                  </button>

                  <div x-show="activePopover === 'durability'"
                       @click.outside="activePopover = null"
                       class="absolute left-0 top-full z-30 mt-2 w-[300px] rounded-[14px] border border-[#1F1E1C]/10 bg-white p-3 shadow-xl"
                       style="animation: popIn 0.2s ease;">
                    <div class="sans text-[11px] font-bold uppercase tracking-widest mb-2">Độ bền</div>
                    <div class="grid gap-1.5">
                      <template x-for="item in lifeCycles" :key="item.id">
                        <button type="button"
                                @click="selectedDurability = item; activePopover = null;"
                                class="rounded-[10px] border px-3 py-2 text-left"
                                :class="selectedDurability && selectedDurability.id === item.id ? 'bg-[#1F1E1C] text-white border-[#1F1E1C]' : 'bg-[#F9F6F0] border-[#1F1E1C]/10'">
                          <span class="sans text-[13px] font-medium" x-text="item.vi"></span>
                          <span class="sans ml-2 text-[11px] opacity-60" x-text="item.en"></span>
                        </button>
                      </template>
                    </div>
                  </div>
                </div>

                <!-- 2. Budget Chip -->
                <div class="relative">
                  <button type="button"
                          @click="togglePopover('budget')"
                          class="sans inline-flex items-center gap-1.5 rounded-full border px-3.5 py-2 text-[12px] font-medium transition-all"
                          :class="activePopover === 'budget' ? 'border-[#C1272D] bg-[#1F1E1C] text-white' : 'border-[#1F1E1C]/15 bg-[#F9F6F0]'">
                    <span x-text="selectedBudget ? selectedBudget.vi : 'Ngân sách'"></span>
                    <span class="text-[10px] opacity-60">›</span>
                  </button>

                  <div x-show="activePopover === 'budget'"
                       @click.outside="activePopover = null"
                       class="absolute left-0 top-full z-30 mt-2 w-[300px] rounded-[14px] border bg-white p-3 shadow-xl"
                       style="animation: popIn 0.2s ease;">
                    <div class="sans text-[11px] font-bold uppercase tracking-widest mb-2">Ngân sách</div>
                    <div class="grid grid-cols-2 gap-1.5">
                      <template x-for="item in budgetLevels" :key="item.level">
                        <button type="button"
                                @click="selectedBudget = item; activePopover = null;"
                                class="rounded-[10px] border px-3 py-2 text-left"
                                :class="selectedBudget && selectedBudget.level === item.level ? 'bg-[#1F1E1C] text-white' : 'bg-[#F9F6F0]'">
                          <div class="sans text-[13px] font-medium" x-text="item.vi"></div>
                          <div class="sans text-[10px] opacity-60" x-text="item.vnd"></div>
                        </button>
                      </template>
                    </div>
                  </div>
                </div>

                <!-- 3. Origin Chip -->
                <div class="relative">
                  <button type="button"
                          @click="togglePopover('origin')"
                          class="sans inline-flex items-center gap-1.5 rounded-full border px-3.5 py-2 text-[12px] font-medium"
                          :class="activePopover === 'origin' ? 'bg-[#1F1E1C] text-white' : 'bg-[#F9F6F0] border-[#1F1E1C]/15'">
                    <span x-text="selectedOrigin === 'Vietnam' ? 'Hàng Việt' : (selectedOrigin === 'Import' ? 'Nhập khẩu' : 'Bỏ qua')"></span>
                    <span class="text-[10px] opacity-60">›</span>
                  </button>

                  <div x-show="activePopover === 'origin'"
                       @click.outside="activePopover = null"
                       class="absolute left-0 top-full z-30 mt-2 w-[240px] rounded-[14px] border bg-white p-3 shadow-xl"
                       style="animation: popIn 0.2s ease;">
                    <template x-for="item in [{id: 'Vietnam', vi: 'Hàng Việt'}, {id: 'Import', vi: 'Nhập khẩu'}, {id: 'Any', vi: 'Bỏ qua'}]" :key="item.id">
                      <button type="button"
                              @click="selectedOrigin = item.id; activePopover = null;"
                              class="w-full rounded-[10px] border px-3 py-2 text-left mb-1 last:mb-0"
                              :class="selectedOrigin === item.id ? 'bg-[#1F1E1C] text-white' : 'bg-[#F9F6F0]'">
                        <span class="sans text-[13px]" x-text="item.vi"></span>
                      </button>
                    </template>
                  </div>
                </div>

                <!-- 4. Availability Chip -->
                <div class="relative">
                  <button type="button"
                          @click="togglePopover('availability')"
                          class="sans inline-flex items-center gap-1.5 rounded-full border px-3.5 py-2 text-[12px] font-medium"
                          :class="activePopover === 'availability' ? 'bg-[#C1272D] text-white border-[#C1272D]' : 'bg-[#F9F6F0] border-[#1F1E1C]/15'">
                    <span x-text="selectedAvailability === 'Stock' ? 'Có sẵn' : (selectedAvailability === '2-3 weeks' ? '2–3 tuần' : (selectedAvailability === '6-8 weeks' ? '6–8 tuần' : 'Bỏ qua'))"></span>
                    <span class="text-[10px] opacity-60">›</span>
                  </button>

                  <div x-show="activePopover === 'availability'"
                       @click.outside="activePopover = null"
                       class="absolute right-0 top-full z-30 mt-2 w-[260px] rounded-[14px] border bg-white p-3 shadow-xl"
                       style="animation: popIn 0.2s ease;">
                    <template x-for="item in [
                      {id: 'Stock', vi: 'Có sẵn'},
                      {id: '2-3 weeks', vi: 'Đặt 2–3 tuần'},
                      {id: '6-8 weeks', vi: '6–8 tuần'},
                      {id: 'Any', vi: 'Bỏ qua'}
                    ]" :key="item.id">
                      <button type="button"
                              @click="selectedAvailability = item.id; activePopover = null;"
                              class="w-full rounded-[10px] border px-3 py-2 text-left mb-1"
                              :class="selectedAvailability === item.id ? 'bg-[#C1272D] text-white border-[#C1272D]' : 'bg-[#F9F6F0]'">
                        <span class="sans text-[13px]" x-text="item.vi"></span>
                      </button>
                    </template>
                  </div>
                </div>
              </div>
              <div class="sans mt-2 text-[11px] text-[#1F1E1C]/50">Giá & tồn kho tham khảo - Vui lòng xác nhận với đại lý</div>
            </div>

            <!-- Tab Switcher (Technical vs Experience) -->
            <div class="mt-4 flex w-full overflow-hidden rounded-[14px] border border-[#1F1E1C]/10 bg-white p-1">
              <button type="button"
                      @click="activeTab = 'ai'"
                      class="flex flex-1 items-center justify-center rounded-[10px] px-4 py-2.5 text-[13px] font-medium transition-all"
                      :class="activeTab === 'ai' ? 'bg-[#1F1E1C] text-white shadow-sm' : 'text-[#1F1E1C]/60 hover:bg-[#F9F6F0]'">
                Tiêu chuẩn kỹ thuật
              </button>
              <button type="button"
                      @click="activeTab = 'workspace'"
                      class="flex flex-1 items-center justify-center rounded-[10px] px-4 py-2.5 text-[13px] font-medium transition-all"
                      :class="activeTab === 'workspace' ? 'bg-[#C1272D] text-white shadow-sm' : 'text-[#1F1E1C]/60 hover:bg-[#F9F6F0]'">
                Kinh nghiệm Workspace
              </button>
            </div>

            <!-- Main Results Layout (2 Columns) -->
            <div class="mt-5 grid grid-cols-12 gap-4 items-start">
              <!-- Left Column: Standards / Field Insights -->
              <div class="col-span-12 md:col-span-5 md:sticky md:top-[72px] self-start md:max-h-[calc(100vh-88px)] md:overflow-y-auto scrollbar-thin space-y-3">
                <div class="rounded-[16px] bg-[#1F1E1C] p-4 md:p-5 text-[#F9F6F0] transition-all">
                  <div class="flex items-center justify-between gap-2">
                    <div class="sans text-[10px] font-bold uppercase tracking-widest text-white/50"
                         x-text="activeTab === 'ai' ? 'Tiêu chuẩn kỹ thuật' : 'Kinh nghiệm Workspace'"></div>
                    <template x-if="activeMaterial">
                      <span class="sans rounded-full bg-[#C1272D] px-2.5 py-0.5 text-[10px] font-semibold text-white truncate max-w-[200px]"
                            x-text="activeMaterial.name"></span>
                    </template>
                  </div>
                  
                  <div class="sans mt-2.5 text-[18px] md:text-[20px] font-semibold leading-[1.2]">
                    <span x-text="activeMaterial ? activeMaterial.name : (selectedTypology ? selectedTypology.vi : '')"></span>
                  </div>
                  <div class="sans mt-1 text-[11px] text-white/60">
                    <span x-text="selectedTypology ? selectedTypology.vi : ''"></span> •
                    <span x-text="selectedSurface ? selectedSurface.vi : ''"></span> •
                    <span x-text="selectedArea ? selectedArea.vi : ''"></span>
                  </div>

                  <!-- Tab 1: AI / Tiêu chuẩn (Theo từng sản phẩm được click) -->
                  <div x-show="activeTab === 'ai'" class="mt-3.5 space-y-2.5">
                    <div class="rounded-xl bg-white/10 p-3">
                      <div class="sans text-[10px] uppercase tracking-widest text-white/50">Căn cứ áp dụng</div>
                      <div class="sans mt-1.5 space-y-1.5 text-[11px] leading-[1.55] text-white/80 max-h-[180px] overflow-y-auto scrollbar-thin pr-1">
                        <template x-for="(line, lIdx) in getMaterialStandardsLines(activeMaterial)" :key="lIdx">
                          <div x-html="line"></div>
                        </template>
                      </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                      <div class="rounded-xl bg-white/10 p-2.5">
                        <div class="sans text-[9px] uppercase tracking-widest text-white/50">Yêu cầu cháy</div>
                        <div class="sans mt-1 text-[11px] font-medium leading-tight"
                             x-text="getMaterialFireRequirement(activeMaterial)"></div>
                      </div>
                      <div class="rounded-xl bg-[#C1272D] p-2.5">
                        <div class="sans text-[9px] uppercase tracking-widest text-white/70">Chỉ số</div>
                        <div class="sans mt-1 text-[11px] leading-tight"
                             x-text="getMaterialMetrics(activeMaterial)"></div>
                      </div>
                    </div>
                  </div>

                  <!-- Tab 2: Workspace Field Experience (Theo từng sản phẩm được click) -->
                  <div x-show="activeTab === 'workspace'" class="mt-3.5 space-y-2.5">
                    <div class="rounded-xl bg-white/10 p-3">
                      <div class="sans text-[10px] uppercase tracking-widest text-white/50">Thực tế thi công</div>
                      <div class="sans mt-1.5 space-y-1.5 text-[11px] leading-[1.55] text-white/80 max-h-[180px] overflow-y-auto scrollbar-thin pr-1">
                        <template x-for="(line, wIdx) in getMaterialExperienceLines(activeMaterial)" :key="wIdx">
                          <div x-html="line"></div>
                        </template>
                      </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                      <div class="rounded-xl bg-white/10 p-2.5">
                        <div class="sans text-[9px] uppercase tracking-widest text-white/50">Lỗi thường gặp</div>
                        <div class="sans mt-1 text-[10px] leading-tight"
                             x-text="getMaterialCommonErrors(activeMaterial)"></div>
                      </div>
                      <div class="rounded-xl bg-[#C1272D] p-2.5">
                        <div class="sans text-[9px] uppercase tracking-widest text-white/70">Mẹo</div>
                        <div class="sans mt-1 text-[10px] leading-tight"
                             x-text="getMaterialTips(activeMaterial)"></div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Breadcrumb Path & Action Buttons -->
                <div class="rounded-[12px] border border-[#1F1E1C]/10 bg-white p-3.5">
                  <div class="sans text-[10px] font-bold uppercase tracking-widest text-[#1F1E1C]/40">Đường dẫn</div>
                  <div class="mt-2 flex flex-wrap gap-1.5">
                    <button type="button" @click="goToStep(1, -1)" class="sans rounded-full bg-[#1F1E1C] px-3 py-1 text-[11px] text-white" x-text="selectedTypology ? selectedTypology.vi : ''"></button>
                    <button type="button" @click="goToStep(2, -1)" class="sans rounded-full bg-[#1F1E1C]/10 px-3 py-1 text-[11px]" x-text="selectedSurface ? selectedSurface.vi : ''"></button>
                    <button type="button" @click="goToStep(3, -1)" class="sans rounded-full border px-3 py-1 text-[11px]" x-text="selectedArea ? selectedArea.vi : ''"></button>
                  </div>
                  <div class="mt-2.5 flex gap-2">
                    <button type="button" @click="resetAll()" class="sans rounded-full border px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest hover:bg-gray-50 transition-colors">Làm lại từ đầu</button>
                    <button type="button" @click="goToStep(4, -1)" class="sans rounded-full bg-[#C1272D] px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-white hover:bg-[#a81f25] transition-colors">Sửa nhu cầu</button>
                  </div>
                </div>
              </div>

              <!-- Right Column: Materials Cards Grid -->
              <div class="col-span-12 md:col-span-7">
                <div class="grid grid-cols-1 gap-3">
                  <template x-for="item in filteredMaterials" :key="item.id">
                    <div @click="selectMaterial(item)"
                         class="flex items-stretch gap-3 rounded-[14px] border p-4 shadow-sm hover:shadow-md transition-all cursor-pointer"
                         :class="activeMaterial && activeMaterial.id === item.id ? 'border-[#C1272D] ring-2 ring-[#C1272D]/20 bg-[#FFFDF9]' : 'border-[#1F1E1C]/10 bg-white hover:border-[#1F1E1C]/25'">
                      <!-- Left Swatch Icon Column -->
                      <div class="flex w-[72px] shrink-0 flex-col items-center justify-center rounded-[10px] bg-[#F9F6F0] p-2">
                        <div class="sans text-[8px] font-bold uppercase tracking-widest text-[#1F1E1C]/40 text-center truncate w-full" x-text="item.category"></div>
                        <template x-if="item.image_url">
                          <img :src="item.image_url" :alt="item.name" class="mt-1 h-9 w-9 rounded-full border border-[#1F1E1C]/10 object-cover bg-white">
                        </template>
                        <template x-if="!item.image_url">
                          <div class="mt-1 h-8 w-8 rounded-full border border-[#1F1E1C]/10 bg-white"
                               :style="getSwatchGradient(item.id)"></div>
                        </template>
                        <div class="mt-2 sans text-[9px] font-bold text-[#1F1E1C]/50" x-text="item.durabilityYears + ' năm'"></div>
                      </div>

                      <!-- Center & Content Column -->
                      <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                          <div class="flex items-center gap-2 min-w-0">
                            <div class="sans text-[15px] font-semibold leading-none truncate" x-text="item.name"></div>
                            <span class="sans text-[10px] text-[#1F1E1C]/40 shrink-0" x-text="item.brand"></span>
                          </div>
                          <span x-show="activeMaterial && activeMaterial.id === item.id"
                                class="sans shrink-0 rounded-full bg-[#C1272D]/10 px-2 py-0.5 text-[9px] font-bold text-[#C1272D]">
                            Đang chọn
                          </span>
                        </div>

                        <div class="mt-2 flex flex-wrap gap-1.5">
                          <span class="sans rounded-full bg-[#1F1E1C] px-2.5 py-1 text-[10px] font-medium text-white" x-text="item.originLabel"></span>
                          <span class="sans rounded-full bg-[#1F1E1C]/10 px-2.5 py-1 text-[10px] font-medium" x-text="item.availability"></span>
                          <span class="sans rounded-full bg-[#C1272D]/10 px-2.5 py-1 text-[10px] font-medium text-[#C1272D]" x-text="item.costLabel"></span>
                          <span class="sans rounded-full border border-[#1F1E1C]/10 bg-white px-2.5 py-1 text-[10px] text-[#1F1E1C]/70" x-text="item.finish"></span>
                        </div>

                        <!-- Technical Specification or Practical Experience Note -->
                        <div class="mt-2.5 rounded-[8px] bg-[#F9F6F0] p-2.5">
                          <div class="sans text-[11px] leading-[1.5] text-[#1F1E1C]/80 whitespace-pre-line"
                               x-html="activeTab === 'ai' ? (item.ai_standards || item.tech_desc || item.tech) : (item.ws_experience || item.experience_desc || item.application)"></div>
                        </div>

                        <!-- Score Match Badges -->
                        <div class="mt-2 flex items-center justify-between text-[11px]">
                          <span class="sans text-[10px] text-[#1F1E1C]/50" x-text="item.tech"></span>
                          <div class="flex items-center gap-2">
                            <span class="sans rounded-full px-2 py-0.5 text-[10px] font-bold"
                                  :class="activeTab === 'ai' ? 'bg-[#1F1E1C] text-white' : 'bg-[#1F1E1C]/10 text-[#1F1E1C]'"
                                  x-text="getAiScore(item) + '% AI Match'"></span>
                            <span class="sans rounded-full px-2 py-0.5 text-[10px] font-bold"
                                  :class="activeTab === 'workspace' ? 'bg-[#C1272D] text-white' : 'bg-[#C1272D]/10 text-[#C1272D]'"
                                  x-text="getWorkspaceScore(item) + '% Workspace'"></span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </template>

                  <!-- Empty State -->
                  <div x-show="filteredMaterials.length === 0"
                       class="rounded-[14px] border border-dashed border-[#1F1E1C]/20 bg-white p-8 text-center">
                    <div class="text-[32px]">🔍</div>
                    <div class="sans mt-2 text-[15px] font-semibold">Chưa tìm thấy vật liệu phù hợp với tất cả bộ lọc</div>
                    <p class="sans mt-1 text-[12px] text-[#1F1E1C]/50">Thử nới lỏng ngân sách, thời gian tồn kho hoặc nguồn gốc để xem thêm các lựa chọn tương đương.</p>
                    <button type="button" @click="relaxFilters()" class="sans mt-4 rounded-full bg-[#1F1E1C] text-white px-4 py-2 text-[12px] font-medium">Bỏ giới hạn ngân sách &amp; tồn kho</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>

  <script>
    function materialLabApp() {
      return {
        step: 1,
        slideDir: 1,
        statusMsg: '',
        resetCount: 0,
        activeTab: 'ai',
        activeFilterFocus: 'durability',
        activePopover: null,

        // Data from Laravel backend
        typologies: @json($typologies ?? []),
        surfaces: @json($surfaces ?? []),
        areaMatrix: @json($areaMatrix ?? []),
        areaLabels: @json($areaLabels ?? []),
        lifeCycles: @json($lifeCycles ?? []),
        budgetLevels: @json($budgetLevels ?? []),
        step5Insight: @json($step5Insight ?? []),
        allMaterials: @json($materials ?? []),

        // User Selection State
        selectedTypology: null,
        selectedSurface: null,
        selectedArea: null,
        selectedDurability: null,
        selectedBudget: null,
        selectedOrigin: null,
        selectedAvailability: null,
        selectedMaterial: null,

        initApp() {
          // Pre-select defaults for quick usability
          this.selectedDurability = this.lifeCycles[1] || null; // medium: 5-10 years
          this.selectedBudget = this.budgetLevels[1] || null; // level 2: Balanced
          this.selectedOrigin = 'Vietnam';
          this.selectedAvailability = 'Stock';
        },

        get activeMaterial() {
          const list = this.filteredMaterials;
          if (!list || list.length === 0) return null;
          if (this.selectedMaterial) {
            const found = list.find(m => m.id === this.selectedMaterial.id);
            if (found) return found;
          }
          return list[0];
        },

        selectMaterial(item) {
          this.selectedMaterial = item;
          this.statusMsg = 'Đang xem: ' + item.name;
        },

        getMaterialStandardsLines(m) {
          const raw = (m && (m.ai_standards || m.tech_desc)) || this.step5Insight.ai_standards || '';
          const lines = raw.split(/\r?\n/).map(s => s.trim()).filter(Boolean);
          if (lines.length > 0) return lines;
          return ['• QCVN 06:2022/BXD – An toàn cháy: lối thoát nạn yêu cầu vật liệu không cháy / chậm cháy.'];
        },

        getMaterialFireRequirement(m) {
          if (m && m.fire_requirement) return m.fire_requirement;
          if (this.selectedSurface && this.selectedSurface.en === 'Floor') {
            return this.step5Insight.ai_fire_floor || 'Bfl-s1 / B1 cho thoát nạn';
          }
          if (this.selectedSurface && this.selectedSurface.en === 'Ceiling') {
            return this.step5Insight.ai_fire_ceiling || 'Class A – 30 phút';
          }
          return this.step5Insight.ai_fire_other || 'B1 hành lang';
        },

        getMaterialMetrics(m) {
          if (m && (m.metrics || m.tech)) return m.metrics || m.tech;
          return this.step5Insight.ai_metrics || 'R9-11 • VOC <50 • NRC 0.45-0.70 • E0';
        },

        getMaterialExperienceLines(m) {
          const raw = (m && (m.ws_experience || m.experience_desc)) || this.step5Insight.ws_experience || '';
          const lines = raw.split(/\r?\n/).map(s => s.trim()).filter(Boolean);
          if (lines.length > 0) return lines;
          return ['• Đã kiểm chứng thực tế thi công tại các dự án Workspace.'];
        },

        getMaterialCommonErrors(m) {
          if (m && m.common_errors) return m.common_errors;
          return this.step5Insight.ws_common_errors || 'Nền không phẳng • Ẩm >75% • Keo rẻ bong mép';
        },

        getMaterialTips(m) {
          if (m && m.tips) return m.tips;
          return this.step5Insight.ws_tips || 'Đo ẩm • Cán phẳng 3mm • Keo Wakol/Weber • Khe 5mm';
        },

        get availableAreas() {
          if (!this.selectedTypology || !this.selectedSurface) return [];
          const typoKey = this.selectedTypology.en;
          const surfKey = this.selectedSurface.en;
          const list = (this.areaMatrix[typoKey] && this.areaMatrix[typoKey][surfKey]) || [];
          return list.map(enArea => ({
            en: enArea,
            vi: this.areaLabels[enArea] || enArea
          }));
        },

        get filteredMaterials() {
          let list = this.allMaterials;

          const typoKey = this.selectedTypology ? this.selectedTypology.en : null;
          const surfKey = this.selectedSurface ? this.selectedSurface.en : null;
          const areaKey = this.selectedArea ? this.selectedArea.en : null;

          const aliasMap = {
            'Reception Desk': 'Reception',
            'Meeting Table': 'Meeting Room',
            'Pantry Counter': 'Pantry',
            'Executive Desk': 'Executive',
            'Corridor Handrail': 'Corridor',
            'Classroom Desk': 'Classroom',
            'Library Shelf': 'Library',
            'Canteen Counter': 'Canteen',
            'Cashier Counter': 'Cashier',
            'Kitchen Counter': 'Kitchen',
            'Stair Handrail': 'Stair',
            'Wardrobe Surface': 'Wardrobe',
            'Bath Vanity': 'WC',
            'Facade Cladding': 'Facade',
            'Fitting Bench': 'Fitting Room',
            'Window Display': 'Display Shelf',
            'Balcony Railing': 'Balcony',
          };
          const canonTarget = areaKey ? (aliasMap[areaKey] || areaKey) : null;

          // 3-tier compatibility check: Typology -> Surface -> Area
          if (typoKey || surfKey || areaKey) {
            list = list.filter(m => {
              const compat = m.compatibility || null;

              if (compat && typeof compat === 'object') {
                // Tier 1: Typology
                if (typoKey) {
                  const typoConf = compat[typoKey];
                  if (!typoConf || !typoConf.enabled) {
                    return false;
                  }

                  // Tier 2: Surface within this Typology
                  if (surfKey) {
                    const surfConf = typoConf.surfaces ? typoConf.surfaces[surfKey] : null;
                    if (!surfConf || !surfConf.enabled) {
                      return false;
                    }

                    // Tier 3: Area within this (Typology + Surface) pair
                    if (areaKey) {
                      const areas = Array.isArray(surfConf.areas) ? surfConf.areas : [];
                      // Empty array = applies to ALL areas of this surface in this typology
                      if (areas.length > 0) {
                        const hasMatch = areas.some(a => {
                          const cA = aliasMap[a] || a;
                          return cA === canonTarget || a === areaKey;
                        });
                        if (!hasMatch) {
                          return false;
                        }
                      }
                    }
                  }
                }
                return true;
              }

              // Fallback to legacy fields if compatibility matrix is missing
              if (typoKey) {
                if (Array.isArray(m.suitableTypologies) && m.suitableTypologies.length > 0 && !m.suitableTypologies.includes(typoKey)) {
                  return false;
                }
              }
              if (surfKey) {
                if (!Array.isArray(m.suitableLocations) || !m.suitableLocations.includes(surfKey)) {
                  return false;
                }
              }
              if (areaKey) {
                if (Array.isArray(m.suitableAreas) && m.suitableAreas.length > 0) {
                  const hasMatch = m.suitableAreas.some(a => {
                    const cA = aliasMap[a] || a;
                    return cA === canonTarget || a === areaKey;
                  });
                  if (!hasMatch) return false;
                }
              }
              return true;
            });
          }

          if (this.selectedDurability) {
            list = list.filter(m => m.durabilityYears >= (this.selectedDurability.years - 1));
          }
          if (this.selectedBudget && !this.selectedBudget.isSkip) {
            list = list.filter(m => m.budgetLevel <= (this.selectedBudget.level + 1));
          }
          if (this.selectedOrigin && this.selectedOrigin !== 'Any') {
            list = list.filter(m => m.origin === this.selectedOrigin || m.origin === 'Both');
          }
          if (this.selectedAvailability) {
            if (this.selectedAvailability === 'Stock') {
              list = list.filter(m => m.availability === 'Stock');
            } else if (this.selectedAvailability === '2-3 weeks') {
              list = list.filter(m => m.availability === 'Stock' || m.availability === '2-3 weeks');
            }
          }

          // Sort by match score
          return list.sort((a, b) => {
            const scoreA = this.getAiScore(a) + this.getWorkspaceScore(a);
            const scoreB = this.getAiScore(b) + this.getWorkspaceScore(b);
            return scoreB - scoreA;
          });
        },

        selectTypology(item) {
          this.selectedTypology = item;
          this.selectedSurface = null;
          this.selectedArea = null;
          this.statusMsg = 'Chọn ' + item.vi;
          setTimeout(() => this.goToStep(2, 1), 250);
        },

        selectSurface(item) {
          this.selectedSurface = item;
          this.selectedArea = null;
          this.statusMsg = 'Chọn ' + item.vi;
          setTimeout(() => this.goToStep(3, 1), 250);
        },

        selectArea(item) {
          this.selectedArea = item;
          this.activeFilterFocus = 'durability';
          this.statusMsg = 'Chọn ' + item.vi;
          setTimeout(() => this.goToStep(4, 1), 250);
        },

        onDurabilitySlider(val) {
          const idx = parseInt(val, 10);
          if (this.lifeCycles[idx]) {
            this.selectedDurability = this.lifeCycles[idx];
            this.activeFilterFocus = 'budget';
          }
        },

        setDurability(item) {
          this.selectedDurability = item;
          this.activeFilterFocus = 'budget';
        },

        onBudgetSlider(val) {
          const lvl = parseInt(val, 10);
          const found = this.budgetLevels.find(b => b.level === lvl);
          if (found) {
            this.selectedBudget = found;
            this.activeFilterFocus = 'origin';
          }
        },

        setBudget(item) {
          this.selectedBudget = item;
          this.activeFilterFocus = 'origin';
        },

        setOrigin(orig) {
          this.selectedOrigin = orig;
          this.activeFilterFocus = 'availability';
        },

        setAvailability(avail) {
          this.selectedAvailability = avail;
        },

        skipStep4() {
          this.selectedDurability = null;
          this.selectedBudget = this.budgetLevels.find(b => b.isSkip) || this.budgetLevels[4] || null;
          this.selectedOrigin = 'Any';
          this.selectedAvailability = 'Any';
          this.goToStep(5, 1);
        },

        relaxFilters() {
          this.selectedBudget = this.budgetLevels.find(b => b.isSkip) || this.budgetLevels[4];
          this.selectedAvailability = 'Any';
          this.selectedOrigin = 'Any';
        },

        goToStep(s, dir = 1) {
          this.slideDir = dir;
          this.step = s;
          const container = document.getElementById('wizard-scroll-container');
          if (container) container.scrollTop = 0;
          window.scrollTo({ top: 0, behavior: 'smooth' });

          if (s === 5) {
            setTimeout(() => {
              const res = document.getElementById('results-section');
              if (res) res.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
          }
        },

        togglePopover(name) {
          this.activePopover = this.activePopover === name ? null : name;
        },

        resetAll() {
          this.selectedTypology = null;
          this.selectedSurface = null;
          this.selectedArea = null;
          this.selectedMaterial = null;
          this.selectedDurability = this.lifeCycles[1];
          this.selectedBudget = this.budgetLevels[1];
          this.selectedOrigin = 'Vietnam';
          this.selectedAvailability = 'Stock';
          this.activePopover = null;
          this.activeTab = 'ai';
          this.resetCount++;
          this.statusMsg = 'Đã đặt lại';
          this.goToStep(1, -1);
        },

        hashString(str) {
          let h = 0;
          for (let i = 0; i < str.length; i++) {
            h = (h * 31 + str.charCodeAt(i)) % 100;
          }
          return h;
        },

        getAiScore(m) {
          const areaKey = this.selectedArea ? this.selectedArea.en : '';
          const typoKey = this.selectedTypology ? this.selectedTypology.en : '';
          let score = 84 + (this.hashString(m.id + areaKey + 'AI' + typoKey) % 12);
          if (m.tech && (m.tech.includes('Fire A') || m.tech.includes('Bfl'))) score += 3;
          if (m.tech && (m.tech.includes('R11') || m.tech.includes('R10') || m.tech.includes('R9'))) score += 2;
          if (m.durabilityYears >= 10) score += 2;
          return Math.min(98, score);
        },

        getWorkspaceScore(m) {
          const typoKey = this.selectedTypology ? this.selectedTypology.en : '';
          const bLvl = this.selectedBudget ? this.selectedBudget.level : 2;
          let score = 82 + (this.hashString(m.id + typoKey + 'WS' + bLvl) % 14);
          if (this.selectedBudget && m.budgetLevel === this.selectedBudget.level) score += 4;
          if (this.selectedAvailability === 'Stock' && m.availability === 'Stock') score += 4;
          if (this.selectedOrigin === 'Vietnam' && m.origin === 'Vietnam') score += 2;
          return Math.min(97, score);
        },

        getSwatchGradient(id) {
          const colorMap = {
            'lvt': '#D9C5A0',
            'terrazzo': '#CFC9C0',
            'porcelain': '#D6D2C8',
            'dulux': '#EAE6E0',
            'corian': '#E6E4DC',
            'veneer-oak': '#C5A880',
            'carpet': '#9E9D99',
            'pet-felt': '#7A7D7E',
            'inox': '#BDC3C7',
            'rubber-nora': '#5D6D7E',
            'armstrong': '#ECEAE4',
            'gypsum': '#E5E4E2',
            'kvadrat': '#8C584E',
            'glass-lowe': '#D0E1FD',
            'laminate-hpl': '#B8977E'
          };
          const baseColor = colorMap[id] || '#CFC9C0';
          return `background: radial-gradient(circle at 30% 30%, ${baseColor} 0%, white 70%);`;
        }
      };
    }
  </script>
</body>
</html>
