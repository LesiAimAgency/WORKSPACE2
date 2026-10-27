<?php

namespace App\Services\FieldTypes;

use App\Contracts\FieldTypeInterface;

class PostObjectField implements FieldTypeInterface
{
    public static function getTypeName(): string
    {
        return 'post_object';
    }

    public function render(array $config, mixed $value = null): string
    {
        $name = $config['name'] ?? '';
        $label = $config['label'] ?? $name;
        $required = $config['required'] ?? false;
        $help = $config['help'] ?? '';
        $postType = $config['post_type'] ?? 'product';

        $selectedId = is_array($value) ? ($value[0] ?? '') : ($value ?? '');
        $requiredAttr = $required ? 'required' : '';

        // Determine API base URL based on context
        $projectCode = session('current_project')['code'] ?? null;
        $apiBase = $projectCode ? "/{$projectCode}/api" : '/api';
        $assetBase = rtrim(asset('/'), '/');

        return <<<HTML
        <div class="mb-4" x-data="postObjectField_{$name}()" x-init="init()">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {$label} {$this->renderRequiredBadge($required)}
            </label>
            
            <div class="relative">
                <div class="border rounded-lg">
                    <!-- Selected Item Display -->
                    <div x-show="selectedItem" class="flex items-center gap-3 p-3 bg-blue-50">
                        <template x-if="selectedItem && selectedItem.image">
                            <img :src="formatUrl(selectedItem.image)" class="w-12 h-12 object-cover rounded">
                        </template>
                        <div class="flex-1">
                            <p class="font-medium" x-text="selectedItem?.title"></p>
                            <p class="text-sm text-gray-500" x-text="selectedItem?.type"></p>
                        </div>
                        <button type="button" @click="clearSelection()" class="text-red-500 hover:text-red-700 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Search Input -->
                    <div x-show="!selectedItem" class="p-2">
                        <input type="text" 
                               x-model="search" 
                               @input.debounce.300ms="searchItems()"
                               @focus="showDropdown = true"
                               placeholder="Tìm kiếm {$postType}..."
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                
                <!-- Dropdown -->
                <div x-show="showDropdown && !selectedItem" 
                     @click.away="showDropdown = false"
                     class="absolute z-50 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-64 overflow-y-auto">
                    <template x-if="loading">
                        <div class="p-4 text-center text-gray-500">Đang tải...</div>
                    </template>
                    <template x-for="item in items" :key="item.id">
                        <div @click="selectItem(item)" 
                             class="flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0">
                            <template x-if="item.image">
                                <img :src="formatUrl(item.image)" class="w-10 h-10 object-cover rounded">
                            </template>
                            <div class="flex-1">
                                <p class="font-medium text-sm" x-text="item.title"></p>
                                <p class="text-xs text-gray-500" x-text="item.type"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="!loading && items.length === 0">
                        <div class="p-4 text-center text-gray-500">Không tìm thấy kết quả</div>
                    </template>
                </div>
            </div>
            
            <input type="hidden" name="{$name}" :value="selectedItem?.id || ''" {$requiredAttr}>
            
            {$this->renderHelp($help)}
            
            <script>
                function postObjectField_{$name}() {
                    return {
                        search: '',
                        items: [],
                        selectedItem: null,
                        showDropdown: false,
                        loading: false,
                        postType: '{$postType}',
                        initialId: '{$selectedId}',
                        apiBase: '{$apiBase}',
                        formatUrl(url) {
                            if (!url) return '';
                            url = url.toString().trim();
                            if (!url) return '';
                            if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//') || url.startsWith('data:') || url.startsWith('blob:')) return url;
                            return '{$assetBase}/' + url.replace(/^\/+/,'');
                        },
                        
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
                        },
                        
                        clearSelection() {
                            this.selectedItem = null;
                            this.search = '';
                        }
                    }
                }
            </script>
        </div>
        HTML;
    }

    protected function renderRequiredBadge(bool $required): string
    {
        return $required ? '<span class="text-red-500">*</span>' : '';
    }

    protected function renderHelp(string $help): string
    {
        return $help ? "<p class=\"mt-1 text-sm text-gray-500\">{$help}</p>" : '';
    }

    public function validate(mixed $value, array $rules = []): bool
    {
        if (empty($value)) {
            return ! in_array('required', $rules);
        }

        return is_numeric($value);
    }

    public function transform(mixed $value): mixed
    {
        return $value ? (int) $value : null;
    }
}
