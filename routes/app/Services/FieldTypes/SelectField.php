<?php

namespace App\Services\FieldTypes;

class SelectField extends BaseFieldType
{
    public function render(array $config, mixed $value = null): string
    {
        $attributes = $this->getFieldAttributes($config, $value);
        $isMultiple = (bool) ($config['multiple'] ?? false);
        $selectedValue = $value ?? $config['default'] ?? ($isMultiple ? [] : '');
        if ($isMultiple && \is_string($selectedValue)) {
            $selectedValue = array_filter(array_map('trim', explode('|', $selectedValue)));
        }

        // Add class 'select2-enable' for Select2 integration
        $attributes['class'] = trim(($attributes['class'] ?? '') . ' select2-enable');

        if ($isMultiple) {
            $attributes['multiple'] = 'multiple';
            if (! str_ends_with($attributes['name'] ?? '', '[]')) {
                $attributes['name'] = ($attributes['name'] ?? '').'[]';
            }
            $attributes['class'] .= ' select2-multiple';
        } else {
            $attributes['class'] .= ' appearance-none bg-white select2-single';
        }

        $options = $config['options'] ?? [];
        $optionsHtml = '';

        // Add empty option if not required and not multiple
        if (! $isMultiple && ! ($config['required'] ?? false)) {
            $placeholder = $config['placeholder'] ?? '-- Chọn --';
            $optionsHtml .= '<option value="">' . htmlspecialchars($placeholder) . '</option>';
        }

        foreach ($options as $optionValue => $optionLabel) {
            // Support both key-value format and array format
            if (\is_array($optionLabel)) {
                $optionValue = $optionLabel['value'] ?? $optionValue;
                $optionLabel = $optionLabel['label'] ?? $optionValue;
            }
            if ($isMultiple) {
                $selected = in_array((string) $optionValue, array_map('strval', (array) $selectedValue), true) ? ' selected' : '';
            } else {
                $selected = ((string) $optionValue === (string) $selectedValue) ? ' selected' : '';
            }
            $optionsHtml .= '<option value="'.htmlspecialchars((string) $optionValue)."\"{$selected}>".htmlspecialchars((string) $optionLabel).'</option>';
        }

        if ($isMultiple) {
            $fieldHtml = '<div class="select2-multi-wrapper space-y-1.5">';
            $fieldHtml .= '<select'.$this->renderAttributes($attributes).">{$optionsHtml}</select>";
            $fieldHtml .= '<div class="flex items-center justify-between text-[11px] text-gray-500 mt-1">';
            $fieldHtml .= '<span class="flex items-center gap-1.5 text-gray-500 font-medium">';
            $fieldHtml .= '<svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>';
            $fieldHtml .= 'Gõ tên để tìm kiếm &amp; bấm chọn vào danh sách';
            $fieldHtml .= '</span>';
            $fieldHtml .= '<button type="button" onclick="const sel = $(this).closest(\'.select2-multi-wrapper\').find(\'select\'); sel.val(null).trigger(\'change\');" class="text-xs text-red-500 hover:text-red-700 hover:underline font-medium">Bỏ chọn tất cả</button>';
            $fieldHtml .= '</div>';
            $fieldHtml .= '</div>';
        } else {
            $fieldHtml = '<div class="select2-single-wrapper relative">';
            $fieldHtml .= '<select'.$this->renderAttributes($attributes).">{$optionsHtml}</select>";
            $fieldHtml .= '</div>';
        }

        return $this->renderFieldWrapper($config, $fieldHtml);
    }

    public function validate(mixed $value, array $rules): bool
    {
        // If value is empty and not required, it's valid
        if (empty($value)) {
            return true;
        }

        // Get options from config if available
        $options = $this->config['options'] ?? [];

        // Support both key-value format ['100vh' => 'Full Screen'] and array format [['value' => '100vh', 'label' => 'Full Screen']]
        $validValues = [];
        foreach ($options as $key => $option) {
            if (\is_array($option)) {
                $validValues[] = (string) ($option['value'] ?? $key);
            } else {
                $validValues[] = (string) $key;
            }
        }

        // Filter out empty placeholder options when checking if any valid options exist
        $nonEmptyOptions = array_filter($validValues, fn ($v) => $v !== '');

        // If no options or only empty placeholder option defined, skip strict option validation
        if (empty($nonEmptyOptions)) {
            return true;
        }

        if (\is_array($value)) {
            foreach ($value as $v) {
                if ($v !== '' && ! \in_array((string) $v, $validValues, true)) {
                    return false;
                }
            }

            return true;
        }

        return \in_array((string) $value, $validValues, true);
    }

    /**
     * Set config for validation
     */
    public function setConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }

    public static function getTypeName(): string
    {
        return 'select';
    }
}
