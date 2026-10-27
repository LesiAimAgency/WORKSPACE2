<?php

namespace App\Services\FieldTypes;

class RangeField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $fieldId = $this->getFieldId($config);
        $name = $config['name'] ?? '';
        $min = $config['min'] ?? 0;
        $max = $config['max'] ?? 100;
        $step = $config['step'] ?? 1;
        $currentValue = $value ?? $config['default'] ?? $min;

        $fieldHtml = '<div class="flex items-center gap-4">';
        $fieldHtml .= "<span class=\"text-sm text-gray-500 w-8 text-right\">{$min}</span>";
        $fieldHtml .= "<input type=\"range\" id=\"{$fieldId}\" name=\"{$name}\" value=\"{$currentValue}\" min=\"{$min}\" max=\"{$max}\" step=\"{$step}\" class=\"flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600\" oninput=\"document.getElementById('{$fieldId}_value').textContent = this.value\">";
        $fieldHtml .= "<span class=\"text-sm text-gray-500 w-8\">{$max}</span>";
        $fieldHtml .= "<span id=\"{$fieldId}_value\" class=\"text-sm font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full min-w-[3rem] text-center\">{$currentValue}</span>";
        $fieldHtml .= '</div>';

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
        return 'range';
    }
}
