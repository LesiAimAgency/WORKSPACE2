<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Taxonomy;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    /**
     * XML Sitemap Chính (Consolidated Sitemap - sitemaps.org / Google Search Console)
     */
    public function index(): Response
    {
        $urls = collect();

        // 1. Core Front Pages
        $urls = $urls->merge($this->getCorePages());

        // 2. Published Products
        $urls = $urls->merge($this->getProductUrls());

        // 3. Published News / Posts
        $urls = $urls->merge($this->getNewsUrls());

        // 4. Published Projects
        $urls = $urls->merge($this->getProjectUrls());

        // 5. Published Pages
        $urls = $urls->merge($this->getPageUrls());

        // 6. Product Categories
        $urls = $urls->merge($this->getCategoryUrls());

        return response()->view('sitemap.urlset', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('X-Robots-Tag', 'noindex, follow'); // Sitemap itself doesn't need to be in search results, but follow links
    }

    /**
     * XML Sitemap Index (Trỏ đến các sub-sitemaps)
     */
    public function sitemapIndex(): Response
    {
        $sitemaps = [
            ['loc' => route('sitemap.products'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('sitemap.news'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('sitemap.projects'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('sitemap.pages'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('sitemap.categories'), 'lastmod' => now()->toAtomString()],
        ];

        return response()->view('sitemap.index', compact('sitemaps'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Sub-sitemap: Sản phẩm
     */
    public function products(): Response
    {
        $urls = $this->getProductUrls();

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Sub-sitemap: Tin tức
     */
    public function news(): Response
    {
        $urls = $this->getNewsUrls();

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Sub-sitemap: Dự án
     */
    public function projects(): Response
    {
        $urls = $this->getProjectUrls();

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Sub-sitemap: Trang tĩnh
     */
    public function pages(): Response
    {
        $urls = $this->getCorePages()->merge($this->getPageUrls());

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * Sub-sitemap: Danh mục
     */
    public function categories(): Response
    {
        $urls = $this->getCategoryUrls();

        return response()->view('sitemap.urlset', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    /**
     * HTML Sitemap dành cho người dùng
     */
    public function htmlIndex()
    {
        $products = Post::where('post_type', 'product')->published()->latest()->take(60)->get();
        $news = Post::whereIn('post_type', ['news', 'post'])->published()->latest()->take(30)->get();
        $pages = Post::where('post_type', 'page')->published()->latest()->get();
        $categories = Taxonomy::whereIn('taxonomy', ['product_cat', 'category'])->where('status', 'published')->get();

        if (view()->exists('themes.daiphuc68.pages.sitemap-html')) {
            return view('themes.daiphuc68.pages.sitemap-html', compact('products', 'news', 'pages', 'categories'));
        }

        return view('sitemap.index', [
            'sitemaps' => [
                ['loc' => url('/'), 'lastmod' => now()->toAtomString()],
                ['loc' => route('products.index'), 'lastmod' => now()->toAtomString()],
            ],
        ]);
    }

    /**
     * Danh sách URL Trang chính
     */
    private function getCorePages(): \Illuminate\Support\Collection
    {
        return collect([
            [
                'loc' => url('/'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('products.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'loc' => route('projects.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('news.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => route('about-us'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('contact.page'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ],
        ]);
    }

    /**
     * Danh sách URL Sản phẩm
     */
    private function getProductUrls(): \Illuminate\Support\Collection
    {
        $products = Post::where('post_type', 'product')
            ->published()
            ->select('id', 'slug', 'title', 'featured_image', 'updated_at', 'seo_data', 'meta_data')
            ->latest('updated_at')
            ->get();

        return $products->filter(function ($prod) {
            $isNoindex = !empty($prod->seo_data['noindex']) || !empty($prod->meta_data['noindex']);
            return ! $isNoindex && ! empty($prod->slug);
        })->map(function ($prod) {
            $imgUrl = null;
            if (!empty($prod->featured_image)) {
                $imgUrl = Str::startsWith($prod->featured_image, ['http://', 'https://'])
                    ? $prod->featured_image
                    : asset(ltrim($prod->featured_image, '/'));
            }

            return [
                'loc' => route('products.show', $prod->slug),
                'lastmod' => $prod->updated_at ? $prod->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
                'image' => $imgUrl,
                'title' => $prod->title,
            ];
        })->values();
    }

    /**
     * Danh sách URL Tin tức / Bài viết
     */
    private function getNewsUrls(): \Illuminate\Support\Collection
    {
        $articles = Post::whereIn('post_type', ['news', 'post'])
            ->published()
            ->select('id', 'slug', 'title', 'featured_image', 'updated_at', 'seo_data', 'meta_data')
            ->latest('updated_at')
            ->get();

        return $articles->filter(function ($item) {
            $isNoindex = !empty($item->seo_data['noindex']) || !empty($item->meta_data['noindex']);
            return ! $isNoindex && ! empty($item->slug);
        })->map(function ($item) {
            $imgUrl = null;
            if (!empty($item->featured_image)) {
                $imgUrl = Str::startsWith($item->featured_image, ['http://', 'https://'])
                    ? $item->featured_image
                    : asset(ltrim($item->featured_image, '/'));
            }

            return [
                'loc' => route('news.show', $item->slug),
                'lastmod' => $item->updated_at ? $item->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'image' => $imgUrl,
                'title' => $item->title,
            ];
        })->values();
    }

    /**
     * Danh sách URL Dự án
     */
    private function getProjectUrls(): \Illuminate\Support\Collection
    {
        $projects = Post::where('post_type', 'project')
            ->published()
            ->select('id', 'slug', 'title', 'featured_image', 'updated_at', 'seo_data', 'meta_data')
            ->latest('updated_at')
            ->get();

        return $projects->filter(function ($item) {
            $isNoindex = !empty($item->seo_data['noindex']) || !empty($item->meta_data['noindex']);
            return ! $isNoindex && ! empty($item->slug);
        })->map(function ($item) {
            $imgUrl = null;
            if (!empty($item->featured_image)) {
                $imgUrl = Str::startsWith($item->featured_image, ['http://', 'https://'])
                    ? $item->featured_image
                    : asset(ltrim($item->featured_image, '/'));
            }

            return [
                'loc' => url('/projects/' . $item->slug),
                'lastmod' => $item->updated_at ? $item->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
                'image' => $imgUrl,
                'title' => $item->title,
            ];
        })->values();
    }

    /**
     * Danh sách URL Trang tĩnh (Pages)
     */
    private function getPageUrls(): \Illuminate\Support\Collection
    {
        $excludeSlugs = ['about-us', 'contact', 've-chung-toi', 'lien-he', 'home', 'trang-chu'];

        $pages = Post::where('post_type', 'page')
            ->published()
            ->whereNotIn('slug', $excludeSlugs)
            ->select('id', 'slug', 'title', 'featured_image', 'updated_at', 'seo_data', 'meta_data')
            ->latest('updated_at')
            ->get();

        return $pages->filter(function ($page) {
            $isNoindex = !empty($page->seo_data['noindex']) || !empty($page->meta_data['noindex']);
            return ! $isNoindex && ! empty($page->slug);
        })->map(function ($page) {
            return [
                'loc' => route('pages.show', $page->slug),
                'lastmod' => $page->updated_at ? $page->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        })->values();
    }

    /**
     * Danh sách Danh mục sản phẩm & bài viết
     */
    private function getCategoryUrls(): \Illuminate\Support\Collection
    {
        $categories = Taxonomy::whereIn('taxonomy', ['product_cat', 'category'])
            ->where('status', 'published')
            ->select('id', 'slug', 'taxonomy', 'updated_at')
            ->get();

        return $categories->filter(fn ($cat) => ! empty($cat->slug))->map(function ($cat) {
            $url = $cat->taxonomy === 'product_cat'
                ? url('/products?category=' . urlencode($cat->slug))
                : url('/news?category=' . urlencode($cat->slug));

            return [
                'loc' => $url,
                'lastmod' => $cat->updated_at ? $cat->updated_at->toAtomString() : now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        })->values();
    }
}
