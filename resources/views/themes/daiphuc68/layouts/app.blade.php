<!DOCTYPE html>
<html lang="vi" class="{{ (request()->routeIs('home') || request()->is('/')) ? 'is-homepage' : '' }}">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('themes.daiphuc68.components.seo-head')
    @yield('seo_tags')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>  
    <link rel="stylesheet" crossorigin href="{{ asset('themes/daiphuc68/assets/css/style.css') }}?v={{ file_exists(public_path('themes/daiphuc68/assets/css/style.css')) ? filemtime(public_path('themes/daiphuc68/assets/css/style.css')) : '1.0' }}">
   
    @include('themes.daiphuc68.components.ckeditor-content-styles')
    <script type="module" crossorigin src="{{ asset('themes/daiphuc68/assets/js/main.js') }}?v={{ file_exists(public_path('themes/daiphuc68/assets/js/main.js')) ? filemtime(public_path('themes/daiphuc68/assets/js/main.js')) : '1.0' }}"></script>
    @stack('styles')
  </head>
  @php
    $isHome = request()->routeIs('home') || request()->is('/');
  @endphp
  <body class="bg-[#F6F6F6] text-neutral-900 font-sans min-h-screen selection:bg-brand-navy selection:text-white overflow-x-hidden {{ $isHome ? 'is-homepage' : 'is-subpage is-about-page' }}">
    
    @php
      if ($isHome) {
        $computedHeaderClass = $headerClass ?? (trim($__env->yieldContent('header_class')) ?: 'header-transparent');
      } else {
        $computedHeaderClass = 'header-light header-about-page';
      }
      $computedHeaderClass = trim(str_replace(['header-relative', 'header-static'], '', $computedHeaderClass));
    @endphp
    @include('themes.daiphuc68.components.header', ['headerClass' => $computedHeaderClass, 'isHome' => $isHome])

    <main id="app-main-content">
      @yield('content')
    </main>

    @unless(View::hasSection('hide_footer'))
        @include('themes.daiphuc68.components.footer')
    @endunless

    @include('themes.daiphuc68.components.navigation-drawer')

    @include('themes.daiphuc68.components.contact-modal')

    @include('frontend.partials.contact-buttons')

    @if(config('app.figma_layout_guide', env('FIGMA_LAYOUT_GUIDE', false)))
        @include('themes.daiphuc68.components.layout-grid-overlay')
    @endif

    <div class="toast-notification" id="global-toast-notification"></div>

    <script>
      // Global Toast Notification Helper
      window.showToast = function(message, type = 'success') {
        const toast = document.getElementById('global-toast-notification');
        if (!toast) return;
        toast.textContent = message;
        toast.className = 'toast-notification show ' + (type === 'error' ? 'toast-error bg-red-600 text-white' : 'toast-success bg-brand-navy text-white');
        setTimeout(() => {
          toast.className = 'toast-notification';
        }, 4000);
      };

      // Handle Modal Contact Form via AJAX
      document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('modal-contact-form');
        if (form) {
          form.addEventListener('submit', function(e) {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            const formData = new FormData(form);
            if (!formData.get('page_url')) {
              formData.set('page_url', window.location.href);
            }

            fetch(form.action || '{{ route("contact.submit") }}', {
              method: 'POST',
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              },
              body: formData
            })
            .then(res => res.json())
            .then(data => {
              if (submitBtn) submitBtn.disabled = false;
              if (data.status === 'success') {
                showToast(data.message || 'Gửi yêu cầu thành công!');
                form.reset();
                if (typeof window.closeContactModal === 'function') {
                  window.closeContactModal();
                } else {
                  const overlay = document.getElementById('contact-modal-overlay');
                  if (overlay) overlay.classList.remove('active');
                  document.body.classList.remove('modal-open');
                  document.body.style.overflow = '';
                }
              } else {
                showToast(data.message || 'Vui lòng kiểm tra lại thông tin.', 'error');
              }
            })
            .catch(err => {
              if (submitBtn) submitBtn.disabled = false;
              showToast('Đã có lỗi xảy ra, vui lòng thử lại sau.', 'error');
            });
          });
        }
      });

      // Dynamic Sticky Header Adaptive Color Switching on Scroll (All Pages)
      (function() {
        const header = document.getElementById('main-site-header');
        if (!header) return;

        // Custom handling for all Subpages & Posts (like About Us page):
        // Initially at top: sticky, transparent background, text #051650, dark logo.
        // After scroll: become sticky menu with #F6F6F6 frosted glass & subtle shadow!
        const isSubpage = !document.body.classList.contains('is-homepage') ||
                          document.body.classList.contains('is-subpage') ||
                          document.body.classList.contains('is-about-page') ||
                          header.classList.contains('header-about-page') ||
                          (window.location.pathname !== '/' && !window.location.pathname.endsWith('/index.php'));

        if (isSubpage) {
          header.classList.remove('header-transparent');
          header.classList.add('header-light', 'header-about-page');

          function handleAboutScroll() {
            const scrollY = window.scrollY || window.pageYOffset || 0;
            // "lúc đầu vô trang sẽ không có sticky nằm dưới menu... khi bắt đầu scroll mới có"
            if (scrollY > 30) {
              header.classList.add('is-sticky');
            } else {
              header.classList.remove('is-sticky');
            }
          }

          window.addEventListener('scroll', function() {
            window.requestAnimationFrame(handleAboutScroll);
          }, { passive: true });

          // Initialize state on load
          handleAboutScroll();
          window.addEventListener('load', handleAboutScroll, { once: true });
          return;
        }

        function updateHeaderColor() {
          const headerHeight = header.offsetHeight || 90;
          const headerMid = headerHeight / 2;

          // Find dark background sections across home and all subpages
          const darkElements = document.querySelectorAll(
            '#home-hero, #home-workflow, section[class*="bg-brand-navy"], section[class*="bg-dark"], section[id*="hero"], section[aria-label*="Hero Banner"], .hero-section'
          );

          let isOverDark = false;
          darkElements.forEach(function(el) {
            const rect = el.getBoundingClientRect();
            if (rect.top <= headerMid && rect.bottom >= headerMid) {
              isOverDark = true;
            }
          });

          if (isOverDark) {
            header.classList.remove('header-light');
            header.classList.add('header-transparent');
          } else {
            header.classList.remove('header-transparent');
            header.classList.add('header-light');
          }
        }

        window.addEventListener('scroll', function() {
          window.requestAnimationFrame(updateHeaderColor);
        }, { passive: true });

        updateHeaderColor();
        setTimeout(updateHeaderColor, 100);
      })();
    </script>
    
    @stack('scripts')
  </body>
</html>
