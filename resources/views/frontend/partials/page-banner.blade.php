@php
    $themeBanner = setting('theme_option_banner', []);
    $bannerHeight = $themeBanner['banner_height'] ?? 220;
    $bannerStyle = $themeBanner['banner_style'] ?? 'center';
    
    $type = $type ?? 'page';
    $bannerModeKey = "banner_{$type}";
    $widthMode = $themeBanner[$bannerModeKey] ?? 'container';
    
    $textAlignClass = match($bannerStyle) {
        'left' => 'text-left',
        'right' => 'text-right',
        default => 'text-center',
    };
    
    $itemsAlignClass = match($bannerStyle) {
        'left' => 'items-start',
        'right' => 'items-end',
        default => 'items-center',
    };
@endphp

@if($widthMode === 'full-width')
    {{-- Full Width Banner --}}
    <div class="w-full bg-gradient-to-r from-slate-900 via-gray-900 to-slate-800 text-white relative overflow-hidden mb-8"
         style="min-height: {{ $bannerHeight }}px;">
        <div class="absolute inset-0 bg-black/40"></div>
        @if(isset($bgImage) && $bgImage)
            <img src="{{ $bgImage }}" alt="{{ $title }}" class="absolute inset-0 w-full h-full object-cover mix-blend-overlay">
        @endif
        <div class="container mx-auto px-4 py-8 relative z-10 flex flex-col justify-center {{ $itemsAlignClass }}" style="min-height: {{ $bannerHeight }}px;">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-2 {{ $textAlignClass }}">{{ $title }}</h1>
            @if(isset($description) && $description)
                <p class="text-gray-200 text-sm max-w-2xl {{ $textAlignClass }}">{{ $description }}</p>
            @endif
        </div>
    </div>
@else
    {{-- Container Width Banner --}}
    <div class="container mx-auto px-4 pt-6 pb-2">
        <div class="bg-gradient-to-r from-slate-900 via-gray-900 to-slate-800 text-white rounded-2xl p-6 sm:p-8 relative overflow-hidden mb-8 shadow-lg"
             style="min-height: {{ $bannerHeight }}px;">
            <div class="absolute inset-0 bg-black/30"></div>
            @if(isset($bgImage) && $bgImage)
                <img src="{{ $bgImage }}" alt="{{ $title }}" class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-60">
            @endif
            <div class="relative z-10 flex flex-col justify-center {{ $itemsAlignClass }}" style="min-height: {{ $bannerHeight - 48 }}px;">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2 {{ $textAlignClass }}">{{ $title }}</h1>
                @if(isset($description) && $description)
                    <p class="text-gray-200 text-sm max-w-2xl {{ $textAlignClass }}">{{ $description }}</p>
                @endif
            </div>
        </div>
    </div>
@endif
