<?php

namespace App\Services\FieldTypes;

class ImageField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $fieldId = $this->getFieldId($config);
        $name = $config['name'] ?? '';
        $strValue = is_array($value) ? ($value['url'] ?? (is_string(reset($value)) ? reset($value) : '')) : (string) ($value ?? '');
        $escapedValue = htmlspecialchars($strValue, ENT_QUOTES, 'UTF-8');
        $assetBase = rtrim(asset('/'), '/');

        // Use data attribute and x-init to safely pass the value
        $fieldHtml = "<div class=\"space-y-3\" data-image-url=\"{$escapedValue}\" x-data=\"{ imageUrl: '', fieldName: '{$name}', formatUrl(url) { if (!url) return ''; url = url.toString().trim(); if (!url) return ''; if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//') || url.startsWith('data:') || url.startsWith('blob:')) return url; return '{$assetBase}/' + url.replace(/^\/+/,''); } }\" x-init=\"imageUrl = \$el.dataset.imageUrl || ''\" @media-selected.window=\"if (\$event.detail.field === fieldName) { imageUrl = Array.isArray(\$event.detail.urls) ? \$event.detail.urls[0] : \$event.detail.urls; \$nextTick(() => { const inp = document.getElementById('{$fieldId}'); if (inp) { inp.value = imageUrl; inp.dispatchEvent(new Event('input', { bubbles: true })); } }); }\" id=\"{$fieldId}_wrapper\">";

        // Show current image if exists
        $fieldHtml .= '<div x-show="imageUrl" class="relative inline-block group">';
        $fieldHtml .= '<img :src="formatUrl(imageUrl)" alt="Current image" class="max-w-xs max-h-40 object-cover rounded-lg border-2 border-gray-200 shadow-sm">';
        $fieldHtml .= '<div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center gap-2">';
        $fieldHtml .= '<a :href="formatUrl(imageUrl)" target="_blank" class="p-2 bg-white rounded-full hover:bg-gray-100">';
        $fieldHtml .= '<svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
        $fieldHtml .= '</a>';
        $fieldHtml .= "<button type=\"button\" @click=\"imageUrl = ''\" class=\"p-2 bg-red-500 text-white rounded-full hover:bg-red-600\">";
        $fieldHtml .= '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
        $fieldHtml .= '</button>';
        $fieldHtml .= '</div>';
        $fieldHtml .= '</div>';

        // Hidden input for form submission
        $fieldHtml .= "<input type=\"hidden\" name=\"{$name}\" :value=\"imageUrl\" value=\"{$escapedValue}\" id=\"{$fieldId}\">";

        // Input URL + Media Picker Button
        $fieldHtml .= '<div class="flex gap-2">';
        $fieldHtml .= "<input type=\"text\" name=\"{$name}_url_input\" x-model=\"imageUrl\" value=\"{$escapedValue}\" @change=\"imageUrl = \$event.target.value; const inp = document.getElementById('{$fieldId}'); if (inp) { inp.value = imageUrl; inp.dispatchEvent(new Event('input', { bubbles: true })); }\" @input=\"imageUrl = \$event.target.value; const inp = document.getElementById('{$fieldId}'); if (inp) { inp.value = imageUrl; }\" placeholder=\"URL hình ảnh hoặc chọn từ thư viện\" class=\"flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition\">";
        $fieldHtml .= "<button type=\"button\" @click=\"\$dispatch('open-media-picker', { field: fieldName, multiple: false })\" class=\"px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition flex items-center gap-2 shadow-sm\">";
        $fieldHtml .= '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
        $fieldHtml .= 'Chọn ảnh';
        $fieldHtml .= '</button>';
        $fieldHtml .= '</div>';

        $fieldHtml .= '</div>';

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        $defaultRules = \is_string($value) ? ['string'] : ['image', 'max:2048'];

        return parent::validate($value, [...$defaultRules, ...$rules]);
    }

    public function transform(mixed $value): mixed
    {
        return $value;
    }

    public static function getTypeName(): string
    {
        return 'image';
    }
}
