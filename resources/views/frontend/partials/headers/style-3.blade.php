{{-- Header Style 3: Minimal Search Header (Khung Tìm Kiếm Rộng Trung Tâm) - Logo High Visibility 150px --}}
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
    $inputFontSize = round(12.5 * $scale, 1);
@endphp

<header class="{{ $headerSticky ? 'sticky top-0 z-50 shadow-md' : 'relative z-50 shadow-sm' }}" style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
    <div class="container mx-auto px-4 py-2">
        <div class="flex items-center justify-between gap-4">
            
            {{-- Left: Logo --}}
            <div class="flex-shrink-0 flex items-center gap-3 py-1">
                @if($logo)
                    <a href="/{{ $projectCode }}" class="inline-block">
                        <img src="{{ $logo }}" alt="{{ $siteName }}" class="w-auto object-contain transition-all duration-200" style="height: 150px; max-height: 150px;">
                    </a>
                @else
                    <a href="/{{ $projectCode }}" class="font-extrabold tracking-tight" style="color: {{ $headerText }}; font-size: {{ $siteNameFontSize }}px;">{{ $siteName }}</a>
                @endif
            </div>

            {{-- Center: Expanded Search Bar --}}
            @if($showSearch)
            <div class="flex-1 max-w-xl hidden md:block">
                <form action="/{{ $projectCode }}/search" method="GET" class="relative">
                    <input type="text" name="q" placeholder="Tìm kiếm sản phẩm, bài viết..." 
                           class="w-full pl-9 pr-4 py-1.5 bg-gray-100 hover:bg-gray-100/90 border border-gray-200 focus:border-blue-600 focus:bg-white rounded-xl font-medium transition duration-200 focus:outline-none text-xs"
                           style="font-size: {{ $inputFontSize }}px;">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg style="width: {{ round(15 * $scale, 1) }}px; height: {{ round(15 * $scale, 1) }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </form>
            </div>
            @endif

            {{-- Right: Actions & Nav --}}
            <div class="flex items-center gap-4">
                {{-- Desktop Nav Menu Items --}}
                @if($navMenu?->items)
                <div class="hidden lg:block">
                    @include('frontend.partials.navigation.desktop-menu', ['navMenu' => $navMenu, 'headerText' => $headerText, 'headerHeight' => $headerHeight])
                </div>
                @endif

                @if($showCart)
                <a href="/{{ $projectCode }}/cart" class="bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-full transition relative flex items-center justify-center" 
                   style="padding: {{ $btnPadding }}px;" title="Giỏ hàng">
                    <div class="relative">
                        <svg style="width: {{ $iconSize }}px; height: {{ $iconSize }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] w-3.5 h-3.5 rounded-full flex items-center justify-center font-bold cart-count">0</span>
                    </div>
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
