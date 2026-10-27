<?php

namespace App\Services\FieldTypes;

class TextField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $attributes = $this->getFieldAttributes($config, $value);
        $attributes['type'] = 'text';
        $attributes['value'] = $value ?? $config['default'] ?? '';

        if (isset($config['max_length'])) {
            $attributes['maxlength'] = $config['max_length'];
        }

        $fieldHtml = '<input'.$this->renderAttributes($attributes).'>';

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        $defaultRules = ['string'];

        return parent::validate($value, [...$defaultRules, ...$rules]);
    }

    public static function getTypeName(): string
    {
        return 'text';
    }
}
