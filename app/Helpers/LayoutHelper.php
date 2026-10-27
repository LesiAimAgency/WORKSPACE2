<?php

if (! function_exists('get_theme_layout')) {
    function get_theme_layout($type = 'page')
    {
        $layoutSettings = setting('theme_option_layout', []);

        if (is_string($layoutSettings)) {
            $layoutSettings = json_decode($layoutSettings, true) ?: [];
        }

        return $layoutSettings["{$type}_layout"] ?? 'full-width';
    }
}

if (! function_exists('get_layout_config')) {
    function get_layout_config($layoutType)
    {
        $configs = [
            'full-width' => [
                'sidebar' => false,
                'banner' => false,
                'container_class' => 'container mx-auto',
                'content_class' => 'w-full',
                'grid_class' => 'grid grid-cols-1',
            ],
            'full-width-banner' => [
                'sidebar' => false,
                'banner' => true,
                'banner_style' => 'style-2',
                'container_class' => 'container mx-auto',
                'content_class' => 'w-full',
                'grid_class' => 'grid grid-cols-1',
            ],
            'sidebar-left' => [
                'sidebar' => 'left',
                'banner' => false,
                'content_frame' => true,
                'container_class' => 'container mx-auto',
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'grid_class' => 'grid grid-cols-1 lg:grid-cols-3 gap-8',
            ],
            'sidebar-left-1' => [
                'sidebar' => 'left',
                'banner' => 'content-only',
                'banner_style' => 'style-1',
                'content_frame' => true,
                'container_class' => 'container mx-auto',
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'grid_class' => 'grid grid-cols-1 lg:grid-cols-3 gap-8',
            ],
            'sidebar-left-2' => [
                'sidebar' => 'left',
                'banner' => 'full-width',
                'banner_style' => 'style-2',
                'content_frame' => true,
                'container_class' => 'container mx-auto',
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'grid_class' => 'grid grid-cols-1 lg:grid-cols-3 gap-8',
            ],
            'sidebar-right' => [
                'sidebar' => 'right',
                'banner' => false,
                'content_frame' => true,
                'container_class' => 'container mx-auto',
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'grid_class' => 'grid grid-cols-1 lg:grid-cols-3 gap-8',
            ],
            'sidebar-right-1' => [
                'sidebar' => 'right',
                'banner' => 'content-only',
                'banner_style' => 'style-1',
                'content_frame' => true,
                'container_class' => 'container mx-auto',
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'grid_class' => 'grid grid-cols-1 lg:grid-cols-3 gap-8',
            ],
            'sidebar-right-2' => [
                'sidebar' => 'right',
                'banner' => 'full-width',
                'banner_style' => 'style-2',
                'content_frame' => true,
                'container_class' => 'container mx-auto',
                'content_class' => 'w-full lg:w-2/3',
                'sidebar_class' => 'w-full lg:w-1/3',
                'grid_class' => 'grid grid-cols-1 lg:grid-cols-3 gap-8',
            ],
        ];

        return $configs[$layoutType] ?? $configs['full-width'];
    }
}

if (! function_exists('render_widgets')) {
    function render_widgets($position = 'sidebar')
    {
        // This function would render widgets for the specified position
        // Implementation depends on your widget system
        return '';
    }
}
