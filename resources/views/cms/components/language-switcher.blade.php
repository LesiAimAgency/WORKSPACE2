@php
    $languages = setting('languages', [
        ['name' => 'Tiếng Việt', 'code' => 'vi', 'is_default' => true],
        ['name' => 'English', 'code' => 'en', 'is_default' => false]
    ]);
    $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
    $multilingualEnabled = setting('multilingual_enabled', false);
    
    // Lấy ngôn ngữ hiện tại từ URL hoặc session
    $currentLang = request()->get('lang', session('admin_language', $defaultLang));
    
    // Nếu chức năng đa ngôn ngữ bị tắt, chỉ sử dụng ngôn ngữ mặc định
    if (!$multilingualEnabled) {
        $currentLang = $defaultLang;
    }
@endphp

@if($multilingualEnabled && count($languages) > 1)
<div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
            </svg>
            <h3 class="font-medium text-gray-900">Ngôn ngữ hiện tại</h3>
        </div>
        
        <div class="flex items-center gap-4">
            <!-- Language Selector -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                    @php $currentLangData = collect($languages)->firstWhere('code', $currentLang); @endphp
                    <span class="w-6 h-4 rounded overflow-hidden flex-shrink-0">
                        @if($currentLang === 'vi')
                            <div class="w-full h-full bg-red-600 relative">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-3 h-3 border border-yellow-400" style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%);">
                                        <div class="w-full h-full bg-yellow-400"></div>
                                    </div>
                                </div>
                            </div>
                        @elseif($currentLang === 'en')
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
                                {{ strtoupper($currentLang) }}
                            </div>
                        @endif
                    </span>
                    <span class="font-medium">{{ $currentLangData['name'] ?? 'Unknown' }}</span>
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                    @foreach($languages as $lang)
                    <a href="{{ request()->fullUrlWithQuery(['lang' => $lang['code']]) }}" 
                       class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 {{ $currentLang === $lang['code'] ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }} {{ $loop->first ? 'rounded-t-lg' : '' }} {{ $loop->last ? 'rounded-b-lg' : 'border-b border-gray-100' }}">
                        <span class="w-6 h-4 rounded overflow-hidden flex-shrink-0">
                            @if($lang['code'] === 'vi')
                                <div class="w-full h-full bg-red-600 relative">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-3 h-3 border border-yellow-400" style="clip-path: polygon(50% 0%, 0% 100%, 100% 100%);">
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
                        <span class="flex-1">{{ $lang['name'] }}</span>
                        @if($currentLang === $lang['code'])
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
            
            <!-- Language Status Indicators -->
            <div class="flex items-center gap-2">
                @foreach($languages as $lang)
                @php
                    $hasContent = false;
                    if (isset($model) && $model) {
                        // Kiểm tra xem có bản ghi cho ngôn ngữ này không
                        $langVersion = $model->where('language', $lang['code'])->first();
                        $hasContent = $langVersion && ($langVersion->name || $langVersion->description);
                    }
                @endphp
                <div class="flex items-center gap-1 px-2 py-1 rounded-full text-xs {{ $hasContent ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    <span class="w-2 h-2 rounded-full {{ $hasContent ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    {{ strtoupper($lang['code']) }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    @if($currentLang !== $defaultLang)
    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="font-medium text-blue-900">Đang chỉnh sửa bản {{ $currentLangData['name'] ?? 'Unknown' }}</h4>
                <p class="text-sm text-blue-800 mt-1">
                    Bạn đang chỉnh sửa nội dung cho ngôn ngữ {{ $currentLangData['name'] ?? 'Unknown' }}. 
                    Đây sẽ là một bản ghi riêng biệt với URL: <code class="bg-blue-100 px-1 rounded">/{{ $lang['code'] }}/slug-name</code>
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<!-- Hidden field để xác định ngôn ngữ hiện tại -->
<input type="hidden" name="language" value="{{ $currentLang }}">
