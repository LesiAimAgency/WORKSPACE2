{{-- Frame 413: Đối Tác Thi Công (Partners Slider) - Figma Node 357:11846 --}}
@php
  $settings = $settings ?? [];
  $title = $settings['partners_title'] ?? $settings['title'] ?? 'ĐỐI TÁC THI CÔNG';
  
  $defaultPartners = [
    ['name' => 'Vina Electric', 'logo' => 'storage/media/partners/brand-vinaelectric.png'],
    ['name' => 'Schneider Electric', 'logo' => 'storage/media/brands/brand-schneider.svg'],
    ['name' => 'CADIVI', 'logo' => 'storage/media/brands/brand-cadivi.svg'],
    ['name' => 'MBT', 'logo' => 'storage/media/brands/brand-mbt.svg'],
    ['name' => 'ABB', 'logo' => 'storage/media/brands/brand-abb.svg'],
    ['name' => 'EMIC', 'logo' => 'storage/media/brands/brand-emic.svg'],
    ['name' => 'Thang Long', 'logo' => 'storage/media/brands/thang-long.png'],
    ['name' => 'BIS HCMC', 'logo' => 'storage/media/brands/bis-hcmc.png'],
    ['name' => 'Hanwha Life', 'logo' => 'storage/media/brands/hanwha.png'],
    ['name' => 'Filmore', 'logo' => 'storage/media/brands/filmore.png'],
  ];

  $partners = (!empty($settings['partners']) && is_array($settings['partners'])) ? $settings['partners'] : $defaultPartners;
  $loopPartners = (count($partners) < 18) ? array_merge($partners, $partners, $partners) : $partners;
@endphp

<section class="partners-slider-section w-full bg-[#F6F6F6] text-neutral-900 flex flex-col items-center justify-center py-10 sm:py-12 md:py-14 select-none relative z-10 box-border overflow-hidden" id="about-partners" aria-label="{{ $title }}">
  <div class="w-full max-w-[1440px] px-4 sm:px-8 lg:px-16 flex flex-col items-center gap-6 sm:gap-8">
    
    {{-- Title --}}
    <h2 class="text-[18px] sm:text-[20px] font-medium uppercase text-[#051650] text-center tracking-normal leading-tight reveal-item reveal-delay-1">
      {{ $title }}
    </h2>

    {{-- Slider Container with Soft Edge Gradients --}}
    <div class="relative w-full overflow-hidden reveal-item reveal-delay-2">
      {{-- Left & Right Gradient Shadows --}}
      <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-16 sm:w-24 bg-gradient-to-r from-[#F6F6F6] to-transparent z-10" aria-hidden="true"></div>
      <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-16 sm:w-24 bg-gradient-to-l from-[#F6F6F6] to-transparent z-10" aria-hidden="true"></div>

      {{-- Swiper Container --}}
      <div class="swiper home-partners-swiper w-full overflow-hidden">
        <div class="swiper-wrapper items-center">
          @foreach($loopPartners as $partner)
            @php
              $pLogo = !empty($partner['logo'])
                ? (str_starts_with($partner['logo'], 'http') ? $partner['logo'] : asset(ltrim($partner['logo'], '/')))
                : asset('storage/media/partners/brand-vinaelectric.png');
              $pName = $partner['name'] ?? 'Đối tác thi công';
              $pUrl = $partner['url'] ?? null;
            @endphp
            <div class="swiper-slide !flex items-center justify-center h-12 sm:h-14 px-4 sm:px-6">
              @if(!empty($pUrl))
                <a href="{{ $pUrl }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-center w-full h-full opacity-80 hover:opacity-100 transition-opacity" title="{{ $pName }}">
                  <img class="max-h-8 sm:max-h-10 w-auto max-w-[120px] sm:max-w-[150px] object-contain shrink-0 filter grayscale hover:grayscale-0 transition-all duration-300" 
                       src="{{ $pLogo }}" 
                       alt="{{ $pName }}" 
                       loading="lazy"
                       onerror="this.onerror=null; this.src='{{ asset('storage/media/partners/brand-vinaelectric.png') }}';">
                </a>
              @else
                <div class="flex items-center justify-center w-full h-full opacity-80 hover:opacity-100 transition-opacity" title="{{ $pName }}">
                  <img class="max-h-8 sm:max-h-10 w-auto max-w-[120px] sm:max-w-[150px] object-contain shrink-0 filter grayscale hover:grayscale-0 transition-all duration-300" 
                       src="{{ $pLogo }}" 
                       alt="{{ $pName }}" 
                       loading="lazy"
                       onerror="this.onerror=null; this.src='{{ asset('storage/media/partners/brand-vinaelectric.png') }}';">
                </div>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    </div>

  </div>
</section>
