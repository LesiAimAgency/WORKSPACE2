@extends('themes.daiphuc68.layouts.app', ['headerClass' => 'header-light'])

@section('title', 'Liên Hệ - CÔNG TY TNHH ĐẠI PHÚC 68')
@section('meta_description', 'Thông tin liên hệ Công ty TNHH Đại Phúc 68 - Trụ sở Huỳnh Thiện Lộc, P. Tân Phú, TP. HCM. Cung ứng vật tư thiết bị điện và thi công xây lắp trọn gói.')
@section('hide_footer', 'true')

@section('content')
  @include('themes.daiphuc68.pages.home.contact')
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize Partners Swiper
    function initPartnersSwiper() {
      const partnersEl = document.querySelector('.home-partners-swiper');
      if (partnersEl && typeof Swiper !== 'undefined' && !partnersEl.swiper) {
        new Swiper(partnersEl, {
          slidesPerView: 2.5,
          spaceBetween: 18,
          loop: true,
          speed: 800,
          grabCursor: true,
          autoplay: {
            delay: 2200,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
          },
          navigation: {
            prevEl: '.home-partners-prev',
            nextEl: '.home-partners-next',
          },
          breakpoints: {
            400: { slidesPerView: 3, spaceBetween: 20 },
            640: { slidesPerView: 4, spaceBetween: 24 },
            768: { slidesPerView: 5, spaceBetween: 28 },
            1024: { slidesPerView: 6, spaceBetween: 32 },
          },
        });
      }
    }
    
    if (typeof Swiper !== 'undefined') {
      initPartnersSwiper();
    } else {
      window.addEventListener('load', initPartnersSwiper);
    }

    // 2. Scroll to top indicator handler
    document.querySelectorAll('[data-scroll-to], [data-scroll-target], .btn-scroll-indicator').forEach(trigger => {
      trigger.addEventListener('click', function (e) {
        e.preventDefault();
        const targetSelector = this.getAttribute('data-scroll-to') || this.getAttribute('data-scroll-target');
        if (targetSelector && document.querySelector(targetSelector)) {
          document.querySelector(targetSelector).scrollIntoView({ behavior: 'smooth' });
        } else {
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      });
    });

    // 3. Tab Deep Linking (#faq)
    if (window.location.hash === '#faq' || window.location.hash === '#contact-panel-faq' || new URLSearchParams(window.location.search).get('tab') === 'faq') {
      const faqTab = document.getElementById('contact-tab-faq');
      if (faqTab) {
        faqTab.click();
      }
    }
  });
</script>
@endpush
