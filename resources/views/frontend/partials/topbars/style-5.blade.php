{{-- Top Bar Style 5: Left - Địa chỉ, Email | Right - Phone number + icon --}}
@php
    $phone = setting('phone', '');
    $email = setting('email', '');
    $address = setting('address', '');
@endphp

<div class="text-sm" style="background-color: {{ $topbarBg }}; color: {{ $topbarText }};">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-2">
            {{-- Left: Address & Email --}}
            <div class="flex items-center gap-4 text-sm">
                @if($address)
                <span class="hidden lg:flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="truncate max-w-sm">{{ $address }}</span>
                </span>
                @endif
                @if($email)
                <a href="mailto:{{ $email }}" class="flex items-center gap-1 hover:opacity-80 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="hidden sm:inline">{{ $email }}</span>
                </a>
                @endif
            </div>
            
            {{-- Right: Phone with icon --}}
            <div class="flex items-center">
                @if($phone)
                <a href="tel:{{ $phone }}" class="flex items-center gap-2 hover:opacity-80 transition font-semibold">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <span>{{ $phone }}</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
