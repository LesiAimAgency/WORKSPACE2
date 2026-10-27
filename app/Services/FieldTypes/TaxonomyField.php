<?php

namespace App\Services\FieldTypes;

use App\Contracts\FieldTypeInterface;

class TaxonomyField implements FieldTypeInterface
{
    public static function getTypeName(): string
    {
        return 'taxonomy';
    }

    public function render(array $config, mixed $value = null): string
    {
        $name = $config['name'] ?? '';
        $label = $config['label'] ?? $name;
        $required = $config['required'] ?? false;
        $help = $config['help'] ?? '';
        $taxonomy = $config['taxonomy'] ?? 'category'; // category, brand, tag
        $multiple = $config['multiple'] ?? false;
        $displayType = $config['display'] ?? 'select'; // select, checkbox, radio

        $selectedIds = is_array($value) ? $value : ($value ? [$value] : []);
        $selectedJson = json_encode($selectedIds);
        $multipleAttr = $multiple ? 'multiple' : '';
        $requiredAttr = $required ? 'required' : '';

        // Determine API base URL based on context
        $projectCode = session('current_project')['code'] ?? null;
        $apiBase = $projectCode ? "/{$projectCode}/api" : '/api';

        return <<<HTML
        <div class="mb-4" x-data="{
            items: [],
            loading: true,
            taxonomy: '{$taxonomy}',
            selectedIds: {$selectedJson},
            apiBase: '{$apiBase}',
            
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
                    this.items = [];
                }
                this.loading = false;
            },
            
            isSelected(id) {
                return this.selectedIds.includes(id) || this.selectedIds.includes(String(id));
            }
        }">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {$label} {$this->renderRequiredBadge($required)}
            </label>
            
            <template x-if="loading">
                <div class="p-3 bg-gray-50 rounded-lg text-gray-500">Đang tải danh mục...</div>
            </template>
            
            <template x-if="!loading">
                <select name="{$name}{$this->getNameSuffix($multiple)}" 
                        {$multipleAttr} 
                        {$requiredAttr}
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Chọn --</option>
                    <template x-for="item in items" :key="item.id">
                        <option :value="item.id" 
                                :selected="isSelected(item.id)"
                                x-text="item.name + (item.count ? ' (' + item.count + ')' : '')">
                        </option>
                    </template>
                </select>
            </template>
            
            {$this->renderHelp($help)}
        </div>
        HTML;
    }

    protected function getNameSuffix(bool $multiple): string
    {
        return $multiple ? '[]' : '';
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

        return is_numeric($value) || is_array($value);
    }

    public function transform(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map('intval', $value);
        }

        return $value ? (int) $value : null;
    }
}
