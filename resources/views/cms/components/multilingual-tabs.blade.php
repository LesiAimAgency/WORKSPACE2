@php
    $languages = setting('languages', [
        ['name' => 'Tiếng Việt', 'code' => 'vi', 'is_default' => true],
        ['name' => 'English', 'code' => 'en', 'is_default' => false]
    ]);
    $defaultLang = collect($languages)->firstWhere('is_default', true)['code'] ?? 'vi';
    $multilingualEnabled = setting('multilingual_enabled', false);
    
    // Nếu chức năng đa ngôn ngữ bị tắt, chỉ hiển thị form thông thường
    if (!$multilingualEnabled) {
        $languages = [collect($languages)->firstWhere('is_default', true) ?: $languages[0]];
    }
@endphp

@if($multilingualEnabled)
<div x-data="multilingualTabs()" class="border-b border-gray-200 mb-6">
    <nav class="flex space-x-8">
        @foreach($languages as $index => $lang)
        <button type="button" 
                @click="activeTab = '{{ $lang['code'] }}'"
                :class="activeTab === '{{ $lang['code'] }}' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
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
            {{ $lang['name'] }}
            <span x-show="activeTab !== '{{ $lang['code'] }}' && hasContent('{{ $lang['code'] }}')" 
                  class="w-2 h-2 bg-green-500 rounded-full"></span>
        </button>
        @endforeach
    </nav>
</div>
@endif

<!-- Language Content Panels -->
<div class="multilingual-content">
    @foreach($languages as $lang)
    <div @if($multilingualEnabled) x-show="activeTab === '{{ $lang['code'] }}'" @endif class="space-y-6">
        @if($multilingualEnabled && $lang['code'] !== $defaultLang)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="font-medium text-blue-900">Bản {{ $lang['name'] }}</h4>
                    <p class="text-sm text-blue-800 mt-1">
                        Nếu để trống, sẽ tạo bản ghi riêng cho ngôn ngữ này. 
                        URL sẽ có dạng: <code class="bg-blue-100 px-1 rounded">/{{ $lang['code'] }}/slug-name</code>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Hidden field để xác định ngôn ngữ -->
        <input type="hidden" name="language_versions[{{ $lang['code'] }}][language]" value="{{ $lang['code'] }}">

        <!-- Tên -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tên {{ $lang['name'] }} 
                @if($lang['code'] === $defaultLang || !$multilingualEnabled)<span class="text-red-500">*</span>@endif
            </label>
            <input type="text" 
                   name="language_versions[{{ $lang['code'] }}][name]" 
                   value="{{ old('language_versions.'.$lang['code'].'.name', $model->name ?? '') }}"
                   @if($lang['code'] === $defaultLang || !$multilingualEnabled) required @endif
                   @if($multilingualEnabled) @input="updateContentStatus('{{ $lang['code'] }}')" @endif
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <!-- Mô tả ngắn -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả ngắn {{ $lang['name'] }}</label>
            <textarea name="language_versions[{{ $lang['code'] }}][short_description]" 
                      rows="2" 
                      @if($multilingualEnabled) @input="updateContentStatus('{{ $lang['code'] }}')" @endif
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('language_versions.'.$lang['code'].'.short_description', $model->short_description ?? '') }}</textarea>
        </div>

        <!-- Mô tả đầy đủ -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Mô tả đầy đủ {{ $lang['name'] }}
                @if($lang['code'] === $defaultLang || !$multilingualEnabled)<span class="text-red-500">*</span>@endif
            </label>
            <div class="summernote-container">
                <textarea name="language_versions[{{ $lang['code'] }}][description]" 
                          class="summernote-{{ $lang['code'] }}"
                          @if($lang['code'] === $defaultLang || !$multilingualEnabled) required @endif
                          @if($multilingualEnabled) @input="updateContentStatus('{{ $lang['code'] }}')" @endif>{{ old('language_versions.'.$lang['code'].'.description', $model->description ?? '') }}</textarea>
            </div>
        </div>

        <!-- SEO Fields -->
        <div class="bg-gray-50 rounded-lg p-4 space-y-4">
            <h3 class="font-medium text-gray-900">SEO {{ $lang['name'] }}</h3>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                <input type="text" 
                       name="language_versions[{{ $lang['code'] }}][meta_title]" 
                       value="{{ old('language_versions.'.$lang['code'].'.meta_title', $model->meta_title ?? '') }}"
                       @if($multilingualEnabled) @input="updateContentStatus('{{ $lang['code'] }}')" @endif
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                <textarea name="language_versions[{{ $lang['code'] }}][meta_description]" 
                          rows="2" 
                          @if($multilingualEnabled) @input="updateContentStatus('{{ $lang['code'] }}')" @endif
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('language_versions.'.$lang['code'].'.meta_description', $model->meta_description ?? '') }}</textarea>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($multilingualEnabled)
<script>
function multilingualTabs() {
    return {
        activeTab: '{{ $defaultLang }}',
        contentStatus: {},
        
        init() {
            // Initialize content status for all languages
            @foreach($languages as $lang)
            this.updateContentStatus('{{ $lang['code'] }}');
            @endforeach
            
            // Initialize Summernote for all language tabs
            this.initSummernote();
        },
        
        updateContentStatus(langCode) {
            const nameField = document.querySelector(`[name="language_versions[${langCode}][name]"]`);
            const descField = document.querySelector(`[name="language_versions[${langCode}][description]"]`);
            
            const hasName = nameField && nameField.value.trim().length > 0;
            const hasDesc = descField && descField.value.trim().length > 0;
            
            this.contentStatus[langCode] = hasName || hasDesc;
        },
        
        hasContent(langCode) {
            return this.contentStatus[langCode] || false;
        },
        
        initSummernote() {
            @foreach($languages as $lang)
            if (typeof $ !== 'undefined' && $.fn.summernote) {
                $('.summernote-{{ $lang['code'] }}').summernote({
                    height: 300,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onChange: () => {
                            this.updateContentStatus('{{ $lang['code'] }}');
                        }
                    }
                });
            }
            @endforeach
        }
    }
}
</script>
@endif

<style>
.multilingual-content .note-editor {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
}

.multilingual-content .note-toolbar {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}
</style>
