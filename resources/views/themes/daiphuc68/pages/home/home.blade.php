@extends('themes.daiphuc68.layouts.app')

@section('title', 'Đại Phúc 68 - Xây Dựng Giá Trị, Kiến Tạo Tương Lai')
@section('meta_description', 'Đại Phúc 68 - Cung ứng vật tư thiết bị điện trung thế, giải pháp kiến trúc và thi công xây lắp trọn gói chuyên nghiệp hàng đầu.')
@section('header_class', 'header-transparent')
@section('hide_footer', 'true')

@section('content')
<div class="homepage-main w-full flex flex-col items-center">
  {{-- Onepage Side Menu Navigation Component --}}
  @include('themes.daiphuc68.components.side-menu')

  @if(function_exists('render_widget_area') && \App\Models\Widget::withoutGlobalScope('tenant')->where('area', 'homepage-main')->where('is_active', true)->exists())
    {!! render_widget_area('homepage-main', compact('products', 'projects', 'news')) !!}
  @else
    {{-- Section 1: Hero Banner --}}
    @include('themes.daiphuc68.pages.home.hero')

    {{-- Section 2: About Us (Về Chúng Tôi) --}}
    @include('themes.daiphuc68.pages.home.about')

    {{-- Section 3: Featured Projects (Những Dự Án Tự Hào) --}}
    @include('themes.daiphuc68.pages.home.projects')

    {{-- Section 4: Products Showcase (Vật Tư Thiết Bị Điện) --}}
    @include('themes.daiphuc68.pages.home.products')

    {{-- Section 5: Workflow (Quy Trình Làm Việc) --}}
    @include('themes.daiphuc68.pages.home.workflow')

    {{-- Section 6: Commitments (Vì Sao Chọn Chúng Tôi) --}}
    @include('themes.daiphuc68.pages.home.commitments')

    {{-- Section 7: Clients Showcase (Khách Hàng) --}}
    @include('themes.daiphuc68.pages.home.clients')

    {{-- Section 8: News Showcase (Tin Tức Mới Nhất) --}}
    @include('themes.daiphuc68.pages.home.news')

    {{-- Section 9: Contact & Partners (Thông Tin Liên Hệ & Đối Tác) --}}
    @include('themes.daiphuc68.pages.home.contact')
  @endif
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const isDesktop = () => window.innerWidth >= 1024;
    const heroSec = document.getElementById('home-hero');
    const sections = Array.from(document.querySelectorAll('section.homepage-section'));
    const sideMenu = document.querySelector('.side-menu-hp');
    const menuItems = document.querySelectorAll('[data-side-menu-item]');
    const hiddenSectionIds = ['home-hero', 'home-clients'];

    document.documentElement.classList.add('reveal-ready');
    if (heroSec) {
      heroSec.classList.add('section-revealed');
    }

    // =========================================================================
    // 1. GSAP LAZY LOAD & STAGGERED DOWNWARD ENTRANCE SYSTEM FOR ALL SECTIONS
    // =========================================================================
    const animatedSectionIds = new Set();

    function animateSectionDown(sec) {
      if (!sec || animatedSectionIds.has(sec.id)) return;
      animatedSectionIds.add(sec.id);
      sec.classList.add('section-revealed', 'is-revealed');

      if (typeof gsap === 'undefined') {
        sec.querySelectorAll('.reveal-item, .reveal-fade, .reveal-scale').forEach(el => {
          el.style.opacity = '1';
          el.style.transform = 'translateY(0) scale(1)';
        });
        return;
      }

      // --- HERO SECTION ENTRANCE ---
      if (sec.id === 'home-hero') {
        const heroTl = gsap.timeline({ defaults: { ease: 'power2.out' } });
        const heroCenter = sec.querySelector('#hero-center-text');
        const heroBottom = sec.querySelector('#hero-bottom-row');
        const heroIndicator = sec.querySelector('.btn-scroll-indicator');

        if (heroCenter) {
          heroTl.fromTo(heroCenter, 
            { y: -45, opacity: 0, scale: 0.96 }, 
            { y: 0, opacity: 1, scale: 1, duration: 1.0, clearProps: 'transform,opacity' }, 
            0.2
          );
        }
        if (heroBottom) {
          heroTl.fromTo(heroBottom.children, 
            { y: -35, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 0.85, stagger: 0.12, clearProps: 'transform,opacity' }, 
            0.5
          );
        }
        if (heroIndicator) {
          heroTl.fromTo(heroIndicator, 
            { y: -20, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 0.7, clearProps: 'transform,opacity' }, 
            0.8
          );
        }
        return;
      }

      // --- WORKFLOW SECTION ENTRANCE ---
      if (sec.id === 'home-workflow') {
        const wfTl = gsap.timeline({ defaults: { ease: 'power2.out' } });
        const header = sec.querySelector('.workflow-header');
        const steps = sec.querySelectorAll('.workflow-step');
        const ctaBtn = sec.querySelector('.workflow-cta-btn');
        const indicator = sec.querySelector('.btn-scroll-indicator');

        if (header) {
          wfTl.fromTo(header.children, 
            { y: -40, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 0.8, stagger: 0.1, clearProps: 'transform,opacity' }
          );
        }
        if (steps.length) {
          wfTl.fromTo(steps, 
            { y: -45, opacity: 0 }, 
            { 
              y: 0, 
              opacity: 1, 
              duration: 0.75, 
              stagger: 0.08, 
              clearProps: 'transform,opacity',
              onStart: () => {
                steps.forEach((st, idx) => {
                  const lineFill = st.querySelector('.workflow-step-line-fill');
                  if (lineFill) {
                    gsap.to(lineFill, { scaleX: 1, duration: 0.6, delay: idx * 0.1, ease: 'power2.out' });
                  }
                });
              }
            }, 
            '-=0.3'
          );
        }
        if (ctaBtn) {
          wfTl.fromTo(ctaBtn, 
            { y: -25, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 0.7, clearProps: 'transform,opacity' }, 
            '-=0.2'
          );
        }
        if (indicator) {
          wfTl.fromTo(indicator, 
            { y: -20, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 0.6, clearProps: 'transform,opacity' }, 
            '-=0.2'
          );
        }
        return;
      }

      // --- CLIENTS SHOWCASE ENTRANCE ---
      if (sec.id === 'home-clients') {
        const clientsTl = gsap.timeline({ defaults: { ease: 'power2.out' } });
        const centerBlock = sec.querySelector('#clients-canvas-stage > .pointer-events-auto');
        const cols = sec.querySelectorAll('.client-stage-col');
        const mobileContent = sec.querySelector('.lg\\:hidden');
        const indicator = sec.querySelector('.btn-scroll-indicator');

        if (centerBlock) {
          clientsTl.fromTo(centerBlock.children, 
            { y: -40, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 0.85, stagger: 0.1, clearProps: 'transform,opacity' }
          );
        }
        if (cols.length) {
          clientsTl.fromTo(cols, 
            { y: -35, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 0.8, stagger: 0.05, clearProps: 'transform,opacity' }, 
            0.15
          );
        }
        if (mobileContent) {
          clientsTl.fromTo(mobileContent.children, 
            { y: -30, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 0.75, stagger: 0.08, clearProps: 'transform,opacity' }, 
            0.1
          );
        }
        if (indicator) {
          clientsTl.fromTo(indicator, 
            { y: -20, opacity: 0 }, 
            { y: 0, opacity: 1, duration: 0.6, clearProps: 'transform,opacity' }, 
            '-=0.2'
          );
        }
        return;
      }

      // --- ALL OTHER SECTIONS ---
      let animItems = Array.from(sec.querySelectorAll('.reveal-item, .reveal-fade, .reveal-scale'));
      if (!animItems.length) {
        animItems = Array.from(sec.querySelectorAll('.figma-frame-1440 > *'));
      }

      const indicator = sec.querySelector('.btn-scroll-indicator');
      if (indicator && !animItems.includes(indicator)) {
        animItems.push(indicator);
      }

      if (animItems.length) {
        gsap.fromTo(animItems, 
          {
            y: -35,
            opacity: 0,
            force3D: true
          },
          {
            y: 0,
            opacity: 1,
            duration: 0.8,
            ease: 'power2.out',
            stagger: 0.08,
            clearProps: 'transform,opacity'
          }
        );
      }
    }

    function initGsapSectionTriggers() {
      if (typeof gsap !== 'undefined') {
        document.documentElement.classList.add('gsap-active');
        if (typeof ScrollTrigger !== 'undefined') {
          gsap.registerPlugin(ScrollTrigger);

          sections.forEach(sec => {
            if (sec.id === 'home-hero') {
              animateSectionDown(sec);
            } else {
              ScrollTrigger.create({
                trigger: sec,
                start: 'top 80%',
                once: true,
                onEnter: () => animateSectionDown(sec)
              });
            }
          });
          return;
        }
      }

      // Fallback
      const revealObserver = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            animateSectionDown(entry.target);
            obs.unobserve(entry.target);
          }
        });
      }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

      sections.forEach(sec => {
        if (sec.id === 'home-hero') {
          animateSectionDown(sec);
        } else {
          revealObserver.observe(sec);
        }
      });
    }

    if (typeof gsap !== 'undefined') {
      initGsapSectionTriggers();
    } else {
      window.addEventListener('load', initGsapSectionTriggers);
      setTimeout(initGsapSectionTriggers, 200);
    }

    // =========================================================================
    // 2. FULL-SECTION SCROLL CONTROLLER ("khi scroll xuống phải xuống toàn bộ")
    // =========================================================================
    document.documentElement.classList.add('is-homepage-snap');

    let isSectionNavigating = false;
    let sectionNavTimer = null;

    function getActiveSectionIndex() {
      const scrollY = window.scrollY;
      let closestIdx = 0;
      let minDistance = Infinity;

      sections.forEach((sec, idx) => {
        const top = sec.offsetTop;
        const dist = Math.abs(scrollY - top);
        if (dist < minDistance) {
          minDistance = dist;
          closestIdx = idx;
        }
      });
      return closestIdx;
    }

    function scrollToSectionIndex(targetIdx) {
      if (targetIdx < 0 || targetIdx >= sections.length) return;
      const targetSec = sections[targetIdx];
      if (!targetSec) return;

      isSectionNavigating = true;
      if (sectionNavTimer) clearTimeout(sectionNavTimer);

      window.scrollTo({
        top: targetSec.offsetTop,
        behavior: 'smooth'
      });

      animateSectionDown(targetSec);

      sectionNavTimer = setTimeout(() => {
        isSectionNavigating = false;
      }, 750);
    }

    // Wheel Scroll Down / Up (Desktop >= 1024px)
    window.addEventListener('wheel', function (e) {
      if (!isDesktop()) return;

      if (Math.abs(e.deltaY) < 18) return;

      if (isSectionNavigating) {
        e.preventDefault();
        return;
      }

      const currentIdx = getActiveSectionIndex();

      if (e.deltaY > 0) {
        if (currentIdx < sections.length - 1) {
          e.preventDefault();
          scrollToSectionIndex(currentIdx + 1);
        }
      } else if (e.deltaY < 0) {
        if (currentIdx > 0) {
          e.preventDefault();
          scrollToSectionIndex(currentIdx - 1);
        }
      }
    }, { passive: false });

    // Keyboard navigation
    window.addEventListener('keydown', function (e) {
      if (!isDesktop()) return;
      if (['ArrowDown', 'PageDown', ' '].includes(e.key) && !e.target.matches('input, textarea, select')) {
        const currentIdx = getActiveSectionIndex();
        if (currentIdx < sections.length - 1) {
          e.preventDefault();
          scrollToSectionIndex(currentIdx + 1);
        }
      } else if (['ArrowUp', 'PageUp'].includes(e.key) && !e.target.matches('input, textarea, select')) {
        const currentIdx = getActiveSectionIndex();
        if (currentIdx > 0) {
          e.preventDefault();
          scrollToSectionIndex(currentIdx - 1);
        }
      }
    });

    // 3. Click Handler for All Scroll Down Indicators and Links
    document.querySelectorAll('[data-scroll-to], [data-scroll-target], .btn-scroll-indicator').forEach(trigger => {
      trigger.addEventListener('click', function (e) {
        const targetSelector = this.getAttribute('data-scroll-to') || 
                               this.getAttribute('data-scroll-target') || 
                               this.getAttribute('href');
        if (targetSelector && targetSelector.startsWith('#')) {
          e.preventDefault();
          const targetEl = document.querySelector(targetSelector);
          if (targetEl) {
            const idx = sections.indexOf(targetEl);
            if (idx !== -1 && isDesktop()) {
              scrollToSectionIndex(idx);
            } else {
              targetEl.scrollIntoView({ behavior: 'smooth' });
              animateSectionDown(targetEl);
            }
          }
        }
      });
    });

    // =========================================================================
    // 4. Hero 3-Frame Intro Controller
    // =========================================================================
    if (heroSec) {
      let isIntroSettled = false;
      let timerF2 = null;
      let timerF3 = null;
      let timerFinish = null;

      function settleIntro() {
        if (isIntroSettled) return;
        isIntroSettled = true;
        if (timerF2) clearTimeout(timerF2);
        if (timerF3) clearTimeout(timerF3);
        if (timerFinish) clearTimeout(timerFinish);

        heroSec.classList.remove('hero-intro-f1', 'hero-intro-f2');
        heroSec.classList.add('hero-intro-f3');
        setTimeout(() => {
          heroSec.classList.add('hero-intro-settled');
        }, 1200);
      }

      heroSec.classList.add('hero-intro-f1');

      timerF2 = setTimeout(() => {
        if (isIntroSettled) return;
        heroSec.classList.remove('hero-intro-f1');
        heroSec.classList.add('hero-intro-f2');
      }, 500);

      timerF3 = setTimeout(() => {
        if (isIntroSettled) return;
        heroSec.classList.remove('hero-intro-f2');
        heroSec.classList.add('hero-intro-f3');
      }, 1800);

      timerFinish = setTimeout(() => {
        settleIntro();
      }, 3000);

      const handleUserEarlyInteract = function () {
        if (!isIntroSettled) {
          settleIntro();
        }
      };

      window.addEventListener('wheel', handleUserEarlyInteract, { passive: true, once: true });
      window.addEventListener('touchmove', handleUserEarlyInteract, { passive: true, once: true });
      const scrollIndicatorBtn = heroSec.querySelector('.btn-scroll-indicator');
      if (scrollIndicatorBtn) {
        scrollIndicatorBtn.addEventListener('click', handleUserEarlyInteract, { once: true });
      }
    }

    // =========================================================================
    // 5. Side Menu ScrollSpy & Adaptive Theme Switching
    // =========================================================================
    const sideMenuObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const id = entry.target.id;
          if (sideMenu) {
            if (hiddenSectionIds.includes(id)) {
              sideMenu.classList.add('side-menu-hidden');
            } else {
              sideMenu.classList.remove('side-menu-hidden');
              if (id === 'home-workflow') {
                sideMenu.classList.add('side-menu-dark');
                sideMenu.classList.remove('side-menu-light');
              } else {
                sideMenu.classList.add('side-menu-light');
                sideMenu.classList.remove('side-menu-dark');
              }
            }
          }
          menuItems.forEach(item => {
            if (item.getAttribute('data-side-menu-item') === id) {
              item.classList.add('active');
            } else {
              item.classList.remove('active');
            }
          });
        }
      });
    }, { threshold: 0.35 });

    sections.forEach(sec => sideMenuObserver.observe(sec));

    function checkTopScroll() {
      if (window.scrollY < 120 && sideMenu) {
        sideMenu.classList.add('side-menu-hidden');
      }
    }
    window.addEventListener('scroll', checkTopScroll, { passive: true });
    checkTopScroll();

    // 4. Project Card Switcher
    const projSlides = document.querySelectorAll('.proj-card__img-slide');
    const projSpecs = document.querySelectorAll('.proj-specs-set');
    const projTitle = document.getElementById('projCardTitle');
    const projDesc = document.getElementById('projCardDesc');
    const projViewMore = document.getElementById('projViewMoreBtn');
    let currentProjIdx = 0;

    const projectsData = [
      @foreach($projects as $p)
        {
          title: @json($p->title),
          desc: @json($p->excerpt),
          url: @json($p->url)
        },
      @endforeach
    ];

    function showProjectSlide(idx) {
      if (idx < 0) idx = projSlides.length - 1;
      if (idx >= projSlides.length) idx = 0;
      currentProjIdx = idx;

      projSlides.forEach((s, i) => s.classList.toggle('active', i === idx));
      projSpecs.forEach((s, i) => {
        s.classList.toggle('active', i === idx);
        s.setAttribute('aria-hidden', i === idx ? 'false' : 'true');
      });

      if (projectsData[idx]) {
        if (projTitle) projTitle.textContent = projectsData[idx].title;
        if (projDesc) projDesc.textContent = projectsData[idx].desc;
        if (projViewMore) projViewMore.href = projectsData[idx].url;
      }
    }

    const btnPrev = document.getElementById('projBtnPrev');
    const btnNext = document.getElementById('projBtnNext');
    if (btnPrev) btnPrev.addEventListener('click', () => showProjectSlide(currentProjIdx - 1));
    if (btnNext) btnNext.addEventListener('click', () => showProjectSlide(currentProjIdx + 1));

    // 5. Initialize Swiper Sliders
    function initAllSwipers() {
      if (typeof Swiper === 'undefined') {
        setTimeout(initAllSwipers, 60);
        return;
      }

      // News Swiper
      const newsEl = document.querySelector('.home-news-swiper');
      if (newsEl && !newsEl.swiper) {
        newsEl.classList.remove('overflow-visible');
        newsEl.style.overflow = 'hidden';

        const syncNewsNavToActiveSlide = (swiperInstance) => {
          const swiper = swiperInstance || newsSwiper;
          if (!swiper) return;
          const activeSlide = swiper.slides ? (swiper.slides[swiper.activeIndex] || newsEl.querySelector('.swiper-slide-active')) : newsEl.querySelector('.swiper-slide-active');
          const nav = document.getElementById('home-news-nav');
          if (activeSlide && nav) {
            const actionContainer = activeSlide.querySelector('.news-slide-action');
            if (actionContainer && nav.parentElement !== actionContainer) {
              actionContainer.appendChild(nav);
              if (swiper.navigation && typeof swiper.navigation.update === 'function') {
                swiper.navigation.update();
              }
            }
          }
        };

        const newsSwiper = new Swiper(newsEl, {
          slidesPerView: 1,
          spaceBetween: 16,
          speed: 600,
          grabCursor: true,
          watchSlidesProgress: true,
          navigation: {
            prevEl: '.home-news-prev',
            nextEl: '.home-news-next',
          },
          breakpoints: {
            640: {
              slidesPerView: 1.15,
              spaceBetween: 20,
            },
            1024: {
              slidesPerView: 1.45,
              spaceBetween: 28,
            },
            1280: {
              slidesPerView: 1.45,
              spaceBetween: 32,
            }
          },
          on: {
            init: function () {
              syncNewsNavToActiveSlide(this);
            },
            slideChange: function () {
              syncNewsNavToActiveSlide(this);
            },
            slideChangeTransitionStart: function () {
              syncNewsNavToActiveSlide(this);
            },
            slideChangeTransitionEnd: function () {
              syncNewsNavToActiveSlide(this);
            }
          }
        });

        syncNewsNavToActiveSlide(newsSwiper);

        const newsSection = document.getElementById('home-news');
        if (newsSection) {
          newsSection.addEventListener('click', function (e) {
            const prevBtn = e.target.closest('.home-news-prev');
            if (prevBtn) {
              e.preventDefault();
              e.stopPropagation();
              newsSwiper.slidePrev();
              return;
            }
            const nextBtn = e.target.closest('.home-news-next');
            if (nextBtn) {
              e.preventDefault();
              e.stopPropagation();
              newsSwiper.slideNext();
              return;
            }
          });
        }
      }

      // Products Swiper
      const productsEl = document.querySelector('.home-products-swiper');
      if (productsEl && !productsEl.swiper) {
        const productsProgressBar = document.querySelector('.home-products-progress-bar');
        const updateProductsProgress = (swiper) => {
          if (!productsProgressBar) return;
          const total = swiper.snapGrid ? swiper.snapGrid.length : (swiper.slides.length - swiper.params.slidesPerView + 1);
          const progress = total > 1 ? (swiper.snapIndex / (total - 1)) : 0;
          const pct = 25 + progress * 75;
          productsProgressBar.style.width = `${Math.min(100, Math.max(25, pct))}%`;
        };

        new Swiper(productsEl, {
          slidesPerView: 1.2,
          spaceBetween: 14,
          speed: 500,
          grabCursor: true,
          navigation: {
            prevEl: '.home-products-prev',
            nextEl: '.home-products-next',
          },
          breakpoints: {
            480: { slidesPerView: 2, spaceBetween: 16 },
            768: { slidesPerView: 3, spaceBetween: 18 },
            1024: { slidesPerView: 4, spaceBetween: 20 },
          },
          on: {
            init: function () { updateProductsProgress(this); },
            slideChange: function () { updateProductsProgress(this); }
          }
        });
      }

      // Partners Swiper (Đối tác thi công)
      const partnersEl = document.querySelector('.home-partners-swiper');
      if (partnersEl && !partnersEl.swiper) {
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
            400: {
              slidesPerView: 3,
              spaceBetween: 20,
            },
            640: {
              slidesPerView: 4,
              spaceBetween: 24,
            },
            768: {
              slidesPerView: 5,
              spaceBetween: 28,
            },
            1024: {
              slidesPerView: 6,
              spaceBetween: 32,
            },
          },
        });
      }
    }

    initAllSwipers();
  });
</script>
@endpush
@endsection
