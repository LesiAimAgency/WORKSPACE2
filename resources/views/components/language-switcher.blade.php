<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-lg border hover:bg-gray-50">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
        </svg>
        <span class="font-medium">{{ strtoupper(app()->getLocale()) }}</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div x-show="open" 
         @click.away="open = false"
         x-cloak
         class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border z-50">
        @foreach(setting('languages', []) as $lang)
            <a href="{{ route('lang.switch', $lang['code']) }}" 
               class="block px-4 py-2 hover:bg-gray-50 {{ app()->getLocale() == $lang['code'] ? 'bg-blue-50 text-blue-600 font-semibold' : '' }}">
                {{ $lang['name'] }}
            </a>
        @endforeach
    </div>
</div>

<style>
[x-cloak] { display: none; }
</style>
