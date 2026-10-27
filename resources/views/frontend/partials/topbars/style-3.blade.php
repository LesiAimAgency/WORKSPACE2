{{-- Top Bar Style 3: Left - Thông tin liên hệ | Center - Tên website + Search icon --}}
@php
    $phone = setting('phone', '');
    $email = setting('email', '');
    $siteName = setting('site_name', config('app.name'));
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
            </div>
            
            {{-- Center: Site Name + Search --}}
            <div class="flex items-center gap-3">
                <span class="font-semibold text-base hidden sm:inline">{{ $siteName }}</span>
                <button type="button" 
                        onclick="document.getElementById('topbar-search-modal').classList.remove('hidden')"
                        class="p-1 hover:opacity-80 transition" 
                        title="Tìm kiếm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
            
            {{-- Right: Empty for balance --}}
            <div class="w-32 hidden md:block"></div>
        </div>
    </div>
</div>

{{-- Search Modal --}}
<div id="topbar-search-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-start justify-center min-h-screen pt-20 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('topbar-search-modal').classList.add('hidden')"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="/{{ $projectCode }}/search" method="GET" class="p-4">
                <div class="flex items-center gap-2">
                    <input type="text" name="q" placeholder="Tìm kiếm..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" autofocus>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <button type="button" onclick="document.getElementById('topbar-search-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Đóng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
