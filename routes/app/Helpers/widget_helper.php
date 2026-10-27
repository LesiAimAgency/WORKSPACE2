<?php

use App\Models\Widget;
use App\Services\WidgetRenderingService;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Cache;

if (! function_exists('render_widget_area')) {
    function render_widget_area($areaKey, $context = [])
    {
        $renderingService = new WidgetRenderingService;

        return $renderingService->renderArea($areaKey, $context);
    }
}

if (! function_exists('clear_widget_cache')) {
    function clear_widget_cache($areaKey = null)
    {
        $renderingService = new WidgetRenderingService;

        if ($areaKey) {
            Cache::forget("widget_area_{$areaKey}");
            $renderingService->clearCache();
        } else {
            // Clear all widget area caches
            $areas = ['homepage-main', 'about-main', 'sidebar', 'footer', 'header'];
            foreach ($areas as $area) {
                Cache::forget("widget_area_{$area}");
            }

            // Clear widget discovery cache
            WidgetRegistry::clearCache();
            $renderingService->clearCache();
        }
    }
}

if (! function_exists('render_widgets')) {
    function render_widgets($area, $context = [])
    {
        $renderingService = new WidgetRenderingService;

        return $renderingService->renderArea($area, $context);
    }
}

if (! function_exists('render_widget')) {
    function render_widget($type, $settings = [], $variant = 'default', $context = [])
    {
        $renderingService = new WidgetRenderingService;

        return $renderingService->render($type, $settings, $variant, $context);
    }
}

if (! function_exists('render_widget_with_template')) {
    function render_widget_with_template($type, $settings = [], $variant = 'default', $template = null)
    {
        $renderingService = new WidgetRenderingService;

        return $renderingService->renderWithTemplate($type, $settings, $variant, $template);
    }
}

if (! function_exists('get_widget_preview')) {
    function get_widget_preview($type, $settings = [], $variant = 'default')
    {
        try {
            return WidgetRegistry::getPreview($type, $settings, $variant);
        } catch (Exception $e) {
            return '<div class="widget-error">Preview Error: '.htmlspecialchars($e->getMessage()).'</div>';
        }
    }
}

if (! function_exists('get_available_widgets')) {
    function get_available_widgets()
    {
        return WidgetRegistry::getByCategory();
    }
}

if (! function_exists('widget_exists')) {
    function widget_exists($type)
    {
        return WidgetRegistry::exists($type);
    }
}

if (! function_exists('batch_render_widgets')) {
    function batch_render_widgets($widgetConfigs)
    {
        $renderingService = new WidgetRenderingService;

        return $renderingService->batchRender($widgetConfigs);
    }
}

if (! function_exists('render_widget_isolated')) {
    function render_widget_isolated($type, $settings = [], $variant = 'default')
    {
        $renderingService = new WidgetRenderingService;

        return $renderingService->renderIsolated($type, $settings, $variant);
    }
}
