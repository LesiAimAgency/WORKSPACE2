{{-- Section 5: Workflow --}}
@php
  $settings = $settings ?? [];
  $title = $settings['title'] ?? 'QUY TRÌNH LÀM VIỆC';
  $desc = $settings['description'] ?? 'Chúng tôi luôn tuân thủ theo một quy trình làm việc hiệu quả để mang lại kết quả tối ưu về chất lượng và thời gian cho khách hàng.';
  $btnText = $settings['btn_text'] ?? 'LIÊN HỆ NGAY';
  $btnUrl = $settings['btn_url'] ?? route('contact.page');

  $defaultSteps = [
    ['step_num' => 'Bước 1', 'title' => 'Tiếp nhận yêu cầu', 'description' => 'Lắng nghe nhu cầu, khảo sát thực địa và tư vấn giải pháp'],
    ['step_num' => 'Bước 2', 'title' => 'Thiết kế & Báo giá', 'description' => 'Lên phương án kỹ thuật, bản vẽ chi tiết và tối ưu chi phí'],
    ['step_num' => 'Bước 3', 'title' => 'Ký hợp đồng', 'description' => 'Thống nhất tiến độ cam kết & ký hợp đồng cung ứng thi công'],
    ['step_num' => 'Bước 4', 'title' => 'Thi công & Cung ứng', 'description' => 'Triển khai cung ứng vật tư & thi công chuẩn an toàn nghiêm ngặt'],
    ['step_num' => 'Bước 5', 'title' => 'Nghiệm thu & Bàn giao', 'description' => 'Thử nghiệm xuất xưởng, bàn giao hệ thống và bảo hành dài hạn.'],
  ];

  $steps = (!empty($settings['steps']) && is_array($settings['steps'])) ? $settings['steps'] : $defaultSteps;
  $row1 = array_slice($steps, 0, 3);
  $row2 = array_slice($steps, 3);
@endphp

<section class="homepage-section relative w-full lg:min-h-[100svh] box-border overflow-hidden bg-[#212121] text-white flex flex-col items-center justify-between select-none homepage-section-safe" id="home-workflow" aria-label="Quy Trình Làm Việc - Đại Phúc 68">
  <div class="figma-frame-1440 has-side-menu workflow-figma-frame relative z-10 flex-1 flex flex-col justify-center items-center gap-8 sm:gap-10 lg:gap-14 w-full">
    <div class="workflow-header w-full max-w-[1110px] flex flex-col gap-3 text-left">
      <h2 class="workflow-title text-[24px] sm:text-[28px] md:text-[31px] font-medium uppercase tracking-tight text-white leading-tight">{{ $title }}</h2>
      <div class="workflow-desc text-[16px] sm:text-[16px] md:text-[16px] text-neutral-300 leading-relaxed max-w-[540px]">{!! strip_tags($desc) !== $desc ? $desc : nl2br(e($desc)) !!}</div>
    </div>
    <div class="workflow-steps-wrap w-full max-w-[1110px] flex flex-col gap-10 sm:gap-14">
      {{-- Row 1: Steps 1-3 --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10 relative">
        @foreach($row1 as $sIdx => $step)
          <div class="workflow-step flex flex-col gap-2.5 relative group cursor-pointer" data-step="{{ $sIdx + 1 }}">
            <div class="flex items-center gap-3 relative">
              <span class="workflow-step-num text-[14px] sm:text-[16px] font-light capitalize tracking-wider whitespace-nowrap transition-colors duration-300">{{ $step['step_num'] ?? ('Bước ' . ($sIdx + 1)) }}</span>
              <div class="workflow-step-line-wrap relative flex-grow h-[1.5px] overflow-hidden">
                <div class="workflow-step-line-bg absolute inset-0 bg-white/20"></div>
                <div class="workflow-step-line-fill absolute inset-0 bg-white origin-left scale-x-0 transition-transform duration-500 shadow-[0_0_8px_rgba(255,255,255,0.7)]"></div>
              </div>
            </div>
            <h3 class="workflow-step-title text-[18px] sm:text-[20px] font-bold text-white tracking-tight leading-snug transition-colors duration-300">{{ $step['title'] ?? '' }}</h3>
            <div class="workflow-step-desc text-[16px] sm:text-[16px] text-neutral-400 leading-relaxed transition-colors duration-300">{!! strip_tags($step['description'] ?? '') !== ($step['description'] ?? '') ? ($step['description'] ?? '') : nl2br(e($step['description'] ?? '')) !!}</div>
          </div>
        @endforeach
      </div>

      {{-- Row 2: Steps 4+ and CTA --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10 items-start relative">
        @foreach($row2 as $sIdx => $step)
          <div class="workflow-step flex flex-col gap-2.5 relative group cursor-pointer" data-step="{{ $sIdx + 4 }}">
            <div class="flex items-center gap-3 relative">
              <span class="workflow-step-num text-[14px] sm:text-[16px] font-light capitalize tracking-wider whitespace-nowrap transition-colors duration-300">{{ $step['step_num'] ?? ('Bước ' . ($sIdx + 4)) }}</span>
              <div class="workflow-step-line-wrap relative flex-grow h-[1.5px] overflow-hidden">
                <div class="workflow-step-line-bg absolute inset-0 bg-white/20"></div>
                <div class="workflow-step-line-fill absolute inset-0 bg-white origin-left scale-x-0 transition-transform duration-500 shadow-[0_0_8px_rgba(255,255,255,0.7)]"></div>
              </div>
            </div>
            <h3 class="workflow-step-title text-[18px] sm:text-[20px] font-bold text-white tracking-tight leading-snug transition-colors duration-300">{{ $step['title'] ?? '' }}</h3>
            <div class="workflow-step-desc text-[14px] sm:text-[14.5px] text-neutral-400 leading-relaxed transition-colors duration-300">{!! strip_tags($step['description'] ?? '') !== ($step['description'] ?? '') ? ($step['description'] ?? '') : nl2br(e($step['description'] ?? '')) !!}</div>
          </div>
        @endforeach

        <div class="workflow-cta-col flex flex-col gap-2.5 relative pt-0">
          <div class="flex items-center h-[24px] sm:h-[26px]">
            <a class="workflow-cta-btn nav-contact-link inline-flex items-center gap-1 bg-white text-[#1a1a1a] hover:bg-neutral-100 transition-all duration-200 h-[38px] sm:h-[40px]  rounded-full font-bold text-[12px] sm:text-[13px] tracking-wider uppercase cursor-pointer shadow-sm" href="{{ $btnUrl }}" data-contact-modal-toggle title="{{ $btnText }}">
              <span class="text-[14px] sm:text-[15px] font-bold leading-none">&rarr;</span>
              <span class="leading-none">{{ $btnText }}</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="relative z-20 flex justify-center pb-4 md:pb-6 pt-2">
    <button class="btn-scroll-indicator group text-white hover:text-amber-300 transition-colors focus:outline-none flex flex-col items-center gap-1.5" type="button" data-scroll-target="#home-commitments" aria-label="Cuộn xuống nội dung">
    <div class="scroll-indicator-wrap w-[33px] h-[33px] flex items-center justify-center">
        <svg class="icon-bottom-svg" width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path class="ring-inner" d="M17.9479 26.434C12.2209 27.6774 6.46305 24.2225 4.9143 18.5018L4.85684 18.2635C5.23233 18.4517 5.62336 18.609 6.02629 18.734C7.67677 23.805 13.0346 26.7432 18.2399 25.3341C23.6119 23.8798 26.7879 18.3457 25.3335 12.9737C23.9242 7.76852 18.6843 4.62471 13.4732 5.75791C13.162 5.47306 12.829 5.21478 12.477 4.98555C12.5552 4.96254 12.6327 4.93631 12.7118 4.9149L13.2661 4.78C18.8084 3.57702 24.3788 6.77438 26.1358 12.1667L26.2988 12.7124C27.8974 18.6174 24.4063 24.7007 18.5012 26.2994L17.9479 26.434Z" fill="currentColor"></path>
          <path class="ring-outer" d="M15.9999 0.039248C24.8365 0.0163306 32.0185 7.16117 32.0414 15.9977C32.0461 17.8066 31.7494 19.5458 31.2003 21.1688C30.9953 20.7836 30.7579 20.4187 30.4904 20.0779C30.8523 18.7808 31.0451 17.4132 31.0414 16.0003C31.0199 7.71605 24.2868 1.01776 16.0025 1.03924C7.7183 1.06073 1.02001 7.79385 1.04149 16.0781C1.06298 24.3623 7.7961 31.0606 16.0803 31.0391C17.4933 31.0355 18.8598 30.8356 20.155 30.4671C20.4972 30.7327 20.8634 30.9682 21.2496 31.1712C19.6295 31.7287 17.8918 32.0344 16.0829 32.0391C7.24641 32.0621 0.064413 24.9172 0.0414956 16.0807C0.0185782 7.24416 7.16342 0.0621654 15.9999 0.039248Z" fill="currentColor"></path>
          <path class="arrow-icon" d="M20.377 16.8984C20.3231 16.8337 20.2569 16.7803 20.1824 16.7412C20.1078 16.7022 20.0263 16.6782 19.9424 16.6707C19.8586 16.6632 19.7741 16.6723 19.6938 16.6975C19.6134 16.7227 19.5389 16.7634 19.4743 16.8175L16.69 19.1479L16.6703 11.5576C16.6699 11.3878 16.602 11.2252 16.4817 11.1055C16.3613 10.9858 16.1984 10.9188 16.0286 10.9192C15.8589 10.9197 15.6963 10.9875 15.5766 11.1078C15.4569 11.2282 15.3899 11.3911 15.3903 11.5609L15.41 19.1513L12.6136 16.8353C12.5487 16.7817 12.4739 16.7414 12.3935 16.7166C12.3131 16.6919 12.2285 16.6833 12.1448 16.6912C12.061 16.6992 11.9796 16.7235 11.9052 16.7629C11.8309 16.8023 11.765 16.856 11.7114 16.9208C11.6578 16.9857 11.6174 17.0605 11.5927 17.1409C11.568 17.2213 11.5594 17.3059 11.5673 17.3896C11.5753 17.4734 11.5996 17.5548 11.639 17.6292C11.6784 17.7035 11.7321 17.7694 11.7969 17.823L15.6452 21.013L15.7414 21.0704L15.8247 21.115C15.9731 21.1718 16.1374 21.1714 16.2855 21.1138L16.3686 21.0688L16.4644 21.0109L20.2961 17.801C20.3608 17.7471 20.4142 17.681 20.4532 17.6064C20.4923 17.5318 20.5163 17.4503 20.5238 17.3664C20.5313 17.2826 20.5222 17.1981 20.497 17.1178C20.4718 17.0375 20.431 16.9629 20.377 16.8984V16.8984Z" fill="currentColor"></path>
        </svg>
      </div>
    </button>
  </div>
</section>
