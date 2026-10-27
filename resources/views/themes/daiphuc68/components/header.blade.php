@props(['headerClass' => 'header-light'])

<header class="site-header {{ $headerClass }} w-full flex justify-center items-center" id="main-site-header" data-figma-id="74:1518">
  <div class="figma-header-1440 h-[90px] md:h-[120px] lg:h-[132px] flex items-center justify-between relative">
    <div class="flex items-center z-20">
      <button class="menu-toggle-btn group focus:outline-none min-h-[44px] flex items-center gap-1" type="button" data-menu-toggle aria-label="Mở trình đơn điều hướng" aria-expanded="false">
        <span class="hamburger"><span class="line-top"></span><span class="line-bottom"></span></span>
        <span class="menu-text text-[16px] sm:text-[16px] font-normal tracking-normal ">Menu</span>
      </button>
    </div>
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none flex items-center justify-center">
      <a class="pointer-events-auto flex items-center gap-3 transition-transform duration-200 hover:scale-[1.02]" href="{{ route('home') }}" title="Đại Phúc 68 - Xây dựng giá trị Kiến tạo tương lai">
        <img class="logo-white-variant w-auto object-contain" src="{{ asset('themes/daiphuc68/assets/icons/logo-daiphuc68-white.svg') }}" style="height:60px" alt="Logo Đại Phúc 68">
        <img class="logo-dark-variant hidden w-auto object-contain" src="{{ asset('themes/daiphuc68/assets/icons/logo-daiphuc68.svg') }}" style="height:60px" alt="Logo Đại Phúc 68">
      </a>
    </div>
    <div class="flex items-center gap-3 sm:gap-6 z-20">
      <a class="nav-contact-link text-[14px] sm:text-[14px] font-normal tracking-normal capitalize hover:underline transition-colors inline-block cursor-pointer" href="{{ route('contact.page') }}" data-contact-modal-toggle data-nav-link>Liên Hệ</a>
    </div>
  </div>
</header>
