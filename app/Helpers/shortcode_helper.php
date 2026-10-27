<?php

use App\Models\ArchiveTemplate;
use App\Services\ShortcodeService;
use Illuminate\Support\Collection;

if (! function_exists('parse_shortcodes')) {
    /**
     * Parse shortcodes in content
     */
    function parse_shortcodes(string $content): string
    {
        return app(ShortcodeService::class)->parse($content);
    }
}

if (! function_exists('shortcode')) {
    /**
     * Render a single shortcode
     * Usage: shortcode('products', ['limit' => 6, 'columns' => 3])
     */
    function shortcode(string $tag, array $attrs = []): string
    {
        $attrString = collect($attrs)
            ->map(fn ($v, $k) => "{$k}=\"{$v}\"")
            ->implode(' ');

        $shortcodeString = "[{$tag} {$attrString}]";

        return app(ShortcodeService::class)->parse($shortcodeString);
    }
}

if (! function_exists('register_shortcode')) {
    /**
     * Register a custom shortcode
     */
    function register_shortcode(string $tag, callable $callback): void
    {
        app(ShortcodeService::class)->register($tag, $callback);
    }
}

if (! function_exists('render_archive')) {
    /**
     * Render archive template for a content type
     */
    function render_archive(string $type, array $data = [], ?string $templateSlug = null): string
    {
        $template = $templateSlug
            ? ArchiveTemplate::where('slug', $templateSlug)->first()
            : ArchiveTemplate::getDefault($type);

        if (! $template) {
            // Fallback to default view
            $viewPath = "frontend.archives.{$type}";
            if (View::exists($viewPath)) {
                return view($viewPath, $data)->render();
            }

            return '';
        }

        return $template->render($data);
    }
}

if (! function_exists('get_archive_templates')) {
    /**
     * Get all archive templates for a type
     */
    function get_archive_templates(string $type): Collection
    {
        return ArchiveTemplate::where('type', $type)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
