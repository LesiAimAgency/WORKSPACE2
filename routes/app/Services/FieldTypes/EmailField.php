<?php

namespace App\Services\FieldTypes;

class EmailField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $attributes = $this->getFieldAttributes($config, $value);
        $attributes['type'] = 'email';
        $attributes['value'] = $value ?? $config['default'] ?? '';
        $attributes['placeholder'] = $config['placeholder'] ?? 'email@example.com';

        $fieldHtml = '<input'.$this->renderAttributes($attributes).'>';

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        $defaultRules = ['email'];

        return parent::validate($value, [...$defaultRules, ...$rules]);
    }

    public static function getTypeName(): string
    {
        return 'email';
    }
}
