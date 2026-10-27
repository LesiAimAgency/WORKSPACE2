<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\Product;

class ShortcodeService
{
    protected array $shortcodes = [];

    public function __construct()
    {
        $this->registerDefaultShortcodes();
    }

    /**
     * Register default shortcodes
     */
    protected function registerDefaultShortcodes(): void
    {
        // Widget shortcode: [widget type="hero" title="Hello"]
        $this->register('widget', function ($attrs) {
            $type = $attrs['type'] ?? '';
            if (! $type) {
                return '';
            }

            unset($attrs['type']);

            return render_widget($type, $attrs);
        });

        // Products shortcode: [products limit="6" category="1" columns="3"]
        $this->register('products', function ($attrs) {
            return $this->renderProducts($attrs);
        });

        // Posts shortcode: [posts limit="4" category="news"]
        $this->register('posts', function ($attrs) {
            return $this->renderPosts($attrs);
        });

        // Categories shortcode: [categories type="product" limit="6"]
        $this->register('categories', function ($attrs) {
            return $this->renderCategories($attrs);
        });

        // Button shortcode: [button url="/contact" text="Liên hệ" style="primary"]
        $this->register('button', function ($attrs) {
            return $this->renderButton($attrs);
        });

        // Image shortcode: [image src="/path/to/image.jpg" alt="Description"]
        $this->register('image', function ($attrs) {
            return $this->renderImage($attrs);
        });

        // Gallery shortcode: [gallery ids="1,2,3" columns="3"]
        $this->register('gallery', function ($attrs) {
            return $this->renderGallery($attrs);
        });

        // Contact form shortcode: [contact_form]
        $this->register('contact_form', function ($attrs) {
            return view('components.shortcodes.contact-form', $attrs)->render();
        });

        // Map shortcode: [map lat="10.123" lng="106.456" zoom="15"]
        $this->register('map', function ($attrs) {
            return $this->renderMap($attrs);
        });

        // Archive shortcode: [archive type="product" template="grid"]
        $this->register('archive', function ($attrs) {
            return $this->renderArchive($attrs);
        });
    }

    /**
     * Register a shortcode
     */
    public function register(string $tag, callable $callback): void
    {
        $this->shortcodes[$tag] = $callback;
    }

    /**
     * Parse and render shortcodes in content
     */
    public function parse(string $content): string
    {
        // Pattern: [shortcode attr="value" attr2="value2"]
        $pattern = '/\[(\w+)([^\]]*)\]/';

        return preg_replace_callback($pattern, function ($matches) {
            $tag = $matches[1];
            $attrString = $matches[2];

            if (! isset($this->shortcodes[$tag])) {
                return $matches[0]; // Return original if shortcode not found
            }

            $attrs = $this->parseAttributes($attrString);

            try {
                return call_user_func($this->shortcodes[$tag], $attrs);
            } catch (\Exception $e) {
                if (config('app.debug')) {
                    return "<!-- Shortcode Error [{$tag}]: {$e->getMessage()} -->";
                }

                return '';
            }
        }, $content);
    }

    /**
     * Parse shortcode attributes
     */
    protected function parseAttributes(string $attrString): array
    {
        $attrs = [];

        // Pattern: attr="value" or attr='value' or attr=value
        preg_match_all('/(\w+)=["\']?([^"\'>\s]+)["\']?/', $attrString, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $attrs[$match[1]] = $match[2];
        }

        return $attrs;
    }

    /**
     * Render products shortcode
     */
    protected function renderProducts(array $attrs): string
    {
        $limit = (int) ($attrs['limit'] ?? 6);
        $columns = (int) ($attrs['columns'] ?? 3);
        $categoryId = $attrs['category'] ?? null;
        $orderBy = $attrs['orderby'] ?? 'created_at';
        $order = $attrs['order'] ?? 'desc';
        $template = $attrs['template'] ?? 'grid';

        $query = Product::query()
            ->where('status', 'published')
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy($orderBy, $order)
            ->limit($limit);

        $products = $query->get();

        return view("components.shortcodes.products-{$template}", [
            'products' => $products,
            'columns' => $columns,
            'attrs' => $attrs,
        ])->render();
    }

    /**
     * Render posts shortcode
     */
    protected function renderPosts(array $attrs): string
    {
        $limit = (int) ($attrs['limit'] ?? 4);
        $columns = (int) ($attrs['columns'] ?? 2);
        $category = $attrs['category'] ?? null;
        $postType = $attrs['type'] ?? 'post';
        $template = $attrs['template'] ?? 'grid';

        $query = Post::query()
            ->where('status', 'published')
            ->where('post_type', $postType)
            ->when($category, fn ($q) => $q->whereHas('categories', fn ($c) => $c->where('slug', $category)))
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        $posts = $query->get();

        return view("components.shortcodes.posts-{$template}", [
            'posts' => $posts,
            'columns' => $columns,
            'attrs' => $attrs,
        ])->render();
    }

    /**
     * Render categories shortcode
     */
    protected function renderCategories(array $attrs): string
    {
        $type = $attrs['type'] ?? 'product';
        $limit = (int) ($attrs['limit'] ?? 6);
        $template = $attrs['template'] ?? 'grid';

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();

        return view("components.shortcodes.categories-{$template}", [
            'categories' => $categories,
            'attrs' => $attrs,
        ])->render();
    }

    /**
     * Render button shortcode
     */
    protected function renderButton(array $attrs): string
    {
        $url = $attrs['url'] ?? '#';
        $text = $attrs['text'] ?? 'Click here';
        $style = $attrs['style'] ?? 'primary';
        $target = $attrs['target'] ?? '_self';
        $icon = $attrs['icon'] ?? '';

        $classes = match ($style) {
            'primary' => 'bg-blue-600 hover:bg-blue-700 text-white',
            'secondary' => 'bg-gray-600 hover:bg-gray-700 text-white',
            'outline' => 'border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white',
            'danger' => 'bg-red-600 hover:bg-red-700 text-white',
            default => 'bg-blue-600 hover:bg-blue-700 text-white',
        };

        return "<a href=\"{$url}\" target=\"{$target}\" class=\"inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium transition {$classes}\">{$icon}{$text}</a>";
    }

    /**
     * Render image shortcode
     */
    protected function renderImage(array $attrs): string
    {
        $src = $attrs['src'] ?? '';
        $alt = $attrs['alt'] ?? '';
        $class = $attrs['class'] ?? 'w-full h-auto rounded-lg';
        $link = $attrs['link'] ?? '';

        $img = "<img src=\"{$src}\" alt=\"{$alt}\" class=\"{$class}\" loading=\"lazy\">";

        if ($link) {
            return "<a href=\"{$link}\">{$img}</a>";
        }

        return $img;
    }

    /**
     * Render gallery shortcode
     */
    protected function renderGallery(array $attrs): string
    {
        $ids = array_filter(explode(',', $attrs['ids'] ?? ''));
        $columns = (int) ($attrs['columns'] ?? 3);

        if (empty($ids)) {
            return '';
        }

        $media = Media::whereIn('id', $ids)->get();

        return view('components.shortcodes.gallery', [
            'media' => $media,
            'columns' => $columns,
        ])->render();
    }

    /**
     * Render map shortcode
     */
    protected function renderMap(array $attrs): string
    {
        $lat = $attrs['lat'] ?? '10.762622';
        $lng = $attrs['lng'] ?? '106.660172';
        $zoom = $attrs['zoom'] ?? '15';
        $height = $attrs['height'] ?? '400';

        return "<div class=\"w-full rounded-lg overflow-hidden\" style=\"height: {$height}px;\">
            <iframe 
                src=\"https://maps.google.com/maps?q={$lat},{$lng}&z={$zoom}&output=embed\" 
                width=\"100%\" 
                height=\"100%\" 
                style=\"border:0;\" 
                allowfullscreen=\"\" 
                loading=\"lazy\">
            </iframe>
        </div>";
    }

    /**
     * Render archive shortcode
     */
    protected function renderArchive(array $attrs): string
    {
        $type = $attrs['type'] ?? 'product';
        $template = $attrs['template'] ?? 'default';

        return view("components.shortcodes.archive-{$type}", [
            'template' => $template,
            'attrs' => $attrs,
        ])->render();
    }

    /**
     * Get all registered shortcodes
     */
    public function getAll(): array
    {
        return array_keys($this->shortcodes);
    }

    /**
     * Check if shortcode exists
     */
    public function exists(string $tag): bool
    {
        return isset($this->shortcodes[$tag]);
    }
}
