<?php

namespace App\Services\FieldTypes;

class NumberField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $attributes = $this->getFieldAttributes($config, $value);
        $attributes['type'] = 'number';
        $attributes['value'] = $value ?? $config['default'] ?? '';

        if (isset($config['min'])) {
            $attributes['min'] = $config['min'];
        }

        if (isset($config['max'])) {
            $attributes['max'] = $config['max'];
        }

        if (isset($config['step'])) {
            $attributes['step'] = $config['step'];
        }

        $fieldHtml = '<input'.$this->renderAttributes($attributes).'>';

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        $defaultRules = ['numeric'];

        if (isset($this->config['min'])) {
            $defaultRules[] = 'min:'.$this->config['min'];
        }

        if (isset($this->config['max'])) {
            $defaultRules[] = 'max:'.$this->config['max'];
        }

        return parent::validate($value, [...$defaultRules, ...$rules]);
    }

    public function transform(mixed $value): mixed
    {
        return is_numeric($value) ? (float) $value : $value;
    }

    public static function getTypeName(): string
    {
        return 'number';
    }
}
