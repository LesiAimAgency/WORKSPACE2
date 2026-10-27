<?php

namespace App\Services\FieldTypes;

class RadioField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $name = $config['name'] ?? '';
        $selectedValue = $value ?? $config['default'] ?? '';
        $options = $config['options'] ?? [];

        $fieldHtml = '<div class="flex flex-wrap items-center gap-2">';

        $index = 0;
        foreach ($options as $optionValue => $optionLabel) {
            if (\is_array($optionLabel)) {
                $optionValue = $optionLabel['value'] ?? $optionValue;
                $optionLabel = $optionLabel['label'] ?? $optionValue;
            }

            $optionId = 'radio_'.$name.'_'.$index;
            $isChecked = ((string) $optionValue === (string) $selectedValue);
            $checkedAttr = $isChecked ? ' checked' : '';

            $fieldHtml .= '<label for="'.$optionId.'" class="relative cursor-pointer select-none group">';
            $fieldHtml .= '<input type="radio" id="'.$optionId.'" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars((string) $optionValue).'" class="peer sr-only"'.$checkedAttr.'>';
            $fieldHtml .= '<div class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-700 shadow-sm transition-all duration-150 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 peer-checked:font-semibold peer-checked:shadow-sm hover:border-gray-300 hover:bg-gray-50">';
            $fieldHtml .= htmlspecialchars((string) $optionLabel);
            $fieldHtml .= '</div>';
            $fieldHtml .= '</label>';

            $index++;
        }

        $fieldHtml .= '</div>';

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        $options = $this->config['options'] ?? [];
        $validValues = [];
        foreach ($options as $key => $option) {
            if (\is_array($option)) {
                $validValues[] = (string) ($option['value'] ?? $key);
            } else {
                $validValues[] = (string) $key;
            }
        }

        if (empty($validValues)) {
            return true;
        }

        return \in_array((string) $value, $validValues, true);
    }

    public static function getTypeName(): string
    {
        return 'radio';
    }
}
