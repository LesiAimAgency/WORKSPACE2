<?php

namespace App\Services\FieldTypes;

use App\Contracts\FieldTypeInterface;

class CkeditorField implements FieldTypeInterface
{
    public static function getTypeName(): string
    {
        return 'ckeditor';
    }

    public function render(array $config, mixed $value = null): string
    {
        $name = $config['name'] ?? '';
        $label = $config['label'] ?? $name;
        $required = $config['required'] ?? false;
        $help = $config['help'] ?? ($config['description'] ?? '');
        $rows = $config['rows'] ?? 4;
        $height = $config['height'] ?? '160px';

        if (is_array($value)) {
            $value = $value['value'] ?? $value['html'] ?? reset($value) ?? '';
        }
        $value = htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
        $requiredAttr = $required ? 'required' : '';
        $uniqueId = 'ckeditor_'.preg_replace('/[^a-zA-Z0-9_]/', '_', $name).'_'.uniqid();

        return <<<HTML
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {$label} {$this->renderRequiredBadge($required)}
            </label>
            
            <div class="ckeditor-field-wrapper" style="min-height: {$height};">
                <textarea id="{$uniqueId}" 
                          name="{$name}" 
                          rows="{$rows}"
                          data-editor="ckeditor"
                          data-height="{$height}"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg ckeditor-editor"
                          {$requiredAttr}>{$value}</textarea>
            </div>
            
            {$this->renderHelp($help)}
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

        return is_string($value) || is_array($value);
    }

    public function transform(mixed $value): mixed
    {
        if (is_array($value)) {
            $value = $value['value'] ?? $value['html'] ?? reset($value) ?? '';
        }

        if (! is_string($value)) {
            return '';
        }

        $trimmed = trim($value);

        return function_exists('sanitize_editor_html') ? sanitize_editor_html($trimmed) : $trimmed;
    }
}
