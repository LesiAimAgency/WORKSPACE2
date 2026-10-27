@php
    $languages = setting('languages', []);
    $currentLocale = app()->getLocale();
    $currentLang = collect($languages)->firstWhere('code', $currentLocale);
@endphp

@if(count($languages) > 1)
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" 
            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none">
        <span class="w-5 h-3 rounded overflow-hidden flex-shrink-0">
            @if($currentLocale === 'vi')
                <div class="w-full h-full bg-red-600 relative">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-2 h-2 border border-yellow-400" style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%);">
                            <div class="w-full h-full bg-yellow-400"></div>
                        </div>
                    </div>
                </div>
            @elseif($currentLocale === 'en')
                <div class="w-full h-full bg-blue-800 relative">
                    <div class="absolute inset-0">
                        <div class="w-full h-1/3 bg-red-600"></div>
                        <div class="w-full h-1/3 bg-white"></div>
                        <div class="w-full h-1/3 bg-red-600"></div>
                    </div>
                    <div class="absolute top-0 left-0 w-1/3 h-1/2 bg-blue-800"></div>
                </div>
            @else
                <div class="w-full h-full bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600">
                    {{ strtoupper($currentLocale) }}
                </div>
            @endif
        </span>
        <span>{{ $currentLang['name'] ?? strtoupper($currentLocale) }}</span>
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1">
            @foreach($languages as $lang)
            @if($lang['code'] !== $currentLocale)
            <a href="{{ switchLanguageUrl($lang['code']) }}" 
               class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                <span class="w-5 h-3 rounded overflow-hidden flex-shrink-0">
                    @if($lang['code'] === 'vi')
                        <div class="w-full h-full bg-red-600 relative">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-2 h-2 border border-yellow-400" style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%);">
                                    <div class="w-full h-full bg-yellow-400"></div>
                                </div>
                            </div>
                        </div>
                    @elseif($lang['code'] === 'en')
                        <div class="w-full h-full bg-blue-800 relative">
                            <div class="absolute inset-0">
                                <div class="w-full h-1/3 bg-red-600"></div>
                                <div class="w-full h-1/3 bg-white"></div>
                                <div class="w-full h-1/3 bg-red-600"></div>
                            </div>
                            <div class="absolute top-0 left-0 w-1/3 h-1/2 bg-blue-800"></div>
                        </div>
                    @else
                        <div class="w-full h-full bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600">
                            {{ strtoupper($lang['code']) }}
                        </div>
                    @endif
                </span>
                <span>{{ $lang['name'] }}</span>
            </a>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endif
