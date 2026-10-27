@php
  $defaultSideMenuItems = [
    ['id' => 'home-about', 'label' => 'VỀ CHÚNG TÔI', 'target' => '#home-about'],
    ['id' => 'home-projects', 'label' => 'DỰ ÁN', 'target' => '#home-projects'],
    ['id' => 'home-products', 'label' => 'SẢN PHẨM', 'target' => '#home-products'],
    ['id' => 'home-workflow', 'label' => 'QUY TRÌNH LÀM VIỆC', 'target' => '#home-workflow'],
    ['id' => 'home-commitments', 'label' => 'VÌ SAO CHỌN CHÚNG TÔI', 'target' => '#home-commitments'],
    ['id' => 'home-news', 'label' => 'TIN TỨC', 'target' => '#home-news'],
    ['id' => 'home-contact', 'label' => 'LIÊN HỆ', 'target' => '#home-contact'],
  ];
  $items = $sideMenuItems ?? $defaultSideMenuItems;
@endphp

<nav class="side-menu-hp fixed left-4 sm:left-6 md:left-8 top-1/2 -translate-y-1/2 z-40 select-none side-menu-dark side-menu-hidden" aria-label="Thanh điều hướng Onepage" data-side-menu="data-side-menu">
  <div class="side-menu-container relative w-[210px] flex flex-col items-start gap-2.5">
    {{-- Top vertical line segment (SVG) --}}
    <div class="side-menu-line-top w-[18px] flex items-center justify-start shrink-0 pointer-events-none transition-colors duration-300">
      <svg class="side-menu-line-svg" width="15" height="41" viewBox="0 0 15 41" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left: 2.5px;">
        <path d="M6 40.0155C6.33334 40.0163 6.66667 40.0172 7 40.0181C6.9934 39.3514 6.98679 38.6847 6.98019 38.018C6.86131 26.0177 6.74243 14.0173 6.62355 2.01696C6.61695 1.35028 6.61035 0.683589 6.60374 0.0169056C6.59368 0.683546 6.58362 1.35019 6.57355 2.01683C6.39243 14.0164 6.21131 26.016 6.03019 38.0155C6.02013 38.6822 6.01007 39.3488 6 40.0155Z" fill="currentColor"/>
      </svg>
    </div>
    
    {{-- Background vertical rail track --}}
    <div class="side-menu-track absolute left-[8.5px] top-[48px] bottom-[48px] w-[1px] bg-[#818181]/25 pointer-events-none"></div>

    {{-- Menu Items List --}}
    <ul class="side-menu-list flex flex-col gap-[9px] py-0.5 relative z-10 w-full">
      @foreach($items as $index => $item)
        <li class="side-menu-item group relative flex items-center {{ $index === 0 ? 'active' : '' }}" data-side-menu-item="{{ $item['id'] }}">
          <a class="side-menu-link flex items-center gap-3.5 focus:outline-none py-1 group w-full cursor-pointer" 
             href="{{ $item['target'] }}" 
             data-scroll-to="{{ $item['target'] }}" 
             aria-label="Chuyển đến phần {{ $item['label'] }}">
            <span class="side-menu-dot-wrap w-[18px] h-[18px] flex items-center justify-center shrink-0 text-[#818181] transition-colors duration-300">
              <svg class="side-menu-dot-svg icon-bottom-svg" width="18" height="18" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path class="ring-inner" d="M17.9479 26.434C12.2209 27.6774 6.46305 24.2225 4.9143 18.5018L4.85684 18.2635C5.23233 18.4517 5.62336 18.609 6.02629 18.734C7.67677 23.805 13.0346 26.7432 18.2399 25.3341C23.6119 23.8798 26.7879 18.3457 25.3335 12.9737C23.9242 7.76852 18.6843 4.62471 13.4732 5.75791C13.162 5.47306 12.829 5.21478 12.477 4.98555C12.5552 4.96254 12.6327 4.93631 12.7118 4.9149L13.2661 4.78C18.8084 3.57702 24.3788 6.77438 26.1358 12.1667L26.2988 12.7124C27.8974 18.6174 24.4063 24.7007 18.5012 26.2994L17.9479 26.434Z" fill="currentColor"></path>
                <path class="ring-outer" d="M15.9999 0.039248C24.8365 0.0163306 32.0185 7.16117 32.0414 15.9977C32.0461 17.8066 31.7494 19.5458 31.2003 21.1688C30.9953 20.7836 30.7579 20.4187 30.4904 20.0779C30.8523 18.7808 31.0451 17.4132 31.0414 16.0003C31.0199 7.71605 24.2868 1.01776 16.0025 1.03924C7.7183 1.06073 1.02001 7.79385 1.04149 16.0781C1.06298 24.3623 7.7961 31.0606 16.0803 31.0391C17.4933 31.0355 18.8598 30.8356 20.155 30.4671C20.4972 30.7327 20.8634 30.9682 21.2496 31.1712C19.6295 31.7287 17.8918 32.0344 16.0829 32.0391C7.24641 32.0621 0.064413 24.9172 0.0414956 16.0807C0.0185782 7.24416 7.16342 0.0621654 15.9999 0.039248Z" fill="currentColor"></path>
                <circle class="dot-core" cx="16.04" cy="16.04" r="4.2" fill="currentColor"></circle>
              </svg>
            </span>
            <span class="side-menu-label text-[12px] sm:text-[12px] font-bold uppercase tracking-[0.14em] text-[#818181] transition-all duration-300 whitespace-nowrap leading-none">
              {{ $item['label'] }}
            </span>
          </a>
        </li>
      @endforeach
    </ul>

    {{-- Bottom vertical line segment (SVG) --}}
    <div class="side-menu-line-bottom w-[18px] flex items-center justify-start shrink-0 pointer-events-none transition-colors duration-300">
      <svg class="side-menu-line-svg" width="15" height="41" viewBox="0 0 15 41" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left: 2.5px; transform: scaleY(-1);">
        <path d="M6 40.0155C6.33334 40.0163 6.66667 40.0172 7 40.0181C6.9934 39.3514 6.98679 38.6847 6.98019 38.018C6.86131 26.0177 6.74243 14.0173 6.62355 2.01696C6.61695 1.35028 6.61035 0.683589 6.60374 0.0169056C6.59368 0.683546 6.58362 1.35019 6.57355 2.01683C6.39243 14.0164 6.21131 26.016 6.03019 38.0155C6.02013 38.6822 6.01007 39.3488 6 40.0155Z" fill="currentColor"/>
      </svg>
    </div>
  </div>
</nav>
