{{-- Header Style 2: Centered Stacked Header (Logo Căn Giữa Tầng Trên, Menu bên dưới) - Logo High Visibility 150px --}}
@php
    $showSearch = $showSearch ?? true;
    $showCart = $showCart ?? true;
    $showAccount = $showAccount ?? true;
    $headerHeight = (int) ($headerHeight ?? 60);
    if ($headerHeight <= 0) $headerHeight = 60;

    $scale = max(0.65, min(1.2, $headerHeight / 60));
    $iconSize = round(16 * $scale, 1);
    $btnPadding = round(6 * $scale, 1);
    $siteNameFontSize = round(20 * $scale, 1);
    $inputFontSize = round(12 * $scale, 1);
@endphp

<header class="{{ $headerSticky ? 'sticky top-0 z-50 shadow-md' : 'relative z-50 shadow-sm' }}" style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
    {{-- Top Bar: Centered Logo + Side Actions --}}
    <div class="border-b border-gray-100">
        <div class="container mx-auto px-4 flex items-center justify-between gap-4 py-2">
            
            {{-- Left Side: Search Bar --}}
            <div class="w-1/3 hidden md:block">
                @if($showSearch)
                <form action="/{{ $projectCode }}/search" method="GET" class="relative max-w-xs">
                    <input type="text" name="q" placeholder="Tìm kiếm..." class="w-full px-3.5 py-1.5 pl-9 bg-gray-50 border border-gray-200 rounded-full focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 font-medium" style="font-size: {{ $inputFontSize }}px;">
                    <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600">
                        <svg style="width: {{ round(14 * $scale, 1) }}px; height: {{ round(14 * $scale, 1) }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </form>
                @endif
            </div>

            {{-- Center: Logo --}}
            <div class="flex flex-col items-center justify-center flex-1 py-1">
                @if($logo)
                    <a href="/{{ $projectCode }}" class="inline-block">
                        <img src="{{ $logo }}" alt="{{ $siteName }}" class="w-auto object-contain transition-all duration-200" style="height: 150px; max-height: 150px;">
                    </a>
                @else
                    <a href="/{{ $projectCode }}" class="font-black tracking-tight" style="color: {{ $headerText }}; font-size: {{ $siteNameFontSize }}px;">{{ $siteName }}</a>
                @endif
            </div>

            {{-- Right Side: Actions --}}
            <div class="w-1/3 flex items-center justify-end gap-2">
                @if($showAccount)
                <a href="/{{ $projectCode }}/account" class="text-gray-700 hover:text-blue-600 hover:bg-gray-100/80 rounded-full transition hidden sm:inline-flex items-center justify-center" 
                   style="padding: {{ $btnPadding }}px;" title="Tài khoản">
                    <svg style="width: {{ $iconSize }}px; height: {{ $iconSize }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
                @endif

                @if($showCart)
                <a href="/{{ $projectCode }}/cart" class="flex items-center gap-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-full transition font-extrabold shadow-xs"
                   style="padding: {{ round(5 * $scale, 1) }}px {{ round(12 * $scale, 1) }}px; font-size: {{ round(12 * $scale, 1) }}px;">
                    <div class="relative">
                        <svg style="width: {{ $iconSize }}px; height: {{ $iconSize }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
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

    {{-- Bottom Row: Dedicated Navigation Bar --}}
    @if($navMenu?->items)
    <div class="hidden lg:block shadow-xs border-t border-b border-gray-100/10" style="background-color: {{ $navBgColor ?? '#98191F' }}; color: {{ $navTextColor ?? '#ffffff' }};">
        <div class="container mx-auto px-4 flex justify-center py-1">
            @include('frontend.partials.navigation.desktop-menu', [
                'navMenu' => $navMenu, 
                'headerText' => $navTextColor ?? '#ffffff', 
                'headerHeight' => $headerHeight
            ])
        </div>
    </div>
    @endif

    {{-- Mobile Menu --}}
    @include('frontend.partials.navigation.mobile-menu', ['navMenu' => $navMenu, 'headerText' => $headerText])
</header>

<script>
function toggleMobileMenu() {
    window.dispatchEvent(new CustomEvent('toggle-mobile-menu'));
}
</script>
