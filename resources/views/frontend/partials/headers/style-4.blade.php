{{-- Header Style 4: Glassmorphism Sticky Header (Khung Kính Mờ Sticky) - Respects ALL Website Config Settings --}}
@php
    $showSearch = $showSearch ?? true;
    $showCart = $showCart ?? true;
    $showAccount = $showAccount ?? true;
    $showHotlineBadge = $showHotlineBadge ?? true;
    $headerHeight = (int) ($headerHeight ?? 60);
    if ($headerHeight <= 0) $headerHeight = 60;

    $scale = max(0.65, min(1.2, $headerHeight / 60));
    $iconSize = round(16 * $scale, 1);
    $btnPadding = round(6 * $scale, 1);
    $siteNameFontSize = round(20 * $scale, 1);
    $actionFontSize = round(12 * $scale, 1);

    // Dynamic RGBA glass background color from headerBg setting
    $bgHex = ltrim($headerBg ?: '#ffffff', '#');
    if (strlen($bgHex) == 3) {
        $bgHex = $bgHex[0].$bgHex[0].$bgHex[1].$bgHex[1].$bgHex[2].$bgHex[2];
    }
    $r = hexdec(substr($bgHex, 0, 2));
    $g = hexdec(substr($bgHex, 2, 2));
    $b = hexdec(substr($bgHex, 4, 2));
    $headerBgRgba = "rgba({$r}, {$g}, {$b}, 0.85)";
@endphp

<header class="{{ $headerSticky ? 'sticky top-0 z-50' : 'relative z-50' }} backdrop-blur-md border-b border-gray-100/20 shadow-sm transition-all duration-300"
        style="background-color: {{ $headerBgRgba }}; color: {{ $headerText }};">
    <div class="container mx-auto px-4 py-2">
        <div class="flex items-center justify-between gap-4">
            
            {{-- Brand Logo --}}
            <div class="flex-shrink-0 flex items-center gap-3 py-1">
                @if($logo)
                    <a href="/{{ $projectCode }}" class="inline-block">
                        <img src="{{ $logo }}" alt="{{ $siteName }}" class="w-auto object-contain transition-all duration-200" style="height: 150px; max-height: 150px;">
                    </a>
                @else
                    <a href="/{{ $projectCode }}" class="font-extrabold tracking-tight" style="color: {{ $headerText }}; font-size: {{ $siteNameFontSize }}px;">{{ $siteName }}</a>
                @endif
            </div>

            {{-- Center Navigation --}}
            @if($navMenu?->items)
            <div class="hidden lg:block">
                @include('frontend.partials.navigation.desktop-menu', ['navMenu' => $navMenu, 'headerText' => $headerText, 'headerHeight' => $headerHeight])
            </div>
            @endif

            {{-- Right Actions --}}
            <div class="flex items-center gap-2">
                {{-- Hotline Badge --}}
                @if($showHotlineBadge && $hotline)
                <a href="tel:{{ $hotline }}" class="hidden sm:flex items-center gap-1.5 bg-white/20 hover:bg-white/30 rounded-full border border-current/20 font-semibold transition"
                   style="padding: {{ round(5 * $scale, 1) }}px {{ round(12 * $scale, 1) }}px; font-size: {{ $actionFontSize }}px; color: {{ $headerText }};">
                    <svg style="width: {{ round(14 * $scale, 1) }}px; height: {{ round(14 * $scale, 1) }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>{{ $hotline }}</span>
                </a>
                @endif

                {{-- Account Icon --}}
                @if($showAccount)
                <a href="/{{ $projectCode }}/account" class="hover:bg-white/20 rounded-full transition flex items-center justify-center" 
                   style="padding: {{ $btnPadding }}px; color: {{ $headerText }};" title="Tài khoản">
                    <svg style="width: {{ $iconSize }}px; height: {{ $iconSize }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
                @endif

                {{-- Search Icon --}}
                @if($showSearch)
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('toggle-mobile-menu'))" class="hover:bg-white/20 rounded-full transition flex items-center justify-center" 
                        style="padding: {{ $btnPadding }}px; color: {{ $headerText }};" title="Tìm kiếm">
                    <svg style="width: {{ $iconSize }}px; height: {{ $iconSize }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                @endif

                {{-- Cart Icon --}}
                @if($showCart)
                <a href="/{{ $projectCode }}/cart" class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-xs hover:shadow-sm transition font-bold"
                   style="padding: {{ round(5 * $scale, 1) }}px {{ round(14 * $scale, 1) }}px; font-size: {{ $actionFontSize }}px;">
                    <div class="relative">
                        <svg style="width: {{ round(15 * $scale, 1) }}px; height: {{ round(15 * $scale, 1) }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] w-3.5 h-3.5 rounded-full flex items-center justify-center cart-count font-bold">0</span>
                    </div>
                    <span class="hidden sm:inline">Giỏ hàng</span>
                </a>
                @endif

                <button type="button" class="lg:hidden flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-xl shadow-xs hover:shadow-md transition font-bold" onclick="toggleMobileMenu()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    @if(!empty($showMobileMenuText))
                        <span class="text-xs uppercase tracking-wider font-extrabold">{{ $mobileMenuBtnText ?? 'MENU' }}</span>
                    @endif
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile Menu --}}
    @include('frontend.partials.navigation.mobile-menu', ['navMenu' => $navMenu, 'headerText' => $headerText])
</header>

<script>
function toggleMobileMenu() {
    window.dispatchEvent(new CustomEvent('toggle-mobile-menu'));
}
</script>
