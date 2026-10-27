{{-- Tailwind UI Floating Cart Widget --}}
@php
    $floatingEnabled = (bool) setting_string('floating_cart_enabled', 1);
    $floatingPosition = setting_string('floating_cart_position', 'bottom-right');
    $projectCode = request()->route('projectCode') ?? request()->segment(1);
    $isProject = $projectCode && $projectCode !== 'cms';
    $cartUrl = $isProject ? "/{$projectCode}/cart" : "/cart";
    
    $posClasses = match($floatingPosition) {
        'bottom-left' => 'bottom-6 left-6',
        'top-right' => 'top-24 right-6',
        default => 'bottom-6 right-6',
    };
@endphp

@if($floatingEnabled)
<div class="fixed {{ $posClasses }} z-40">
    <a href="{{ $cartUrl }}" 
       class="flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3.5 rounded-full shadow-2xl hover:shadow-blue-500/50 transition-all transform hover:-translate-y-1 group border-2 border-white/20">
        <div class="relative">
            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-xs cart-count">0</span>
        </div>
        <span class="font-bold text-xs pr-1 hidden sm:inline tracking-wide">GIỎ HÀNG</span>
    </a>
</div>
@endif
