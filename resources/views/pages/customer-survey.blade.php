<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $survey->title ?? 'KHẢO SÁT HÀI LÒNG' }} • {{ $survey->subtitle ?? 'WORKSPACE®' }}</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; }
      .print-shadow { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
    input, textarea, select { font-family: "Times New Roman", Times, serif; }
    .logo-sharp {
      image-rendering: auto;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      transform: translateZ(0);
      backface-visibility: hidden;
      object-fit: contain;
      display: block;
      will-change: transform;
    }
    @media (max-width: 1024px) {
      .logo-sharp { height: 68px !important; }
    }
    @media (max-width: 768px) {
      .logo-sharp { height: 52px !important; max-width: 200px !important; }
    }
    [x-cloak] { display: none !important; }
  </style>
</head>
<body class="min-h-screen bg-[#F6F4F0] text-[#111] print:bg-white"
      style="font-family: 'Times New Roman', Times, Georgia, serif;"
      x-data="customerSurveyApp()"
      x-init="initApp()">

  <main class="max-w-[980px] mx-auto px-4 md:px-8 pt-6 md:pt-8 pb-[80px]">
    <div class="bg-white print-shadow border border-black/10 shadow-[0_8px_30px_rgba(0,0,0,0.06)]">

      <!-- ============================================== -->
      <!-- HEADER BAR WITH LOGO & TITLES                  -->
      <!-- ============================================== -->
      <div class="px-6 md:px-10 border-b border-black/10 flex justify-between items-center"
           style="padding-top: 18px; padding-bottom: 18px; gap: 24px;">
        <div class="flex items-center" style="flex: 0 0 auto;">
          <a href="{{ url('/') }}">
            <img src="{{ file_exists(public_path('images/workspace-logo-survey.png')) ? asset('images/workspace-logo-survey.png') : (file_exists(public_path('images/workspace-logo-mat.png')) ? asset('images/workspace-logo-mat.png') : asset('Logo.png')) }}"
                 alt="Workspace - Interior design & build"
                 width="320" height="119"
                 class="logo-sharp select-none"
                 style="width: auto; height: 76px; max-width: 320px; object-fit: contain; display: block;"
                 decoding="sync" loading="eager">
          </a>
        </div>
        <div class="text-right" style="flex: 1; max-width: 55%; text-align: right;">
          <h1 class="font-bold leading-[1.2] uppercase"
              style="font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">
            {{ $survey->title ?? 'KHẢO SÁT HÀI LÒNG' }}
          </h1>
          <div class="mt-1 text-[11px] md:text-[12px] tracking-[0.22em] text-black/40 uppercase font-light"
               style="font-family: Arial, sans-serif;">
            {{ $survey->subtitle ?? 'WORKSPACE®' }}
          </div>
          <p class="mt-2 text-[12px] md:text-[13px] italic leading-[1.5] text-black/50 ml-auto max-w-[420px]">
            {{ $survey->thank_you_note ?? 'Cảm ơn Quý Khách hàng đã tin tưởng và đồng hành cùng chúng tôi' }}
          </p>
        </div>
      </div>

      <!-- ============================================== -->
      <!-- FORM BODY                                      -->
      <!-- ============================================== -->
      <form @submit.prevent="submitSurvey()" class="px-6 md:px-10 py-7 md:py-9">
        @csrf
        <input type="hidden" name="survey_id" value="{{ $survey->id }}">
        @if($invitation)
          <input type="hidden" name="invitation_token" value="{{ $invitation->token }}">
        @endif

        <!-- Intro Greeting Notice -->
        <div class="mb-8 border-l-[3px] border-[#E30613] bg-[#FFF8F8] px-5 py-4 md:px-6">
          <p class="text-[14px] md:text-[15px] leading-[1.6] italic">
            {{ $survey->intro_note ?? 'Kính chào Quý Khách hàng, nhằm không ngừng nâng cao chất lượng dịch vụ, chúng tôi kính mong Quý vị dành ít phút đánh giá về chất lượng công trình và dịch vụ của chúng tôi.' }}
          </p>
        </div>

        <!-- ============================================== -->
        <!-- SECTION 1: THÔNG TIN DỰ ÁN                     -->
        <!-- ============================================== -->
        <section class="mb-10">
          <div class="flex items-center gap-3 mb-5">
            <div class="w-7 h-7 rounded-full bg-[#E30613] text-white flex items-center justify-center text-[12px] font-bold"
                 style="font-family: Arial, sans-serif;">1</div>
            <h2 class="text-[15px] font-bold uppercase tracking-wide">Thông tin dự án</h2>
            <div class="flex-1 h-px bg-black/10 ml-3"></div>
          </div>

          <div class="grid md:grid-cols-2 gap-5">
            <!-- Tên KH -->
            <div class="space-y-1.5">
              <label class="text-[12px] uppercase tracking-widest opacity-60 font-bold" style="font-family: Arial, sans-serif;">
                Tên khách hàng / Công ty <span class="text-[#E30613]">*</span>
              </label>
              <input type="text"
                     x-model="form.tenKH"
                     required
                     placeholder="VD: Công ty TNHH ABC"
                     class="w-full h-[42px] px-4 border border-black/15 bg-[#FCFCF9] focus:outline-none focus:border-[#E30613] focus:bg-white text-[15px] transition-colors">
            </div>

            <!-- Tên DA -->
            <div class="space-y-1.5">
              <label class="text-[12px] uppercase tracking-widest opacity-60 font-bold" style="font-family: Arial, sans-serif;">
                Tên dự án <span class="text-[#E30613]">*</span>
              </label>
              <input type="text"
                     x-model="form.tenDA"
                     required
                     placeholder="VD: Văn phòng Quận 1 - 200m2"
                     class="w-full h-[42px] px-4 border border-black/15 bg-[#FCFCF9] focus:outline-none focus:border-[#E30613] focus:bg-white text-[15px] transition-colors">
            </div>

            <!-- Ngày Bàn Giao -->
            <div class="space-y-1.5">
              <label class="text-[12px] uppercase tracking-widest opacity-60 font-bold" style="font-family: Arial, sans-serif;">
                Ngày bàn giao <span class="text-[#E30613]">*</span>
              </label>
              <input type="date"
                     x-model="form.ngayBG"
                     required
                     class="w-full h-[42px] px-4 border border-black/15 bg-[#FCFCF9] focus:outline-none focus:border-[#E30613] focus:bg-white text-[15px] transition-colors">
            </div>

            <!-- QLDA Phụ Trách -->
            <div class="space-y-1.5">
              <label class="text-[12px] uppercase tracking-widest opacity-60 font-bold" style="font-family: Arial, sans-serif;">
                QLDA phụ trách
              </label>
              <input type="text"
                     x-model="form.qlda"
                     placeholder="VD: Mr. Vũ - 090..."
                     class="w-full h-[42px] px-4 border border-black/15 bg-[#FCFCF9] focus:outline-none focus:border-[#E30613] focus:bg-white text-[15px] transition-colors">
            </div>

            <!-- Email (Optional) -->
            <div class="space-y-1.5">
              <label class="text-[12px] uppercase tracking-widest opacity-60 font-bold" style="font-family: Arial, sans-serif;">
                Email liên hệ (nhận bản sao kết quả)
              </label>
              <input type="email"
                     x-model="form.email"
                     placeholder="email@company.com"
                     class="w-full h-[42px] px-4 border border-black/15 bg-[#FCFCF9] focus:outline-none focus:border-[#E30613] focus:bg-white text-[15px] transition-colors">
            </div>

            <!-- Số điện thoại (Optional) -->
            <div class="space-y-1.5">
              <label class="text-[12px] uppercase tracking-widest opacity-60 font-bold" style="font-family: Arial, sans-serif;">
                Số điện thoại
              </label>
              <input type="tel"
                     x-model="form.phone"
                     placeholder="0901234567"
                     class="w-full h-[42px] px-4 border border-black/15 bg-[#FCFCF9] focus:outline-none focus:border-[#E30613] focus:bg-white text-[15px] transition-colors">
            </div>
          </div>
        </section>

        <!-- ============================================== -->
        <!-- SCALE INSTRUCTION GUIDE BOX                    -->
        <!-- ============================================== -->
        <section class="mb-10">
          <div class="border border-[#E30613]/20 bg-[#FFF8F8] px-5 py-4 md:px-6 md:py-5 flex gap-4">
            <div class="w-8 h-8 bg-[#E30613] text-white flex items-center justify-center shrink-0 mt-0.5">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="font-bold text-[13px] uppercase tracking-widest mb-2" style="font-family: Arial, sans-serif;">
                Hướng dẫn chấm điểm (Thang 1-5)
              </h3>
              <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-[12px] leading-[1.35]" style="font-family: Arial, sans-serif;">
                <div><span class="font-bold">1 - Rất kém:</span> Không đạt, cần khắc phục ngay</div>
                <div><span class="font-bold">2 - Kém:</span> Dưới kỳ vọng</div>
                <div><span class="font-bold">3 - Trung bình:</span> Đạt yêu cầu cơ bản</div>
                <div><span class="font-bold">4 - Tốt:</span> Vượt kỳ vọng</div>
                <div class="text-[#E30613] font-bold"><span>5 - Xuất sắc:</span> Vượt trội, ấn tượng</div>
              </div>
            </div>
          </div>
        </section>

        <!-- ============================================== -->
        <!-- SECTION 2 (PHẦN A): ĐÁNH GIÁ SAU BÀN GIAO       -->
        <!-- ============================================== -->
        <section class="mb-10">
          <div class="flex items-center justify-between gap-4 mb-5">
            <div class="flex items-center gap-3">
              <div class="w-7 h-7 rounded-full bg-[#111] text-white flex items-center justify-center text-[12px] font-bold"
                   style="font-family: Arial, sans-serif;">A</div>
              <h2 class="text-[14px] font-bold uppercase tracking-[0.08em]">PHẦN A: Đánh giá ngay sau bàn giao</h2>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-[11px] tracking-widest opacity-50 hidden md:block" style="font-family: Arial, sans-serif;">
                <span x-text="countAnsweredA"></span>/{{ count($questionsA) }} ĐÃ CHẤM
              </span>
              <div class="px-3 py-1 bg-black text-white text-[12px] font-bold tracking-wide" style="font-family: Arial, sans-serif;">
                TB: <span x-text="avgScoreA || '--'"></span> / 5.0
              </div>
            </div>
          </div>

          <div class="border border-black/10 divide-y divide-black/10">
            <!-- Table Header for desktop -->
            <div class="hidden md:grid grid-cols-[1fr_260px] bg-[#111] text-white text-[11px] tracking-widest px-5 py-2.5"
                 style="font-family: Arial, sans-serif;">
              <div>TIÊU CHÍ ĐÁNH GIÁ</div>
              <div class="grid grid-cols-5 text-center">
                <span>1</span>
                <span>2</span>
                <span>3</span>
                <span>4</span>
                <span>5</span>
              </div>
            </div>

            <!-- List Questions A -->
            @foreach($questionsA as $index => $qa)
              <div class="grid md:grid-cols-[1fr_260px] px-5 py-4 items-center gap-3 hover:bg-[#FCFCF9] transition-colors">
                <div class="flex items-start gap-3">
                  <span class="text-[11px] font-bold mt-0.5 px-1.5 py-0.5 bg-black/5" style="font-family: Arial, sans-serif;">
                    {{ $qa->code }}
                  </span>
                  <div class="flex-1">
                    <p class="text-[14px] leading-[1.35]">
                      {{ $index + 1 }}. {{ $qa->question_text }}
                    </p>
                  </div>
                </div>
                <div class="grid grid-cols-5 gap-1.5">
                  <template x-for="val in [1, 2, 3, 4, 5]" :key="val">
                    <button type="button"
                            @click="answersA['{{ $qa->code }}'] = val"
                            class="h-[36px] rounded-[6px] border text-[13px] font-bold transition-all"
                            :class="answersA['{{ $qa->code }}'] === val 
                              ? 'bg-[#E30613] text-white border-[#E30613] shadow-[0_2px_10px_rgba(227,6,19,0.35)] scale-[1.05]' 
                              : 'bg-white border-black/15 hover:border-black/30 hover:bg-black/[0.02]'"
                            style="font-family: Arial, sans-serif;"
                            x-text="val">
                    </button>
                  </template>
                </div>
              </div>
            @endforeach
          </div>
        </section>

        <!-- ============================================== -->
        <!-- SECTION 3 (PHẦN B): ĐÁNH GIÁ ĐỊNH KỲ BẢO HÀNH   -->
        <!-- ============================================== -->
        <section class="mb-10">
          <div class="flex items-center justify-between gap-4 mb-5">
            <div class="flex items-center gap-3">
              <div class="w-7 h-7 rounded-full bg-[#E30613] text-white flex items-center justify-center text-[12px] font-bold"
                   style="font-family: Arial, sans-serif;">B</div>
              <h2 class="text-[14px] font-bold uppercase tracking-[0.08em]">PHẦN B: Đánh giá định kỳ bảo hành</h2>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-[11px] tracking-widest opacity-50 hidden md:block" style="font-family: Arial, sans-serif;">
                <span x-text="countAnsweredB"></span>/{{ count($questionsB) }} ĐÃ CHẤM + NPS
              </span>
              <div class="px-3 py-1 bg-[#E30613] text-white text-[12px] font-bold tracking-wide" style="font-family: Arial, sans-serif;">
                TB: <span x-text="avgScoreB || '--'"></span> / 5.0
              </div>
            </div>
          </div>

          <div class="border border-black/10 divide-y divide-black/10">
            <!-- Table Header for desktop -->
            <div class="hidden md:grid grid-cols-[1fr_260px] bg-[#111] text-white text-[11px] tracking-widest px-5 py-2.5"
                 style="font-family: Arial, sans-serif;">
              <div>TIÊU CHÍ BẢO HÀNH</div>
              <div class="grid grid-cols-5 text-center">
                <span>1</span>
                <span>2</span>
                <span>3</span>
                <span>4</span>
                <span>5</span>
              </div>
            </div>

            <!-- List Questions B -->
            @foreach($questionsB as $index => $qb)
              <div class="grid md:grid-cols-[1fr_260px] px-5 py-4 items-center gap-3 hover:bg-[#FCFCF9] transition-colors">
                <div class="flex items-start gap-3">
                  <span class="text-[11px] font-bold mt-0.5 px-1.5 py-0.5 bg-black/5" style="font-family: Arial, sans-serif;">
                    {{ $qb->code }}
                  </span>
                  <div class="flex-1">
                    <p class="text-[14px] leading-[1.35]">
                      {{ $index + 1 }}. {{ $qb->question_text }}
                    </p>
                  </div>
                </div>
                <div class="grid grid-cols-5 gap-1.5">
                  <template x-for="val in [1, 2, 3, 4, 5]" :key="val">
                    <button type="button"
                            @click="answersB['{{ $qb->code }}'] = val"
                            class="h-[36px] rounded-[6px] border text-[13px] font-bold transition-all"
                            :class="answersB['{{ $qb->code }}'] === val 
                              ? 'bg-[#111] text-white border-[#111] shadow-[0_2px_10px_rgba(0,0,0,0.25)] scale-[1.05]' 
                              : 'bg-white border-black/15 hover:border-black/30'"
                            style="font-family: Arial, sans-serif;"
                            x-text="val">
                    </button>
                  </template>
                </div>
              </div>
            @endforeach

            <!-- B5: NPS RATING (0 - 10) -->
            <div class="px-5 py-6 bg-[#FFFDF6] border-t border-black/10">
              <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-4">
                <div class="flex items-start gap-3">
                  <span class="text-[11px] font-bold mt-0.5 px-1.5 py-0.5 bg-[#E30613] text-white" style="font-family: Arial, sans-serif;">B5</span>
                  <p class="text-[14px] font-bold leading-[1.35]">
                    {{ $questionNps->question_text ?? 'Anh/Chị có sẵn sàng giới thiệu Workspace cho bạn bè / đối tác?' }}
                  </p>
                </div>
                <div class="px-3 py-1 bg-[#111] text-white text-[12px] font-bold self-start md:self-auto" style="font-family: Arial, sans-serif;">
                  NPS: <span x-text="npsScore"></span>/10
                </div>
              </div>

              <div class="md:px-12">
                <div class="relative">
                  <!-- Range slider -->
                  <input type="range" min="0" max="10" step="1"
                         x-model="npsScore"
                         class="w-full accent-[#E30613] h-2">

                  <div class="flex justify-between mt-2 text-[10px] tracking-widest opacity-60" style="font-family: Arial, sans-serif;">
                    <span class="text-left">0 = Không bao giờ</span>
                    <span class="hidden md:block">5 = Trung lập</span>
                    <span class="text-right">10 = Chắc chắn giới thiệu</span>
                  </div>

                  <!-- 11 numeric buttons -->
                  <div class="grid grid-cols-11 gap-1 mt-3">
                    <template x-for="idx in [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]" :key="idx">
                      <button type="button"
                              @click="npsScore = idx"
                              class="h-[30px] text-[11px] font-bold border rounded transition-colors"
                              :class="npsScore == idx ? 'bg-[#E30613] text-white border-[#E30613]' : (npsScore > 6 && idx == npsScore ? 'bg-[#111] text-white border-[#111]' : 'bg-white border-black/15')"
                              style="font-family: Arial, sans-serif;"
                              x-text="idx">
                      </button>
                    </template>
                  </div>
                </div>

                <!-- NPS category labels -->
                <div class="mt-3 flex gap-2 text-[10px]" style="font-family: Arial, sans-serif;">
                  <span class="px-2 py-1 border transition-all"
                        :class="npsScore <= 6 ? 'bg-[#FEE2E2] border-[#FCA5A5] text-[#991B1B] font-bold' : 'bg-white border-black/10 opacity-50'">
                    0-6 Phản đối
                  </span>
                  <span class="px-2 py-1 border transition-all"
                        :class="npsScore >= 7 && npsScore <= 8 ? 'bg-[#FEF3C7] border-[#FCD34D] text-[#92400E] font-bold' : 'bg-white border-black/10 opacity-50'">
                    7-8 Trung lập
                  </span>
                  <span class="px-2 py-1 border transition-all"
                        :class="npsScore >= 9 ? 'bg-[#DCFCE7] border-[#86EFAC] text-[#166534] font-bold' : 'bg-white border-black/10 opacity-50'">
                    9-10 Quảng bá
                  </span>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- ============================================== -->
        <!-- SECTION 4: CÂU HỎI MỞ                           -->
        <!-- ============================================== -->
        <section class="mb-10">
          <div class="flex items-center gap-3 mb-5">
            <div class="w-7 h-7 rounded-full bg-white border border-black/15 flex items-center justify-center">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
            </div>
            <h2 class="text-[14px] font-bold uppercase tracking-[0.08em]">Câu hỏi mở</h2>
            <div class="flex-1 h-px bg-black/10 ml-3"></div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
            <div class="flex flex-col h-full">
              <label class="text-[12px] font-bold uppercase tracking-widest leading-[1.4] h-[36px] flex items-center" style="font-family: Arial, sans-serif;">
                Điều chúng tôi làm tốt nhất là gì?
              </label>
              <textarea x-model="form.openBest"
                        placeholder="Chia sẻ điểm ấn tượng nhất..."
                        class="mt-2 w-full flex-1 min-h-[140px] px-4 py-3 border border-black/15 bg-[#FCFCF9] focus:outline-none focus:border-[#E30613] focus:bg-white text-[14px] resize-none leading-[1.5]"></textarea>
            </div>

            <div class="flex flex-col h-full">
              <label class="text-[12px] font-bold uppercase tracking-widest leading-[1.4] h-[36px] flex items-center" style="font-family: Arial, sans-serif;">
                Điều chúng tôi cần cải thiện?
              </label>
              <textarea x-model="form.openImprove"
                        placeholder="Góp ý để chúng tôi phục vụ tốt hơn..."
                        class="mt-2 w-full flex-1 min-h-[140px] px-4 py-3 border border-black/15 bg-[#FCFCF9] focus:outline-none focus:border-[#E30613] focus:bg-white text-[14px] resize-none leading-[1.5]"></textarea>
            </div>
          </div>
        </section>

        <!-- ============================================== -->
        <!-- SECTION 5: CHỮ KÝ XÁC NHẬN                      -->
        <!-- ============================================== -->
        <section class="mt-12 mb-14">
          <div class="border-t border-black/10 pt-10 flex justify-center">
            <div class="w-full max-w-[400px] flex flex-col items-center">
              <div class="w-full text-right mb-16">
                <p class="text-[13px] italic text-black/70" style="font-family: 'Times New Roman', Times, serif;">
                  Tp. Hồ Chí Minh, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}
                </p>
              </div>
              <div class="flex flex-col items-center">
                <div class="w-[220px] h-[80px]" aria-hidden="true"></div>
                <div class="w-[220px] border-t border-black/25"></div>
                <p class="mt-2 text-[11px] italic text-black/40 text-center" style="font-family: Arial, sans-serif;">
                  (Chữ ký Quý Khách hàng)
                </p>
              </div>
            </div>
          </div>
        </section>

        <!-- SUBMIT BUTTON -->
        <div class="mt-2 pt-8 border-t border-black/10 flex justify-center no-print">
          <button type="submit"
                  :disabled="loading"
                  class="h-[48px] px-10 bg-[#E30613] text-white hover:bg-[#C00510] disabled:opacity-50 text-[13px] font-bold tracking-widest flex items-center justify-center gap-2 shadow-[0_6px_20px_rgba(227,6,19,0.25)] transition-colors"
                  style="font-family: Arial, sans-serif;">
            <span x-text="loading ? 'ĐANG GỬI...' : 'GỬI ĐÁNH GIÁ'"></span>
            <svg x-show="!loading" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="22" y1="2" x2="11" y2="13"></line>
              <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
          </button>
        </div>

        <!-- ERROR ALERT -->
        <div x-show="errorMessage" x-cloak class="mt-6 border-2 border-[#E30613] bg-[#FFF8F8] p-4 text-[#E30613] text-[13px]">
          <span class="font-bold">Lỗi: </span><span x-text="errorMessage"></span>
        </div>

        <!-- SUCCESS CONFIRMATION BLOCK -->
        <div id="success-block"
             x-show="isSubmitted"
             x-cloak
             class="mt-8 border-2 border-[#16A34A] bg-[#F0FDF4] px-6 py-6 flex gap-4 print:border-black print:bg-white transition-all">
          <div class="w-10 h-10 bg-[#16A34A] text-white rounded-full flex items-center justify-center shrink-0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="font-bold text-[15px] uppercase tracking-wide text-[#166534]">
              Gửi đánh giá thành công!
            </h4>
            <p class="text-[13px] mt-1 leading-[1.5]">
              Cảm ơn Quý khách <span class="font-bold" x-text="form.tenKH || 'đã tham gia'"></span> đánh giá dự án 
              <span class="font-bold" x-text="form.tenDA"></span>. 
              Kết quả: Phần A <span class="font-bold" x-text="(avgScoreA || '--') + '/5.0'"></span> • 
              Phần B <span class="font-bold" x-text="(avgScoreB || '--') + '/5.0'"></span> • 
              NPS <span class="font-bold" x-text="npsScore + '/10'"></span>. 
              Phiếu này đã sẵn sàng để lưu trữ, in từ trình duyệt sẽ cho bản PDF chuẩn.
            </p>
            <div class="mt-4 flex gap-2 no-print">
              <button type="button"
                      @click="isSubmitted = false"
                      class="h-9 px-4 bg-white border border-[#166534]/30 text-[#166534] text-[11px] font-bold tracking-widest hover:bg-gray-50"
                      style="font-family: Arial, sans-serif;">
                CHỈNH SỬA
              </button>
              <button type="button"
                      onclick="window.print()"
                      class="h-9 px-4 bg-[#16A34A] text-white text-[11px] font-bold tracking-widest hover:bg-[#15803d]"
                      style="font-family: Arial, sans-serif;">
                IN PHIẾU / LƯU PDF
              </button>
            </div>
          </div>
        </div>

      </form>

      <!-- ============================================== -->
      <!-- FOOTER NOTE                                    -->
      <!-- ============================================== -->
      <div class="px-6 md:px-10 py-5 bg-[#111] text-white/60 flex items-center justify-center text-[11px] tracking-[0.18em] uppercase"
           style="font-family: Arial, sans-serif;">
        <span class="text-center leading-[1.5]">
          {{ $survey->footer_text ?? 'Trân trọng cảm ơn Quý Khách hàng!' }}
        </span>
      </div>

    </div>
  </main>

  <script>
    function customerSurveyApp() {
      return {
        form: {
          tenKH: @json($prefill['tenKH'] ?? ''),
          tenDA: @json($prefill['tenDA'] ?? ''),
          ngayBG: '{{ date("Y-m-d") }}',
          qlda: @json($prefill['qlda'] ?? ''),
          email: @json($prefill['email'] ?? ''),
          phone: '',
          openBest: '',
          openImprove: ''
        },
        answersA: {},
        answersB: {},
        npsScore: 8,
        loading: false,
        isSubmitted: false,
        errorMessage: '',

        initApp() {
          @if(session('success'))
            this.isSubmitted = true;
          @endif
        },

        get countAnsweredA() {
          return Object.values(this.answersA).filter(v => v !== null && v !== undefined && v !== '').length;
        },

        get avgScoreA() {
          const vals = Object.values(this.answersA).filter(v => typeof v === 'number');
          if (!vals.length) return null;
          return (vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(1);
        },

        get countAnsweredB() {
          return Object.values(this.answersB).filter(v => v !== null && v !== undefined && v !== '').length;
        },

        get avgScoreB() {
          const vals = Object.values(this.answersB).filter(v => typeof v === 'number');
          if (!vals.length) return null;
          return (vals.reduce((a, b) => a + b, 0) / vals.length).toFixed(1);
        },

        async submitSurvey() {
          this.errorMessage = '';
          this.loading = true;

          const payload = {
            survey_id: {{ $survey->id }},
            tenKH: this.form.tenKH,
            tenDA: this.form.tenDA,
            ngayBG: this.form.ngayBG,
            qlda: this.form.qlda,
            customer_email: this.form.email,
            customer_phone: this.form.phone,
            answers_a: this.answersA,
            answers_b: this.answersB,
            nps_score: parseInt(this.npsScore, 10),
            open_feedback_best: this.form.openBest,
            open_feedback_improve: this.form.openImprove,
            invitation_token: '{{ $invitation ? $invitation->token : '' }}'
          };

          try {
            const res = await fetch('{{ route("customer-survey.submit") }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              },
              body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (res.ok && data.success) {
              this.isSubmitted = true;
              setTimeout(() => {
                const el = document.getElementById('success-block');
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
              }, 100);
            } else {
              if (data.errors) {
                const firstErr = Object.values(data.errors)[0];
                this.errorMessage = Array.isArray(firstErr) ? firstErr[0] : firstErr;
              } else {
                this.errorMessage = data.message || 'Có lỗi xảy ra khi gửi đánh giá.';
              }
            }
          } catch (err) {
            this.errorMessage = 'Lỗi kết nối máy chủ. Vui lòng thử lại sau.';
            console.error(err);
          } finally {
            this.loading = false;
          }
        }
      };
    }
  </script>
</body>
</html>
