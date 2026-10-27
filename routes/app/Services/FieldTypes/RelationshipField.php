<?php

namespace App\Services\FieldTypes;

use App\Contracts\FieldTypeInterface;

class RelationshipField implements FieldTypeInterface
{
    public static function getTypeName(): string
    {
        return 'relationship';
    }

    public function render(array $config, mixed $value = null): string
    {
        $name = $config['name'] ?? '';
        $label = $config['label'] ?? $name;
        $required = $config['required'] ?? false;
        $help = $config['help'] ?? '';
        $postType = $config['post_type'] ?? 'product';
        $multiple = $config['multiple'] ?? true;
        $min = $config['min'] ?? 0;
        $max = $config['max'] ?? 0;

        $selectedIds = \is_array($value) ? $value : ($value ? [$value] : []);
        $selectedJson = json_encode($selectedIds);

        $multipleAttr = $multiple ? 'true' : 'false';

        // Determine API base URL based on context
        $currentProject = session('current_project');
        $projectCode = \is_array($currentProject) ? ($currentProject['code'] ?? null) : ($currentProject->code ?? null);
        $apiBase = $projectCode ? "/{$projectCode}/api" : '/api';
        $assetBase = rtrim(asset('/'), '/');

        // Labels based on post type
        $typeLabels = [
            'product' => ['singular' => 'sản phẩm', 'plural' => 'Sản phẩm'],
            'post' => ['singular' => 'bài viết', 'plural' => 'Bài viết'],
            'page' => ['singular' => 'trang', 'plural' => 'Trang'],
        ];
        $typeLabel = $typeLabels[$postType] ?? ['singular' => $postType, 'plural' => ucfirst($postType)];

        $requiredBadge = $this->renderRequiredBadge($required);
        $helpText = $this->renderHelp($help);
        $minMaxInfo = ($min > 0 || $max > 0) ? $this->renderMinMaxInfo($min, $max) : '';

        return <<<HTML
        <div class="mb-4" x-data="relationshipField_{$name}()" x-init="init()">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {$label} {$requiredBadge}
            </label>
            {$minMaxInfo}
            
            <div class="border rounded-lg overflow-hidden bg-white">
                <!-- Search Box -->
                <div class="p-3 bg-gray-50 border-b">
                    <div class="relative">
                        <input type="text" 
                               x-model="search" 
                               @input.debounce.300ms="searchItems()"
                               @focus="showDropdown = true; if (items.length === 0) searchItems()"
                               placeholder="Click hoặc gõ để tìm kiếm {$typeLabel['singular']}..."
                               class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <template x-if="loading">
                            <svg class="absolute right-3 top-2.5 w-5 h-5 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                    </div>
                </div>
                
                <!-- Search Results Dropdown -->
                <div x-show="showDropdown" 
                     x-transition
                     @click.away="showDropdown = false"
                     class="max-h-64 overflow-y-auto border-b">
                    <template x-if="!loading && items.length === 0 && search.length > 0">
                        <div class="text-center py-6 text-gray-500">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm">Không tìm thấy {$typeLabel['singular']} nào</p>
                        </div>
                    </template>
                    <template x-for="item in items" :key="item.id">
                        <div @click="addItem(item)" 
                             class="flex items-center gap-3 p-3 hover:bg-blue-50 cursor-pointer border-b last:border-b-0 transition-colors"
                             :class="{ 'opacity-50 bg-gray-50 cursor-not-allowed': isSelected(item.id) }">
                            <!-- Thumbnail -->
                            <div class="shrink-0">
                                <template x-if="item.image">
                                    <img :src="formatUrl(item.image)" class="w-12 h-12 object-cover rounded-lg border">
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center border">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>
                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="item.title"></p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded" x-text="item.type"></span>
                                    <template x-if="item.sku">
                                        <span class="text-xs text-gray-500">SKU: <span x-text="item.sku"></span></span>
                                    </template>
                                    <template x-if="item.price">
                                        <span class="text-xs font-medium text-green-600" x-text="formatPrice(item.price)"></span>
                                    </template>
                                </div>
                            </div>
                            <!-- Add Button -->
                            <template x-if="!isSelected(item.id)">
                                <div class="shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                            </template>
                            <template x-if="isSelected(item.id)">
                                <div class="shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
                
                <!-- Selected Items -->
                <div class="p-3 bg-white">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                            Đã chọn (<span x-text="selected.length"></span>)
                        </p>
                        <template x-if="selected.length > 0">
                            <button type="button" @click="clearAll()" class="text-xs text-red-500 hover:text-red-700">Xóa tất cả</button>
                        </template>
                    </div>
                    
                    <template x-if="selected.length === 0">
                        <div class="text-center py-6 border-2 border-dashed border-gray-200 rounded-lg">
                            <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="text-sm text-gray-500">Chưa chọn {$typeLabel['singular']} nào</p>
                            <p class="text-xs text-gray-400 mt-1">Tìm kiếm và click để thêm</p>
                        </div>
                    </template>
                    
                    <div class="space-y-2" x-show="selected.length > 0">
                        <template x-for="(item, index) in selected" :key="'sel-' + item.id">
                            <div class="flex items-center gap-3 p-2 bg-blue-50 rounded-lg border border-blue-100 group">
                                <!-- Drag Handle -->
                                <div class="shrink-0 cursor-move text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                    </svg>
                                </div>
                                <!-- Thumbnail -->
                                <template x-if="item.image">
                                    <img :src="formatUrl(item.image)" class="w-10 h-10 object-cover rounded border">
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </template>
                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="item.title"></p>
                                    <div class="flex items-center gap-2">
                                        <template x-if="item.sku">
                                            <span class="text-xs text-gray-500" x-text="'SKU: ' + item.sku"></span>
                                        </template>
                                        <template x-if="item.price">
                                            <span class="text-xs font-medium text-green-600" x-text="formatPrice(item.price)"></span>
                                        </template>
                                    </div>
                                </div>
                                <!-- Remove Button -->
                                <button type="button" @click="removeItem(index)" 
                                        class="shrink-0 w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            <!-- Hidden inputs for form submission -->
            <template x-for="item in selected" :key="'input-' + item.id">
                <input type="hidden" name="{$name}[]" :value="item.id">
            </template>
            
            {$helpText}
            
            <script>
                function relationshipField_{$name}() {
                    return {
                        search: '',
                        items: [],
                        selected: [],
                        loading: false,
                        showDropdown: false,
                        postType: '{$postType}',
                        multiple: {$multipleAttr},
                        min: {$min},
                        max: {$max},
                        initialIds: {$selectedJson},
                        apiBase: '{$apiBase}',
                        
                        getApiUrl(endpoint) {
                            let base = this.apiBase;
                            if (!base || base === '/api') {
                                const parts = window.location.pathname.split('/').filter(Boolean);
                                if (parts.length > 0 && parts[0] !== 'admin' && parts[0] !== 'api') {
                                    base = '/' + parts[0] + '/api';
                                }
                            }
                            return base + endpoint;
                        },
                        
                        init() {
                            this.loadInitialItems();
                            this.searchItems();
                        },
                        
                        formatUrl(url) {
                            if (!url) return '';
                            url = url.toString().trim();
                            if (!url) return '';
                            if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//') || url.startsWith('data:') || url.startsWith('blob:')) return url;
                            return '{$assetBase}/' + url.replace(/^\/+/,'');
                        },
                        
                        formatPrice(price) {
                            if (!price) return '';
                            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
                        },
                        
                        async loadInitialItems() {
                            if (this.initialIds.length === 0) return;
                            
                            try {
                                const response = await fetch(this.getApiUrl('/relationship-field/items?type=' + this.postType + '&ids=' + this.initialIds.join(',')));
                                const data = await response.json();
                                this.selected = data.items || [];
                            } catch (e) {
                                console.error('Error loading initial items:', e);
                            }
                        },
                        
                        async searchItems() {
                            this.loading = true;
                            try {
                                const response = await fetch(this.getApiUrl('/relationship-field/search?type=' + this.postType + '&q=' + encodeURIComponent(this.search)));
                                const data = await response.json();
                                this.items = data.items || [];
                            } catch (e) {
                                console.error('Error searching:', e);
                                this.items = [];
                            }
                            this.loading = false;
                        },
                        
                        isSelected(id) {
                            return this.selected.some(item => item.id === id);
                        },
                        
                        addItem(item) {
                            if (this.isSelected(item.id)) return;
                            if (this.max > 0 && this.selected.length >= this.max) {
                                alert('Đã đạt số lượng tối đa (' + this.max + ')');
                                return;
                            }
                            if (!this.multiple) {
                                this.selected = [item];
                            } else {
                                this.selected.push(item);
                            }
                        },
                        
                        removeItem(index) {
                            this.selected.splice(index, 1);
                        },
                        
                        clearAll() {
                            if (confirm('Xóa tất cả {$typeLabel['singular']} đã chọn?')) {
                                this.selected = [];
                            }
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

    protected function renderMinMaxInfo(int $min, int $max): string
    {
        $text = '';
        if ($min > 0 && $max > 0) {
            $text = "Chọn từ {$min} đến {$max} mục";
        } elseif ($min > 0) {
            $text = "Chọn ít nhất {$min} mục";
        } elseif ($max > 0) {
            $text = "Chọn tối đa {$max} mục";
        }

        return $text ? "<p class=\"text-xs text-gray-500 mb-2\">{$text}</p>" : '';
    }

    public function validate(mixed $value, array $rules = []): bool
    {
        if (empty($value)) {
            return ! \in_array('required', $rules);
        }

        return \is_array($value) || is_numeric($value);
    }

    public function transform(mixed $value): mixed
    {
        if (\is_array($value)) {
            return array_map('intval', $value);
        }

        return $value ? [(int) $value] : [];
    }
}
