<?php

namespace App\Services\FieldTypes;

class UrlField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $attributes = $this->getFieldAttributes($config, $value);
        $attributes['type'] = 'url';
        $attributes['value'] = $value ?? $config['default'] ?? '';
        $attributes['placeholder'] = $config['placeholder'] ?? 'https://example.com';

        $fieldHtml = '<input'.$this->renderAttributes($attributes).'>';

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        // Allow empty values if not required
        if (empty($value)) {
            return ! \in_array('required', $rules);
        }

        // Check if it's a valid URL or a relative path
        if (filter_var($value, FILTER_VALIDATE_URL) !== false) {
            return true;
        }

        // Check if it's a valid relative path (starts with / or #)
        if (preg_match('/^(\/|#)/', $value)) {
            return true;
        }

        $filteredRules = array_filter($rules, fn ($rule) => $rule !== 'url');

        return parent::validate($value, $filteredRules);
    }

    public static function getTypeName(): string
    {
        return 'url';
    }
}
