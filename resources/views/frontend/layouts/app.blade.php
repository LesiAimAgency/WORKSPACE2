{{-- 
    Alias layout file - extends master.blade.php
    Nhiều view đang sử dụng 'frontend.layouts.app' nên tạo file này để tương thích
--}}
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
    {{-- Google Fonts Injection --}}
    @php
        $headingFont = setting_string('heading_font', 'Inter');
        $bodyFont = setting_string('body_font', 'Inter');
        $headingWeights = setting_string('heading_font_weight', '400,700');
        $bodyWeights = setting_string('body_font_weight', '400,500,600,700');
        $bodyFontSize = setting_string('body_font_size', '1rem');

        // Build Google Fonts URL with saved weights
        $fontEntries = collect([
            ['font' => $headingFont, 'weights' => $headingWeights],
            ['font' => $bodyFont, 'weights' => $bodyWeights],
        ])->unique('font')->filter(fn($e) => !empty($e['font']))->map(function($e) {
            $family = str_replace(' ', '+', $e['font']);
            $wStr = str_replace(',', ';', $e['weights'] ?: '400;700');
            return "{$family}:wght@{$wStr}";
        })->join('&family=');
        $googleFontsUrl = $fontEntries ? "https://fonts.googleapis.com/css2?family={$fontEntries}&display=swap" : null;
    @endphp
    @if($googleFontsUrl)
        <link href="{{ $googleFontsUrl }}" rel="stylesheet">
    @endif
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


    @php
        // Theme & background từ website config
        $themeColor      = setting_string('theme_color', '#98191F');
        $bgType          = setting_string('bg_type', 'color');
        $bgColor         = setting_string('bg_color', '#f9fafb');
        $bgGradStart     = setting_string('bg_gradient_start', '#4F46E5');
        $bgGradEnd       = setting_string('bg_gradient_end', '#7C3AED');
        $bgGradDir       = setting_string('bg_gradient_direction', 'to right');
        $bgImage         = setting_string('bg_image', '');
        $bgImageSize     = setting_string('bg_image_size', 'cover');
        $bgImagePosition = setting_string('bg_image_position', 'center');
        $bgImageRepeat   = setting_string('bg_image_repeat', 'no-repeat');

        $bodyBgStyle = match($bgType) {
            'gradient' => "background: linear-gradient({$bgGradDir}, {$bgGradStart}, {$bgGradEnd});",
            'image'    => $bgImage
                ? "background-image: url('{$bgImage}'); background-size: {$bgImageSize}; background-position: {$bgImagePosition}; background-repeat: {$bgImageRepeat};"
                : "background-color: {$bgColor};",
            default    => "background-color: {$bgColor};",
        };
    @endphp

    <style>
    :root {
        --theme-color: {{ $themeColor }};
        --heading-font: '{{ $headingFont }}', sans-serif;
        --body-font: '{{ $bodyFont }}', sans-serif;
    }
    body { font-family: var(--body-font); }
    h1, h2, h3, h4, h5, h6 { font-family: var(--heading-font); }
</style>
    @stack('styles')

    {{-- Theme Assets (loaded only when shop widgets are present) --}}
    @if(isset($useThemeAssets) && $useThemeAssets)
    <link rel="stylesheet" href="{{ asset('theme/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/css/font-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/libs/swiper/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme/libs/flickity/flickity.min.css') }}">
    @endif
</head>
<body style="{{ $bodyBgStyle }}">

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
    
    {{-- Theme JS (loaded only when shop widgets are present) --}}
    @if(isset($useThemeAssets) && $useThemeAssets)
        <script src="{{ asset('theme/libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('theme/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('theme/libs/swiper/swiper-bundle.min.js') }}"></script>
        <script src="{{ asset('theme/libs/flickity/flickity.min.js') }}"></script>
        <script src="{{ asset('theme/libs/jquery-countdown/jquery.countdown.min.js') }}"></script>
        <script src="{{ asset('theme/js/product-slider.init.js') }}"></script>
    @endif

    {{-- Custom Footer Code --}}
    @if(setting_string('custom_footer_code'))
        {!! setting_string('custom_footer_code') !!}
    @endif
</body>
</html>
