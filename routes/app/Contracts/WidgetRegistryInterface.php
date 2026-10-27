<?php

namespace App\Contracts;

interface WidgetRegistryInterface
{
    /**
     * Discover widgets automatically
     */
    public static function discover(): array;

    /**
     * Register a widget manually
     */
    public static function register(string $type, string $class): void;

    /**
     * Get widget class by type
     */
    public static function get(string $type): ?string;

    /**
     * Get widgets organized by category
     */
    public static function getByCategory(): array;

    /**
     * Render widget with settings and variant
     */
    public static function render(string $type, array $settings = [], string $variant = 'default'): string;

    /**
     * Check if widget type exists
     */
    public static function exists(string $type): bool;

    /**
     * Get all widget types
     */
    public static function getTypes(): array;
}
