@extends('themes.daiphuc68.layouts.app', ['headerClass' => 'header-light header-about-page'])

@section('title', $page?->meta_title ?? 'Về Chúng Tôi - CÔNG TY TNHH ĐẠI PHÚC 68')
@section('meta_description', $page?->meta_description ?? 'Giới thiệu năng lực, quy mô và sứ mệnh của CÔNG TY TNHH ĐẠI PHÚC 68 - Đơn vị hàng đầu về kiến trúc và thi công xây lắp trọn gói.')
@section('header_class', 'header-light header-about-page')

@section('content')
<div class="about-page-main w-full flex flex-col items-center box-border">
  @if(function_exists('render_widget_area') && \App\Models\Widget::withoutGlobalScope('tenant')->where('area', 'about-main')->where('is_active', true)->exists())
    {!! render_widget_area('about-main', compact('page', 'featuredProjects')) !!}
  @else
    {{-- Section 1: Hero Banner --}}
    @include('themes.daiphuc68.pages.home.hero')
    {{-- Section 2: About Overview --}}
    @include('themes.daiphuc68.components.about-overview')
    {{-- Section 3: Dual Image Showcase Gallery --}}
    @include('themes.daiphuc68.components.about-showcase-gallery')
    {{-- Section 4: Commitments / Core Values --}}
    @include('themes.daiphuc68.pages.home.commitments', [
      'settings' => [
        'layout_style' => 'about_values',
        'headline_top' => 'GIÁ TRỊ CỐT LÕI',
        'headline_middle' => '',
        'headline_bottom' => 'MÀ CHÚNG TÔI THEO ĐUỔI',
      ]
    ])
    {{-- Section 5: Projects Ellipse--}}  
    @include('themes.daiphuc68.components.projects-ellipse-showcase', [
      'projects' => $featuredProjects,
      'title_line_1' => 'NHỮNG DỰ ÁN',
      'title_line_2' => 'MÀ CHÚNG TÔI TỰ HÀO',
    ])
    {{-- Section 6: Partners Slider --}}
    @include('themes.daiphuc68.components.partners-slider')
  @endif
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // 0. Staggered GSAP & ScrollTrigger Section Reveal Initializer
    const aboutSections = document.querySelectorAll('.about-page-main section');
    const animatedSectionIds = new Set();

    function animateSectionDown(sec) {
      if (!sec) return;
      const secKey = sec.id || ('sec_' + Array.from(aboutSections).indexOf(sec));
      if (animatedSectionIds.has(secKey)) return;
      animatedSectionIds.add(secKey);

      sec.classList.add('section-revealed', 'is-revealed');

      if (typeof gsap === 'undefined') {
        // Fallback if GSAP is unavailable
        sec.querySelectorAll('.reveal-item, .reveal-fade, .reveal-scale').forEach(el => {
          el.style.opacity = '1';
          el.style.transform = 'translateY(0) scale(1)';
        });
        return;
      }

      let animItems = Array.from(sec.querySelectorAll('.reveal-item, .reveal-fade, .reveal-scale'));
      if (animItems.length) {
        gsap.fromTo(animItems, 
          {
            y: -30,
            opacity: 0,
            force3D: true
          },
          {
            y: 0,
            opacity: 1,
            duration: 0.75,
            ease: 'power2.out',
            stagger: 0.08,
            clearProps: 'transform,opacity'
          }
        );
      }
    }

    function initAboutSectionTriggers() {
      document.documentElement.classList.add('reveal-ready');

      if (typeof gsap !== 'undefined') {
        document.documentElement.classList.add('gsap-active');
        if (typeof ScrollTrigger !== 'undefined') {
          gsap.registerPlugin(ScrollTrigger);

          aboutSections.forEach(sec => {
            if (sec.id === 'home-hero') {
              animateSectionDown(sec);
            } else {
              ScrollTrigger.create({
                trigger: sec,
                start: 'top 88%',
                once: true,
                onEnter: () => animateSectionDown(sec)
              });
            }
          });
          return;
        }
      }

      // IntersectionObserver Fallback
      const revealObserver = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            animateSectionDown(entry.target);
            obs.unobserve(entry.target);
          }
        });
      }, { threshold: 0.02, rootMargin: '0px 0px 40px 0px' });

      aboutSections.forEach(sec => {
        if (sec.id === 'home-hero') {
          animateSectionDown(sec);
        } else {
          revealObserver.observe(sec);
        }
      });
    }

    if (typeof gsap !== 'undefined') {
      initAboutSectionTriggers();
    } else {
      window.addEventListener('load', initAboutSectionTriggers);
      setTimeout(initAboutSectionTriggers, 200);
    }

    // Safety fallback: ensure all content becomes visible even if triggers stall
    setTimeout(() => {
      aboutSections.forEach(sec => animateSectionDown(sec));
    }, 2000);

    const heroSec = document.getElementById('home-hero');

    // 1. Hero 3-Frame Intro Controller (Desktop-3 -> Desktop-2 -> Desktop-1 trong ~3s)
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
        scrollIndicatorBtn.addEventListener('click', function(e) {
          handleUserEarlyInteract();
          const target = document.querySelector('#about-overview');
          if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
          }
        });
      }
    }

    // 2. Partners Swiper (Đối tác thi công)
    const partnersEl = document.querySelector('.home-partners-swiper');
    if (partnersEl && !partnersEl.swiper && typeof Swiper !== 'undefined') {
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
        breakpoints: {
          400: { slidesPerView: 3, spaceBetween: 20 },
          640: { slidesPerView: 4, spaceBetween: 24 },
          768: { slidesPerView: 5, spaceBetween: 28 },
          1024: { slidesPerView: 6, spaceBetween: 32 },
          1280: { slidesPerView: 7, spaceBetween: 36 },
        }
      });
    }
  });
</script>
@endpush
