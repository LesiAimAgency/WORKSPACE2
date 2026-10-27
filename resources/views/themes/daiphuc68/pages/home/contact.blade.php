{{-- Section 9: Contact & Partners --}}
@php
  $settings = $settings ?? [];
  $tabCompany = $settings['tab_company'] ?? 'THÔNG TIN CÔNG TY';
  $tabFaq = $settings['tab_faq'] ?? 'CÂU HỎI THƯỜNG GẶP (FAQS)';
  $headline = $settings['headline'] ?? "XÂY DỰNG GIÁ TRỊ\nKIẾN TẠO TƯƠNG LAI";
  $companyName = $settings['company_name'] ?? setting('company_name', 'CÔNG TY TNHH ĐẠI PHÚC 68');
  $companyAddr = $settings['company_address'] ?? setting('contact_address', setting('company_address', '67 Huỳnh Thiện Lộc, Phường Tân Phú, TP. Hồ Chí Minh, Việt Nam'));
  $taxCode = $settings['tax_code'] ?? setting('tax_code', '0000999988');
  $homePhone = $settings['phone'] ?? setting('contact_phone', setting('company_phone', '0988 888 888'));
  $homePhoneClean = preg_replace('/[^0-9+]/', '', $homePhone);
  $homeEmail = $settings['email'] ?? setting('contact_email', setting('company_email', 'contact@daiphuc68.vn'));
  $partnersTitle = $settings['partners_title'] ?? 'ĐỐI TÁC THI CÔNG';

  $defaultPartners = [
    ['name' => 'Vina Electric', 'logo' => 'storage/media/partners/brand-vinaelectric.png'],
    ['name' => 'Schneider Electric', 'logo' => 'storage/media/brands/brand-schneider.svg'],
    ['name' => 'CADIVI', 'logo' => 'storage/media/brands/brand-cadivi.svg'],
    ['name' => 'MBT', 'logo' => 'storage/media/brands/brand-mbt.svg'],
    ['name' => 'ABB', 'logo' => 'storage/media/brands/brand-abb.svg'],
    ['name' => 'EMIC', 'logo' => 'storage/media/brands/brand-emic.svg'],
  ];

  $partners = (!empty($settings['partners']) && is_array($settings['partners'])) ? $settings['partners'] : $defaultPartners;
@endphp

<section class="homepage-section relative w-full lg:min-h-[100svh] box-border overflow-hidden bg-[#F6F6F6] text-neutral-900 flex flex-col items-center justify-between select-none homepage-section-safe"id="home-contact" aria-label="Thông Tin Liên Hệ &amp; Đối Tác - Đại Phúc 68">
  <div class="gap-6 figma-frame-1440 has-side-menu commitments-figma-frame relative z-10 flex-1 flex flex-col justify-center items-center w-full">
    <div class="w-full max-w-[1110px] flex flex-col">
      <div class="flex items-center gap-4 sm:gap-6 text-[12px] sm:text-[13px] tracking-wider uppercase select-none border-b border-neutral-300/80 reveal-item reveal-delay-1">
        <button class="text-neutral-900 font-extrabold cursor-pointer border-b-2 border-neutral-900 pb-3 translate-y-[1px] inline-block focus:outline-none transition-all" id="contact-tab-company" type="button">{{ $tabCompany }}</button>
        <span class="text-neutral-300 font-light pb-2">|</span>
        <button class="text-neutral-400 font-medium hover:text-neutral-700 transition-colors cursor-pointer border-b-2 border-transparent pb-3 translate-y-[1px] inline-block focus:outline-none" id="contact-tab-faq" type="button">{{ $tabFaq }}</button>
      </div>
    </div>
    <div class="w-full max-w-[1110px] flex flex-col gap-4 sm:gap-5" id="contact-panel-company">
      <div class="flex flex-col gap-0.5 text-left reveal-item reveal-delay-2">
        <h2 class="headline-text-contact   tracking-normal leading-[1.08]">
          {!! nl2br(e($headline)) !!}
        </h2>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-12 gap-5 lg:gap-8 pt-1 text-[13px] sm:text-[14px] reveal-item reveal-delay-3">
        <div class="md:col-span-5 flex flex-col gap-1.5 text-neutral-700">
          <div class="font-bold text-neutral-900 uppercase tracking-wide" style="font-size: 16px;">{{ $companyName }}</div>
          <p class="leading-relaxed text-[16px]" style="font-size: 16px;"><span class="font-medium text-neutral-900">Địa chỉ: </span>{{ $companyAddr }}</p>
          <p class="text-[16px]" style="font-size: 16px;"><span class="font-medium text-neutral-900">Mã số thuế: </span>{{ $taxCode }}</p>
        </div>
        <div class="md:col-span-4 flex flex-col gap-1 text-neutral-700 gap-2">
          <div class="font-bold text-neutral-900 uppercase tracking-wide"style="font-size: 16px;">LIÊN HỆ CHÚNG TÔI</div>
          <div><a class="hover:text-brand-navy font-semibold text-neutral-900 transition-colors flex items-center gap-2" href="tel:{{ $homePhoneClean }}"><svg class="w-3.5 h-3.5 text-neutral-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>{{ $homePhone }}</a></div>
          <div><a class="hover:text-brand-navy font-medium transition-colors flex items-center gap-2" href="mailto:{{ $homeEmail }}"><svg class="w-3.5 h-3.5 text-neutral-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>{{ $homeEmail }}</a></div>
        </div>
        <div class="md:col-span-3 flex flex-col gap-2">
          <div class="font-bold text-neutral-900 uppercase tracking-wide">KẾT NỐI VỚI CHÚNG TÔI</div>
          <div class="flex items-center gap-4 flex-wrap">
            @php
              $homeSocials = function_exists('get_social_items') ? get_social_items(true) : [];
            @endphp
            @foreach($homeSocials as $s)
              @php
                $sName = $s['name'] ?? 'Social';
                $sLink = $s['link'] ?? '#';
                $sTarget = !empty($s['target']) ? $s['target'] : '_blank';
                $sIcon = $s['icon'] ?? '';
                $isSvg = str_starts_with(trim($sIcon), '<svg');
                $isFa = str_starts_with($sIcon, 'fa-') || str_contains($sIcon, ' fa-');
                $isImg = !$isSvg && !$isFa && !empty($sIcon);
                $sImgUrl = '';
                if ($isImg) {
                  $sImgUrl = (str_starts_with($sIcon, 'http://') || str_starts_with($sIcon, 'https://')) ? $sIcon : asset(ltrim($sIcon, '/'));
                }
              @endphp
              <a class="contact-social-link w-6 h-6 flex items-center justify-center text-neutral-900 hover:text-brand-navy transition-all duration-200 hover:scale-110 active:scale-95" 
                 href="{{ $sLink }}" 
                 target="{{ $sTarget }}" 
                 rel="noopener noreferrer" 
                 aria-label="{{ $sName }}" 
                 title="{{ $sName }}">
                @if($isSvg)
                  <span class="w-5 h-5 inline-flex items-center justify-center shrink-0 [&>svg]:w-full [&>svg]:h-full [&>svg]:fill-current">{!! $sIcon !!}</span>
                @elseif($isImg)
                  <img src="{{ $sImgUrl }}" alt="{{ $sName }}" class="w-5 h-5 object-contain">
                @elseif($isFa)
                  <i class="{{ $sIcon }} text-[20px] leading-none"></i>
                @else
                  <i class="fa-solid fa-share-nodes text-[20px] leading-none"></i>
                @endif
              </a>
            @endforeach
          </div>
        </div>
      </div>
      <div class="flex flex-col gap-2 pt-1 sm:pt-2 reveal-item reveal-delay-4">
        <h3 class="text-center text-[13px] sm:text-[14px] md:text-[16px] font-bold uppercase tracking-widest text-neutral-800">{{ $partnersTitle }}</h3>
        <div class="relative w-full py-1 sm:py-2 px-2 sm:px-6 overflow-hidden group/partner-swiper">
          {{-- Subtle Left & Right Fade Masks matching section background #F6F6F6 --}}
          <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-10 sm:w-16 bg-gradient-to-r from-[#F6F6F6] via-[#F6F6F6]/80 to-transparent z-10" aria-hidden="true"></div>
          <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-10 sm:w-16 bg-gradient-to-l from-[#F6F6F6] via-[#F6F6F6]/80 to-transparent z-10" aria-hidden="true"></div>

          {{-- Navigation Arrows (hidden by default, appear on hover or focus) --}}
          <button type="button" class="home-partners-prev absolute left-0 sm:left-1 top-1/2 -translate-y-1/2 z-20 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/95 border border-neutral-300 shadow-sm flex items-center justify-center text-neutral-600 hover:text-brand-navy hover:bg-neutral-50 hover:scale-105 active:scale-95 transition-all opacity-0 group-hover/partner-swiper:opacity-100 focus:opacity-100 cursor-pointer" aria-label="Đối tác trước">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button type="button" class="home-partners-next absolute right-0 sm:right-1 top-1/2 -translate-y-1/2 z-20 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/95 border border-neutral-300 shadow-sm flex items-center justify-center text-neutral-600 hover:text-brand-navy hover:bg-neutral-50 hover:scale-105 active:scale-95 transition-all opacity-0 group-hover/partner-swiper:opacity-100 focus:opacity-100 cursor-pointer" aria-label="Đối tác kế tiếp">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </button>

          {{-- Swiper Container --}}
          <div class="swiper home-partners-swiper w-full overflow-hidden">
            <div class="swiper-wrapper items-center">
              @php
                // Duplicate partners to guarantee smooth infinite loop in Swiper
                $loopPartners = (count($partners) < 18) ? array_merge($partners, $partners, $partners) : $partners;
              @endphp
              @foreach($loopPartners as $partner)
                @php
                  $pLogo = !empty($partner['logo'])
                    ? (str_starts_with($partner['logo'], 'http') ? $partner['logo'] : asset(ltrim($partner['logo'], '/')))
                    : asset('storage/media/partners/brand-vinaelectric.png');
                  $pName = $partner['name'] ?? 'Đối tác thi công';
                  $pUrl = $partner['url'] ?? null;
                @endphp
                <div class="swiper-slide !flex items-center justify-center h-12 sm:h-14 md:h-16">
                  @if(!empty($pUrl))
                    <a href="{{ $pUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-full h-full" title="{{ $pName }}">
                      <img class="h-10 sm:h-12 md:h-14 max-h-14 w-auto max-w-[150px] sm:max-w-[170px] md:max-w-[190px] object-contain shrink-0 transition-transform duration-200 hover:scale-110 cursor-pointer select-none" src="{{ $pLogo }}" alt="{{ $pName }}" loading="lazy">
                    </a>
                  @else
                    <div class="flex items-center justify-center w-full h-full" title="{{ $pName }}">
                      <img class="h-10 sm:h-12 md:h-14 max-h-14 w-auto max-w-[150px] sm:max-w-[170px] md:max-w-[190px] object-contain shrink-0 transition-transform duration-200 hover:scale-110 cursor-pointer select-none" src="{{ $pLogo }}" alt="{{ $pName }}" loading="lazy">
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Panel 2: FAQs (Cục bộ trên Homepage) --}}
    @php
      $homeFaqs = $faqs ?? \App\Models\Post::where('post_type', 'faq')->where('status', 'published')->orderBy('order', 'asc')->orderBy('id', 'asc')->get();
    @endphp
    <div class="w-full max-w-[1110px] hidden transition-opacity duration-300" id="contact-panel-faq">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 items-start pt-1">
        <div class="lg:col-span-5 flex flex-col gap-2">
          <h3 class="text-[22px] sm:text-[28px] md:text-[32px] font-bold uppercase text-neutral-900 tracking-tight leading-tight">
            CÂU HỎI THƯỜNG GẶP
          </h3>
          <p class="text-[16px] text-neutral-600 font-normal leading-relaxed" style="font-size: 16px;">
            Giải đáp những thắc mắc chung của quý khách hàng về sản phẩm, dịch vụ và chính sách của Đại Phúc 68
          </p>
        </div>

        <div class="lg:col-span-7 contact-faq-scroll pr-3 sm:pr-4 custom-scrollbar divide-y divide-neutral-200/80" style="max-height: min(440px, 50vh); overflow-y: auto;">
          @if(isset($homeFaqs) && $homeFaqs->isNotEmpty())
            @foreach($homeFaqs as $index => $faq)
              <div class="faq-minimal-item {{ $loop->first ? 'active' : '' }} py-2.5">
                <button class="faq-trigger w-full flex items-center justify-between text-left cursor-pointer border-none bg-transparent" type="button" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                  <span class="text-[25px] sm:text-[25px] font-bold text-neutral-900 pr-3">{{ $faq->title }}</span>
                  <span class="faq-icon shrink-0 text-neutral-900"><svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg></span>
                </button>
                <div class="faq-content">
                  <div class="text-[16px] text-neutral-600 font-normal leading-relaxed prose max-w-none pt-1.5" style="font-size: 16px;">
                    {!! !empty($faq->content) ? $faq->content : nl2br(e($faq->excerpt ?? '')) !!}
                  </div>
                </div>
              </div>
            @endforeach
          @else
            <div class="faq-minimal-item active py-2.5">
              <button class="faq-trigger w-full flex items-center justify-between text-left cursor-pointer border-none bg-transparent" type="button" aria-expanded="true">
                <span class="text-[14px] sm:text-[15px] font-bold text-neutral-900 pr-3">Câu hỏi 1: Đại Phúc 68 cung cấp những dịch vụ &amp; giải pháp nào?</span>
                <span class="faq-icon shrink-0 text-neutral-900"><svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg></span>
              </button>
              <div class="faq-content">
                <p class="text-[16px] text-neutral-600 font-normal leading-relaxed pt-1.5" style="font-size: 16px;">
                  Đại Phúc 68 chuyên cung ứng vật tư thiết bị điện trung thế, hạ thế, trạm biến áp, tư vấn thiết kế kiến trúc kỹ thuật và thi công xây lắp trọn gói.
                </p>
              </div>
            </div>
            <div class="faq-minimal-item py-2.5">
              <button class="faq-trigger w-full flex items-center justify-between text-left cursor-pointer border-none bg-transparent" type="button" aria-expanded="false">
                <span class="text-[14px] sm:text-[15px] font-bold text-neutral-900 pr-3">Câu hỏi 2: Chính sách bảo hành và cam kết chất lượng dự án?</span>
                <span class="faq-icon shrink-0 text-neutral-900"><svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg></span>
              </button>
              <div class="faq-content">
                <p class="text-[16px] text-neutral-600 font-normal leading-relaxed pt-1.5" style="font-size: 16px;">
                  Toàn bộ vật tư và công trình được bảo hành chính hãng từ 12 đến 36 tháng kèm dịch vụ hỗ trợ kỹ thuật bảo trì định kỳ 24/7.
                </p>
              </div>
            </div>
            <div class="faq-minimal-item py-2.5">
              <button class="faq-trigger w-full flex items-center justify-between text-left cursor-pointer border-none bg-transparent" type="button" aria-expanded="false">
                <span class="text-[14px] sm:text-[15px] font-bold text-neutral-900 pr-3">Câu hỏi 3: Quy trình tiếp nhận yêu cầu báo giá diễn ra bao lâu?</span>
                <span class="faq-icon shrink-0 text-neutral-900"><svg class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg></span>
              </button>
              <div class="faq-content">
                <p class="text-[16px] text-neutral-600 font-normal leading-relaxed pt-1.5" style="font-size: 16px;">
                  Đội ngũ kỹ sư sẽ khảo sát, tư vấn giải pháp sơ bộ và gửi bảng khái toán chi tiết trong vòng 24 giờ làm việc.
                </p>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="relative z-20 flex justify-center pb-1">
    <button class="btn-scroll-indicator group text-brand-navy hover:text-amber-600 transition-colors focus:outline-none flex flex-col items-center gap-1.5" type="button" data-scroll-target="#home-hero" aria-label="Cuộn lên đầu trang">
    <div class="scroll-indicator-wrap w-[33px] h-[33px] flex items-center justify-center text-brand-navy">
        <svg class="icon-bottom-svg direction-up" width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path class="ring-inner" d="M17.9479 26.434C12.2209 27.6774 6.46305 24.2225 4.9143 18.5018L4.85684 18.2635C5.23233 18.4517 5.62336 18.609 6.02629 18.734C7.67677 23.805 13.0346 26.7432 18.2399 25.3341C23.6119 23.8798 26.7879 18.3457 25.3335 12.9737C23.9242 7.76852 18.6843 4.62471 13.4732 5.75791C13.162 5.47306 12.829 5.21478 12.477 4.98555C12.5552 4.96254 12.6327 4.93631 12.7118 4.9149L13.2661 4.78C18.8084 3.57702 24.3788 6.77438 26.1358 12.1667L26.2988 12.7124C27.8974 18.6174 24.4063 24.7007 18.5012 26.2994L17.9479 26.434Z" fill="currentColor"></path>
          <path class="ring-outer" d="M15.9999 0.039248C24.8365 0.0163306 32.0185 7.16117 32.0414 15.9977C32.0461 17.8066 31.7494 19.5458 31.2003 21.1688C30.9953 20.7836 30.7579 20.4187 30.4904 20.0779C30.8523 18.7808 31.0451 17.4132 31.0414 16.0003C31.0199 7.71605 24.2868 1.01776 16.0025 1.03924C7.7183 1.06073 1.02001 7.79385 1.04149 16.0781C1.06298 24.3623 7.7961 31.0606 16.0803 31.0391C17.4933 31.0355 18.8598 30.8356 20.155 30.4671C20.4972 30.7327 20.8634 30.9682 21.2496 31.1712C19.6295 31.7287 17.8918 32.0344 16.0829 32.0391C7.24641 32.0621 0.064413 24.9172 0.0414956 16.0807C0.0185782 7.24416 7.16342 0.0621654 15.9999 0.039248Z" fill="currentColor"></path>
          <path class="arrow-icon" d="M20.377 16.8984C20.3231 16.8337 20.2569 16.7803 20.1824 16.7412C20.1078 16.7022 20.0263 16.6782 19.9424 16.6707C19.8586 16.6632 19.7741 16.6723 19.6938 16.6975C19.6134 16.7227 19.5389 16.7634 19.4743 16.8175L16.69 19.1479L16.6703 11.5576C16.6699 11.3878 16.602 11.2252 16.4817 11.1055C16.3613 10.9858 16.1984 10.9188 16.0286 10.9192C15.8589 10.9197 15.6963 10.9875 15.5766 11.1078C15.4569 11.2282 15.3899 11.3911 15.3903 11.5609L15.41 19.1513L12.6136 16.8353C12.5487 16.7817 12.4739 16.7414 12.3935 16.7166C12.3131 16.6919 12.2285 16.6833 12.1448 16.6912C12.061 16.6992 11.9796 16.7235 11.9052 16.7629C11.8309 16.8023 11.765 16.856 11.7114 16.9208C11.6578 16.9857 11.6174 17.0605 11.5927 17.1409C11.568 17.2213 11.5594 17.3059 11.5673 17.3896C11.5753 17.4734 11.5996 17.5548 11.639 17.6292C11.6784 17.7035 11.7321 17.7694 11.7969 17.823L15.6452 21.013L15.7414 21.0704L15.8247 21.115C15.9731 21.1718 16.1374 21.1714 16.2855 21.1138L16.3686 21.0688L16.4644 21.0109L20.2961 17.801C20.3608 17.7471 20.4142 17.681 20.4532 17.6064C20.4923 17.5318 20.5163 17.4503 20.5238 17.3664C20.5313 17.2826 20.5222 17.1981 20.497 17.1178C20.4718 17.0375 20.431 16.9629 20.377 16.8984V16.8984Z" fill="currentColor"></path>
        </svg>
      </div>
    </button>
  </div>
</section>
