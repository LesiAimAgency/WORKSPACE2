<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class MetadataValidationService
{
    /**
     * Widget metadata schema rules
     */
    protected array $schemaRules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:500',
        'category' => 'required|string|max:100',
        'version' => 'required|string|regex:/^\d+\.\d+\.\d+$/',
        'author' => 'nullable|string|max:255',
        'icon' => 'nullable|string',
        'preview_image' => 'nullable|string',
        'variants' => 'nullable|array',
        'variants.*' => 'string',
        'fields' => 'required|array|min:1',
        'fields.*.name' => 'required|string|regex:/^[a-z_][a-z0-9_]*$/',
        'fields.*.label' => 'required|string|max:255',
        'fields.*.type' => 'required|string|in:text,textarea,image,gallery,select,checkbox,repeatable,nested,url,number,email,date,color,range',
        'fields.*.required' => 'nullable|boolean',
        'fields.*.default' => 'nullable',
        'fields.*.validation' => 'nullable|string',
        'fields.*.help' => 'nullable|string|max:500',
        'fields.*.placeholder' => 'nullable|string|max:255',
        'fields.*.show_if' => 'nullable|array',
        'fields.*.show_if.*' => 'string',
        'fields.*.options' => 'nullable|array',
        'fields.*.options.*' => 'string',
        'fields.*.max_items' => 'nullable|integer|min:1|max:100',
        'fields.*.min_items' => 'nullable|integer|min:0',
        'fields.*.fields' => 'nullable|array', // For repeatable/nested fields
        'settings' => 'nullable|array',
        'settings.cacheable' => 'nullable|boolean',
        'settings.cache_duration' => 'nullable|integer|min:0',
        'settings.permissions' => 'nullable|array',
        'settings.permissions.*' => 'string',
        'settings.dependencies' => 'nullable|array',
        'settings.dependencies.*' => 'string',
    ];

    /**
     * Supported field types
     */
    protected array $supportedFieldTypes = [
        'text',
        'textarea',
        'image',
        'gallery',
        'select',
        'checkbox',
        'repeatable',
        'nested',
        'url',
        'number',
        'email',
        'date',
        'color',
        'range',
    ];

    /**
     * Validate widget metadata against schema
     */
    public function validateMetadata(array $metadata): array
    {
        $validator = Validator::make($metadata, $this->schemaRules);

        if ($validator->fails()) {
            throw new \InvalidArgumentException('Invalid widget metadata: '.$validator->errors()->first());
        }

        // Additional custom validations
        $this->validateFieldTypes($metadata['fields'] ?? []);
        $this->validateFieldDependencies($metadata['fields'] ?? []);
        $this->validateVariants($metadata['variants'] ?? []);

        return $metadata;
    }

    /**
     * Validate field types and their configurations
     */
    protected function validateFieldTypes(array $fields): void
    {
        foreach ($fields as $index => $field) {
            $fieldType = $field['type'] ?? '';

            if (! in_array($fieldType, $this->supportedFieldTypes)) {
                throw new \InvalidArgumentException("Unsupported field type '{$fieldType}' in field {$index}");
            }

            // Type-specific validations
            switch ($fieldType) {
                case 'select':
                    if (empty($field['options']) || ! is_array($field['options'])) {
                        throw new \InvalidArgumentException("Select field '{$field['name']}' must have options array");
                    }
                    break;

                case 'repeatable':
                    if (empty($field['fields']) || ! is_array($field['fields'])) {
                        throw new \InvalidArgumentException("Repeatable field '{$field['name']}' must have fields array");
                    }
                    // Recursively validate nested fields
                    $this->validateFieldTypes($field['fields']);
                    break;

                case 'nested':
                    if (empty($field['fields']) || ! is_array($field['fields'])) {
                        throw new \InvalidArgumentException("Nested field '{$field['name']}' must have fields array");
                    }
                    // Recursively validate nested fields
                    $this->validateFieldTypes($field['fields']);
                    break;

                case 'range':
                    if (! isset($field['min']) || ! isset($field['max'])) {
                        throw new \InvalidArgumentException("Range field '{$field['name']}' must have min and max values");
                    }
                    if ($field['min'] >= $field['max']) {
                        throw new \InvalidArgumentException("Range field '{$field['name']}' min value must be less than max value");
                    }
                    break;

                case 'gallery':
                    if (isset($field['max_items']) && $field['max_items'] < 1) {
                        throw new \InvalidArgumentException("Gallery field '{$field['name']}' max_items must be at least 1");
                    }
                    break;
            }
        }
    }

    /**
     * Validate field dependencies and references
     */
    protected function validateFieldDependencies(array $fields): void
    {
        $fieldNames = array_column($fields, 'name');

        foreach ($fields as $field) {
            // Check for duplicate field names
            $nameCount = array_count_values($fieldNames)[$field['name']] ?? 0;
            if ($nameCount > 1) {
                throw new \InvalidArgumentException("Duplicate field name '{$field['name']}'");
            }

            // Validate conditional field dependencies
            if (isset($field['depends_on'])) {
                if (! in_array($field['depends_on'], $fieldNames)) {
                    throw new \InvalidArgumentException("Field '{$field['name']}' depends on non-existent field '{$field['depends_on']}'");
                }
            }
        }
    }

    /**
     * Validate widget variants
     */
    protected function validateVariants(array $variants): void
    {
        if (empty($variants)) {
            return;
        }

        // Ensure 'default' variant exists
        if (! array_key_exists('default', $variants)) {
            throw new \InvalidArgumentException("Widget must have a 'default' variant");
        }

        // Validate variant names
        foreach (array_keys($variants) as $variantName) {
            if (! preg_match('/^[a-z][a-z0-9_]*$/', $variantName)) {
                throw new \InvalidArgumentException("Invalid variant name '{$variantName}'. Must be lowercase with underscores only.");
            }
        }
    }

    /**
     * Validate field value against field configuration
     */
    public function validateFieldValue(array $fieldConfig, mixed $value): bool
    {
        $fieldName = $fieldConfig['name'];
        $fieldType = $fieldConfig['type'];
        $rules = [];

        // Required validation
        if ($fieldConfig['required'] ?? false) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Type-specific validation
        switch ($fieldType) {
            case 'text':
                $rules[] = 'string';
                if (isset($fieldConfig['max_length'])) {
                    $rules[] = 'max:'.$fieldConfig['max_length'];
                }
                break;

            case 'textarea':
                $rules[] = 'string';
                break;

            case 'email':
                $rules[] = 'email';
                break;

            case 'url':
                $rules[] = 'url';
                break;

            case 'number':
                $rules[] = 'numeric';
                if (isset($fieldConfig['min'])) {
                    $rules[] = 'min:'.$fieldConfig['min'];
                }
                if (isset($fieldConfig['max'])) {
                    $rules[] = 'max:'.$fieldConfig['max'];
                }
                break;

            case 'date':
                $rules[] = 'date';
                break;

            case 'image':
                $rules[] = 'string'; // Assuming we store image paths/URLs
                break;

            case 'gallery':
                $rules[] = 'array';
                if (isset($fieldConfig['max_items'])) {
                    $rules[] = 'max:'.$fieldConfig['max_items'];
                }
                if (isset($fieldConfig['min_items'])) {
                    $rules[] = 'min:'.$fieldConfig['min_items'];
                }
                break;

            case 'select':
                if (isset($fieldConfig['options']) && is_array($fieldConfig['options'])) {
                    $rules[] = 'in:'.implode(',', array_keys($fieldConfig['options']));
                }
                break;

            case 'checkbox':
                $rules[] = 'boolean';
                break;

            case 'repeatable':
                $rules[] = 'array';
                if (isset($fieldConfig['max_items'])) {
                    $rules[] = 'max:'.$fieldConfig['max_items'];
                }
                if (isset($fieldConfig['min_items'])) {
                    $rules[] = 'min:'.$fieldConfig['min_items'];
                }
                break;

            case 'color':
                $rules[] = 'string';
                // Allow hex colors, gradients, and CSS color values
                $rules[] = 'regex:/^(#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})|linear-gradient|radial-gradient|rgba?|hsla?|[a-z]+).*$/i';
                break;

            case 'range':
                $rules[] = 'numeric';
                if (isset($fieldConfig['min'])) {
                    $rules[] = 'min:'.$fieldConfig['min'];
                }
                if (isset($fieldConfig['max'])) {
                    $rules[] = 'max:'.$fieldConfig['max'];
                }
                break;
        }

        // Custom validation rules
        if (! empty($fieldConfig['validation'])) {
            $customRules = explode('|', $fieldConfig['validation']);
            $rules = array_merge($rules, $customRules);
        }

        $validator = Validator::make(
            [$fieldName => $value],
            [$fieldName => implode('|', array_unique($rules))]
        );

        return ! $validator->fails();
    }

    /**
     * Get validation errors for field value
     */
    public function getFieldValidationErrors(array $fieldConfig, mixed $value): array
    {
        try {
            $this->validateFieldValue($fieldConfig, $value);

            return [];
        } catch (\Exception $e) {
            return [$e->getMessage()];
        }
    }

    /**
     * Get supported field types
     */
    public function getSupportedFieldTypes(): array
    {
        return $this->supportedFieldTypes;
    }

    /**
     * Get schema rules
     */
    public function getSchemaRules(): array
    {
        return $this->schemaRules;
    }
}
