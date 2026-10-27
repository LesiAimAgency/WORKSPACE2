<?php

namespace App\Services\FieldTypes;

use App\Contracts\FieldTypeInterface;

class WysiwygField implements FieldTypeInterface
{
    public static function getTypeName(): string
    {
        return 'wysiwyg';
    }

    public function render(array $config, mixed $value = null): string
    {
        $name = $config['name'] ?? '';
        $label = $config['label'] ?? $name;
        $required = $config['required'] ?? false;
        $help = $config['help'] ?? '';
        $rows = $config['rows'] ?? 10;

        $value = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
        $requiredAttr = $required ? 'required' : '';
        $uniqueId = 'wysiwyg_'.preg_replace('/[^a-zA-Z0-9_]/', '_', $name).'_'.uniqid();

        return <<<HTML
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                {$label} {$this->renderRequiredBadge($required)}
            </label>
            
            <div class="ckeditor-field-wrapper">
                <textarea id="{$uniqueId}" 
                          name="{$name}" 
                          rows="{$rows}"
                          data-editor="ckeditor"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg tinymce-editor ckeditor-editor"
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

        return is_string($value);
    }

    public function transform(mixed $value): mixed
    {
        return $value ? (function_exists('clean') ? clean($value) : $value) : '';
    }
}
