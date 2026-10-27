@extends('cms.layouts.app')

@section('title', 'Cấu hình hệ thống')
@section('page-title', 'Cấu hình hệ thống')

@section('content')
<div class="space-y-6" x-data="{ 
    search: '', 
    activeTab: 'all',
    matches(box) {
        const matchesCategory = (this.activeTab === 'all' || box.category === this.activeTab);
        const query = this.search.toLowerCase().trim();
        const matchesSearch = !query || box.title.toLowerCase().includes(query) || (box.category_name && box.category_name.toLowerCase().includes(query));
        return matchesCategory && matchesSearch;
    }
}">

    <!-- Top Header Banner -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold uppercase tracking-wider mb-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    Trung Tâm Điều Khiển
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Cài Đặt &amp; Cấu Hình Hệ Thống
                </h1>
                <p class="text-slate-500 mt-1 text-sm sm:text-base">
                    Chọn một chức năng để thiết lập và tùy chỉnh chi tiết cho website của bạn.
                </p>
            </div>

            <!-- Live Search Bar -->
            <div class="w-full md:w-80 relative flex-shrink-0">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           x-model="search"
                           placeholder="Tìm nhanh cấu hình..." 
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                    <button type="button" 
                            x-show="search.length > 0" 
                            @click="search = ''" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                            style="display: none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Subtle Background Glow Decoration -->
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-gradient-to-br from-blue-100/40 to-indigo-100/40 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar text-sm">
        <button type="button" 
                @click="activeTab = 'all'" 
                :class="activeTab === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'"
                class="px-4 py-2 rounded-xl font-medium transition-all whitespace-nowrap">
            Tất cả ({{ count($boxes ?? []) }})
        </button>
        <button type="button" 
                @click="activeTab = 'general'" 
                :class="activeTab === 'general' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'"
                class="px-4 py-2 rounded-xl font-medium transition-all whitespace-nowrap">
            Cấu hình chung
        </button>
        <button type="button" 
                @click="activeTab = 'appearance'" 
                :class="activeTab === 'appearance' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'"
                class="px-4 py-2 rounded-xl font-medium transition-all whitespace-nowrap">
            Giao diện &amp; Hiển thị
        </button>
        <button type="button" 
                @click="activeTab = 'marketing'" 
                :class="activeTab === 'marketing' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'"
                class="px-4 py-2 rounded-xl font-medium transition-all whitespace-nowrap">
            Bán hàng &amp; Chuyển đổi
        </button>
        <button type="button" 
                @click="activeTab = 'system'" 
                :class="activeTab === 'system' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'"
                class="px-4 py-2 rounded-xl font-medium transition-all whitespace-nowrap">
            Hệ thống &amp; Kỹ thuật
        </button>
    </div>

    <!-- Boxes Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 sm:gap-5">
        @forelse($boxes as $box)
            @php
                $colorClasses = match($box['color'] ?? 'blue') {
                    'blue' => 'bg-blue-50 text-blue-600 border-blue-100 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600',
                    'orange' => 'bg-orange-50 text-orange-600 border-orange-100 group-hover:bg-orange-600 group-hover:text-white group-hover:border-orange-600',
                    'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600',
                    'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600',
                    'sky' => 'bg-sky-50 text-sky-600 border-sky-100 group-hover:bg-sky-600 group-hover:text-white group-hover:border-sky-600',
                    'teal' => 'bg-teal-50 text-teal-600 border-teal-100 group-hover:bg-teal-600 group-hover:text-white group-hover:border-teal-600',
                    'violet' => 'bg-violet-50 text-violet-600 border-violet-100 group-hover:bg-violet-600 group-hover:text-white group-hover:border-violet-600',
                    'rose' => 'bg-rose-50 text-rose-600 border-rose-100 group-hover:bg-rose-600 group-hover:text-white group-hover:border-rose-600',
                    'amber' => 'bg-amber-50 text-amber-600 border-amber-100 group-hover:bg-amber-600 group-hover:text-white group-hover:border-amber-600',
                    'slate' => 'bg-slate-100 text-slate-700 border-slate-200 group-hover:bg-slate-800 group-hover:text-white group-hover:border-slate-800',
                    'red' => 'bg-red-50 text-red-600 border-red-100 group-hover:bg-red-600 group-hover:text-white group-hover:border-red-600',
                    'yellow' => 'bg-amber-50 text-amber-600 border-amber-100 group-hover:bg-amber-600 group-hover:text-white group-hover:border-amber-600',
                    'gray' => 'bg-gray-100 text-gray-700 border-gray-200 group-hover:bg-gray-800 group-hover:text-white group-hover:border-gray-800',
                    'purple' => 'bg-purple-50 text-purple-600 border-purple-100 group-hover:bg-purple-600 group-hover:text-white group-hover:border-purple-600',
                    'fuchsia' => 'bg-fuchsia-50 text-fuchsia-600 border-fuchsia-100 group-hover:bg-fuchsia-600 group-hover:text-white group-hover:border-fuchsia-600',
                    'cyan' => 'bg-cyan-50 text-cyan-600 border-cyan-100 group-hover:bg-cyan-600 group-hover:text-white group-hover:border-cyan-600',
                    'pink' => 'bg-pink-50 text-pink-600 border-pink-100 group-hover:bg-pink-600 group-hover:text-white group-hover:border-pink-600',
                    default => 'bg-blue-50 text-blue-600 border-blue-100 group-hover:bg-blue-600 group-hover:text-white group-hover:border-blue-600'
                };
            @endphp
            
            <a href="{{ $box['url'] }}" 
               x-show="matches({{ json_encode($box) }})"
               class="group relative bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-blue-400 hover:-translate-y-1.5 transition-all duration-200 flex flex-col items-center justify-center text-center min-h-[160px] aspect-square">
                
                <!-- Icon Box -->
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-3.5 border transition-all duration-200 shadow-sm {{ $colorClasses }} group-hover:scale-110">
                    @if($box['icon'] === 'globe')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                    @elseif($box['icon'] === 'filter')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                    @elseif($box['icon'] === 'window')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    @elseif($box['icon'] === 'mail')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    @elseif($box['icon'] === 'phone')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    @elseif($box['icon'] === 'share')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                        </svg>
                    @elseif($box['icon'] === 'chat')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                        </svg>
                    @elseif($box['icon'] === 'language')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                        </svg>
                    @elseif($box['icon'] === 'font')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"></path>
                        </svg>
                    @elseif($box['icon'] === 'stamp')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    @elseif($box['icon'] === 'toc')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    @elseif($box['icon'] === 'megaphone')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    @elseif($box['icon'] === 'bell')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    @elseif($box['icon'] === 'code')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                    @elseif($box['icon'] === 'credit-card')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    @elseif($box['icon'] === 'truck')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8m0 0a2 2 0 104 0 2 2 0 00-4 0zm-8 0a2 2 0 10-4 0 2 2 0 004 0zm-5-3V5a2 2 0 012-2h8a2 2 0 012 2v9m0 0a2 2 0 002 2h2a2 2 0 002-2V9.828a2 2 0 00-.586-1.414l-2.828-2.828A2 2 0 0017.172 5H15"></path>
                        </svg>
                    @elseif($box['icon'] === 'forms')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    @elseif($box['icon'] === 'sparkles')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    @elseif($box['icon'] === 'chart')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    @elseif($box['icon'] === 'palette')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4 5 5 0 014-4h4a5 5 0 014 4 4 4 0 01-4 4H7zm0 0a4 4 0 004-4H7a4 4 0 000 4zm10-12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    @elseif($box['icon'] === 'users')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    @else
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        </svg>
                    @endif
                </div>

                <!-- Title -->
                <h3 class="text-sm font-semibold text-slate-800 group-hover:text-blue-600 transition-colors leading-snug px-1">
                    {{ $box['title'] }}
                </h3>

            </a>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-slate-200">
                <p class="text-slate-500 font-medium">Chưa có mục cấu hình nào được thiết lập.</p>
            </div>
        @endforelse
    </div>

    <!-- Empty Search State -->
    <div x-show="search.length > 0" 
         x-cloak
         class="text-center py-12 bg-white rounded-2xl border border-slate-200/80 shadow-sm"
         style="display: none;">
        <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <h4 class="text-base font-semibold text-slate-800">Không tìm thấy chức năng phù hợp</h4>
        <p class="text-sm text-slate-500 mt-1">Vui lòng thử lại với từ khóa khác hoặc xóa bộ lọc.</p>
        <button type="button" @click="search = ''; activeTab = 'all'" class="mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition">
            Xóa tìm kiếm
        </button>
    </div>

</div>
@endsection
