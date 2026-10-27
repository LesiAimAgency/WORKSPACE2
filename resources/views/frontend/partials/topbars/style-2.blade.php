{{-- Top Bar Style 2: Left - Thông tin liên hệ | Right - Đăng ký, Đăng nhập --}}
@php
    $phone = setting('phone', '');
    $email = setting('email', '');
    $address = setting('address', '');
    $projectCode = request()->route('projectCode');
@endphp

<div class="text-sm" style="background-color: {{ $topbarBg }}; color: {{ $topbarText }};">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-2">
            {{-- Left: Contact Info --}}
            <div class="flex items-center gap-4 text-sm">
                @if($phone)
                <a href="tel:{{ $phone }}" class="flex items-center gap-1 hover:opacity-80 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span class="hidden sm:inline">{{ $phone }}</span>
                </a>
                @endif
                @if($email)
                <a href="mailto:{{ $email }}" class="hidden md:flex items-center gap-1 hover:opacity-80 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>{{ $email }}</span>
                </a>
                @endif
                @if($address)
                <span class="hidden lg:flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="truncate max-w-xs">{{ $address }}</span>
                </span>
                @endif
            </div>
            
            {{-- Right: Login/Register --}}
            <div class="flex items-center gap-3 text-sm">
                @auth
                    <a href="/{{ $projectCode }}/account" class="flex items-center gap-1 hover:opacity-80 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Tài khoản</span>
                    </a>
                    <span class="text-gray-400">|</span>
                    <form action="/{{ $projectCode }}/logout" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="hover:opacity-80 transition">Đăng xuất</button>
                    </form>
                @else
                    <a href="/{{ $projectCode }}/login" class="flex items-center gap-1 hover:opacity-80 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Đăng nhập</span>
                    </a>
                    <span class="text-gray-400">|</span>
                    <a href="/{{ $projectCode }}/register" class="flex items-center gap-1 hover:opacity-80 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        <span>Đăng ký</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
