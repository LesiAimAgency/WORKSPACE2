<?php

namespace App\Services\FieldTypes;

class TextareaField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $attributes = $this->getFieldAttributes($config, $value);
        $attributes['rows'] = $config['rows'] ?? 4;
        $attributes['class'] .= ' resize-y';

        if (isset($config['max_length'])) {
            $attributes['maxlength'] = $config['max_length'];
        }

        if (is_array($value)) {
            $parts = [];
            foreach ($value as $item) {
                if (is_array($item)) {
                    $parts[] = $item['title'] ?? $item['name'] ?? $item['text'] ?? implode(' ', array_filter($item, 'is_scalar'));
                } elseif (is_scalar($item)) {
                    $parts[] = (string) $item;
                }
            }
            $value = implode(', ', array_filter(array_map('trim', $parts)));
        }

        $useEditor = ($config['editor'] ?? true) !== false
            && ($config['ckeditor'] ?? true) !== false
            && ($config['raw'] ?? false) !== true;

        $height = $config['height'] ?? '150px';

        if ($useEditor) {
            $attributes['data-editor'] = 'ckeditor';
            $attributes['data-height'] = $height;
            $attributes['class'] = trim(($attributes['class'] ?? '').' ckeditor-editor ckeditor');

            if (empty($attributes['id'])) {
                $cleanName = preg_replace('/[^a-zA-Z0-9_]/', '_', (string) ($config['name'] ?? 'textarea'));
                $attributes['id'] = 'ckeditor_'.$cleanName.'_'.uniqid();
            }
        }

        $content = htmlspecialchars((string) ($value ?? $config['default'] ?? ''));
        $textareaHtml = '<textarea'.$this->renderAttributes($attributes).">{$content}</textarea>";

        $fieldHtml = $useEditor
            ? "<div class=\"ckeditor-field-wrapper\" style=\"min-height: {$height};\">{$textareaHtml}</div>"
            : $textareaHtml;

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        $defaultRules = ['string'];

        return parent::validate($value, [...$defaultRules, ...$rules]);
    }

    public function transform(mixed $value): mixed
    {
        if (is_array($value)) {
            $value = $value['value'] ?? $value['html'] ?? reset($value) ?? '';
        }

        if (! is_string($value)) {
            return $value;
        }

        $trimmed = trim($value);

        return function_exists('sanitize_editor_html') ? sanitize_editor_html($trimmed) : $trimmed;
    }

    public static function getTypeName(): string
    {
        return 'textarea';
    }
}
