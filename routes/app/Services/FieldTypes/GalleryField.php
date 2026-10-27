<?php

namespace App\Services\FieldTypes;

class GalleryField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $fieldId = $this->getFieldId($config);
        $name = $config['name'] ?? '';
        $images = \is_array($value) ? $value : [];
        $maxItems = $config['max_items'] ?? 10;
        $assetBase = rtrim(asset('/'), '/');

        // Properly escape JSON for HTML attribute - use htmlspecialchars on the JSON string
        $imagesJson = htmlspecialchars(json_encode($images, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');

        // Use x-init to parse the JSON data instead of inline in x-data
        $fieldHtml = "<div class=\"gallery-field\" data-max-items=\"{$maxItems}\" data-images=\"{$imagesJson}\" x-data=\"{ images: [], maxItems: {$maxItems}, fieldName: '{$name}', formatUrl(url) { if (!url) return ''; url = url.toString().trim(); if (!url) return ''; if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//') || url.startsWith('data:') || url.startsWith('blob:')) return url; return '{$assetBase}/' + url.replace(/^\/+/,''); } }\" x-init=\"images = JSON.parse(\$el.dataset.images || '[]')\" @media-selected.window=\"if (\$event.detail.field === fieldName) { const urls = Array.isArray(\$event.detail.urls) ? \$event.detail.urls : [\$event.detail.urls]; urls.forEach(url => { if (images.length < maxItems) images.push(url); }); }\">";

        // Image container
        $fieldHtml .= '<div class="grid grid-cols-5 gap-3 mb-4">';
        $fieldHtml .= '<template x-for="(image, index) in images" :key="index">';
        $fieldHtml .= '<div class="relative group aspect-square">';
        $fieldHtml .= '<img :src="formatUrl(image)" class="w-full h-full object-cover rounded-lg border-2 border-gray-200">';
        $fieldHtml .= '<div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center gap-1">';
        $fieldHtml .= '<a :href="formatUrl(image)" target="_blank" class="p-1.5 bg-white rounded-full hover:bg-gray-100">';
        $fieldHtml .= '<svg class="w-3 h-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
        $fieldHtml .= '</a>';
        $fieldHtml .= '<button type="button" @click="images.splice(index, 1)" class="p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600">';
        $fieldHtml .= '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
        $fieldHtml .= '</button>';
        $fieldHtml .= '</div>';
        $fieldHtml .= '<div class="absolute top-1 left-1 w-5 h-5 bg-black/50 rounded text-white text-xs flex items-center justify-center" x-text="index + 1"></div>';
        $fieldHtml .= "<input type=\"hidden\" :name=\"'{$name}[' + index + ']'\" :value=\"image\">";
        $fieldHtml .= '</div>';
        $fieldHtml .= '</template>';
        $fieldHtml .= '</div>';

        // Add button - opens media picker
        $fieldHtml .= "<button type=\"button\" @click=\"if (images.length >= maxItems) { alert('Tối đa ' + maxItems + ' ảnh'); return; } \$dispatch('open-media-picker', { field: fieldName, multiple: true })\" class=\"w-full py-4 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition flex items-center justify-center gap-2\">";
        $fieldHtml .= '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
        $fieldHtml .= '<span>Thêm hình ảnh từ thư viện</span>';
        $fieldHtml .= '</button>';

        $fieldHtml .= '</div>';

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        if (! \is_array($value)) {
            return false;
        }

        $maxItems = $this->config['max_items'] ?? 10;
        if (\count($value) > $maxItems) {
            return false;
        }

        foreach ($value as $image) {
            if (! \is_string($image) || empty($image)) {
                return false;
            }
        }

        return parent::validate($value, $rules);
    }

    public static function getTypeName(): string
    {
        return 'gallery';
    }
}
