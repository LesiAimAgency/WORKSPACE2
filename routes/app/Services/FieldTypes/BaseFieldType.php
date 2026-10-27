<?php

namespace App\Services\FieldTypes;

use App\Contracts\FieldTypeInterface;
use Illuminate\Support\Facades\Validator;

abstract class BaseFieldType implements FieldTypeInterface
{
    protected array $config;

    protected mixed $value;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Render the field for admin interface
     */
    abstract public function render(array $config, mixed $value = null): string;

    /**
     * Validate field value
     */
    public function validate(mixed $value, array $rules): bool
    {
        $validator = Validator::make(
            ['field' => $value],
            ['field' => implode('|', $rules)]
        );

        return ! $validator->fails();
    }

    /**
     * Transform field value for storage/processing
     */
    public function transform(mixed $value): mixed
    {
        return $value;
    }

    /**
     * Get field type name
     */
    abstract public static function getTypeName(): string;

    /**
     * Generate field ID
     */
    protected function getFieldId(array $config): string
    {
        return 'field_'.($config['name'] ?? 'unnamed');
    }

    /**
     * Generate field attributes
     */
    protected function getFieldAttributes(array $config, mixed $value = null): array
    {
        $baseClasses = 'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors';

        $attributes = [
            'id' => $this->getFieldId($config),
            'name' => $config['name'] ?? '',
            'class' => $baseClasses.' '.($config['class'] ?? ''),
        ];

        if (isset($config['placeholder'])) {
            $attributes['placeholder'] = $config['placeholder'];
        }

        if ($config['required'] ?? false) {
            $attributes['required'] = 'required';
        }

        if (isset($config['readonly']) && $config['readonly']) {
            $attributes['readonly'] = 'readonly';
            $attributes['class'] .= ' bg-gray-100 cursor-not-allowed';
        }

        return $attributes;
    }

    /**
     * Render field attributes as string
     */
    protected function renderAttributes(array $attributes): string
    {
        $html = '';
        foreach ($attributes as $key => $value) {
            if ($value === true || $value === $key) {
                $html .= " {$key}";
            } elseif ($value !== false && $value !== null) {
                $html .= " {$key}=\"".htmlspecialchars($value).'"';
            }
        }

        return $html;
    }

    /**
     * Render field label
     */
    protected function renderLabel(array $config): string
    {
        if (! isset($config['label'])) {
            return '';
        }

        $required = ($config['required'] ?? false) ? ' <span class="text-red-500">*</span>' : '';
        $fieldId = $this->getFieldId($config);

        return "<label for=\"{$fieldId}\" class=\"block text-sm font-medium text-gray-700 mb-2\">{$config['label']}{$required}</label>";
    }

    /**
     * Render field help text
     */
    protected function renderHelp(array $config): string
    {
        if (! isset($config['help'])) {
            return '';
        }

        return "<p class=\"mt-2 text-xs text-gray-500\">{$config['help']}</p>";
    }

    /**
     * Render complete field wrapper
     */
    protected function renderFieldWrapper(array $config, string $fieldHtml): string
    {
        $label = $this->renderLabel($config);
        $help = $this->renderHelp($config);

        // Conditional display support
        $dataAttrs = '';
        if (isset($config['show_if'])) {
            $showIfJson = htmlspecialchars(json_encode($config['show_if']), ENT_QUOTES, 'UTF-8');
            $dataAttrs = ' data-show-if=\''.$showIfJson.'\' style="display:none"';
        }

        return "<div class=\"mb-6 widget-field\"{$dataAttrs}>{$label}{$fieldHtml}{$help}</div>";
    }
}
