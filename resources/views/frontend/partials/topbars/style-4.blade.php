{{-- Top Bar Style 4: Center - Tên website + Search icon --}}
@php
    $siteName = setting('site_name', config('app.name'));
    $projectCode = request()->route('projectCode');
@endphp

<div class="text-sm" style="background-color: {{ $topbarBg }}; color: {{ $topbarText }};">
    <div class="container mx-auto px-4">
        <div class="flex justify-center items-center py-2">
            {{-- Center: Site Name + Search --}}
            <div class="flex items-center gap-3">
                <span class="font-semibold text-base sm:text-lg">{{ $siteName }}</span>
                <button type="button" 
                        onclick="document.getElementById('topbar-search-modal-4').classList.remove('hidden')"
                        class="p-1 hover:opacity-80 transition" 
                        title="Tìm kiếm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Search Modal --}}
<div id="topbar-search-modal-4" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-start justify-center min-h-screen pt-20 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('topbar-search-modal-4').classList.add('hidden')"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="/{{ $projectCode }}/search" method="GET" class="p-4">
                <div class="flex items-center gap-2">
                    <input type="text" name="q" placeholder="Tìm kiếm..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" autofocus>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <button type="button" onclick="document.getElementById('topbar-search-modal-4').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Đóng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
