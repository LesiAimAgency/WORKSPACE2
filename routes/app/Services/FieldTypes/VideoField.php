<?php

namespace App\Services\FieldTypes;

class VideoField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        // Video field is rendered via Blade template in widget-field.blade.php
        // This render method is only used for non-Livewire contexts
        $attributes = $this->getFieldAttributes($config, $value);
        $currentValue = htmlspecialchars($value ?? $config['default'] ?? '');
        $fieldName = htmlspecialchars($config['name'] ?? '');

        $previewHtml = '';
        if (! empty($value)) {
            $videoSrc = (str_starts_with($value, 'http') || str_starts_with($value, '//') || str_starts_with($value, 'data:')) ? $value : asset($value);
            $videoSrcEscaped = htmlspecialchars($videoSrc, ENT_QUOTES, 'UTF-8');
            $previewHtml = "<div class=\"mb-3\"><video src=\"{$videoSrcEscaped}\" class=\"w-full max-w-md h-auto rounded-lg border-2 border-gray-200 shadow-sm\" controls></video></div>";
        }

        $fieldHtml = '<div class="space-y-3" x-data>';
        $fieldHtml .= $previewHtml;
        $fieldHtml .= '<div class="flex gap-2">';
        $fieldHtml .= "<input type=\"text\" {$this->renderAttributes($attributes)} value=\"{$currentValue}\" placeholder=\"URL video hoặc chọn từ thư viện\" class=\"flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500\">";
        $fieldHtml .= "<button type=\"button\" @click=\"\$dispatch('open-media-picker', { field: '{$fieldName}', multiple: false, mediaType: 'video' })\" class=\"px-4 py-2.5 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition flex items-center gap-2 shadow-sm\">";
        $fieldHtml .= '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>';
        $fieldHtml .= 'Chọn video</button>';
        $fieldHtml .= '</div></div>';

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        // If empty and not required, it's valid
        if (empty($value)) {
            return true;
        }

        // Basic URL validation for video
        if (! \is_string($value)) {
            return false;
        }

        // Check if it's a valid URL or relative path
        if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
            return true;
        }

        // Allow relative paths starting with /
        if (str_starts_with($value, '/')) {
            return true;
        }

        // Allow storage paths
        if (str_starts_with($value, 'storage/')) {
            return true;
        }

        return true;
    }

    public static function getTypeName(): string
    {
        return 'video';
    }
}
