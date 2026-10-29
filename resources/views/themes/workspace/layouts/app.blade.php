<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', setting('site_title', 'Workspace Interior & Architecture'))</title>
    <meta name="description" content="@yield('meta_description', setting('meta_description', 'Workspace - Design & Build Solutions'))" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet" />
    
    <link rel="stylesheet" href="{{ asset('themes/workspace/css/style.css') }}?v={{ file_exists(public_path('themes/workspace/css/style.css')) ? filemtime(public_path('themes/workspace/css/style.css')) : '1.0' }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    

    <script type="importmap">
      {
        "imports": {
          "three": "https://cdn.jsdelivr.net/npm/three@0.178.0/build/three.module.js",
          "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.178.0/examples/jsm/"
        }
      }
    </script>

    <style>
      /* Smooth Entrance Animation Styles */
      .panel {
          transition: opacity 1.2s ease-out;
      }
      
      .panel [class*="animate__"] {
          opacity: 0;
          transition: opacity 0.8s ease-out;
      }
      
      .panel.is-active .animate__animated {
          opacity: 1;
      }
      
      :root {
          --animate-duration: 1.2s;
          --animate-delay: 0.2s;
      }
      
      .pdf-profile-link {
        position: fixed;
        bottom: clamp(24px, 5vh, 42px);
        left: var(--page-gutter, 32px);
        z-index: 50;
        display: flex;
        align-items: center;
        gap: 12px;
        font-family: var(--font-nav, inherit);
        font-size: 0.8125rem;
        font-weight: 500;
        letter-spacing: 0.5px;
        color: var(--color-text, #111111);
        line-height: 1;
        text-decoration: none;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(8px);
        transition: opacity 300ms ease, transform 300ms ease, visibility 300ms ease;
      }

      body.is-scrolled-past-home .pdf-profile-link {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
      }

      body.is-workflow-section .pdf-profile-link {
        color: #ffffff;
      }

      .pdf-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background-color: #eb2026;
        color: #ffffff;
        border-radius: 50%;
        transition: transform 260ms ease, background-color 260ms ease;
      }

      .pdf-profile-link:hover .pdf-icon {
        transform: translateY(-2px);
        background-color: #d11920;
      }

      @media (max-width: 900px) {
        .pdf-profile-link {
          font-size: 0.6875rem;
          bottom: clamp(20px, 4vh, 32px);
          gap: 8px;
        }
        .pdf-icon {
          width: 28px;
          height: 28px;
        }
        .pdf-icon svg {
          width: 12px;
          height: 12px;
        }
      }
    </style>
    
    <script src="{{ asset('themes/workspace/js/main.js') }}?v={{ file_exists(public_path('themes/workspace/js/main.js')) ? filemtime(public_path('themes/workspace/js/main.js')) : '1.0' }}" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
  </head>
  <body>
    <header class="site-header" aria-label="Dieu huong chinh">
      <button class="menu-trigger" type="button" aria-label="Mo menu" aria-controls="menuOverlay" aria-expanded="false">
        <span class="menu-icon" aria-hidden="true">
          <span class="menu-line"></span>
          <span class="menu-line"></span>
        </span>
        <span>MENU</span>
      </button>
      <a href="#connect" class="contact-link">CONTACT</a>
    </header>

    @if(setting('contact_pdf_link', setting('company_profile_pdf')))
    <a href="{{ setting('contact_pdf_link', setting('company_profile_pdf')) }}" target="_blank" class="pdf-profile-link">
      <span class="pdf-icon">
        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
          <polyline points="7 10 12 15 17 10"></polyline>
          <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
      </span>
      PDF PROFILE
    </a>
    @endif

    <main class="fullpage" id="fullpage">
      @yield('content')
    </main>

    <!-- Global Section Nav -->
    <nav class="service-mini-nav global-section-nav" aria-label="Section menu">
      <span class="mini-rule" aria-hidden="true"></span>
      <a href="#core-values" class="is-current" aria-current="page">Core Values</a>
      <a href="#service" aria-current="false">Our Services</a>
      <a href="#why-workspace" aria-current="false">Why Choose Us</a>
      <span class="mini-rule" aria-hidden="true"></span>
    </nav>

    <!-- Global Workflow Nav -->
    <nav class="workflow-mini-nav global-workflow-nav" aria-label="Workflow menu">
      <span class="mini-rule" aria-hidden="true"></span>
      <button type="button">What we do</button>
      <button type="button">Projects</button>
      <button type="button">About the expert</button>
      <span class="mini-rule" aria-hidden="true"></span>
    </nav>

    <aside class="menu-overlay" id="menuOverlay" aria-hidden="true">
      <div class="menu-backdrop" aria-hidden="true">

        <nav class="menu-panel" aria-label="Menu">
          <a href="#about-workspace" data-menu-section="1">ABOUT WORKSPACE</a>
          <a href="#core-values" data-menu-section="2">CORE VALUES</a>
          <a href="#service" data-menu-section="3">OUR SERVICES</a>
          <a href="#why-workspace" data-menu-section="4">WHY CHOOSE US</a>
          <a href="#connect" data-menu-section="5">CONTACT</a>
          @if(setting('contact_pdf_link', setting('company_profile_pdf')))
          <a href="{{ setting('contact_pdf_link', setting('company_profile_pdf')) }}" target="_blank" data-menu-section="6">COMPANY PROFILE</a>
          @endif
        </nav>
      
      @php
        $settings = $settings ?? [];
        $logo = !empty($settings['logo']) 
        ? (Str::startsWith($settings['logo'], ['http://', 'https://', '/']) ? $settings['logo'] : asset($settings['logo'])) 
        : asset('themes/workspace/assets/logo.png');
        $alt = $settings['alt'] ?? 'Workspace Interior design and build';
        $ariaLabel = $settings['aria_label'] ?? 'Workspace';
       
      @endphp
      <img class="brand-logo brand-logo-menu logo-sidebar"  src="{{ $logo }}" alt="{{ $alt }}" />

      </div>
      <button class="menu-close" type="button" aria-label="Dong menu"></button>
     
    </aside>

    <button class="scroll-button" type="button" aria-label="Chuyen xuong section tiep theo">
      <img src="{{ asset('themes/workspace/assets/Icons.png') }}" alt="" aria-hidden="true" />
    </button>
    <script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
    @stack('scripts')
  </body>
</html>
