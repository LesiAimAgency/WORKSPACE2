{{-- Header Style 1: Modern Light Header (Logo Trái, Menu Giữa, Actions Phải) - Logo High Visibility 150px --}}
@php
    $showSearch = $showSearch ?? true;
    $showCart = $showCart ?? true;
    $showAccount = $showAccount ?? true;
    $headerHeight = (int) ($headerHeight ?? 60);
    if ($headerHeight <= 0) $headerHeight = 60;

    $scale = max(0.65, min(1.2, $headerHeight / 60));
    $logoMaxHeight = 150; // Set to 150px for max logo visibility as requested
    $iconSize = round(16 * $scale, 1);
    $btnPadding = round(6 * $scale, 1);
    $siteNameFontSize = round(22 * $scale, 1);
@endphp

<header class="{{ $headerSticky ? 'sticky top-0 z-50 shadow-md' : 'relative z-50 shadow-sm' }}" style="background-color: {{ $headerBg }}; color: {{ $headerText }};">
    <div class="container mx-auto px-4 py-2">
        <div class="flex justify-between items-center gap-4">
            
            {{-- Logo --}}
            <div class="flex-shrink-0 flex items-center py-1">
                @if($logo)
                    <a href="/{{ $projectCode }}" class="flex items-center">
                        <img src="{{ $logo }}" alt="{{ $siteName }}" class="w-auto object-contain transition-all duration-200" style="height: 150px; max-height: 150px;">
                    </a>
                @else
                    <a href="/{{ $projectCode }}" class="font-extrabold tracking-tight" style="color: {{ $headerText }}; font-size: {{ $siteNameFontSize }}px;">{{ $siteName }}</a>
                @endif
            </div>
            
            {{-- Navigation Menu --}}
            @if($navMenu?->items)
                @include('frontend.partials.navigation.desktop-menu', ['navMenu' => $navMenu, 'headerText' => $headerText, 'headerHeight' => $headerHeight])
            @endif
            
            {{-- Right Actions --}}
            <div class="flex items-center gap-1.5">
                {{-- Search --}}
                @if($showSearch)
                <button type="button" onclick="toggleSearchModal()" class="hover:bg-gray-100/80 rounded-full transition flex items-center justify-center" 
                        style="padding: {{ $btnPadding }}px;" title="Tìm kiếm">
                    <svg style="width: {{ $iconSize }}px; height: {{ $iconSize }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                @endif
                
                {{-- Account --}}
                @if($showAccount)
                <a href="/{{ $projectCode }}/account" class="hover:bg-gray-100/80 rounded-full transition flex items-center justify-center" 
                   style="padding: {{ $btnPadding }}px;" title="Tài khoản">
                    <svg style="width: {{ $iconSize }}px; height: {{ $iconSize }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
                @endif
                
                {{-- Cart --}}
                @if($showCart)
                <a href="/{{ $projectCode }}/cart" class="bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-full transition relative flex items-center justify-center" 
                   style="padding: {{ $btnPadding }}px;" title="Giỏ hàng">
                    <div class="relative">
                        <svg style="width: {{ $iconSize }}px; height: {{ $iconSize }}px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] w-3.5 h-3.5 rounded-full flex items-center justify-center font-bold cart-count">0</span>
                    </div>
                </a>
                @endif
                
                {{-- Mobile menu button --}}
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

{{-- Search Modal --}}
@if($showSearch)
<div id="search-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" onclick="toggleSearchModal()"></div>
    <div class="absolute top-0 left-0 right-0 bg-white shadow-lg p-4">
        <div class="container mx-auto max-w-xl">
            <form action="/{{ $projectCode }}/search" method="GET" class="flex gap-2">
                <input type="text" name="q" placeholder="Nhập từ khóa tìm kiếm..." 
                       class="flex-1 px-3.5 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 text-xs font-medium" autofocus>
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-semibold text-xs hover:bg-blue-700 transition">
                    Tìm kiếm
                </button>
                <button type="button" onclick="toggleSearchModal()" class="px-2 py-2 text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function toggleMobileMenu() {
    window.dispatchEvent(new CustomEvent('toggle-mobile-menu'));
}

function toggleSearchModal() {
    const modal = document.getElementById('search-modal');
    if (modal) {
        modal.classList.toggle('hidden');
        if (!modal.classList.contains('hidden')) {
            modal.querySelector('input[name="q"]')?.focus();
        }
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('search-modal');
        if (modal && !modal.classList.contains('hidden')) {
            toggleSearchModal();
        }
    }
});
</script>
