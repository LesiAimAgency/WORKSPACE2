<?php

namespace App\Contracts;

interface FieldTypeInterface
{
    /**
     * Render the field for admin interface
     */
    public function render(array $config, mixed $value = null): string;

    /**
     * Validate field value
     */
    public function validate(mixed $value, array $rules): bool;

    /**
     * Transform field value for storage/processing
     */
    public function transform(mixed $value): mixed;

    /**
     * Get field type name
     */
    public static function getTypeName(): string;
}
