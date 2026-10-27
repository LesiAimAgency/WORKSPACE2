@php
    $fieldName = $field['name'];
    $fieldType = $field['type'];
    $fieldLabel = $field['label'];
    $isRequired = $field['required'] ?? false;
    $helpText = $field['help'] ?? '';
    $placeholder = $field['placeholder'] ?? $field['default'] ?? '';
    $fieldValue = $settings[$fieldName] ?? null;
    
    // Get project code for API calls
    $currentProject = session('current_project');
    $projectCode = is_array($currentProject) 
        ? ($currentProject['code'] ?? null) 
        : (is_object($currentProject) 
            ? ($currentProject->code ?? null) 
            : (is_string($currentProject) ? $currentProject : null));
    $apiBase = $projectCode ? "/{$projectCode}/api" : '/api';
@endphp

<div class="field-wrapper" wire:key="field-{{ $fieldName }}">
    {{-- Field Label --}}
    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
        {{ $fieldLabel }}
        @if($isRequired)
            <span class="text-red-500">*</span>
        @endif
        @if($helpText)
            <span class="text-gray-400 cursor-help" title="{{ $helpText }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
        @endif
    </label>

    {{-- Field Input based on type --}}
    @switch($fieldType)
        {{-- TEXT --}}
        @case('text')
            <input type="text" 
                wire:model="settings.{{ $fieldName }}" 
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="{{ $placeholder }}">
            @break

        {{-- TEXTAREA --}}
        @case('textarea')
            <textarea 
                wire:model="settings.{{ $fieldName }}" 
                rows="{{ $field['rows'] ?? 4 }}"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="{{ $placeholder }}"></textarea>
            @break

        {{-- WYSIWYG --}}
        @case('wysiwyg')
            <div x-data="{ content: @entangle('settings.' . $fieldName) }" class="wysiwyg-wrapper">
                <div class="border border-gray-300 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 border-b px-3 py-2 flex flex-wrap gap-1">
                        <button type="button" onclick="document.execCommand('bold')" class="p-1.5 hover:bg-gray-200 rounded" title="Bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                        </button>
                        <button type="button" onclick="document.execCommand('italic')" class="p-1.5 hover:bg-gray-200 rounded" title="Italic">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4h4m-2 0v16m-4 0h8"/></svg>
                        </button>
                        <button type="button" onclick="document.execCommand('underline')" class="p-1.5 hover:bg-gray-200 rounded" title="Underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8v4a5 5 0 0010 0V8M5 20h14"/></svg>
                        </button>
                        <span class="w-px h-6 bg-gray-300 mx-1"></span>
                        <button type="button" onclick="document.execCommand('insertUnorderedList')" class="p-1.5 hover:bg-gray-200 rounded" title="Bullet List">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <button type="button" onclick="document.execCommand('insertOrderedList')" class="p-1.5 hover:bg-gray-200 rounded" title="Numbered List">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                        </button>
                    </div>
                    <div contenteditable="true" 
                         x-ref="editor"
                         @input="content = $el.innerHTML"
                         x-html="content"
                         class="min-h-[150px] p-4 focus:outline-none prose prose-sm max-w-none"
                         style="min-height: {{ $field['height'] ?? 150 }}px"></div>
                </div>
            </div>
            @break

        {{-- NUMBER --}}
        @case('number')
            <input type="number" 
                wire:model="settings.{{ $fieldName }}" 
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                min="{{ $field['min'] ?? '' }}"
                max="{{ $field['max'] ?? '' }}"
                placeholder="{{ $placeholder }}">
            @break

        {{-- URL --}}
        @case('url')
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </span>
                <input type="url" 
                    wire:model="settings.{{ $fieldName }}" 
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="https://example.com">
            </div>
            @break

        {{-- EMAIL --}}
        @case('email')
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <input type="email" 
                    wire:model="settings.{{ $fieldName }}" 
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="email@example.com">
            </div>
            @break

        {{-- SELECT --}}
        @case('select')
            <select wire:model="settings.{{ $fieldName }}" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white"
                    {{ ($field['multiple'] ?? false) ? 'multiple' : '' }}>
                <option value="">-- Chọn --</option>
                @foreach($field['options'] ?? [] as $key => $option)
                    @if(is_array($option))
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @else
                        <option value="{{ $key }}">{{ $option }}</option>
                    @endif
                @endforeach
            </select>
            @break

        {{-- CHECKBOX --}}
        @case('checkbox')
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model="settings.{{ $fieldName }}" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-sm text-gray-600">{{ $helpText ?: 'Bật/Tắt' }}</span>
            </label>
            @break

        {{-- IMAGE --}}
        @case('image')
            @php
                $imgRaw = $settings[$fieldName] ?? '';
                $imgSrc = $imgRaw ? ((str_starts_with($imgRaw, 'http') || str_starts_with($imgRaw, '//') || str_starts_with($imgRaw, 'data:')) ? $imgRaw : asset($imgRaw)) : '';
            @endphp
            <div class="space-y-3">
                @if(!empty($imgRaw))
                    <div class="relative inline-block group">
                        <img src="{{ $imgSrc }}" class="w-40 h-40 object-cover rounded-lg border-2 border-gray-200 shadow-sm">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center gap-2">
                            <a href="{{ $imgSrc }}" target="_blank" class="p-2 bg-white rounded-full hover:bg-gray-100">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <button type="button" 
                                    wire:click="$set('settings.{{ $fieldName }}', '')"
                                    class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                @endif
                <div class="flex gap-2">
                    <input type="text" 
                        wire:model="settings.{{ $fieldName }}" 
                        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="URL hình ảnh hoặc chọn từ thư viện">
                    <button type="button" 
                        @click="$dispatch('open-media-picker', { field: '{{ $fieldName }}', multiple: false })"
                        class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition flex items-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Chọn ảnh
                    </button>
                </div>
            </div>
            @break

        {{-- COLOR --}}
        @case('color')
            <div class="flex items-center gap-3">
                <input type="color" 
                    wire:model="settings.{{ $fieldName }}" 
                    class="w-14 h-12 rounded-lg border border-gray-300 cursor-pointer">
                <input type="text" 
                    wire:model="settings.{{ $fieldName }}" 
                    class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono"
                    placeholder="#000000">
            </div>
            @break

        {{-- DATE --}}
        @case('date')
            <input type="date" 
                wire:model="settings.{{ $fieldName }}" 
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            @break

        {{-- RANGE --}}
        @case('range')
            <div class="space-y-2">
                <input type="range" 
                    wire:model="settings.{{ $fieldName }}" 
                    min="{{ $field['min'] ?? 0 }}"
                    max="{{ $field['max'] ?? 100 }}"
                    step="{{ $field['step'] ?? 1 }}"
                    class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                <div class="flex justify-between text-xs text-gray-500">
                    <span>{{ $field['min'] ?? 0 }}</span>
                    <span class="font-medium text-blue-600">{{ $settings[$fieldName] ?? $field['default'] ?? 50 }}</span>
                    <span>{{ $field['max'] ?? 100 }}</span>
                </div>
            </div>
            @break

        {{-- RELATIONSHIP (Multiple posts/products) --}}
        @case('relationship')
            @php
                $postType = $field['post_type'] ?? 'product';
                $selectedIds = $settings[$fieldName] ?? [];
            @endphp
            <div x-data="relationshipField_{{ $fieldName }}()" x-init="init()" class="space-y-3">
                {{-- Search --}}
                <div class="relative">
                    <input type="text" 
                           x-model="search" 
                           @input.debounce.300ms="searchItems()"
                           @focus="showDropdown = true"
                           placeholder="Tìm kiếm {{ $postType === 'product' ? 'sản phẩm' : 'bài viết' }}..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="w-5 h-5 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>
                </div>
                
                {{-- Dropdown Results --}}
                <div x-show="showDropdown && items.length > 0" 
                     @click.away="showDropdown = false"
                     class="absolute z-50 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-64 overflow-y-auto">
                    <template x-for="item in items" :key="item.id">
                        <div @click="addItem(item)" 
                             class="flex items-center gap-3 p-3 hover:bg-blue-50 cursor-pointer border-b last:border-b-0"
                             :class="{ 'opacity-50 pointer-events-none': isSelected(item.id) }">
                            <template x-if="item.image">
                                <img :src="item.image" class="w-10 h-10 object-cover rounded">
                            </template>
                            <template x-if="!item.image">
                                <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </template>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm truncate" x-text="item.title"></p>
                                <p class="text-xs text-gray-500" x-text="item.type"></p>
                            </div>
                            <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </template>
                </div>
                
                {{-- Selected Items --}}
                <div class="space-y-2">
                    <template x-for="(item, index) in selected" :key="item.id">
                        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <template x-if="item.image">
                                <img :src="item.image" class="w-12 h-12 object-cover rounded">
                            </template>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm truncate" x-text="item.title"></p>
                                <p class="text-xs text-gray-500" x-text="item.type"></p>
                            </div>
                            <button type="button" @click="removeItem(index)" class="p-1.5 text-red-500 hover:bg-red-100 rounded-full transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
                
                {{-- Hidden inputs --}}
                <template x-for="item in selected" :key="'input-' + item.id">
                    <input type="hidden" name="settings[{{ $fieldName }}][]" :value="item.id">
                </template>
            </div>
            
            <script>
                function relationshipField_{{ $fieldName }}() {
                    return {
                        search: '',
                        items: [],
                        selected: [],
                        showDropdown: false,
                        loading: false,
                        postType: '{{ $postType }}',
                        initialIds: @json($selectedIds),
                        apiBase: '{{ $apiBase }}',
                        
                        init() {
                            this.loadInitialItems();
                        },
                        
                        async loadInitialItems() {
                            if (!this.initialIds || this.initialIds.length === 0) return;
                            try {
                                const response = await fetch(this.apiBase + '/relationship-field/items?type=' + this.postType + '&ids=' + this.initialIds.join(','));
                                const data = await response.json();
                                this.selected = data.items || [];
                            } catch (e) {
                                console.error('Error loading initial items:', e);
                            }
                        },
                        
                        async searchItems() {
                            if (!this.search) {
                                this.items = [];
                                return;
                            }
                            this.loading = true;
                            try {
                                const response = await fetch(this.apiBase + '/relationship-field/search?type=' + this.postType + '&q=' + encodeURIComponent(this.search));
                                const data = await response.json();
                                this.items = data.items || [];
                                this.showDropdown = true;
                            } catch (e) {
                                this.items = [];
                            }
                            this.loading = false;
                        },
                        
                        isSelected(id) {
                            return this.selected.some(item => item.id === id);
                        },
                        
                        addItem(item) {
                            if (this.isSelected(item.id)) return;
                            this.selected.push(item);
                            this.search = '';
                            this.showDropdown = false;
                            this.updateLivewire();
                        },
                        
                        removeItem(index) {
                            this.selected.splice(index, 1);
                            this.updateLivewire();
                        },
                        
                        updateLivewire() {
                            const ids = this.selected.map(item => item.id);
                            @this.set('settings.{{ $fieldName }}', ids);
                        }
                    }
                }
            </script>
            @break

        {{-- POST OBJECT (Single post/product) --}}
        @case('post_object')
            @php
                $postType = $field['post_type'] ?? 'product';
                $selectedId = $settings[$fieldName] ?? null;
            @endphp
            <div x-data="postObjectField_{{ $fieldName }}()" x-init="init()" class="relative">
                {{-- Selected Item Display --}}
                <div x-show="selectedItem" class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg mb-2">
                    <template x-if="selectedItem && selectedItem.image">
                        <img :src="selectedItem.image" class="w-12 h-12 object-cover rounded">
                    </template>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm truncate" x-text="selectedItem?.title"></p>
                        <p class="text-xs text-gray-500" x-text="selectedItem?.type"></p>
                    </div>
                    <button type="button" @click="clearSelection()" class="p-1.5 text-red-500 hover:bg-red-100 rounded-full transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                {{-- Search Input --}}
                <div x-show="!selectedItem" class="relative">
                    <input type="text" 
                           x-model="search" 
                           @input.debounce.300ms="searchItems()"
                           @focus="showDropdown = true"
                           placeholder="Tìm kiếm {{ $postType === 'product' ? 'sản phẩm' : 'bài viết' }}..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                
                {{-- Dropdown --}}
                <div x-show="showDropdown && !selectedItem && items.length > 0" 
                     @click.away="showDropdown = false"
                     class="absolute z-50 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-64 overflow-y-auto">
                    <template x-for="item in items" :key="item.id">
                        <div @click="selectItem(item)" 
                             class="flex items-center gap-3 p-3 hover:bg-blue-50 cursor-pointer border-b last:border-b-0">
                            <template x-if="item.image">
                                <img :src="item.image" class="w-10 h-10 object-cover rounded">
                            </template>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm truncate" x-text="item.title"></p>
                                <p class="text-xs text-gray-500" x-text="item.type"></p>
                            </div>
                        </div>
                    </template>
                </div>
                
                <input type="hidden" wire:model="settings.{{ $fieldName }}" :value="selectedItem?.id || ''">
            </div>
            
            <script>
                function postObjectField_{{ $fieldName }}() {
                    return {
                        search: '',
                        items: [],
                        selectedItem: null,
                        showDropdown: false,
                        loading: false,
                        postType: '{{ $postType }}',
                        initialId: '{{ $selectedId }}',
                        apiBase: '{{ $apiBase }}',
                        
                        init() {
                            if (this.initialId) {
                                this.loadInitialItem();
                            }
                        },
                        
                        async loadInitialItem() {
                            try {
                                const response = await fetch(this.apiBase + '/relationship-field/items?type=' + this.postType + '&ids=' + this.initialId);
                                const data = await response.json();
                                if (data.items && data.items.length > 0) {
                                    this.selectedItem = data.items[0];
                                }
                            } catch (e) {
                                console.error('Error loading initial item:', e);
                            }
                        },
                        
                        async searchItems() {
                            if (!this.search) {
                                this.items = [];
                                return;
                            }
                            this.loading = true;
                            try {
                                const response = await fetch(this.apiBase + '/relationship-field/search?type=' + this.postType + '&q=' + encodeURIComponent(this.search));
                                const data = await response.json();
                                this.items = data.items || [];
                            } catch (e) {
                                this.items = [];
                            }
                            this.loading = false;
                        },
                        
                        selectItem(item) {
                            this.selectedItem = item;
                            this.showDropdown = false;
                            this.search = '';
                            @this.set('settings.{{ $fieldName }}', item.id);
                        },
                        
                        clearSelection() {
                            this.selectedItem = null;
                            this.search = '';
                            @this.set('settings.{{ $fieldName }}', null);
                        }
                    }
                }
            </script>
            @break

        {{-- TAXONOMY --}}
        @case('taxonomy')
            @php
                $taxonomy = $field['taxonomy'] ?? 'category';
                $selectedTaxIds = $settings[$fieldName] ?? [];
                $isMultiple = $field['multiple'] ?? false;
            @endphp
            <div x-data="taxonomyField_{{ $fieldName }}()" x-init="init()">
                <select wire:model="settings.{{ $fieldName }}" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white"
                        {{ $isMultiple ? 'multiple' : '' }}>
                    <option value="">-- Chọn {{ $taxonomy === 'category' ? 'danh mục' : ($taxonomy === 'brand' ? 'thương hiệu' : 'taxonomy') }} --</option>
                    <template x-for="item in items" :key="item.id">
                        <option :value="item.id" x-text="item.name + (item.count ? ' (' + item.count + ')' : '')"></option>
                    </template>
                </select>
            </div>
            
            <script>
                function taxonomyField_{{ $fieldName }}() {
                    return {
                        items: [],
                        loading: true,
                        taxonomy: '{{ $taxonomy }}',
                        apiBase: '{{ $apiBase }}',
                        
                        init() {
                            this.loadTaxonomies();
                        },
                        
                        async loadTaxonomies() {
                            try {
                                const response = await fetch(this.apiBase + '/taxonomy-field/list?type=' + this.taxonomy);
                                const data = await response.json();
                                this.items = data.items || [];
                            } catch (e) {
                                console.error('Error loading taxonomies:', e);
                            }
                            this.loading = false;
                        }
                    }
                }
            </script>
            @break

        {{-- REPEATER --}}
        @case('repeater')
        @case('repeatable')
            @php
                $subFields = $field['fields'] ?? [];
                $buttonLabel = $field['button_label'] ?? 'Thêm mục';
                $layout = $field['layout'] ?? 'block';
                $allowAdd = $field['allow_add'] ?? true;
                $allowDelete = $field['allow_delete'] ?? true;
            @endphp
            <div class="border border-gray-200 rounded-lg overflow-hidden"
                 x-data="{
                     initSortable() {
                         const container = this.$refs.itemsContainer;
                         if (!container) return;
                         if (container._sortable) {
                             container._sortable.destroy();
                         }
                         if (typeof Sortable === 'undefined') {
                             if (!window._loadingSortable) {
                                 window._loadingSortable = true;
                                 const s = document.createElement('script');
                                 s.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
                                 s.onload = () => { window._loadingSortable = false; this.initSortable(); };
                                 document.head.appendChild(s);
                             } else {
                                 setTimeout(() => this.initSortable(), 200);
                             }
                             return;
                         }
                         container._sortable = new Sortable(container, {
                             handle: '.repeater-drag-handle',
                             animation: 180,
                             ghostClass: 'bg-blue-50/80',
                             chosenClass: 'bg-blue-100/70',
                             dragClass: 'shadow-xl',
                             onEnd: (evt) => {
                                 if (evt.oldIndex === evt.newIndex) return;
                                 const items = Array.from(container.children).filter(el => el.hasAttribute('data-item-index'));
                                 const newOrder = items.map(el => parseInt(el.getAttribute('data-item-index'), 10));
                                 $wire.reorderRepeater('{{ $fieldName }}', newOrder);
                             }
                         });
                     }
                 }"
                 x-init="initSortable()"
                 @repeater-updated.window="if ($event.detail?.fieldName === '{{ $fieldName }}') $nextTick(() => initSortable())"
            >
                {{-- Items --}}
                <div class="divide-y divide-gray-200" x-ref="itemsContainer">
                    @foreach($settings[$fieldName] ?? [] as $itemIndex => $item)
                        <div class="p-4 bg-gray-50 hover:bg-gray-100 transition" 
                             wire:key="repeater-{{ $fieldName }}-{{ $itemIndex }}"
                             data-item-index="{{ $itemIndex }}">
                            <div class="flex items-start gap-4">
                                {{-- Drag Handle --}}
                                <div class="pt-2 repeater-drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-blue-600 transition-colors p-1 -ml-1 rounded hover:bg-gray-200 flex-shrink-0" title="Kéo thả để đổi vị trí">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                    </svg>
                                </div>
                                
                                {{-- Fields --}}
                                <div class="flex-1 grid {{ $layout === 'table' ? 'grid-cols-' . count($subFields) : 'grid-cols-1 lg:grid-cols-2' }} gap-4">
                                    @foreach($subFields as $subField)
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $subField['label'] }}</label>
                                            @if($subField['type'] === 'textarea')
                                                <textarea 
                                                    wire:model="settings.{{ $fieldName }}.{{ $itemIndex }}.{{ $subField['name'] }}"
                                                    rows="2"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                                            @elseif($subField['type'] === 'image')
                                                @php
                                                    $repImgRaw = $settings[$fieldName][$itemIndex][$subField['name']] ?? '';
                                                    $repImgSrc = $repImgRaw ? ((str_starts_with($repImgRaw, 'http') || str_starts_with($repImgRaw, '//') || str_starts_with($repImgRaw, 'data:')) ? $repImgRaw : asset($repImgRaw)) : '';
                                                @endphp
                                                <div class="space-y-2">
                                                    @if(!empty($repImgRaw))
                                                        <div class="relative w-24 h-24 bg-gray-100 rounded-lg border border-gray-200 overflow-hidden">
                                                            <img src="{{ $repImgSrc }}" class="w-full h-full object-cover">
                                                            <button type="button" 
                                                                wire:click="$set('settings.{{ $fieldName }}.{{ $itemIndex }}.{{ $subField['name'] }}', '')"
                                                                class="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                            </button>
                                                        </div>
                                                    @endif
                                                    <div class="flex gap-2">
                                                        <input type="text" 
                                                            wire:model="settings.{{ $fieldName }}.{{ $itemIndex }}.{{ $subField['name'] }}"
                                                            class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg"
                                                            placeholder="URL hình ảnh">
                                                        <button type="button" 
                                                            @click="$dispatch('open-media-picker', { field: '{{ $fieldName }}.{{ $itemIndex }}.{{ $subField['name'] }}', multiple: false })"
                                                            class="px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 rounded-lg text-sm flex items-center gap-1 shadow-sm">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @elseif($subField['type'] === 'select')
                                                <select wire:model="settings.{{ $fieldName }}.{{ $itemIndex }}.{{ $subField['name'] }}"
                                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                                                    <option value="">-- Chọn --</option>
                                                    @foreach($subField['options'] ?? [] as $opt)
                                                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                                    @endforeach
                                                </select>
                                            @elseif($subField['type'] === 'wysiwyg')
                                                <div x-data="{ content: @entangle('settings.' . $fieldName . '.' . $itemIndex . '.' . $subField['name']) }">
                                                    <div class="border border-gray-300 rounded-lg overflow-hidden">
                                                        <div class="bg-gray-50 border-b px-2 py-1.5 flex flex-wrap gap-1">
                                                            <button type="button" onclick="document.execCommand('bold')" class="p-1 hover:bg-gray-200 rounded text-gray-700" title="Bold">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                                                            </button>
                                                            <button type="button" onclick="document.execCommand('italic')" class="p-1 hover:bg-gray-200 rounded text-gray-700" title="Italic">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 4h4m-2 0v16m-4 0h8"/></svg>
                                                            </button>
                                                        </div>
                                                        <div contenteditable="true" 
                                                             @input="content = $el.innerHTML"
                                                             x-html="content"
                                                             class="min-h-[100px] p-3 focus:outline-none prose prose-sm max-w-none text-sm"></div>
                                                    </div>
                                                </div>
                                            @else
                                                <input type="{{ $subField['type'] === 'number' ? 'number' : ($subField['type'] === 'url' ? 'url' : 'text') }}" 
                                                    wire:model="settings.{{ $fieldName }}.{{ $itemIndex }}.{{ $subField['name'] }}"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                
                                {{-- Remove Button --}}
                                @if($allowDelete)
                                <button type="button" 
                                    wire:click="removeRepeaterItem('{{ $fieldName }}', {{ $itemIndex }})"
                                    class="p-2 text-red-500 hover:bg-red-100 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                {{-- Add Button --}}
                @if($allowAdd)
                <button type="button" 
                    wire:click="addRepeaterItem('{{ $fieldName }}')"
                    class="w-full py-3 bg-white border-t border-gray-200 text-blue-600 hover:bg-blue-50 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ $buttonLabel }}
                </button>
                @endif
            </div>
            @break

        {{-- VIDEO --}}
        @case('video')
            <div class="space-y-3">
                @if(!empty($settings[$fieldName]))
                    <div class="relative inline-block group">
                        <video src="{{ $settings[$fieldName] }}" class="w-full max-w-md h-auto rounded-lg border-2 border-gray-200 shadow-sm" controls></video>
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex gap-2">
                            <a href="{{ $settings[$fieldName] }}" target="_blank" class="p-2 bg-white rounded-full hover:bg-gray-100 shadow">
                                <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <button type="button" 
                                    wire:click="$set('settings.{{ $fieldName }}', '')"
                                    class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 shadow">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                @endif
                <div class="flex gap-2">
                    <input type="text" 
                        wire:model="settings.{{ $fieldName }}" 
                        class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="URL video hoặc chọn từ thư viện">
                    <button type="button" 
                        @click="$dispatch('open-media-picker', { field: '{{ $fieldName }}', multiple: false, mediaType: 'video' })"
                        class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition flex items-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Chọn video
                    </button>
                </div>
            </div>
            @break

        {{-- GALLERY --}}
        @case('gallery')
            <div class="space-y-3">
                @if(!empty($settings[$fieldName]) && is_array($settings[$fieldName]))
                    <div class="grid grid-cols-5 gap-3">
                        @foreach($settings[$fieldName] as $imgIndex => $imgUrl)
                            <div class="relative group aspect-square" wire:key="gallery-{{ $fieldName }}-{{ $imgIndex }}">
                                <img src="{{ $imgUrl }}" class="w-full h-full object-cover rounded-lg border-2 border-gray-200">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center gap-1">
                                    <a href="{{ $imgUrl }}" target="_blank" class="p-1.5 bg-white rounded-full hover:bg-gray-100">
                                        <svg class="w-3 h-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </a>
                                    <button type="button" 
                                            wire:click="removeGalleryImage('{{ $fieldName }}', {{ $imgIndex }})"
                                            class="p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div class="absolute top-1 left-1 w-5 h-5 bg-black/50 rounded text-white text-xs flex items-center justify-center">
                                    {{ $imgIndex + 1 }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <button type="button" 
                    @click="$dispatch('open-media-picker', { field: '{{ $fieldName }}', multiple: true })"
                    class="w-full py-4 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Thêm hình ảnh từ thư viện</span>
                </button>
            </div>
            @break

        @default
            <input type="text" 
                wire:model="settings.{{ $fieldName }}" 
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
    @endswitch

    {{-- Help Text --}}
    @if($helpText && !in_array($fieldType, ['checkbox']))
        <p class="text-xs text-gray-500 mt-1.5">{{ $helpText }}</p>
    @endif

    {{-- Error Message --}}
    @error('settings.' . $fieldName) 
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
