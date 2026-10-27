{{-- Tailwind CSS Mobile Menu Drawer & Overlay with Tone & Theme Preset Support --}}
@php
    $getSettingValue = function($key, $default = '') {
        return setting_string($key, $default);
    };

    $mobileMenuStyle = $getSettingValue('mobile_menu_style', 'fullscreen');
    $mobileThemePreset = $getSettingValue('mobile_theme_preset', 'light_clean');
    $mobileShowSearch = (bool) $getSettingValue('mobile_show_search', 1);
    $mobileShowCart = (bool) $getSettingValue('mobile_show_cart', 1);
    $mobileShowHotline = (bool) $getSettingValue('mobile_show_hotline', 1);
    $mobileShowSocial = (bool) $getSettingValue('mobile_show_social', 1);

    // Color Presets Map
    $presets = [
        'light_clean' => [
            'bg' => '#ffffff',
            'text' => '#0f172a',
            'card_bg' => '#f8fafc',
            'accent' => '#2563eb',
            'accent_bg' => '#eff6ff',
            'btn_bg' => '#2563eb',
            'btn_text' => '#ffffff',
            'border' => '#e2e8f0',
            'backdrop' => 'bg-gray-900/60'
        ],
        'slate_modern' => [
            'bg' => '#f8fafc',
            'text' => '#0f172a',
            'card_bg' => '#ffffff',
            'accent' => '#0284c7',
            'accent_bg' => '#f0f9ff',
            'btn_bg' => '#0284c7',
            'btn_text' => '#ffffff',
            'border' => '#e2e8f0',
            'backdrop' => 'bg-slate-900/70'
        ],
        'glassmorphism' => [
            'bg' => 'rgba(255, 255, 255, 0.88)',
            'text' => '#0f172a',
            'card_bg' => 'rgba(255, 255, 255, 0.65)',
            'accent' => '#4f46e5',
            'accent_bg' => 'rgba(238, 242, 255, 0.8)',
            'btn_bg' => '#4f46e5',
            'btn_text' => '#ffffff',
            'border' => 'rgba(226, 232, 240, 0.6)',
            'backdrop' => 'bg-slate-900/40 backdrop-blur-md'
        ],
        'amber_warm' => [
            'bg' => '#fffbeb',
            'text' => '#78350f',
            'card_bg' => '#ffffff',
            'accent' => '#b45309',
            'accent_bg' => '#fef3c7',
            'btn_bg' => '#b45309',
            'btn_text' => '#ffffff',
            'border' => '#fde68a',
            'backdrop' => 'bg-amber-950/60'
        ],
    ];

    $preset = $presets[$mobileThemePreset] ?? $presets['light_clean'];

    // Custom colors override background/text if explicitly configured
    $customBg = $getSettingValue('mobile_bg_color', '');
    $customText = $getSettingValue('mobile_text_color', '');

    $mobileBgColor = !empty($customBg) ? $customBg : $preset['bg'];
    $mobileTextColor = !empty($customText) ? $customText : $preset['text'];
    $cardBg = $preset['card_bg'];
    $accentColor = $preset['accent'];
    $accentBg = $preset['accent_bg'];
    $btnBg = $preset['btn_bg'];
    $btnText = $preset['btn_text'];
    $borderColor = $preset['border'];
    $backdropClass = $preset['backdrop'];

    $siteLogo = $getSettingValue('site_logo', '');
    $siteName = $getSettingValue('site_name', 'Website');
    $hotline = $getSettingValue('hotline', '1900 1234');
    $projectCode = request()->route('projectCode') ?? request()->segment(1);
    $isProject = $projectCode && $projectCode !== 'cms';
    $homeUrl = $isProject ? "/{$projectCode}" : "/";
@endphp

<!-- Mobile Menu Drawer Wrapper -->
<!-- DEBUG: mobile_menu_style={{ $mobileMenuStyle }} | mobile_theme_preset={{ $mobileThemePreset }} | bg={{ $mobileBgColor }} | text={{ $mobileTextColor }} -->
<div x-data="{ open: false, activeSub: null }" 
     @toggle-mobile-menu.window="open = !open" 
     @keydown.escape.window="open = false" 
     class="relative">

    <!-- 1. FULLSCREEN OVERLAY STYLE -->
    @if($mobileMenuStyle === 'fullscreen')
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[100] flex flex-col justify-between overflow-y-auto backdrop-blur-md"
         style="background-color: {{ $mobileBgColor }}; color: {{ $mobileTextColor }}; display: none;">
        
        <!-- Header Bar -->
        <div class="flex items-center justify-between px-6 py-4 border-b sticky top-0 z-10" style="border-color: {{ $borderColor }}; background-color: {{ $cardBg }};">
            <div class="flex items-center gap-3">
                @if($siteLogo)
                    <a href="{{ $homeUrl }}"><img src="{{ $siteLogo }}" alt="{{ $siteName }}" class="h-9 w-auto object-contain"></a>
                @else
                    <a href="{{ $homeUrl }}" class="font-bold text-xl tracking-tight" style="color: {{ $mobileTextColor }};">{{ $siteName }}</a>
                @endif
            </div>
            
            <button type="button" @click="open = false" class="p-2 rounded-full transition-all focus:outline-none" style="color: {{ $mobileTextColor }}; background-color: {{ $accentBg }};">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body Content -->
        <div class="px-6 py-6 space-y-6 flex-1">
            
            <!-- Mobile Search Bar -->
            @if($mobileShowSearch)
            <form action="{{ $homeUrl }}/search" method="GET" class="relative">
                <input type="text" name="q" placeholder="Tìm kiếm sản phẩm, bài viết..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl text-xs transition-all focus:outline-none border"
                       style="background-color: {{ $cardBg }}; color: {{ $mobileTextColor }}; border-color: {{ $borderColor }};">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </form>
            @endif

            <!-- Navigation Links -->
            @if($navMenu?->items)
            <nav class="space-y-1">
                @foreach($navMenu->items as $item)
                    @if($item->children && $item->children->count() > 0)
                        <div x-data="{ expanded: false }" class="border-b py-1" style="border-color: {{ $borderColor }};">
                            <button @click="expanded = !expanded" type="button" class="w-full flex items-center justify-between py-2.5 text-sm font-semibold transition" style="color: {{ $mobileTextColor }};">
                                <span>{{ $item->title }}</span>
                                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 transition-transform duration-200 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="expanded" x-collapse class="pl-4 pb-2 space-y-1">
                                @foreach($item->children as $child)
                                    @if($child->children && $child->children->count() > 0)
                                        <div x-data="{ subExpanded: false }">
                                            <button @click="subExpanded = !subExpanded" type="button" class="w-full flex items-center justify-between py-1.5 text-xs font-medium opacity-80">
                                                <span>{{ $child->title }}</span>
                                                <svg :class="{'rotate-180': subExpanded}" class="w-3.5 h-3.5 transition-transform opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                            <div x-show="subExpanded" x-collapse class="pl-3 space-y-1">
                                                @foreach($child->children as $subChild)
                                                    <a href="{{ $subChild->url }}" class="block py-1.5 text-[11px] opacity-70 hover:opacity-100">{{ $subChild->title }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ $child->url }}" class="block py-1.5 text-xs opacity-80 hover:opacity-100 font-medium">{{ $child->title }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="border-b py-1" style="border-color: {{ $borderColor }};">
                            <a href="{{ $item->url }}" class="block py-2.5 text-sm font-semibold transition" style="color: {{ $mobileTextColor }};">{{ $item->title }}</a>
                        </div>
                    @endif
                @endforeach
            </nav>
            @endif

        </div>

        <!-- Footer Actions Bar -->
        <div class="p-5 border-t space-y-3" style="background-color: {{ $cardBg }}; border-color: {{ $borderColor }};">
            @if($mobileShowHotline && $hotline)
            <a href="tel:{{ preg_replace('/[^0-9]/', '', $hotline) }}" 
               class="w-full py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow-sm transition-all text-xs"
               style="background-color: {{ $btnBg }}; color: {{ $btnText }};">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span>Hotline: {{ $hotline }}</span>
            </a>
            @endif

            <div class="flex items-center justify-around pt-1 text-xs font-medium opacity-80">
                @if($mobileShowCart)
                <a href="{{ $homeUrl }}/cart" class="flex flex-col items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Giỏ hàng</span>
                </a>
                @endif
                <a href="{{ $homeUrl }}/contact" class="flex flex-col items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Liên hệ</span>
                </a>
            </div>
        </div>

    </div>

    <!-- 2. SIDEBAR SLIDE-OVER DRAWER STYLE -->
    @elseif($mobileMenuStyle === 'sidebar')
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false" 
         class="fixed inset-0 z-[99] {{ $backdropClass }}" 
         style="display: none;"></div>

    <!-- Slide-over Drawer -->
    <div x-show="open" 
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 z-[100] w-full max-w-xs shadow-2xl flex flex-col justify-between overflow-y-auto"
         style="background-color: {{ $mobileBgColor }}; color: {{ $mobileTextColor }}; display: none;">

        <div>
            <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color: {{ $borderColor }}; background-color: {{ $cardBg }};">
                <span class="font-bold text-sm">Danh Mục Menu</span>
                <button type="button" @click="open = false" class="p-1.5 rounded-full transition opacity-70 hover:opacity-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-5 space-y-4">
                @if($mobileShowSearch)
                <form action="{{ $homeUrl }}/search" method="GET" class="relative">
                    <input type="text" name="q" placeholder="Tìm kiếm..." 
                           class="w-full pl-9 pr-3 py-2 rounded-xl text-xs border focus:outline-none"
                           style="background-color: {{ $cardBg }}; color: {{ $mobileTextColor }}; border-color: {{ $borderColor }};">
                    <svg class="w-4 h-4 opacity-50 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </form>
                @endif

                @if($navMenu?->items)
                <nav class="space-y-1 text-xs">
                    @foreach($navMenu->items as $item)
                        @if($item->children && $item->children->count() > 0)
                            <div x-data="{ expanded: false }">
                                <button @click="expanded = !expanded" type="button" class="w-full flex items-center justify-between py-2 font-semibold transition" style="color: {{ $mobileTextColor }};">
                                    <span>{{ $item->title }}</span>
                                    <svg :class="{'rotate-180': expanded}" class="w-3.5 h-3.5 transition-transform opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="expanded" x-collapse class="pl-3 border-l space-y-1" style="border-color: {{ $accentColor }};">
                                    @foreach($item->children as $child)
                                        <a href="{{ $child->url }}" class="block py-1.5 text-[11px] opacity-80 hover:opacity-100">{{ $child->title }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->url }}" class="block py-2 font-semibold transition" style="color: {{ $mobileTextColor }};">{{ $item->title }}</a>
                        @endif
                    @endforeach
                </nav>
                @endif
            </div>
        </div>

        @if($mobileShowHotline && $hotline)
        <div class="p-4 border-t" style="border-color: {{ $borderColor }}; background-color: {{ $cardBg }};">
            <a href="tel:{{ preg_replace('/[^0-9]/', '', $hotline) }}" 
               class="w-full py-2.5 rounded-xl font-bold text-xs flex items-center justify-center gap-2"
               style="background-color: {{ $btnBg }}; color: {{ $btnText }};">
                <span>Hotline: {{ $hotline }}</span>
            </a>
        </div>
        @endif

    </div>

    <!-- 3. ACCORDION TOP DROPDOWN STYLE -->
    @elseif($mobileMenuStyle === 'top_dropdown')
    <div x-show="open" 
         x-collapse 
         class="w-full border-t shadow-xl px-4 py-4 space-y-3"
         style="background-color: {{ $mobileBgColor }}; color: {{ $mobileTextColor }}; border-color: {{ $borderColor }}; display: none;">
        @if($mobileShowSearch)
        <form action="{{ $homeUrl }}/search" method="GET" class="relative">
            <input type="text" name="q" placeholder="Tìm kiếm..." 
                   class="w-full px-4 py-2 border rounded-xl text-xs focus:outline-none"
                   style="background-color: {{ $cardBg }}; color: {{ $mobileTextColor }}; border-color: {{ $borderColor }};">
        </form>
        @endif

        @if($navMenu?->items)
        <nav class="space-y-1">
            @foreach($navMenu->items as $item)
                @if($item->children && $item->children->count() > 0)
                    <div x-data="{ expanded: false }">
                        <button @click="expanded = !expanded" type="button" class="w-full flex items-center justify-between py-2 text-xs font-semibold" style="color: {{ $mobileTextColor }};">
                            <span>{{ $item->title }}</span>
                            <svg :class="{'rotate-180': expanded}" class="w-3.5 h-3.5 transition-transform opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="expanded" x-collapse class="pl-4 space-y-1">
                            @foreach($item->children as $child)
                                <a href="{{ $child->url }}" class="block py-1.5 text-[11px] opacity-80 hover:opacity-100">{{ $child->title }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item->url }}" class="block py-2 text-xs font-semibold" style="color: {{ $mobileTextColor }};">{{ $item->title }}</a>
                @endif
            @endforeach
        </nav>
        @endif
    </div>

    <!-- 4. BOTTOM SHEET DRAWER STYLE -->
    @else
    <div x-show="open" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false" 
         class="fixed inset-0 z-[99] {{ $backdropClass }}" 
         style="display: none;"></div>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0"
         x-transition:leave-end="translate-y-full"
         class="fixed inset-x-0 bottom-0 z-[100] max-h-[85vh] rounded-t-3xl shadow-2xl flex flex-col justify-between overflow-y-auto"
         style="background-color: {{ $mobileBgColor }}; color: {{ $mobileTextColor }}; display: none;">

        <div class="px-6 pt-3 pb-6">
            <!-- Drag Handle Bar -->
            <div class="w-12 h-1.5 rounded-full mx-auto mb-4 cursor-pointer opacity-40" style="background-color: {{ $mobileTextColor }};" @click="open = false"></div>

            <div class="flex items-center justify-between border-b pb-3 mb-4" style="border-color: {{ $borderColor }};">
                <span class="font-bold text-sm">Menu Điều Hướng</span>
                <button type="button" @click="open = false" class="p-1.5 rounded-full opacity-70 hover:opacity-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if($mobileShowSearch)
            <form action="{{ $homeUrl }}/search" method="GET" class="relative mb-4">
                <input type="text" name="q" placeholder="Tìm kiếm nhanh..." 
                       class="w-full pl-9 pr-3 py-2 rounded-xl text-xs border focus:outline-none"
                       style="background-color: {{ $cardBg }}; color: {{ $mobileTextColor }}; border-color: {{ $borderColor }};">
                <svg class="w-4 h-4 opacity-50 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
            @endif

            @if($navMenu?->items)
            <nav class="space-y-1 text-xs">
                @foreach($navMenu->items as $item)
                    @if($item->children && $item->children->count() > 0)
                        <div x-data="{ expanded: false }">
                            <button @click="expanded = !expanded" type="button" class="w-full flex items-center justify-between py-2 font-semibold" style="color: {{ $mobileTextColor }};">
                                <span>{{ $item->title }}</span>
                                <svg :class="{'rotate-180': expanded}" class="w-3.5 h-3.5 transition-transform opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="expanded" x-collapse class="pl-3 border-l space-y-1" style="border-color: {{ $accentColor }};">
                                @foreach($item->children as $child)
                                    <a href="{{ $child->url }}" class="block py-1.5 text-[11px] opacity-80 hover:opacity-100">{{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->url }}" class="block py-2 font-semibold" style="color: {{ $mobileTextColor }};">{{ $item->title }}</a>
                    @endif
                @endforeach
            </nav>
            @endif
        </div>

        @if($mobileShowHotline && $hotline)
        <div class="p-4 border-t" style="border-color: {{ $borderColor }}; background-color: {{ $cardBg }};">
            <a href="tel:{{ preg_replace('/[^0-9]/', '', $hotline) }}" 
               class="w-full py-2.5 rounded-xl font-bold text-xs flex items-center justify-center gap-2"
               style="background-color: {{ $btnBg }}; color: {{ $btnText }};">
                <span>Hotline: {{ $hotline }}</span>
            </a>
        </div>
        @endif
    </div>
    @endif

</div>
