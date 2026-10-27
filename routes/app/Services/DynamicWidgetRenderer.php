<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Widgets\WidgetRegistry;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class DynamicWidgetRenderer
{
    /**
     * Render a widget by its ID
     */
    public function renderById(int $widgetId): string
    {
        return '';
    }

    /**
     * Render a widget instance
     */
    public function render($widget): string
    {
        // First try code-based widget
        if (isset($widget->type) && WidgetRegistry::exists($widget->type)) {
            return $widget->getRenderedContent();
        }

        return $this->renderError('Widget template not found');
    }

    /**
     * Render widget from database template
     */
    public function renderFromTemplate($template, array $settings): string
    {
        // First priority: Check if there's a custom blade view file in folder structure
        // (views/widgets/custom/{type}/view.blade.php)
        $customViewPath = "widgets.custom.{$template->type}.view";

        if (View::exists($customViewPath)) {
            return $this->renderFromCustomView($template, $settings, $customViewPath);
        }

        // Second priority: Check if there's a direct blade file
        // (views/widgets/custom/{type}.blade.php)
        $directViewPath = "widgets.custom.{$template->type}";

        if (View::exists($directViewPath)) {
            return $this->renderFromCustomView($template, $settings, $directViewPath);
        }

        // Third priority: Check if there's a dynamic blade view
        $viewName = "widgets.dynamic.{$template->type}";

        if (View::exists($viewName)) {
            return view($viewName, [
                'settings' => $settings,
                'template' => $template,
            ])->render();
        }

        // Fallback to generic renderer
        return $this->renderGeneric($template, $settings);
    }

    /**
     * Render widget from custom view file (stored in views/widgets/custom/{type}/)
     */
    protected function renderFromCustomView($template, array $settings, string $viewPath): string
    {
        try {
            // Merge default settings with provided settings
            $mergedSettings = array_merge($template->default_settings ?? [], $settings);

            // Prepare helper functions
            $products = fn ($limit = 10) => Product::take($limit)->get();
            $posts = fn ($limit = 10) => Post::take($limit)->get();
            $categories = fn () => Category::all();

            // Render the Blade view
            $html = view($viewPath, [
                'settings' => $mergedSettings,
                'widget' => $template,
                'products' => $products,
                'posts' => $posts,
                'categories' => $categories,
            ])->render();

            // Inject CSS if available (from file)
            $css = $template->getCss();
            if (! empty(trim($css))) {
                $html = "<style>{$css}</style>".$html;
            }

            // Inject JS if available (from file)
            $js = $template->getJs();
            if (! empty(trim($js))) {
                $html .= "<script>{$js}</script>";
            }

            return $html;
        } catch (\Exception $e) {
            return $this->renderError('Template render error: '.$e->getMessage());
        }
    }

    /**
     * Generic renderer for templates without custom views
     */
    protected function renderGeneric($template, array $settings): string
    {
        $fields = $template->config_schema['fields'] ?? [];

        $html = '<div class="widget widget-'.e($template->type).'">';

        foreach ($fields as $field) {
            $value = $settings[$field['name']] ?? $field['default'] ?? '';

            if (empty($value)) {
                continue;
            }

            $html .= $this->renderFieldValue($field, $value);
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render a single field value
     */
    protected function renderFieldValue(array $field, mixed $value): string
    {
        $type = $field['type'] ?? 'text';

        return match ($type) {
            'image' => $this->renderImage($value, $field),
            'gallery' => $this->renderGallery($value, $field),
            'textarea' => $this->renderTextarea($value, $field),
            'url' => $this->renderUrl($value, $field),
            'repeatable' => $this->renderRepeatable($value, $field),
            default => $this->renderText($value, $field),
        };
    }

    protected function renderText(mixed $value, array $field): string
    {
        return '<div class="widget-field widget-field-text">'.e($value).'</div>';
    }

    protected function renderTextarea(mixed $value, array $field): string
    {
        return '<div class="widget-field widget-field-textarea">'.nl2br(e($value)).'</div>';
    }

    protected function renderImage(mixed $value, array $field): string
    {
        if (empty($value)) {
            return '';
        }

        return '<div class="widget-field widget-field-image"><img src="'.e($value).'" alt="'.e($field['label'] ?? '').'" class="max-w-full h-auto"></div>';
    }

    protected function renderGallery(mixed $value, array $field): string
    {
        if (! is_array($value) || empty($value)) {
            return '';
        }

        $html = '<div class="widget-field widget-field-gallery grid grid-cols-3 gap-2">';
        foreach ($value as $image) {
            $html .= '<img src="'.e($image).'" alt="" class="w-full h-auto">';
        }
        $html .= '</div>';

        return $html;
    }

    protected function renderUrl(mixed $value, array $field): string
    {
        if (empty($value)) {
            return '';
        }

        return '<div class="widget-field widget-field-url"><a href="'.e($value).'" class="text-blue-600 hover:underline">'.e($value).'</a></div>';
    }

    protected function renderRepeatable(mixed $value, array $field): string
    {
        if (! is_array($value) || empty($value)) {
            return '';
        }

        $subFields = $field['fields'] ?? [];
        $html = '<div class="widget-field widget-field-repeatable space-y-2">';

        foreach ($value as $item) {
            $html .= '<div class="repeatable-item p-2 border rounded">';
            foreach ($subFields as $subField) {
                $subValue = $item[$subField['name']] ?? '';
                if (! empty($subValue)) {
                    $html .= $this->renderFieldValue($subField, $subValue);
                }
            }
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    protected function renderError(string $message): string
    {
        if (config('app.debug')) {
            return '<div class="widget-error bg-red-50 border border-red-200 text-red-600 p-4 rounded">'.e($message).'</div>';
        }

        return '';
    }

    /**
     * Render all widgets for an area
     */
    public function renderArea(string $area): string
    {
        if (function_exists('render_widgets')) {
            return render_widgets($area);
        }

        $renderingService = new WidgetRenderingService;

        return $renderingService->renderArea($area);
    }

    /**
     * Render a custom widget template (called from WidgetRegistry)
     */
    public function renderCustomWidget($template, array $settings): string
    {
        return $this->renderFromTemplate($template, $settings);
    }
}
