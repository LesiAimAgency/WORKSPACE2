<?php

namespace App\Services\FieldTypes;

class DateField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $attributes = $this->getFieldAttributes($config, $value);
        $attributes['type'] = 'date';
        $attributes['value'] = $value ?? $config['default'] ?? '';

        if (isset($config['min_date'])) {
            $attributes['min'] = $config['min_date'];
        }

        if (isset($config['max_date'])) {
            $attributes['max'] = $config['max_date'];
        }

        $fieldHtml = '<input'.$this->renderAttributes($attributes).'>';

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        $defaultRules = ['date'];

        return parent::validate($value, [...$defaultRules, ...$rules]);
    }

    public static function getTypeName(): string
    {
        return 'date';
    }
}
