<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" prefix="og: https://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    {{-- SEO Meta Tags --}}
    <x-seo-meta 
        :title="$seoTitle ?? null"
        :description="$seoDescription ?? null"
        :keywords="$seoKeywords ?? null"
        :image="$seoImage ?? null"
        :url="$seoUrl ?? null"
        :breadcrumbs="$breadcrumbs ?? []"
    />
    
    <!-- Tailwind CSS Framework CDN & Config -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fdf2f2',
                            100: '#fde8e8',
                            500: '#98191f',
                            600: '#801318',
                            700: '#680e12',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js Collapse Plugin & Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-50">
    {{-- Custom Body Code --}}
    @if(setting_string('custom_body_code'))
        {!! setting_string('custom_body_code') !!}
    @endif
    
    <!-- Header -->
    @php
        $activeTheme = setting('theme');
    @endphp
    @if($activeTheme && view()->exists("frontend.themes.{$activeTheme}.partials.header"))
        @include("frontend.themes.{$activeTheme}.partials.header")
    @elseif($activeTheme && view()->exists("frontend.themes.{$activeTheme}.layouts.partials.header"))
        @include("frontend.themes.{$activeTheme}.layouts.partials.header")
    @else
        @include('frontend.partials.header')
    @endif
    
    <!-- Main Content -->
    <main role="main" id="main-content">
        @yield('content')
    </main>
    
    <!-- Footer -->
    @if($activeTheme && view()->exists("frontend.themes.{$activeTheme}.partials.footer"))
        @include("frontend.themes.{$activeTheme}.partials.footer")
    @elseif($activeTheme && view()->exists("frontend.themes.{$activeTheme}.layouts.partials.footer"))
        @include("frontend.themes.{$activeTheme}.layouts.partials.footer")
    @else
        @include('frontend.partials.footer')
    @endif
    
    <!-- Image Protection (Watermark) -->
    @include('frontend.partials.image-protection')
    
    <!-- Popup -->
    @include('frontend.partials.popup')
    
    <!-- Fake Notifications -->
    @include('frontend.partials.fake-notifications')
    
    <!-- Floating Cart Widget -->
    @include('frontend.partials.floating-cart')
    
    <!-- Floating Contact Buttons -->
    @include('frontend.partials.contact-buttons')
    
    @stack('scripts')
    
    {{-- Custom Footer Code --}}
    @if(setting_string('custom_footer_code'))
        {!! setting_string('custom_footer_code') !!}
    @endif
</body>
</html>
