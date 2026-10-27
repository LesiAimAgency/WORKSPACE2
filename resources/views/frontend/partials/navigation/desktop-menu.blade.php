{{-- Desktop Navigation Menu - Hỗ trợ 4 kiểu dáng từ nav_style setting --}}
@php
    $headerHeight = (int) ($headerHeight ?? 60);
    if ($headerHeight <= 0) $headerHeight = 60;

    $scale = max(0.65, min(1.2, $headerHeight / 60));
    $menuFontSize = round(13 * $scale, 1);
    $menuIconSize = round(12 * $scale, 1);
    $menuGap = round(20 * $scale, 1);
    $submenuFontSize = round(12 * $scale, 1);

    // Đọc nav_style từ setting
    $navStyle = setting_string('nav_style', 'horizontal');
    $activeColor = $headerText ?? '#000000';
@endphp

@if($navMenu?->items)

    {{-- =========================================== --}}
    {{-- 1. HORIZONTAL - Thanh Ngang Tiêu Chuẩn     --}}
    {{-- =========================================== --}}
    @if($navStyle === 'horizontal' || !in_array($navStyle, ['mega','pills','underline_glow']))
    <nav class="hidden lg:flex items-center" style="gap: {{ $menuGap }}px;">
        @foreach($navMenu->items as $item)
            @if($item->children && $item->children->count() > 0)
                <div class="relative group">
                    <a href="{{ $item->url }}" 
                       class="font-medium tracking-normal hover:text-blue-600 transition flex items-center gap-1 py-1" 
                       style="color: {{ $activeColor }}; font-size: {{ $menuFontSize }}px;">
                        {{ $item->title }}
                        <svg class="transition-transform group-hover:rotate-180 opacity-60" 
                             style="width: {{ $menuIconSize }}px; height: {{ $menuIconSize }}px;" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                    <div class="absolute left-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="bg-white shadow-xl rounded-xl border border-gray-100 min-w-[180px] py-1.5">
                            @foreach($item->children as $child)
                                @if($child->children && $child->children->count() > 0)
                                    <div class="relative group/sub">
                                        <a href="{{ $child->url }}" class="flex items-center justify-between px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition font-medium"
                                           style="font-size: {{ $submenuFontSize }}px;">
                                            {{ $child->title }}
                                            <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                        <div class="absolute left-full top-0 pl-1 opacity-0 invisible group-hover/sub:opacity-100 group-hover/sub:visible transition-all duration-200">
                                            <div class="bg-white shadow-xl rounded-xl border border-gray-100 min-w-[160px] py-1.5">
                                                @foreach($child->children as $subChild)
                                                    <a href="{{ $subChild->url }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition font-medium"
                                                       style="font-size: {{ $submenuFontSize }}px;">{{ $subChild->title }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $child->url }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition font-medium"
                                       style="font-size: {{ $submenuFontSize }}px;">{{ $child->title }}</a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ $item->url }}" 
                   class="font-medium tracking-normal hover:text-blue-600 transition py-1" 
                   style="color: {{ $activeColor }}; font-size: {{ $menuFontSize }}px;">{{ $item->title }}</a>
            @endif
        @endforeach
    </nav>

    {{-- =========================================== --}}
    {{-- 2. MEGA MENU - Full-Width Dropdown Grid     --}}
    {{-- =========================================== --}}
    @elseif($navStyle === 'mega')
    <nav class="hidden lg:flex items-center" style="gap: {{ $menuGap }}px;">
        @foreach($navMenu->items as $item)
            @if($item->children && $item->children->count() > 0)
                <div class="relative group">
                    <a href="{{ $item->url }}" 
                       class="font-semibold tracking-normal flex items-center gap-1 py-2 border-b-2 border-transparent hover:border-blue-500 hover:text-blue-600 transition"
                       style="color: {{ $activeColor }}; font-size: {{ $menuFontSize }}px;">
                        {{ $item->title }}
                        <svg class="transition-transform group-hover:rotate-180 opacity-60" 
                             style="width: {{ $menuIconSize }}px; height: {{ $menuIconSize }}px;" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                    {{-- Mega Dropdown Panel --}}
                    <div class="absolute left-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 w-max max-w-2xl">
                        <div class="bg-white shadow-2xl rounded-2xl border border-gray-100 p-4">
                            <div class="grid gap-2" style="grid-template-columns: repeat({{ min(4, ceil($item->children->count() / 3)) }}, minmax(140px, 1fr));">
                                @foreach($item->children as $child)
                                    <a href="{{ $child->url }}" 
                                       class="flex items-start gap-2 p-2.5 rounded-xl hover:bg-blue-50 group/card transition"
                                       style="font-size: {{ $submenuFontSize }}px;">
                                        <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                        </div>
                                        <span class="font-medium text-gray-800 group-hover/card:text-blue-600 transition leading-tight">{{ $child->title }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ $item->url }}" 
                   class="font-semibold tracking-normal py-2 border-b-2 border-transparent hover:border-blue-500 hover:text-blue-600 transition"
                   style="color: {{ $activeColor }}; font-size: {{ $menuFontSize }}px;">{{ $item->title }}</a>
            @endif
        @endforeach
    </nav>

    {{-- =========================================== --}}
    {{-- 3. PILLS - Thẻ Bo Tròn Hiện Đại            --}}
    {{-- =========================================== --}}
    @elseif($navStyle === 'pills')
    <nav class="hidden lg:flex items-center flex-wrap gap-1.5">
        @foreach($navMenu->items as $item)
            @if($item->children && $item->children->count() > 0)
                <div class="relative group">
                    <a href="{{ $item->url }}" 
                       class="inline-flex items-center gap-1 rounded-full font-semibold hover:bg-blue-600 hover:text-white transition-all duration-200"
                       style="color: {{ $activeColor }}; font-size: {{ $menuFontSize }}px; padding: {{ round(5*$scale,1) }}px {{ round(14*$scale,1) }}px; background: rgba(255,255,255,0.12);">
                        {{ $item->title }}
                        <svg class="transition-transform group-hover:rotate-180 opacity-70"
                             style="width: {{ $menuIconSize }}px; height: {{ $menuIconSize }}px;"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                    <div class="absolute left-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="bg-white shadow-xl rounded-2xl border border-gray-100 min-w-[180px] py-2 px-1.5">
                            @foreach($item->children as $child)
                                <a href="{{ $child->url }}" class="block px-3 py-1.5 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition font-medium"
                                   style="font-size: {{ $submenuFontSize }}px;">{{ $child->title }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ $item->url }}" 
                   class="inline-flex items-center rounded-full font-semibold hover:bg-blue-600 hover:text-white transition-all duration-200"
                   style="color: {{ $activeColor }}; font-size: {{ $menuFontSize }}px; padding: {{ round(5*$scale,1) }}px {{ round(14*$scale,1) }}px; background: rgba(255,255,255,0.12);">
                    {{ $item->title }}
                </a>
            @endif
        @endforeach
    </nav>

    {{-- =========================================== --}}
    {{-- 4. UNDERLINE GLOW - Gạch Dưới Phát Sáng   --}}
    {{-- =========================================== --}}
    @elseif($navStyle === 'underline_glow')
    <nav class="hidden lg:flex items-center" style="gap: {{ $menuGap }}px;">
        @foreach($navMenu->items as $item)
            @if($item->children && $item->children->count() > 0)
                <div class="relative group">
                    <a href="{{ $item->url }}" 
                       class="font-semibold tracking-wide flex items-center gap-1 py-2 relative after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-blue-400 after:transition-all after:duration-300 group-hover:after:w-full hover:text-blue-400 transition"
                       style="color: {{ $activeColor }}; font-size: {{ $menuFontSize }}px;">
                        {{ $item->title }}
                        <svg class="transition-transform group-hover:rotate-180 opacity-60"
                             style="width: {{ $menuIconSize }}px; height: {{ $menuIconSize }}px;"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                    <div class="absolute left-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="bg-white shadow-xl rounded-xl border border-gray-100 min-w-[180px] py-1.5">
                            @foreach($item->children as $child)
                                <a href="{{ $child->url }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition font-medium border-l-2 border-transparent hover:border-blue-400 ml-1"
                                   style="font-size: {{ $submenuFontSize }}px;">{{ $child->title }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ $item->url }}" 
                   class="font-semibold tracking-wide py-2 relative after:absolute after:bottom-0 after:left-0 after:w-0 after:h-0.5 after:bg-blue-400 after:transition-all after:duration-300 hover:after:w-full hover:text-blue-400 transition"
                   style="color: {{ $activeColor }}; font-size: {{ $menuFontSize }}px;">
                    {{ $item->title }}
                </a>
            @endif
        @endforeach
    </nav>
    @endif

@endif
