<?php

declare(strict_types=1);

namespace App\Http\Controllers\Themes\Daiphuc68;

use App\Core\Theme\ThemeManager;
use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use App\Models\Post;
use App\Models\Project;
use App\Models\Taxonomy;
use App\Services\TocService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class Daiphuc68Controller extends Controller
{
    protected function initTheme(): void
    {
        $themeManager = app(ThemeManager::class);
        $themeManager->setActiveTheme('daiphuc68');
    }

    /**
     * Trang Chủ (Homepage - Onepage Showcase)
     */
    public function home(Request $request): View
    {
        $this->initTheme();

        $products = Post::where('post_type', 'product')->published()->orderBy('order', 'asc')->orderBy('created_at', 'desc')->take(8)->get();
        if ($products->isEmpty()) {
            $products = $this->getFallbackProducts()->take(8);
        }

        $projects = Post::where('post_type', 'project')->published()->take(5)->get();
        if ($projects->isEmpty()) {
            $projects = $this->getFallbackProjects()->take(5);
        }

        $news = Post::whereIn('post_type', ['news', 'post'])->published()->latest('published_at')->take(4)->get();
        if ($news->isEmpty()) {
            $news = $this->getFallbackNews()->take(4);
        }

        return view('themes.daiphuc68.pages.home', [
            'products' => $products,
            'projects' => $projects,
            'news' => $news,
        ]);
    }

    /**
     * Trang Giới Thiệu (About Us)
     */
    public function aboutUs(Request $request): View
    {
        $this->initTheme();

        $page = Post::where('post_type', 'page')
            ->where('slug', 'about-us')
            ->first();

        if (! $page) {
            $page = new Post([
                'title' => 'Về Chúng Tôi | Đại Phúc 68',
                'slug' => 'about-us',
                'post_type' => 'page',
                'content' => 'Công ty Cổ phần Thương mại & Kỹ thuật Đại Phúc 68 là đơn vị chuyên phân phối thiết bị điện công nghiệp, thiết bị trạm biến áp và giải pháp cơ điện hàng đầu Việt Nam.',
                'status' => 'published',
            ]);
        }

        $featuredProjects = Post::where('post_type', 'project')
            ->published()
            ->limit(4)
            ->get();

        if ($featuredProjects->isEmpty()) {
            $featuredProjects = $this->getFallbackProjects()->take(4);
        }

        return view('themes.daiphuc68.pages.about-us', [
            'page' => $page,
            'featuredProjects' => $featuredProjects,
        ]);
    }

    /**
     * Danh Sách Sản Phẩm (Products Catalog)
     */
    public function products(Request $request): View
    {
        $this->initTheme();

        // Resolve Filter Settings from Database
        $rawFilterSettings = setting('product_filter_settings');
        if (is_string($rawFilterSettings)) {
            $rawFilterSettings = json_decode($rawFilterSettings, true) ?: [];
        }

        $catConfig = $rawFilterSettings['category_filter'] ?? [
            'enabled' => true,
            'title' => 'LOẠI SẢN PHẨM',
            'mode' => 'custom',
            'selected_categories' => [1, 2, 3],
            'selection_type' => 'checkbox',
            'is_expanded' => true,
        ];

        $brandConfig = $rawFilterSettings['brand_filter'] ?? [
            'enabled' => true,
            'title' => 'THƯƠNG HIỆU',
            'mode' => 'all',
            'selected_brands' => [],
            'display_type' => 'logo',
            'is_expanded' => true,
        ];

        $priceConfig = array_merge([
            'enabled' => true,
            'title' => 'KHOẢNG GIÁ',
            'type' => 'milestones', // 'milestones' or 'slider'
            'min_price' => 0,
            'max_price' => 1000000,
            'step' => 50000,
            'allow_contact' => true,
            'is_expanded' => true,
            'ranges' => [
                ['id' => 'under_200', 'label' => 'Dưới 200.000đ', 'min' => 0, 'max' => 200000, 'type' => 'range'],
                ['id' => '200_400', 'label' => '200.000đ - 400.000đ', 'min' => 200000, 'max' => 400000, 'type' => 'range'],
                ['id' => 'over_400', 'label' => 'Trên 400.000đ', 'min' => 400000, 'max' => null, 'type' => 'range'],
                ['id' => 'contact', 'label' => 'Giá liên hệ dự án', 'min' => null, 'max' => null, 'type' => 'contact'],
            ],
        ], $rawFilterSettings['price_filter'] ?? []);

        // Fetch categories for sidebar
        $allTaxCategories = Taxonomy::where('taxonomy', 'product_cat')->orderBy('order')->orderBy('id')->get();
        if (($catConfig['mode'] ?? 'custom') === 'all_root') {
            $filterCategories = $allTaxCategories->whereNull('parent_id')->values();
        } elseif (($catConfig['mode'] ?? 'custom') === 'all') {
            $filterCategories = $allTaxCategories;
        } else {
            $selectedIds = $catConfig['selected_categories'] ?? [1, 2, 3];
            $filterCategories = $allTaxCategories->filter(fn ($c) => in_array($c->id, $selectedIds))->values();
            if ($filterCategories->isEmpty() && $allTaxCategories->isNotEmpty()) {
                $filterCategories = $allTaxCategories->take(3);
            }
        }

        // Resolve Featured Categories for top showcase banner (aria-label="Danh mục thiết bị điện nổi bật")
        $featuredCatIds = $rawFilterSettings['featured_categories'] ?? null;
        if (! empty($featuredCatIds) && is_array($featuredCatIds)) {
            $featuredCategories = $allTaxCategories->filter(fn ($c) => in_array($c->id, $featuredCatIds))
                ->sortBy(fn ($c) => array_search($c->id, $featuredCatIds))
                ->values();
        } else {
            // Default: pick root categories from Danh mục sản phẩm (ordered by order)
            $rootCats = $allTaxCategories->whereNull('parent_id')->values();
            if ($rootCats->isNotEmpty()) {
                $featuredCategories = $rootCats->take(8);
            } else {
                $featuredCategories = $allTaxCategories->take(8)->values();
            }
        }

        if ($featuredCategories->isEmpty()) {
            $featuredCategories = collect([
                (object) [
                    'id' => 1,
                    'name' => 'VẬT TƯ THIẾT BỊ ĐIỆN',
                    'slug' => 'vat-tu-thiet-bi-dien',
                    'image' => 'storage/media/banners/hero-banner-main.jpg',
                ],
                (object) [
                    'id' => 6,
                    'name' => 'ỐNG TRỤ BÊ TÔNG LY TÂM',
                    'slug' => 'ong-tru-be-tong-ly-tam',
                    'image' => 'storage/media/projects/project-detail-hero.jpg',
                ],
                (object) [
                    'id' => 7,
                    'name' => 'THIẾT BỊ NĂNG LƯỢNG MẶT TRỜI',
                    'slug' => 'thiet-bi-nang-luong-mat-troi',
                    'image' => 'storage/media/projects/project-item-1.png',
                ],
                (object) [
                    'id' => 8,
                    'name' => 'ĐÓNG CẮT TRUNG THẾ & RMU',
                    'slug' => 'dong-cat-trung-the',
                    'image' => 'storage/media/projects/project-detail-hero.jpg',
                ],
                (object) [
                    'id' => 9,
                    'name' => 'MÁY BIẾN ÁP & TRẠM BIẾN ÁP',
                    'slug' => 'may-bien-ap-tram',
                    'image' => 'storage/media/projects/project-item-2.jpg',
                ],
            ]);
        }

        // Fetch brands for sidebar
        $allTaxBrands = Taxonomy::whereIn('taxonomy', ['brand', 'product_brand'])->orderBy('name')->get();
        if (($brandConfig['mode'] ?? 'all') === 'custom' && ! empty($brandConfig['selected_brands'])) {
            $filterBrands = $allTaxBrands->filter(fn ($b) => in_array($b->id, $brandConfig['selected_brands']))->values();
        } else {
            $filterBrands = $allTaxBrands;
        }

        $hasDbProducts = Post::where('post_type', 'product')->published()->exists();

        if ($hasDbProducts) {
            $query = Post::where('post_type', 'product')->published()->with(['taxonomies', 'productCategories', 'productBrands', 'variations']);

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            if ($category = $request->input('category')) {
                $query->where(function ($q) use ($category) {
                    $q->where('meta_data->category', 'like', "%{$category}%")
                      ->orWhereHas('taxonomies', fn ($t) => $t->where('name', 'like', "%{$category}%")->orWhere('slug', $category));
                });
            }

            if ($brand = $request->input('brand')) {
                $query->where(function ($q) use ($brand) {
                    $q->where('meta_data->brand', 'like', "%{$brand}%")
                      ->orWhereHas('taxonomies', fn ($t) => $t->where('name', 'like', "%{$brand}%")->orWhere('slug', $brand));
                });
            }

            if ($priceRange = $request->input('price_range')) {
                if ($priceRange === 'under_200') {
                    $query->whereRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(meta_data, '$.price')) AS DECIMAL(15,2)) < 200000");
                } elseif ($priceRange === '200_400') {
                    $query->whereRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(meta_data, '$.price')) AS DECIMAL(15,2)) BETWEEN 200000 AND 400000");
                } elseif ($priceRange === 'over_400') {
                    $query->whereRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(meta_data, '$.price')) AS DECIMAL(15,2)) > 400000");
                } elseif ($priceRange === 'contact') {
                    $query->where(function ($q) {
                        $q->whereNull("meta_data->price")
                          ->orWhere("meta_data->price", "")
                          ->orWhereRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(meta_data, '$.price')) AS DECIMAL(15,2)) = 0");
                    });
                }
            }

            $sort = $request->input('sort', 'name-asc');
            switch ($sort) {
                case 'name-desc':
                    $query->orderBy('order', 'asc')->orderBy('title', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('order', 'asc')->latest('published_at');
                    break;
                case 'price-asc':
                    $query->orderBy('order', 'asc')->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(meta_data, '$.price')) AS DECIMAL(15,2)) ASC");
                    break;
                case 'price-desc':
                    $query->orderBy('order', 'asc')->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(meta_data, '$.price')) AS DECIMAL(15,2)) DESC");
                    break;
                case 'name-asc':
                default:
                    $query->orderBy('order', 'asc')->orderBy('title', 'asc');
                    break;
            }

            $allProducts = (clone $query)->get();
            $products = $query->paginate(9)->withQueryString();
        } else {
            $sort = $request->input('sort', 'name-asc');
            $allProducts = $this->getFallbackProducts();

            if ($search = $request->input('search')) {
                $allProducts = $allProducts->filter(function ($item) use ($search) {
                    return str_contains(mb_strtolower($item->title), mb_strtolower($search));
                });
            }

            if ($category = $request->input('category')) {
                $allProducts = $allProducts->filter(function ($item) use ($category) {
                    return str_contains(mb_strtolower($item->category_name ?? ''), mb_strtolower($category));
                });
            }

            if ($brand = $request->input('brand')) {
                $allProducts = $allProducts->filter(function ($item) use ($brand) {
                    return str_contains(mb_strtolower($item->brand ?? ''), mb_strtolower($brand));
                });
            }

            if ($priceRange = $request->input('price_range')) {
                $allProducts = $allProducts->filter(function ($item) use ($priceRange) {
                    $meta = is_array($item->meta_data) ? $item->meta_data : json_decode($item->meta_data ?? '[]', true);
                    $price = (int) ($meta['price'] ?? 0);
                    if ($priceRange === 'under_200') return $price > 0 && $price < 200000;
                    if ($priceRange === '200_400') return $price >= 200000 && $price <= 400000;
                    if ($priceRange === 'over_400') return $price > 400000;
                    if ($priceRange === 'contact') return $price === 0;
                    return true;
                });
            }

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 9;
            $currentItems = $allProducts->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $products = new LengthAwarePaginator(
                $currentItems,
                $allProducts->count(),
                $perPage,
                $currentPage,
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        }

        // Format products JSON payload for main.js client-side filtering
        $productsJson = $allProducts->map(function ($item, $index) {
            $formattedPrice = $item->display_price ?: 'Giá liên hệ dự án';
            $itemCategories = $item->relationLoaded('productCategories') ? $item->productCategories : ($item->taxonomies ? $item->taxonomies->where('taxonomy', 'product_cat') : collect());
            $itemBrands = $item->relationLoaded('productBrands') ? $item->productBrands : ($item->taxonomies ? $item->taxonomies->whereIn('taxonomy', ['brand', 'product_brand']) : collect());

            return [
                'id' => $item->id ?? ($index + 1),
                'order' => (int) ($item->order ?? 0),
                'productId' => 'prod-' . str_pad((string) ($item->id ?? ($index + 1)), 3, '0', STR_PAD_LEFT),
                'title' => $item->title,
                'slug' => $item->slug,
                'url' => route('products.show', $item->slug),
                'image' => $item->featured_image ?: asset('storage/media/products/product-cau-chi-tu-roi-vina-electric.png'),
                'price' => $formattedPrice,
                'salePrice' => $formattedPrice,
                'priceType' => $item->display_price !== 'Liên hệ' ? 'fixed' : 'contact',
                'badge' => $item->badge ?? null,
                'badgeColor' => 'bg-red-600 text-white',
                'category' => $item->category_name ?: ($itemCategories->first()?->name ?? 'Vật Tư Thiết Bị Điện'),
                'category_ids' => $itemCategories->pluck('id')->values()->toArray(),
                'category_slugs' => $itemCategories->pluck('slug')->values()->toArray(),
                'brand_id' => $itemBrands->first()?->id ?? null,
                'brand_slug' => $itemBrands->first()?->slug ?? Str::slug($item->brand ?? ''),
                'brand' => [
                    'name' => $item->brand ?: ($itemBrands->first()?->name ?? 'Đại Phúc 68'),
                    'logo' => $item->brand_logo,
                    'type' => $item->brand_logo ? 'logo' : 'text',
                    'origin' => 'Việt Nam'
                ],
                'variant' => 'wide'
            ];
        });

        return view('themes.daiphuc68.pages.products', [
            'products' => $products,
            'allProducts' => $allProducts,
            'productsJson' => $productsJson,
            'currentSort' => $sort,
            'searchKeyword' => $request->input('search', ''),
            'filterSettings' => [
                'category' => $catConfig,
                'brand' => $brandConfig,
                'price' => $priceConfig,
            ],
            'filterCategories' => $filterCategories,
            'featuredCategories' => $featuredCategories,
            'allTaxCategories' => $allTaxCategories,
            'filterBrands' => $filterBrands,
            'filterPriceRanges' => $priceConfig['ranges'] ?? [],
        ]);
    }

    /**
     * Chi Tiết Sản Phẩm (Product Detail)
     */
    public function productDetail(Request $request, string $slug): View
    {
        $this->initTheme();

        $product = Post::where('post_type', 'product')
            ->where('slug', $slug)
            ->with(['taxonomies', 'variations' => fn ($q) => $q->where('status', 'active')->orderBy('order')])
            ->first();

        $isFallback = false;
        if (! $product) {
            $fallbackList = $this->getFallbackProducts();
            $product = $fallbackList->firstWhere('slug', $slug) ?? $fallbackList->first();
            $isFallback = true;
        }

        if (! $product) {
            abort(404);
        }

        // ============================================
        // TRACKING SẢN PHẨM ĐÃ XEM (RECENTLY VIEWED)
        // ============================================
        $currentId = $product->id;

        // Retrieve existing viewed IDs from session or persistent cookie
        $viewedIds = session()->get('viewed_product_ids', []);
        if (empty($viewedIds) && $request->hasCookie('viewed_product_ids')) {
            $cookieVal = json_decode((string) $request->cookie('viewed_product_ids'), true);
            if (is_array($cookieVal)) {
                $viewedIds = $cookieVal;
            }
        }

        // Remove current product ID if already exists, then prepend to top (most recently viewed)
        $viewedIds = array_values(array_filter($viewedIds, fn ($id) => (string) $id !== (string) $currentId));
        array_unshift($viewedIds, $currentId);

        // Keep maximum 12 recently viewed products
        $viewedIds = array_slice($viewedIds, 0, 12);

        // Store back to session and persistent cookie (30 days)
        session()->put('viewed_product_ids', $viewedIds);
        Cookie::queue('viewed_product_ids', json_encode($viewedIds), 60 * 24 * 30);

        // Load the viewed products maintaining the exact viewing order
        if (! $isFallback) {
            $dbProducts = Post::where('post_type', 'product')
                ->whereIn('id', $viewedIds)
                ->published()
                ->with(['taxonomies', 'variations'])
                ->get()
                ->keyBy('id');

            $viewedProducts = collect($viewedIds)
                ->map(fn ($id) => $dbProducts->get($id))
                ->filter()
                ->values();

            if ($viewedProducts->isEmpty()) {
                $fallbackList = $this->getFallbackProducts()->keyBy('id');
                $viewedProducts = collect($viewedIds)
                    ->map(fn ($id) => $fallbackList->get($id))
                    ->filter()
                    ->values();
            }
        } else {
            $fallbackList = $this->getFallbackProducts()->keyBy('id');
            $viewedProducts = collect($viewedIds)
                ->map(fn ($id) => $fallbackList->get($id))
                ->filter()
                ->values();
        }

        if ($viewedProducts->isEmpty()) {
            $viewedProducts = collect([$product]);
        }

        $content = (string) ($product->content ?? '');
        $tocService = app(TocService::class);
        $tocData = $tocService->generate($content, 'product');

        // Query related products from same category or brand
        $categoryIds = $product->taxonomies ? $product->taxonomies->pluck('id')->all() : [];
        $relatedProducts = Post::where('post_type', 'product')
            ->where('id', '!=', $product->id)
            ->published()
            ->when(!empty($categoryIds), function ($query) use ($categoryIds) {
                $query->whereHas('taxonomies', function ($q) use ($categoryIds) {
                    $q->whereIn('taxonomies.id', $categoryIds);
                });
            })
            ->with(['taxonomies', 'variations'])
            ->limit(8)
            ->get();

        if ($relatedProducts->count() < 4) {
            $existingIds = $relatedProducts->pluck('id')->push($product->id)->all();
            $additional = Post::where('post_type', 'product')
                ->whereNotIn('id', $existingIds)
                ->published()
                ->with(['taxonomies', 'variations'])
                ->limit(8 - $relatedProducts->count())
                ->get();
            $relatedProducts = $relatedProducts->merge($additional);
        }

        if ($relatedProducts->isEmpty()) {
            $fallbackList = $this->getFallbackProducts()->where('slug', '!=', $slug);
            $relatedProducts = $fallbackList->take(8);
        }

        return view('themes.daiphuc68.pages.product-detail', [
            'product' => $product,
            'viewedProducts' => $viewedProducts,
            'relatedProducts' => $relatedProducts,
            'tocData' => $tocData,
        ]);
    }

    /**
     * Danh Sách Dự Án (Projects Catalog)
     */
    public function projects(Request $request): View
    {
        $this->initTheme();

        // 1. Lấy danh sách tất cả danh mục dự án (taxonomy = 'project_cat')
        $projectCategories = Taxonomy::where('taxonomy', 'project_cat')
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 2. Lấy tất cả dự án đã xuất bản
        $allProjects = Post::where('post_type', 'project')
            ->published()
            ->with('taxonomies')
            ->orderBy('id', 'asc')
            ->get();

        if ($allProjects->isEmpty()) {
            $allProjects = $this->getFallbackProjects();
        }

        // 3. Xử lý danh mục đang chọn (nếu có tham số ?category=...)
        $activeCategory = $request->query('category', '');

        // 4. Gom nhóm dự án theo từng danh mục
        $categoriesWithProjects = collect();

        foreach ($projectCategories as $cat) {
            $matchedProjects = $allProjects->filter(function ($item) use ($cat) {
                // Kiểm tra qua taxonomy relationship
                if ($item->taxonomies && $item->taxonomies->contains(function ($t) use ($cat) {
                    return $t->id == $cat->id || $t->slug === $cat->slug;
                })) {
                    return true;
                }

                // Kiểm tra qua meta_data['category'] hoặc attribute category_name
                $metaCat = mb_strtolower($item->meta_data['category'] ?? ($item->category_name ?? ''));
                $catName = mb_strtolower($cat->name);
                $catSlug = mb_strtolower($cat->slug);

                if ($metaCat !== '' && ($metaCat === $catName || str_contains($metaCat, $catName) || str_contains($catName, $metaCat))) {
                    return true;
                }

                if (str_contains($catSlug, 'nha-hang') && (str_contains($metaCat, 'nhà hàng') || str_contains($metaCat, 'khách sạn') || str_contains($metaCat, 'hotel'))) {
                    return true;
                }
                if (str_contains($catSlug, 'cong-cong') && (str_contains($metaCat, 'công cộng') || str_contains($metaCat, 'trạm') || str_contains($metaCat, 'hội nghị') || str_contains($metaCat, 'sân bay') || str_contains($metaCat, 'bệnh viện'))) {
                    return true;
                }
                if (str_contains($catSlug, 'nha-o') && (str_contains($metaCat, 'nhà ở') || str_contains($metaCat, 'biệt thự') || str_contains($metaCat, 'residence') || str_contains($metaCat, 'villa'))) {
                    return true;
                }

                return false;
            })->values();

            $catClone = clone $cat;
            $catClone->projects = $matchedProjects;
            $categoriesWithProjects->push($catClone);
        }

        // Nếu có $activeCategory và người dùng muốn lọc theo danh mục cụ thể
        $filteredCategories = $categoriesWithProjects;
        if (!empty($activeCategory)) {
            $matched = $categoriesWithProjects->filter(fn ($c) => $c->slug === $activeCategory);
            if ($matched->isNotEmpty()) {
                $filteredCategories = $matched->values();
            }
        }

        $hotelProjects = $categoriesWithProjects->firstWhere('slug', 'nha-hang-khach-san')?->projects ?? collect();
        $publicProjects = $categoriesWithProjects->firstWhere('slug', 'cong-trinh-cong-cong')?->projects ?? collect();
        $residentialProjects = $categoriesWithProjects->firstWhere('slug', 'nha-o-dan-dung')?->projects ?? collect();

        return view('themes.daiphuc68.pages.projects', [
            'allProjects' => $allProjects,
            'projectCategories' => $projectCategories,
            'categoriesWithProjects' => $filteredCategories,
            'allCategoriesWithProjects' => $categoriesWithProjects,
            'activeCategory' => $activeCategory,
            'hotelProjects' => $hotelProjects,
            'publicProjects' => $publicProjects,
            'residentialProjects' => $residentialProjects,
        ]);
    }

    /**
     * Chi Tiết Trang Tĩnh (Static Page Detail)
     */
    public function pageDetail(string $slug): View
    {
        $this->initTheme();

        $page = Post::where('post_type', 'page')
            ->where('slug', $slug)
            ->first();

        if (! $page) {
            abort(404);
        }

        // Lấy danh sách các trang chính sách/thông tin khác để hiển thị điều hướng phụ
        $otherPages = Post::where('post_type', 'page')
            ->where('id', '!=', $page->id)
            ->published()
            ->limit(5)
            ->get();

        // Lấy bài viết tin tức liên quan để hiển thị ở khối dưới
        $relatedNews = Post::whereIn('post_type', ['news', 'post'])
            ->published()
            ->latest('published_at')
            ->limit(3)
            ->get();

        if ($relatedNews->isEmpty()) {
            $relatedNews = $this->getFallbackNews()->take(3);
        }

        $content = (string) ($page->content ?? '');
        if (empty(trim(strip_tags($content))) || ! preg_match('/<h[2-5][^>]*>/i', $content)) {
            $content = $content . $this->getFallbackPageContent();
        }

        $tocService = app(TocService::class);
        $tocData = $tocService->generate($content, 'page');

        return view('themes.daiphuc68.pages.page-detail', [
            'page' => $page,
            'post' => $page,
            'otherPages' => $otherPages,
            'relatedNews' => $relatedNews,
            'tocData' => $tocData,
        ]);
    }

    /**
     * Chi Tiết Dự Án hoặc Trang Tĩnh (Dynamic Fallback Detail)
     */
    public function projectDetail(string $slug): View
    {
        $this->initTheme();

        // 1. Kiểm tra nếu slug thuộc về một Trang Tĩnh (post_type = 'page')
        $page = Post::where('post_type', 'page')
            ->where('slug', $slug)
            ->first();

        if ($page) {
            return $this->pageDetail($slug);
        }

        // 2. Tiếp tục xử lý cho Dự Án (post_type = 'project')
        $project = Post::where('post_type', 'project')
            ->where('slug', $slug)
            ->first();

        if (! $project) {
            $fallbackList = $this->getFallbackProjects();
            $project = $fallbackList->firstWhere('slug', $slug);

            if (! $project) {
                abort(404);
            }

            $relatedProjects = $fallbackList->where('id', '!=', $project->id)->take(3);
        } else {
            $relatedProjects = Post::where('post_type', 'project')
                ->where('id', '!=', $project->id)
                ->published()
                ->limit(3)
                ->get();

            if ($relatedProjects->isEmpty()) {
                $relatedProjects = $this->getFallbackProjects()->where('slug', '!=', $slug)->take(3);
            }
        }

        $content = (string) ($project->content ?? '');
        $tocService = app(TocService::class);
        $tocData = $tocService->generate($content, 'project');

        return view('themes.daiphuc68.pages.project-detail', [
            'project' => $project,
            'relatedProjects' => $relatedProjects,
            'tocData' => $tocData,
        ]);
    }

    /**
     * Danh Sách Tin Tức (News)
     */
    public function news(Request $request): View
    {
        $this->initTheme();

        $newsCategories = Taxonomy::where('taxonomy', 'category')
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $activeCategory = $request->query('category', '');

        $hasDbNews = Post::whereIn('post_type', ['news', 'post'])->published()->exists();

        if ($hasDbNews) {
            $query = Post::whereIn('post_type', ['news', 'post'])
                ->published()
                ->with(['taxonomies', 'author']);

            if (!empty($activeCategory)) {
                $matchedCat = $newsCategories->first(function ($c) use ($activeCategory) {
                    return $c->slug === $activeCategory || (string)$c->id === (string)$activeCategory;
                });

                if ($matchedCat) {
                    $catId = $matchedCat->id;
                    $catSlug = $matchedCat->slug;
                    $catName = mb_strtolower($matchedCat->name);

                    $query->where(function ($q) use ($catId, $catSlug, $catName) {
                        $q->whereHas('taxonomies', function ($tq) use ($catId, $catSlug) {
                            $tq->where('term_taxonomy_id', $catId)
                               ->orWhere('slug', $catSlug);
                        })
                        ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(meta_data, "$.category"))) LIKE ?', ["%{$catName}%"]);
                    });
                }
            }

            $news = $query->latest('published_at')->paginate(6)->withQueryString();
        } else {
            $allNews = $this->getFallbackNews();
            if (!empty($activeCategory)) {
                $allNews = $allNews->filter(function ($item) use ($activeCategory) {
                    $metaCat = mb_strtolower($item->meta_data['category'] ?? '');
                    return str_contains($metaCat, mb_strtolower($activeCategory));
                })->values();
            }
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 6;
            $currentItems = $allNews->slice(($currentPage - 1) * $perPage, $perPage)->values();
            $news = new LengthAwarePaginator(
                $currentItems,
                $allNews->count(),
                $perPage,
                $currentPage,
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        }

        return view('themes.daiphuc68.pages.news', [
            'news' => $news,
            'newsCategories' => $newsCategories,
            'activeCategory' => $activeCategory,
        ]);
    }

    /**
     * Chi Tiết Tin Tức (News Detail)
     */
    public function newsDetail(string $slug): View
    {
        $this->initTheme();

        $post = Post::whereIn('post_type', ['news', 'post'])
            ->where('slug', $slug)
            ->first();

        if (! $post) {
            $fallbackList = $this->getFallbackNews();
            $post = $fallbackList->firstWhere('slug', $slug) ?? $fallbackList->first();
            $relatedNews = $fallbackList->where('id', '!=', $post->id)->take(3);
        } else {
            $relatedNews = Post::whereIn('post_type', ['news', 'post'])
                ->where('id', '!=', $post->id)
                ->published()
                ->latest('published_at')
                ->limit(3)
                ->get();

            if ($relatedNews->isEmpty()) {
                $relatedNews = $this->getFallbackNews()->where('slug', '!=', $slug)->take(3);
            }
        }

        $content = (string) ($post->content ?? '');
        if (empty(trim(strip_tags($content))) || ! preg_match('/<h[2-5][^>]*>/i', $content)) {
            $content = $content . $this->getFallbackNewsContent();
        }

        $tocService = app(TocService::class);
        $tocData = $tocService->generate($content, $post->post_type ?? 'news');

        return view('themes.daiphuc68.pages.news-detail', [
            'post' => $post,
            'relatedNews' => $relatedNews,
            'tocData' => $tocData,
        ]);
    }

    /**
     * Trang Liên Hệ (Contact Page)
     */
    public function contactPage(): View
    {
        $this->initTheme();

        $contactPage = Post::where('post_type', 'page')
            ->where('slug', 'contact')
            ->first();

        if (! $contactPage) {
            $contactPage = new Post([
                'title' => 'Liên Hệ | Đại Phúc 68',
                'slug' => 'contact',
                'post_type' => 'page',
                'content' => 'Liên hệ Công ty Cổ phần Thương mại & Kỹ thuật Đại Phúc 68',
                'status' => 'published',
            ]);
        }

        $faqs = Post::where('post_type', 'faq')
            ->where('status', 'published')
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('themes.daiphuc68.pages.contact', [
            'contactPage' => $contactPage,
            'faqs' => $faqs,
        ]);
    }

    /**
     * Hồ Sơ Năng Lực (Company Profile & Heyzine Interactive Flipbook)
     */
    public function companyProfile(Request $request): View
    {
        $this->initTheme();

        // Lấy cấu hình Heyzine Embed / URL từ settings hoặc fallback
        $heyzineEmbed = setting('company_profile_heyzine_embed', setting('heyzine_embed', ''));
        $profilePdf = setting('company_profile_pdf', asset('themes/daiphuc68/assets/ho-so-nang-luc.pdf'));
        $title = setting('company_profile_title', 'HỒ SƠ NĂNG LỰC - CÔNG TY TNHH ĐẠI PHÚC 68');
        $description = setting('company_profile_desc', 'Xem trực tuyến tài liệu hồ sơ năng lực, chứng chỉ xuất xưởng, năng lực thi công và cung cấp vật tư thiết bị điện Đại Phúc 68.');

        $featuredProjects = Post::where('post_type', 'project')
            ->published()
            ->limit(4)
            ->get();
        if ($featuredProjects->isEmpty()) {
            $featuredProjects = $this->getFallbackProjects()->take(4);
        }

        return view('themes.daiphuc68.pages.company-profile', [
            'heyzineEmbed' => $heyzineEmbed,
            'profilePdf' => $profilePdf,
            'title' => $title,
            'description' => $description,
            'featuredProjects' => $featuredProjects,
        ]);
    }

    /**
     * Xử lý gửi biểu mẫu liên hệ
     */
    public function contactSubmit(Request $request): JsonResponse
    {
        $payload = $request->all();
        if (empty($payload['fullname']) && !empty($payload['name'])) {
            $payload['fullname'] = $payload['name'];
        }

        $validator = Validator::make($payload, [
            'fullname' => 'required|string|min:2|max:255',
            'phone' => 'required|string|min:8|max:30',
            'email' => 'nullable|email|max:255',
            'service' => 'nullable|string|max:150',
            'message' => 'nullable|string|max:3000',
        ], [
            'fullname.required' => 'Please enter your full name.',
            'fullname.min' => 'Your full name must be at least 2 characters.',
            'phone.required' => 'Please enter your contact phone number.',
            'phone.min' => 'The phone number is invalid.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $inputData = [
            'fullname' => trim((string) $request->input('fullname')),
            'phone' => trim((string) $request->input('phone')),
            'email' => trim((string) $request->input('email', '')),
            'service' => trim((string) $request->input('service', 'tu-van-thiet-ke')),
            'message' => trim((string) $request->input('message', '')),
            'page_url' => trim((string) $request->input('page_url', $request->header('referer', url()->current()))),
            'product_title' => $request->input('product_title'),
            'product_sku' => $request->input('product_sku'),
            'product_variation' => $request->input('product_variation'),
            'product_price' => $request->input('product_price'),
            'product_quantity' => $request->input('product_quantity'),
        ];

        // Safe submission logging - respects DB freeze
        try {
            if (Schema::hasTable('form_submissions')) {
                $proj = Project::where('code', 'daiphuc68')->first() ?? Project::first();
                FormSubmission::create([
                    'form_name' => $request->input('form_name', 'daiphuc68_contact'),
                    'data' => $inputData,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'project_id' => $proj?->id ?? 7,
                    'tenant_id' => $proj?->tenant_id ?? 6,
                    'status' => 'pending',
                    'source' => $request->input('source', 'website'),
                    'submitted_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::info('Thông tin liên hệ Đại Phúc 68 nhận được: ' . json_encode($inputData, JSON_UNESCAPED_UNICODE));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Cảm ơn bạn! Yêu cầu tư vấn đã được gửi thành công. Đại Phúc 68 sẽ liên hệ lại với bạn trong thời gian sớm nhất.',
        ]);
    }

    /**
     * In-memory Demo Fallback Products (Zero DB write, 100% design fidelity)
     */
    public function getFallbackProducts(): Collection
    {
        $raw = [
            [
                'id' => 101,
                'title' => 'Cầu chì tự rơi Vina Electric',
                'slug' => 'cau-chi-tu-roi-vina-electric',
                'post_type' => 'product',
                'excerpt' => 'Thiết bị bảo vệ quá tải và ngắn mạch đường dây trung thế 24kV/35kV đạt chuẩn IEC.',
                'content' => '<p>Cầu chì tự rơi (Cut-Out FCO) Vina Electric là thiết bị phân đoạn và bảo vệ mạch điện trung thế hiệu quả, tin cậy cao cho các trạm biến áp phân phối ngoài trời.</p>',
                'featured_image' => 'storage/media/products/product-cau-chi-tu-roi-vina-electric.png',
                'status' => 'published',
                'meta_data' => [
                    'price' => 250000,
                    'category' => 'Vật Tư Thiết Bị Điện',
                    'brand' => 'Vina Electric',
                    'brand_logo' => 'storage/media/partners/brand-vinaelectric.png',
                    'badge' => 'BÁN CHẠY',
                    'voltage' => '24kV / 35kV',
                    'standard' => 'IEC 60282-2, TCVN',
                    'specs' => [
                        ['Điện áp định mức', '24kV / 35kV'],
                        ['Dòng điện định mức', '100A / 200A'],
                        ['Dòng cắt ngắn mạch', '12.5kA / 16kA'],
                        ['Khoảng cách dòng rò', '630mm / 850mm'],
                        ['Vật liệu cách điện', 'Polymer (Silicone Rubber)'],
                        ['Tiêu chuẩn áp dụng', 'IEC 60282-2, ANSI C37.41, TCVN']
                    ],
                    'documents' => [
                        ['title' => 'Catalogue Kỹ Thuật FCO Vina Electric', 'size' => '2.4 MB', 'format' => 'PDF', 'url' => '#'],
                        ['title' => 'Bản Vẽ Kỹ Thuật & Kích Thước Lắp Đặt', 'size' => '1.1 MB', 'format' => 'DWG', 'url' => '#'],
                        ['title' => 'Chứng Chỉ Thử Nghiệm Xuất Xưởng (Test Report)', 'size' => '850 KB', 'format' => 'PDF', 'url' => '#']
                    ]
                ],
            ],
            [
                'id' => 102,
                'title' => 'Biến dòng đo lường EMIC',
                'slug' => 'bien-dong-do-luong-emic',
                'post_type' => 'product',
                'excerpt' => 'Máy biến dòng đo lường trung thế ngâm dầu và đúc epoxy độ chính xác cấp 0.5.',
                'content' => '<p>Máy biến dòng EMIC phục vụ đo lường điện năng và cấp nguồn tín hiệu bảo vệ rơle cho các hệ thống tủ điện trạm biến áp.</p>',
                'featured_image' => 'storage/media/products/product-bien-dong-do-luong-emic.png',
                'status' => 'published',
                'meta_data' => [
                    'price' => 380000,
                    'category' => 'Thiết Bị Đo Lường',
                    'brand' => 'EMIC',
                    'brand_logo' => 'storage/media/brands/brand-emic.svg',
                    'badge' => 'TIÊU CHUẨN',
                    'voltage' => '24kV',
                    'standard' => 'IEC 61869-2, TCVN 7697-2',
                    'specs' => [
                        ['Cấp điện áp', '24kV'],
                        ['Tỷ số biến dòng', '50/5A - 600/5A'],
                        ['Cấp chính xác', '0.5 / 5P20'],
                        ['Dung lượng định mức', '15VA - 30VA'],
                    ]
                ],
            ],
            [
                'id' => 103,
                'title' => 'Máy biến áp dầu MBT 3 Pha 1000kVA',
                'slug' => 'may-bien-ap-dau-mbt-3-pha',
                'post_type' => 'product',
                'excerpt' => 'Máy biến áp ngâm dầu truyền tải điện năng hiệu suất cao, tổn thất thấp theo tiêu chuẩn EVN.',
                'content' => '<p>Máy biến áp phân phối 3 pha MBT được sản xuất trên dây chuyền công nghệ hiện đại, đáp ứng nghiêm ngặt các quy định của Tập đoàn Điện lực Việt Nam EVN.</p>',
                'featured_image' => 'storage/media/products/product-may-bien-ap-dau-mbt.png',
                'status' => 'published',
                'meta_data' => [
                    'price' => 0,
                    'category' => 'Máy Biến Áp & Trạm Biến Áp',
                    'brand' => 'MBT',
                    'brand_logo' => 'storage/media/brands/brand-mbt.svg',
                    'badge' => 'CHÍNH HÃNG',
                    'voltage' => '22/0.4kV',
                    'standard' => 'QĐ 62/EVN, IEC 60076',
                    'specs' => [
                        ['Công suất định mức', '1000kVA'],
                        ['Điện áp sơ/thứ cấp', '22/0.4kV'],
                        ['Tổ đấu dây', 'Dyn11'],
                        ['Tần số định mức', '50Hz'],
                    ]
                ],
            ],
            [
                'id' => 104,
                'title' => 'Máy cắt không khí ACB Schneider Masterpact MTZ',
                'slug' => 'may-cat-khong-khi-acb-schneider',
                'post_type' => 'product',
                'excerpt' => 'Máy cắt không khí hạ thế thông minh tích hợp giám sát năng lượng kỹ thuật số Class 1.',
                'content' => '<p>Masterpact MTZ là thế hệ máy cắt không khí mới nhất của Schneider Electric với khả năng kết nối IoT vượt trội và bảo vệ quá tải tin cậy.</p>',
                'featured_image' => 'storage/media/products/product-may-cat-khong-khi-acb.png',
                'status' => 'published',
                'meta_data' => [
                    'price' => 0,
                    'category' => 'Vật Tư Thiết Bị Điện',
                    'brand' => 'Schneider Electric',
                    'brand_logo' => 'storage/media/brands/brand-schneider.svg',
                    'badge' => 'CAO CẤP',
                    'voltage' => '690V',
                    'standard' => 'IEC 60947-2',
                    'specs' => [
                        ['Dòng định mức (In)', '800A - 4000A'],
                        ['Dòng cắt ngắn mạch (Icu)', '50kA - 150kA'],
                        ['Số cực', '3P / 4P'],
                    ]
                ],
            ],
            [
                'id' => 105,
                'title' => 'Tủ RMU Schneider 24kV RM6',
                'slug' => 'tu-rmu-schneider-24kv-rm6',
                'post_type' => 'product',
                'excerpt' => 'Tủ hợp bộ mạch vòng trung thế cách điện khí SF6 nhỏ gọn, an toàn tuyệt đối.',
                'content' => '<p>Tủ RM6 Schneider Electric là giải pháp phân phối trung thế hoàn hảo cho các trạm biến áp kiosk, trung tâm thương mại và tòa nhà cao tầng.</p>',
                'featured_image' => 'storage/media/products/product-tu-rmu-schneider.png',
                'status' => 'published',
                'meta_data' => [
                    'price' => 0,
                    'category' => 'Tủ Điện Trung Hạ Thế',
                    'brand' => 'Schneider Electric',
                    'brand_logo' => 'storage/media/brands/brand-schneider.svg',
                    'badge' => 'DỰ ÁN',
                    'voltage' => '24kV',
                    'standard' => 'IEC 62271-200',
                    'specs' => [
                        ['Điện áp định mức', '24kV'],
                        ['Dòng định mức thanh cái', '630A'],
                        ['Dòng cắt định mức', '20kA/1s'],
                    ]
                ],
            ],
            [
                'id' => 106,
                'title' => 'Cáp điện lực hạ thế CADIVI CXV 4x300mm2',
                'slug' => 'cap-dien-luc-ha-the-cadivi-cxv',
                'post_type' => 'product',
                'excerpt' => 'Cáp đồng cách điện XLPE, vỏ bọc PVC 0.6/1kV truyền dẫn công suất lớn.',
                'content' => '<p>Cáp đồng CXV CADIVI đáp ứng tiêu chuẩn TCVN 5935-1 / IEC 60502-1 cho hệ thống truyền tải điện trong nhà máy, tòa nhà và khu dân cư.</p>',
                'featured_image' => 'storage/media/products/product-cap-dien-cadivi.png',
                'status' => 'published',
                'meta_data' => [
                    'price' => 185000,
                    'category' => 'Vật Tư Thiết Bị Điện',
                    'brand' => 'CADIVI',
                    'brand_logo' => 'storage/media/brands/brand-cadivi.svg',
                    'badge' => 'BÁN CHẠY',
                    'voltage' => '0.6/1kV',
                    'standard' => 'TCVN 5935-1, IEC 60502-1',
                    'specs' => [
                        ['Ruột dẫn', 'Đồng mềm 99.99%'],
                        ['Cách điện', 'XLPE chống lão hóa'],
                        ['Vỏ bọc ngoài', 'PVC cao cấp chống cháy chậm'],
                    ]
                ],
            ]
        ];

        return collect($raw)->map(function ($item) {
            $post = new Post($item);
            $post->id = $item['id'];
            return $post;
        });
    }

    /**
     * In-memory Demo Fallback Projects (Zero DB write, 100% design fidelity)
     */
    public function getFallbackProjects(): Collection
    {
        $raw = [
            [
                'id' => 201,
                'title' => 'Hệ thống trạm biến áp & tủ điện Khách sạn Marriott Hà Nội',
                'slug' => 'khach-san-marriott-ha-noi',
                'post_type' => 'project',
                'excerpt' => 'Cung ứng trọn gói tủ RMU Schneider, máy biến áp MBT 2500kVA và hệ thống tủ điện hạ thế MSB cho tổ hợp khách sạn 5 sao quốc tế.',
                'content' => '<p>Đại Phúc 68 tự hào là nhà cung cấp vật tư thiết bị điện chính cho dự án nâng cấp hạ tầng năng lượng khách sạn JW Marriott Hà Nội, đảm bảo hệ thống cấp điện liên tục 99.99%.</p>',
                'featured_image' => 'storage/media/projects/project-detail-hero.jpg',
                'status' => 'published',
                'meta_data' => [
                    'category' => 'Nhà Hàng & Khách Sạn',
                    'scale' => '500 phòng nghỉ 5 sao, 3 phòng hội nghị quốc tế',
                    'location' => 'Nam Từ Liêm, Hà Nội',
                    'client' => 'Tập đoàn Bitexco',
                    'year' => '2024',
                    'scope' => 'Cung cấp tủ RMU, Máy biến áp 2500kVA, Cáp ngầm trung thế',
                    'challenges' => 'Yêu cầu tiến độ thi công khắt khe không làm gián đoạn các sự kiện ngoại giao cấp cao đang diễn ra tại khách sạn.'
                ]
            ],
            [
                'id' => 202,
                'title' => 'Cung cấp vật tư trạm biến áp Trung tâm Hội nghị Quốc gia',
                'slug' => 'trung-tam-hoi-nghi-quoc-gia',
                'post_type' => 'project',
                'excerpt' => 'Thiết kế nâng cấp hệ thống phân phối điện ngầm và thiết bị trạm biến áp hợp bộ chất lượng cao.',
                'content' => '<p>Dự án hiện đại hóa hệ thống điện phân phối phục vụ các hội nghị thượng đỉnh quốc tế tổ chức tại Hà Nội.</p>',
                'featured_image' => 'storage/media/projects/project-item-3.jpg',
                'status' => 'published',
                'meta_data' => [
                    'category' => 'Công Trình Công Cộng',
                    'scale' => 'Trung tâm tổ chức sự kiện cấp nhà nước diện tích 64ha',
                    'location' => 'Mễ Trì, Nam Từ Liêm, Hà Nội',
                    'client' => 'Ban Quản lý Dự án Đầu tư Xây dựng',
                    'year' => '2023',
                    'scope' => 'Cung ứng 4 máy biến áp MBT 2000kVA và hệ thống ACB Schneider',
                    'challenges' => 'Tiêu chuẩn an toàn phòng cháy chữa cháy và độ tin cậy nguồn điện dự phòng kép cực kỳ nghiêm ngặt.'
                ]
            ],
            [
                'id' => 203,
                'title' => 'Hệ thống điện thông minh & trạm hạ thế Khu đô thị Vinhomes Riverside',
                'slug' => 'vinhomes-riverside',
                'post_type' => 'project',
                'excerpt' => 'Phân phối cáp ngầm CADIVI và thiết bị đóng cắt trạm kiosk cho toàn khu biệt thự sinh thái cao cấp.',
                'content' => '<p>Giải pháp cáp ngầm trung hạ thế đồng bộ giúp tối ưu hóa cảnh quan xanh và mỹ quan đô thị cao cấp ven sông.</p>',
                'featured_image' => 'storage/media/projects/project-villa-park.jpg',
                'status' => 'published',
                'meta_data' => [
                    'category' => 'Nhà Ở & Biệt Thự',
                    'scale' => 'Hơn 1.500 căn biệt thự cao cấp ven kênh sinh thái',
                    'location' => 'Long Biên, Hà Nội',
                    'client' => 'Tập đoàn Vingroup',
                    'year' => '2024',
                    'scope' => 'Cung ứng 120km cáp ngầm CXV CADIVI và 18 trạm kiosk phân phối',
                    'challenges' => 'Mặt bằng thi công ngầm phức tạp, yêu cầu chống ngập nước và kháng ăn mòn cao.'
                ]
            ]
        ];

        return collect($raw)->map(function ($item) {
            $post = new Post($item);
            $post->id = $item['id'];
            return $post;
        });
    }

    /**
     * In-memory Demo Fallback News (Zero DB write, 100% design fidelity)
     */
    public function getFallbackNews(): Collection
    {
        $raw = [
            [
                'id' => 301,
                'title' => 'Đại Phúc 68 ký kết hợp tác chiến lược cung ứng thiết bị điện trung thế cùng Schneider Electric',
                'slug' => 'dai-phuc-68-hop-tac-schneider-electric',
                'post_type' => 'post',
                'excerpt' => 'Lễ ký kết hợp tác phân phối chính thức các dòng sản phẩm tủ RMU và thiết bị đóng cắt trung thế giữa Đại Phúc 68 và Schneider Electric Việt Nam.',
                'content' => '<p>Vừa qua, tại Hà Nội, Công ty Cổ phần Thương mại & Kỹ thuật Đại Phúc 68 đã chính thức ký kết thỏa thuận hợp tác phân phối chiến lược cùng Schneider Electric Việt Nam. Sự kiện đánh dấu cột mốc quan trọng trong việc nâng cao năng lực cung ứng thiết bị điện chất lượng cao cho thị trường công nghiệp và bất động sản Việt Nam.</p><p>Theo thỏa thuận, Đại Phúc 68 sẽ là nhà phân phối ủy quyền toàn diện cho các dòng sản phẩm tủ mạch vòng trung thế RM6, máy cắt hạ thế Masterpact MTZ và các giải pháp quản lý năng lượng thông minh EcoStruxure.</p>',
                'featured_image' => 'storage/media/news/news-1.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'meta_data' => [
                    'reading_time' => '4 phút',
                    'author' => 'Ban Biên Tập Đại Phúc 68',
                ]
            ],
            [
                'id' => 302,
                'title' => 'Giải pháp tối ưu hóa tổn thất điện năng cho trạm biến áp phân phối công nghiệp',
                'slug' => 'giai-phap-toi-uu-hoa-ton-that-dien-nang',
                'post_type' => 'post',
                'excerpt' => 'Phân tích các giải pháp kỹ thuật giúp doanh nghiệp sản xuất giảm thiểu hao hụt điện áp và tiết kiệm hàng trăm triệu đồng chi phí vận hành mỗi năm.',
                'content' => '<p>Tổn thất điện năng trong trạm biến áp công nghiệp là vấn đề nan giải của nhiều nhà máy sản xuất. Việc lựa chọn công nghệ lõi tôn silic định hướng từ tính cao và cuộn dây dẫn chất lượng từ các thương hiệu như MBT giúp giảm tổn thất không tải đến 25%.</p><p>Đại Phúc 68 cung cấp dịch vụ khảo sát và tư vấn trạm biến áp chuyên nghiệp, giúp nhà máy đạt hiệu suất tối ưu và hoàn vốn đầu tư nhanh chóng.</p>',
                'featured_image' => 'storage/media/news/news-2.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'meta_data' => [
                    'reading_time' => '6 phút',
                    'author' => 'Kỹ sư Trưởng Nguyễn Văn An',
                ]
            ],
            [
                'id' => 303,
                'title' => 'Tiêu chuẩn lựa chọn cáp điện ngầm trung thế cho các dự án bất động sản cao tầng',
                'slug' => 'tieu-chuan-lua-chon-cap-dien-ngam-trung-the',
                'post_type' => 'post',
                'excerpt' => 'Hướng dẫn kỹ thuật chi tiết về tiêu chuẩn TCVN và IEC khi thiết kế và thi công hệ thống cáp ngầm cho các tòa nhà chọc trời.',
                'content' => '<p>Trong các công trình phức hợp và cao ốc hiện đại, độ an toàn về điện và khả năng chịu tải của đường cáp ngầm trung thế là yếu tố sống còn. Bài viết này tổng hợp các tiêu chí kiểm định chất lượng, khả năng chống cháy lan và độ bền cơ học cần có khi chọn mua cáp ngầm CADIVI.</p>',
                'featured_image' => 'storage/media/news/news-3.jpg',
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'meta_data' => [
                    'reading_time' => '5 phút',
                    'author' => 'Phòng Kỹ Thuật Đại Phúc 68',
                ]
            ]
        ];

        return collect($raw)->map(function ($item) {
            $post = new Post($item);
            $post->id = $item['id'];
            return $post;
        });
    }

    /**
     * Nội dung bài viết tin tức mẫu khi bài viết chưa có nội dung
     */
    protected function getFallbackNewsContent(): string
    {
        $img1 = asset('storage/media/news/news-detail-section-1.png');
        $img2 = asset('storage/media/news/news-detail-section-2.jpg');
        $img3 = asset('storage/media/news/news-detail-section-3.png');

        return <<<HTML
<div class="space-y-4 pt-2">
  <h3 class="text-[22px] sm:text-[26px] md:text-[28px] font-bold uppercase text-neutral-900 tracking-wide text-left">
    1. TỔNG QUAN TIÊU CHUẨN KỸ THUẬT
  </h3>
  <p class="text-[14px] sm:text-[15px] md:text-[16px] text-neutral-800 font-normal leading-relaxed text-justify">
    Trong bối cảnh hiện đại hóa lưới điện và tăng cường độ tin cậy cung cấp điện, việc lựa chọn vật tư đạt chuẩn kiểm định của EVN và các tổ chức đo lường quốc tế như IEC, ANSI là điều kiện tiên quyết. Các thiết bị đóng cắt như cầu chì tự rơi FCO/LBFCO, chống sét van Polymer và máy biến áp hiệu suất cao giúp giảm thiểu tổn thất kỹ thuật và đảm bảo an toàn tuyệt đối cho người vận hành.
  </p>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 md:gap-[37px] pt-4">
    <figure class="w-full aspect-[444/334] rounded-[8px] overflow-hidden bg-neutral-100">
      <img class="w-full h-full object-cover object-center" src="{$img1}" alt="Kiểm định kỹ thuật thiết bị">
    </figure>
    <figure class="w-full aspect-[444/334] rounded-[8px] overflow-hidden bg-neutral-100">
      <img class="w-full h-full object-cover object-center" src="{$img2}" alt="Công trình trạm biến áp">
    </figure>
  </div>
</div>

<div class="space-y-4 pt-2">
  <h3 class="text-[22px] sm:text-[26px] md:text-[28px] font-bold uppercase text-neutral-900 tracking-wide text-left">
    2. GIẢI PHÁP THI CÔNG &amp; AN TOÀN VẬN HÀNH
  </h3>
  <p class="text-[14px] sm:text-[15px] md:text-[16px] text-neutral-800 font-normal leading-relaxed text-justify">
    Đại Phúc 68 cung cấp giải pháp đồng bộ từ khâu lập hồ sơ thiết kế, cung ứng thiết bị tại chân công trình, đến thi công lắp đặt và nghiệm thu đóng điện cùng cơ quan quản lý điện lực địa phương. Đội ngũ kỹ sư hiện trường luôn tuân thủ nghiêm ngặt quy trình an toàn điện theo thông tư của Bộ Công Thương.
  </p>
  <figure class="w-full aspect-[925/444] rounded-[12px] overflow-hidden bg-neutral-100 pt-4">
    <img class="w-full h-full object-cover object-center" src="{$img3}" alt="Hiện trường thi công xây lắp">
  </figure>
</div>

<div class="space-y-4 pt-2">
  <h3 class="text-[22px] sm:text-[26px] md:text-[28px] font-bold uppercase text-neutral-900 tracking-wide text-left">
    3. ĐÁNH GIÁ TỔNG THỂ &amp; HIỆU QUẢ ĐẦU TƯ
  </h3>
  <p class="text-[14px] sm:text-[15px] md:text-[16px] text-neutral-800 font-normal leading-relaxed text-justify">
    Sự kết hợp giữa sản phẩm chất lượng cao từ các nhà sản xuất uy tín như Vina Electric, Thăng Long, THC cùng năng lực tổng thầu thi công của Đại Phúc 68 đem lại lợi ích kinh tế lâu dài, giảm thời gian cắt điện sửa chữa và nâng cao tuổi thọ của hệ thống phân phối.
  </p>
</div>
HTML;
    }

    /**
     * Nội dung trang tĩnh mẫu khi trang chưa có nội dung
     */
    protected function getFallbackPageContent(): string
    {
        return <<<HTML
<div class="space-y-4 pt-2">
  <h3 class="text-[22px] sm:text-[26px] md:text-[28px] font-bold uppercase text-neutral-900 tracking-wide text-left">
    1. TỔNG QUAN NỘI DUNG &amp; PHẠM VI ÁP DỤNG
  </h3>
  <p class="text-[14px] sm:text-[15px] md:text-[16px] text-neutral-800 font-normal leading-relaxed text-justify">
    Chính sách này áp dụng cho toàn bộ khách hàng và đối tác khi mua sắm, sử dụng sản phẩm thiết bị điện và dịch vụ thi công cơ điện của Công ty Cổ phần Thương mại & Kỹ thuật Đại Phúc 68. Chúng tôi cam kết minh bạch và bảo đảm quyền lợi tối đa cho quý khách hàng.
  </p>
</div>

<div class="space-y-4 pt-2">
  <h3 class="text-[22px] sm:text-[26px] md:text-[28px] font-bold uppercase text-neutral-900 tracking-wide text-left">
    2. QUY ĐỊNH CHI TIẾT &amp; CAM KẾT CHẤT LƯỢNG
  </h3>
  <p class="text-[14px] sm:text-[15px] md:text-[16px] text-neutral-800 font-normal leading-relaxed text-justify">
    Mọi sản phẩm do Đại Phúc 68 cung cấp đều có đầy đủ chứng chỉ xuất xưởng CO/CQ, đạt tiêu chuẩn kỹ thuật của ngành điện lực Việt Nam (TCVN) và quốc tế (IEC, ANSI). Chúng tôi thực hiện bảo hành chính hãng từ 12 đến 36 tháng tùy theo từng chủng loại thiết bị.
  </p>
</div>

<div class="space-y-4 pt-2">
  <h3 class="text-[22px] sm:text-[26px] md:text-[28px] font-bold uppercase text-neutral-900 tracking-wide text-left">
    3. QUY TRÌNH HỖ TRỢ &amp; LIÊN HỆ GIẢI QUYẾT
  </h3>
  <p class="text-[14px] sm:text-[15px] md:text-[16px] text-neutral-800 font-normal leading-relaxed text-justify">
    Khi phát sinh bất kỳ yêu cầu bảo hành hoặc khiếu nại kỹ thuật, quý khách hàng vui lòng liên hệ trực tiếp với bộ phận chăm sóc khách hàng qua Hotline hoặc gửi yêu cầu tại trang Liên hệ. Đội ngũ kỹ thuật của chúng tôi sẽ tiếp nhận và phản hồi xử lý trong vòng 24 giờ làm việc.
  </p>
</div>
HTML;
    }
}
