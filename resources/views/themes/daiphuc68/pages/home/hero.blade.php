{{-- Section 1: Hero Banner --}}
@php
  $settings = $settings ?? [];
  $heroImg = !empty($settings['hero_image'])
    ? (str_starts_with($settings['hero_image'], 'http') ? $settings['hero_image'] : asset(ltrim($settings['hero_image'], '/')))
    : asset('storage/media/banners/hero-home-villa.png');
  $brandTitle = $settings['brand_title'] ?? 'ĐẠI PHÚC 68';
  $sloganLeft = $settings['slogan_left'] ?? 'XÂY DỰNG GIÁ TRỊ';
  $sloganRight = $settings['slogan_right'] ?? 'KIẾN TẠO TƯƠNG LAI';
  $description = $settings['description'] ?? 'Chúng tôi kiến tạo những công trình bền vững, thẩm mỹ và tối ưu công năng, mang đến giá trị thật cho khách hàng và công chúng.';
  $btnProfileText = $settings['btn_profile_text'] ?? 'HỒ SƠ NĂNG LỰC';
  $rawProfileUrl = $settings['btn_profile_url'] ?? '';
  if (empty($rawProfileUrl) || $rawProfileUrl === '/ho-so-nang-luc' || $rawProfileUrl === 'ho-so-nang-luc' || $rawProfileUrl === 'themes/daiphuc68/assets/ho-so-nang-luc.pdf') {
    $btnProfileUrl = route('company.profile');
  } elseif (str_starts_with($rawProfileUrl, 'http') || str_starts_with($rawProfileUrl, '/')) {
    $btnProfileUrl = $rawProfileUrl;
  } else {
    $btnProfileUrl = asset(ltrim($rawProfileUrl, '/'));
  }
  $btnMoreText = $settings['btn_more_text'] ?? 'XEM THÊM';
  $btnMoreUrl = $settings['btn_more_url'] ?? '#home-projects';
  $badgeNumber = $settings['badge_number'] ?? '150+';
  $badgeLabel = $settings['badge_label'] ?? 'Dự án đã thực hiện';
  $badgeUrl = $settings['badge_url'] ?? route('projects.index');
@endphp

<section class="homepage-section relative w-full min-h-[100svh] min-h-[100dvh] box-border overflow-hidden flex flex-col items-center justify-between select-none z-10 homepage-section-safe hero-intro-f1" id="home-hero" aria-label="Trang Chủ Đại Phúc 68">
  <div class="absolute inset-0 z-0 pointer-events-none">
    <div class="hero-sky-backdrop" aria-hidden="true"></div>
    <img class="w-full h-full object-cover object-center filter brightness-[0.98]" id="hero-villa-img" src="{{ $heroImg }}" alt="{{ $brandTitle }}">
    <!-- <div class="absolute inset-0 bg-gradient-to-b from-[#051650]/40 via-transparent to-[#051650]/65"></div> -->
  </div>
  <div class="figma-frame-1440 hero-content-frame relative z-10 flex-1 flex flex-col justify-between items-center w-full gap-8 sm:gap-12 lg:gap-[180px]">
    <div class="w-full text-center flex flex-col items-center gap-2.5 sm:gap-3 md:gap-4 pt-1 sm:pt-2 md:pt-3" id="hero-center-text">
      <h1 class="hero-brand-title text-[38px] min-[390px]:text-[48px] sm:text-[80px] md:text-[110px] lg:text-[130px] xl:text-[150px] font-black uppercase tracking-[0.05em] leading-[0.92]" style="font-family: 'SVN-Aguda', sans-serif; font-weight: 900; background: linear-gradient(180deg, #D8ECFF 37%, #c2d8ed47 99%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $brandTitle }}</h1>
      <div class="inline-flex items-center justify-center gap-2.5 sm:gap-4 text-[14px] sm:text-[16px] md:text-[20px] lg:text-[25px] font-medium uppercase tracking-[2px] text-white drop-shadow-md">
        <span>{{ $sloganLeft }}</span><span class="w-[1.5px] h-3.5 sm:h-4 lg:h-5 bg-white/70 inline-block"></span><span>{{ $sloganRight }}</span>
      </div>
    </div>
    <div class="w-full relative pb-4 sm:pb-6" id="hero-bottom-row">
      <div class="flex flex-col lg:flex-row items-center lg:items-end justify-between gap-5 lg:gap-8 ">
        <div class="hero-bottom-left-col max-w-[440px] flex flex-col gap-3 text-left w-full lg:w-auto" style="gap:20px  ">
          <div class="text-[13px] sm:text-[13px] md:text-[13px] text-white font-normal leading-relaxed drop-shadow-[0_2px_4px_rgba(0,0,0,0.6)]">
            {!! strip_tags($description) !== $description ? $description : nl2br(e($description)) !!}
          </div>  
          <div class="flex flex-wrap items-center gap-3 pt-0.5">
            <a class="btn-pill-arrow btn-pill-arrow-white shadow-lg text-[12px] sm:text-[13px] font-normal py-2 px-4 sm:px-5" href="{{ $btnProfileUrl }}" target="_blank" rel="noopener" title="{{ $btnProfileText }}">
              <span><img src="{{ asset('themes/daiphuc68/svg/arrow.svg') }}" alt="" style="filter: brightness(0) saturate(100%)"></span><span>{{ $btnProfileText }}</span>
            </a>
            <a class="btn-pill-arrow btn-pill-arrow-white shadow-lg text-[12px] sm:text-[13px] font-normal py-2 px-4 sm:px-5" href="{{ $btnMoreUrl }}" title="{{ $btnMoreText }}">
              <span><img src="{{ asset('themes/daiphuc68/svg/arrow.svg') }}" alt="" style="filter: brightness(0) saturate(100%)"></span><span>{{ $btnMoreText }}</span>
            </a>
          </div>
        </div>
        <div class="hero-bottom-right-col shrink-0 w-full lg:w-auto flex justify-end">
          <div class="bg-white rounded-2xl px-5 py-3 sm:px-6 sm:py-3.5 shadow-[0_12px_32px_rgba(5,22,80,0.18)] border border-white/60 flex items-center justify-between gap-5 min-w-[220px] sm:min-w-[250px]">
            <div class="flex flex-col gap-0.5">
              <div class="text-[28px] sm:text-[28px] font-medium text-brand-navy leading-none tracking-tight">{{ $badgeNumber }}</div>
              <div class="text-[11px] sm:text-[12px] font-medium text-neutral-500 whitespace-nowrap">{{ $badgeLabel }}</div>
            </div>
            <a class="w-6 h-6 rounded-full bg-brand-navy text-white flex items-center justify-center hover:bg-amber-400 hover:text-brand-navy transition-all duration-200 shadow-md group shrink-0" href="{{ $badgeUrl }}" title="Xem tất cả dự án">
              <svg class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H9M17 7V15" /></svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
@php
  $scrollTarget = $settings['scroll_target'] ?? (request()->routeIs('about-us') ? '#about-overview' : '#home-about');
@endphp
<div class="relative z-20 flex justify-center pb-1">
  <button class="btn-scroll-indicator group text-white hover:text-amber-300 transition-colors focus:outline-none flex flex-col items-center gap-1.5" type="button" data-scroll-target="{{ $scrollTarget }}" aria-label="Cuộn xuống nội dung">
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
