{{-- Section 3: Proud Projects --}}
@php
  $settings = $settings ?? [];
  $eyebrow = $settings['eyebrow'] ?? '';
  $titleLine1 = $title_line_1 ?? ($settings['title_line_1'] ?? null);
  $titleLine2 = $title_line_2 ?? ($settings['title_line_2'] ?? null);
  if (empty($titleLine1) && empty($titleLine2)) {
    $rawTitle = $title ?? ($settings['title'] ?? "NHỮNG DỰ ÁN\nMÀ CHÚNG TÔI TỰ HÀO");
    if (str_contains($rawTitle, "\n")) {
      $parts = explode("\n", str_replace("\r", "", $rawTitle), 2);
      $titleLine1 = trim($parts[0]);
      $titleLine2 = trim($parts[1] ?? '');
    } else {
      $titleLine1 = $rawTitle;
      $titleLine2 = '';
    }
  }
  $desc = $settings['description'] ?? 'Chúng tôi kiến tạo những công trình bền vững, thẩm mỹ và tối ưu công năng, mang đến giá trị thật cho khách hàng và công chúng.';
  $btnText = $settings['btn_text'] ?? 'Xem tất cả';
  $btnUrl = $settings['btn_url'] ?? route('projects.index');
  $projects = $projects ?? collect([]);
@endphp

<section class="homepage-section proj-section relative w-full lg:min-h-[100svh] box-border overflow-hidden flex flex-col items-center justify-between select-none homepage-section-safe" id="home-projects" aria-label="Những Dự Án Tự Hào - Đại Phúc 68">
  <div class="figma-frame-1440 has-side-menu proj-outer relative z-10 flex-1 flex flex-col justify-center w-full">
   
    <div class="proj-body w-full">
   
      <div class="proj-left flex flex-col justify-center gap-[36px] reveal-item reveal-delay-1">
        <div class="flex flex-col gap-4 sm:gap-5">
          @php
            $cleanEyebrow = trim(strip_tags(html_entity_decode($eyebrow ?? '')));
          @endphp
          @if(!empty($cleanEyebrow) && !str_contains(mb_strtoupper($cleanEyebrow), 'KIẾN TRÚC'))
            <span class="proj-eyebrow">{{ $cleanEyebrow }}</span>
          @endif
          <h2 class="proj-title">
            @if(!empty($titleLine1))
              <span class="block">{{ $titleLine1 }}</span>
            @endif
            @if(!empty($titleLine2))
              <span class="block whitespace-nowrap">{{ $titleLine2 }}</span>
            @endif
          </h2>
          <div class="proj-desc">{!! strip_tags($desc) !== $desc ? $desc : nl2br(e($desc)) !!}</div>
        </div>
        <div>
          <a class="proj-cta" href="{{ $btnUrl }}" title="{{ $btnText }}"><em>{{ $btnText }}</em></a>
        </div>
      </div>
      <div class="proj-divider" aria-hidden="true"></div>
      <div class="proj-right reveal-item reveal-delay-2">
        <div class="proj-card">
          <div class="proj-card__img-wrap relative w-full aspect-[593/350] min-h-[220px] rounded-xl overflow-hidden bg-neutral-200 shadow-md">
            @foreach($projects as $idx => $p)
              @php
                $pImg = !empty($p->featured_image)
                  ? (str_starts_with($p->featured_image, 'http') ? $p->featured_image : asset(ltrim($p->featured_image, '/')))
                  : asset('storage/media/projects/project-villa-park.jpg');
              @endphp
              <div class="proj-card__img-slide {{ $idx === 0 ? 'active' : '' }}" data-proj-index="{{ $idx }}" data-desc="{{ $p->excerpt }}">
                <img class="w-full h-full object-cover object-center" src="{{ $pImg }}" alt="{{ $p->title }}" loading="{{ $idx === 0 ? 'eager' : 'lazy' }}">
              </div>
            @endforeach
          </div>
          <div class="proj-card__info">
            <div class="proj-card__info-header">
              <h3 class="proj-card__title" id="projCardTitle">{{ $projects->first()?->title }}</h3>
              <div class="proj-card__nav">
                <button class="proj-nav-btn" id="projBtnPrev" type="button" aria-label="Dự án trước"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg></button>
                <button class="proj-nav-btn" id="projBtnNext" type="button" aria-label="Dự án sau"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg></button>
              </div>
            </div>
            <p class="proj-card__desc" id="projCardDesc">{{ $projects->first()?->excerpt ?? 'Tổ hợp biệt thự sinh thái cao cấp với hệ thống trạm biến áp ngầm 22kV.' }}</p>
            <div class="proj-card__specs">
              @foreach($projects as $idx => $p)
                @php
                  $pMeta = is_array($p->meta_data) ? $p->meta_data : json_decode($p->meta_data ?? '[]', true);
                  $pScale = !empty($pMeta['scale']) ? $pMeta['scale'] : ($p->scale ?: 'Tiêu chuẩn EVN');
                  $pLocation = !empty($pMeta['location']) ? $pMeta['location'] : ($p->location ?: 'TP. Hồ Chí Minh');
                  $pCategory = !empty($pMeta['category']) ? $pMeta['category'] : ($p->category_name ?: 'Dự Án Tiêu Biểu');
                  $pClient = !empty($pMeta['client']) ? $pMeta['client'] : ($p->client ?: 'Đại Phúc 68 & Đối Tác');
                @endphp
                <div class="proj-specs-set {{ $idx === 0 ? 'active' : '' }}" data-proj-specs="{{ $idx }}" aria-hidden="{{ $idx === 0 ? 'false' : 'true' }}">
                  <div class="proj-spec-row"><span class="proj-spec-label">QUY MÔ / DIỆN TÍCH</span><span class="proj-spec-value">{{ $pScale }}</span></div>
                  <div class="proj-spec-row"><span class="proj-spec-label">VỊ TRÍ</span><span class="proj-spec-value">{{ $pLocation }}</span></div>
                  <div class="proj-spec-row"><span class="proj-spec-label">LOẠI HÌNH DỰ ÁN</span><span class="proj-spec-value">{{ $pCategory }}</span></div>
                  <div class="proj-spec-row"><span class="proj-spec-label">CHỦ ĐẦU TƯ / ĐỐI TÁC</span><span class="proj-spec-value">{{ $pClient }}</span></div>
                </div>
              @endforeach
            </div>
            <div class="proj-card__footer">
              <a class="proj-viewmore" id="projViewMoreBtn" href="{{ $projects->first()?->url ?? $btnUrl }}" title="Xem chi tiết dự án"><em>Xem thêm</em></a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <div class="relative z-20 flex justify-center pb-1">
    <button class="btn-scroll-indicator group text-brand-navy hover:text-amber-600 transition-colors focus:outline-none flex flex-col items-center gap-1.5" type="button" data-scroll-target="#home-products" aria-label="Cuộn xuống nội dung">
    <div class="scroll-indicator-wrap w-[33px] h-[33px] flex items-center justify-center text-brand-navy">
        <svg class="icon-bottom-svg" width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path class="ring-inner" d="M17.9479 26.434C12.2209 27.6774 6.46305 24.2225 4.9143 18.5018L4.85684 18.2635C5.23233 18.4517 5.62336 18.609 6.02629 18.734C7.67677 23.805 13.0346 26.7432 18.2399 25.3341C23.6119 23.8798 26.7879 18.3457 25.3335 12.9737C23.9242 7.76852 18.6843 4.62471 13.4732 5.75791C13.162 5.47306 12.829 5.21478 12.477 4.98555C12.5552 4.96254 12.6327 4.93631 12.7118 4.9149L13.2661 4.78C18.8084 3.57702 24.3788 6.77438 26.1358 12.1667L26.2988 12.7124C27.8974 18.6174 24.4063 24.7007 18.5012 26.2994L17.9479 26.434Z" fill="currentColor"></path>
          <path class="ring-outer" d="M15.9999 0.039248C24.8365 0.0163306 32.0185 7.16117 32.0414 15.9977C32.0461 17.8066 31.7494 19.5458 31.2003 21.1688C30.9953 20.7836 30.7579 20.4187 30.4904 20.0779C30.8523 18.7808 31.0451 17.4132 31.0414 16.0003C31.0199 7.71605 24.2868 1.01776 16.0025 1.03924C7.7183 1.06073 1.02001 7.79385 1.04149 16.0781C1.06298 24.3623 7.7961 31.0606 16.0803 31.0391C17.4933 31.0355 18.8598 30.8356 20.155 30.4671C20.4972 30.7327 20.8634 30.9682 21.2496 31.1712C19.6295 31.7287 17.8918 32.0344 16.0829 32.0391C7.24641 32.0621 0.064413 24.9172 0.0414956 16.0807C0.0185782 7.24416 7.16342 0.0621654 15.9999 0.039248Z" fill="currentColor"></path>
          <path class="arrow-icon" d="M20.377 16.8984C20.3231 16.8337 20.2569 16.7803 20.1824 16.7412C20.1078 16.7022 20.0263 16.6782 19.9424 16.6707C19.8586 16.6632 19.7741 16.6723 19.6938 16.6975C19.6134 16.7227 19.5389 16.7634 19.4743 16.8175L16.69 19.1479L16.6703 11.5576C16.6699 11.3878 16.602 11.2252 16.4817 11.1055C16.3613 10.9858 16.1984 10.9188 16.0286 10.9192C15.8589 10.9197 15.6963 10.9875 15.5766 11.1078C15.4569 11.2282 15.3899 11.3911 15.3903 11.5609L15.41 19.1513L12.6136 16.8353C12.5487 16.7817 12.4739 16.7414 12.3935 16.7166C12.3131 16.6919 12.2285 16.6833 12.1448 16.6912C12.061 16.6992 11.9796 16.7235 11.9052 16.7629C11.8309 16.8023 11.765 16.856 11.7114 16.9208C11.6578 16.9857 11.6174 17.0605 11.5927 17.1409C11.568 17.2213 11.5594 17.3059 11.5673 17.3896C11.5753 17.4734 11.5996 17.5548 11.639 17.6292C11.6784 17.7035 11.7321 17.7694 11.7969 17.823L15.6452 21.013L15.7414 21.0704L15.8247 21.115C15.9731 21.1718 16.1374 21.1714 16.2855 21.1138L16.3686 21.0688L16.4644 21.0109L20.2961 17.801C20.3608 17.7471 20.4142 17.681 20.4532 17.6064C20.4923 17.5318 20.5163 17.4503 20.5238 17.3664C20.5313 17.2826 20.5222 17.1981 20.497 17.1178C20.4718 17.0375 20.431 16.9629 20.377 16.8984V16.8984Z" fill="currentColor"></path>
        </svg>
      </div>
    </button>
  </div>
</section>
