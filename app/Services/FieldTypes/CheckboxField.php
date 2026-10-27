<?php

namespace App\Services\FieldTypes;

class CheckboxField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $fieldId = $this->getFieldId($config);
        $name = $config['name'] ?? '';

        $isChecked = $value || ($config['default'] ?? false);
        $checkedAttr = $isChecked ? ' checked' : '';

        $fieldHtml = '<div class="flex items-center">';
        $fieldHtml .= "<input type=\"checkbox\" id=\"{$fieldId}\" name=\"{$name}\" value=\"1\" class=\"w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer\"{$checkedAttr}>";

        if (isset($config['label'])) {
            $fieldHtml .= "<label for=\"{$fieldId}\" class=\"ml-3 text-sm font-medium text-gray-700 cursor-pointer\">{$config['label']}</label>";
        }

        $fieldHtml .= '</div>';

        $help = $this->renderHelp($config);

        return "<div class=\"mb-6\">{$fieldHtml}{$help}</div>";
    }

    public function validate(mixed $value, array $rules): bool
    {
        $defaultRules = ['boolean'];

        return parent::validate($value, [...$defaultRules, ...$rules]);
    }

    public function transform(mixed $value): mixed
    {
        return (bool) $value;
    }

    public static function getTypeName(): string
    {
        return 'checkbox';
    }
}
