<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use App\Models\Taxonomy;
use App\Services\ProductService;
use App\Traits\HasCrudAlerts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ProductController extends Controller
{
    use HasCrudAlerts;

    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(Request $request)
    {
        $query = Post::where('post_type', 'product')
            ->with(['taxonomies', 'variations']);

        if ($request->status === 'trashed') {
            $query->onlyTrashed();
        } else {
            $query->when($request->status, fn ($q) => $q->where('status', $request->status));
        }

        if ($request->filled('category')) {
            $query->whereHas('taxonomies', function ($q) use ($request) {
                $q->where('term_taxonomy_id', $request->category);
            });
        }

        $products = $query->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $parentCategories = Taxonomy::where('taxonomy', 'product_cat')->whereNull('parent_id')->with('children')->get();
        $currentProject = Project::where('code', request()->route('projectCode'))->first();
        $languageId = 1;

        return view('cms.products.index', compact('products', 'parentCategories', 'currentProject', 'languageId'));
    }

    public function create(Request $request)
    {
        $categories = $this->productService->getCategories();
        $categoriesTree = $this->buildCategoryOptions($categories);
        $brands = $this->productService->getBrands();
        $globalAttributes = $this->productService->getGlobalAttributes();
        $currentLang = 'vi';

        return view('cms.products.create', compact('categories', 'categoriesTree', 'brands', 'globalAttributes', 'currentLang'));
    }

    private function buildCategoryOptions($categories, $parentId = null, $prefix = '')
    {
        $options = [];
        foreach ($categories->where('parent_id', $parentId) as $category) {
            $options[] = (object) [
                'id' => $category->id,
                'name' => $prefix.$category->name,
            ];
            $childOptions = $this->buildCategoryOptions($categories, $category->id, $prefix.'  └─ ');
            $options = array_merge($options, $childOptions);
        }

        return $options;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:draft,published,archived',
            'product_type' => 'nullable|in:simple,variable',
            'sku' => 'nullable|string|max:100',
            'price' => 'nullable',
            'sale_price' => 'nullable',
            'order' => 'nullable|integer',
            'sort_order' => 'nullable|integer',
        ]);

        try {
            $post = $this->productService->saveProduct($request->all());

            $this->alertCreated('sản phẩm', "Sản phẩm '{$post->title}' đã được thêm thành công.");

            $route = request()->route('projectCode')
                ? route('project.admin.products.index', request()->route('projectCode'))
                : (Route::has('admin.products.index') ? route('admin.products.index') : route('cms.products.index'));

            return redirect($route);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            Log::error('Lỗi lưu sản phẩm: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi khi lưu sản phẩm: '.$e->getMessage(),
            ])->withInput();
        }
    }

    private function resolveProduct($projectCodeOrId, $id = null): Post
    {
        $param = $id ?? $projectCodeOrId;

        return Post::where('post_type', 'product')
            ->where(function ($q) use ($param) {
                $q->where('id', $param)->orWhere('slug', $param);
            })
            ->with(['taxonomies', 'variations'])
            ->firstOrFail();
    }

    public function edit(Request $request, $projectCodeOrId, $id = null)
    {
        $product = $this->resolveProduct($projectCodeOrId, $id);

        // Map Post attributes to view properties
        $product->name = $product->title;
        $product->description = $product->content;
        $product->short_description = $product->excerpt;

        $metaData = is_string($product->meta_data) ? json_decode($product->meta_data, true) : ($product->meta_data ?? []);
        $product->sku = $metaData['sku'] ?? '';
        $product->price = $metaData['price'] ?? 0;
        $product->sale_price = $metaData['sale_price'] ?? 0;
        $product->product_type = $metaData['product_type'] ?? 'simple';
        $product->stock_quantity = $metaData['stock_quantity'] ?? 0;
        $product->manage_stock = $metaData['manage_stock'] ?? false;
        $product->stock_status = $metaData['stock_status'] ?? ($product->manage_stock && $product->stock_quantity <= 0 ? 'out_of_stock' : 'in_stock');
        $product->is_featured = $metaData['is_featured'] ?? false;
        $product->gallery = $metaData['gallery'] ?? [];
        $product->specs = $metaData['specs'] ?? [];
        $product->variation_attributes = $metaData['variation_attributes'] ?? [];
        $product->order = (int) ($product->order ?? ($metaData['order'] ?? 0));
        $product->guarantee_override = $metaData['guarantee_override'] ?? 'default';
        $product->custom_guarantees = $metaData['custom_guarantees'] ?? [];

        $seoData = is_string($product->seo_data) ? json_decode($product->seo_data, true) : ($product->seo_data ?? []);
        $product->focus_keyword = $seoData['focus_keyword'] ?? '';
        $product->meta_keywords = $seoData['meta_keywords'] ?? '';
        $product->schema_type = $seoData['schema_type'] ?? 'Product';
        $product->canonical_url = $seoData['canonical_url'] ?? '';
        $product->custom_schema = $seoData['custom_schema'] ?? '';
        $product->noindex = $seoData['noindex'] ?? false;

        $categories = $this->productService->getCategories();
        $categoriesTree = $this->buildCategoryOptions($categories);
        $brands = $this->productService->getBrands();
        $globalAttributes = $this->productService->getGlobalAttributes();
        $currentLang = 'vi';

        // Categories & Brands for view
        $product->categories = $product->taxonomies->where('taxonomy', 'product_cat');
        $product->brands = $product->taxonomies->whereIn('taxonomy', ['brand', 'product_brand']);

        return view('cms.products.edit', compact('product', 'categories', 'categoriesTree', 'brands', 'globalAttributes', 'currentLang'));
    }

    public function update(Request $request, $projectCodeOrId, $id = null)
    {
        $product = $this->resolveProduct($projectCodeOrId, $id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:draft,published,archived',
            'product_type' => 'nullable|in:simple,variable',
            'sku' => 'nullable|string|max:100',
            'price' => 'nullable',
            'sale_price' => 'nullable',
            'order' => 'nullable|integer',
            'sort_order' => 'nullable|integer',
        ]);

        try {
            $post = $this->productService->saveProduct($request->all(), $product);

            $this->alertUpdated('sản phẩm', "Sản phẩm '{$post->title}' đã được cập nhật thành công.");

            $route = request()->route('projectCode')
                ? route('project.admin.products.index', request()->route('projectCode'))
                : (Route::has('admin.products.index') ? route('admin.products.index') : route('cms.products.index'));

            return redirect($route);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            Log::error('Lỗi cập nhật sản phẩm: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi khi cập nhật sản phẩm: '.$e->getMessage(),
            ])->withInput();
        }
    }

    public function destroy($projectCodeOrId, $id = null)
    {
        $param = $id ?? $projectCodeOrId;
        $post = Post::where('post_type', 'product')
            ->where(function ($q) use ($param) {
                $q->where('id', $param)->orWhere('slug', $param);
            })->firstOrFail();

        $title = $post->title;
        $post->delete();

        $this->alertDeleted('sản phẩm', "Sản phẩm '{$title}' đã được chuyển vào thùng rác.");

        $route = request()->route('projectCode')
            ? route('project.admin.products.index', request()->route('projectCode'))
            : (Route::has('admin.products.index') ? route('admin.products.index') : route('cms.products.index'));

        return redirect($route);
    }

    public function restore($projectCodeOrId, $id = null)
    {
        $param = $id ?? $projectCodeOrId;
        $product = Post::withTrashed()->where('post_type', 'product')
            ->where(function ($q) use ($param) {
                $q->where('id', $param)->orWhere('slug', $param);
            })->firstOrFail();

        $product->restore();
        $product->variations()->withTrashed()->restore();

        $this->alertSuccess('Đã khôi phục sản phẩm thành công.');

        return redirect()->back();
    }

    public function forceDelete($projectCodeOrId, $id = null)
    {
        $param = $id ?? $projectCodeOrId;
        $product = Post::withTrashed()->where('post_type', 'product')
            ->where(function ($q) use ($param) {
                $q->where('id', $param)->orWhere('slug', $param);
            })->firstOrFail();

        $product->variations()->withTrashed()->forceDelete();
        $product->taxonomies()->detach();
        $product->forceDelete();

        $this->alertSuccess('Đã xóa vĩnh viễn sản phẩm thành công.');

        return redirect()->back();
    }

    public function toggleBadge(Request $request)
    {
        $productId = $request->input('product_id');
        $badgeType = $request->input('badge_type');

        $post = Post::where('post_type', 'product')
            ->where(function ($q) use ($productId) {
                $q->where('id', $productId)->orWhere('slug', $productId);
            })->first();

        if (! $post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
        }

        $metaData = is_string($post->meta_data) ? json_decode($post->meta_data, true) : ($post->meta_data ?? []);

        $newState = false;
        if ($badgeType === 'featured') {
            $newState = ! ($metaData['is_featured'] ?? false);
            $metaData['is_featured'] = $newState;
        } elseif ($badgeType === 'favorite') {
            $newState = ! ($metaData['is_favorite'] ?? false);
            $metaData['is_favorite'] = $newState;
        }

        $post->meta_data = $metaData;
        $post->save();

        return response()->json(['success' => true, 'state' => $newState]);
    }

    public function bulkEdit(Request $request)
    {
        $ids = $request->input('ids', []);
        if (! is_array($ids)) {
            $ids = ! empty($ids) ? [$ids] : [];
        }

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Không có sản phẩm nào được chọn']);
        }

        $products = Post::where('post_type', 'product')
            ->where(function ($q) use ($ids) {
                $q->whereIn('id', $ids)->orWhereIn('slug', $ids);
            })
            ->with(['taxonomies'])
            ->get()
            ->map(function ($p) {
                $metaData = is_string($p->meta_data) ? json_decode($p->meta_data, true) : ($p->meta_data ?? []);

                $categoryTaxonomies = $p->taxonomies->where('taxonomy', 'product_cat');
                $brandTaxonomies = $p->taxonomies->whereIn('taxonomy', ['brand', 'product_brand']);

                $categoryIds = $categoryTaxonomies->pluck('id')->values()->all();
                $brandIds = $brandTaxonomies->pluck('id')->values()->all();

                return [
                    'id' => $p->id,
                    'name' => $p->title,
                    'status' => $p->status,
                    'sku' => $metaData['sku'] ?? '',
                    'price' => isset($metaData['price']) && $metaData['price'] !== '' ? (float) $metaData['price'] : 0,
                    'sale_price' => isset($metaData['sale_price']) && $metaData['sale_price'] !== '' && $metaData['sale_price'] !== null ? (float) $metaData['sale_price'] : null,
                    'stock_quantity' => isset($metaData['stock_quantity']) ? (int) $metaData['stock_quantity'] : 0,
                    'order' => (int) ($p->order ?? ($metaData['order'] ?? 0)),
                    'category_ids' => $categoryIds,
                    'brand_ids' => $brandIds,
                    'category' => $categoryTaxonomies->first() ? ['id' => $categoryTaxonomies->first()->id, 'name' => $categoryTaxonomies->first()->name] : null,
                    'brand' => $brandTaxonomies->first() ? ['id' => $brandTaxonomies->first()->id, 'name' => $brandTaxonomies->first()->name] : null,
                ];
            });

        $categories = Taxonomy::where('taxonomy', 'product_cat')->orderBy('name')->get()->map(function ($c) {
            return ['id' => $c->id, 'name' => $c->name];
        });

        $brands = Taxonomy::whereIn('taxonomy', ['brand', 'product_brand'])->orderBy('name')->get()->map(function ($b) {
            return ['id' => $b->id, 'name' => $b->name];
        });

        return response()->json([
            'success' => true,
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $ids = $request->input('ids', []);
        if (! is_array($ids)) {
            $ids = ! empty($ids) ? [$ids] : [];
        }

        $productsData = $request->input('products', []);

        if (empty($ids) && empty($productsData)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ít nhất một sản phẩm để cập nhật.']);
        }

        if (! empty($productsData)) {
            foreach ($productsData as $data) {
                $post = Post::where('post_type', 'product')
                    ->where(function ($q) use ($data) {
                        $q->where('id', $data['id'])->orWhere('slug', $data['id']);
                    })->first();

                if ($post) {
                    $metaData = is_string($post->meta_data) ? json_decode($post->meta_data, true) : ($post->meta_data ?? []);

                    if (array_key_exists('sku', $data)) {
                        $metaData['sku'] = $data['sku'];
                    }
                    if (array_key_exists('price', $data)) {
                        $metaData['price'] = $data['price'] !== null && $data['price'] !== '' ? (float) $data['price'] : ($metaData['price'] ?? 0);
                    }
                    if (array_key_exists('sale_price', $data)) {
                        $metaData['sale_price'] = $data['sale_price'] !== null && $data['sale_price'] !== '' ? (float) $data['sale_price'] : null;
                    }
                    if (array_key_exists('stock_quantity', $data)) {
                        $metaData['stock_quantity'] = (int) $data['stock_quantity'];
                    }
                    if (array_key_exists('order', $data) && $data['order'] !== '' && $data['order'] !== null) {
                        $post->order = (int) $data['order'];
                        $metaData['order'] = (int) $data['order'];
                    }

                    if (! empty($data['name'])) {
                        $post->title = $data['name'];
                    }
                    if (! empty($data['status'])) {
                        $post->status = $data['status'];
                    }
                    if (isset($data['categories']) && is_array($data['categories'])) {
                        $post->taxonomies()->sync($data['categories']);
                    }

                    $post->meta_data = $metaData;
                    $post->save();
                }
            }

            Cache::flush();
            return response()->json(['success' => true, 'message' => 'Đã cập nhật thành công']);
        }

        $status = $request->input('status');
        $priceMode = $request->input('price_mode', 'no_change');
        $priceValue = $request->filled('price_value') ? (float) $request->input('price_value') : null;
        $salePriceMode = $request->input('sale_price_mode', 'no_change');
        $salePriceValue = $request->filled('sale_price_value') ? (float) $request->input('sale_price_value') : null;
        $categories = $request->input('categories');
        $brands = $request->input('brands');

        $posts = Post::where('post_type', 'product')
            ->whereIn('id', $ids)
            ->with('taxonomies')
            ->get();

        foreach ($posts as $post) {
            $metaData = is_string($post->meta_data) ? json_decode($post->meta_data, true) : ($post->meta_data ?? []);
            $currentPrice = isset($metaData['price']) && $metaData['price'] !== '' ? (float) $metaData['price'] : 0;
            $currentSalePrice = isset($metaData['sale_price']) && $metaData['sale_price'] !== '' && $metaData['sale_price'] !== null ? (float) $metaData['sale_price'] : null;

            // 1. Status
            if (! empty($status) && in_array($status, ['draft', 'published', 'archived'])) {
                $post->status = $status;
            }

            // Thứ tự sắp xếp (càng âm thì càng lên cao)
            if ($request->has('order') && $request->input('order') !== '' && $request->input('order') !== null) {
                $orderVal = (int) $request->input('order');
                $post->order = $orderVal;
                $metaData['order'] = $orderVal;
            }

            // 2. Regular Price
            if ($priceMode !== 'no_change' && $priceValue !== null) {
                switch ($priceMode) {
                    case 'fixed':
                        $metaData['price'] = max(0, $priceValue);
                        break;
                    case 'percent_increase':
                        $metaData['price'] = max(0, round($currentPrice * (1 + $priceValue / 100)));
                        break;
                    case 'percent_decrease':
                        $metaData['price'] = max(0, round($currentPrice * (1 - $priceValue / 100)));
                        break;
                    case 'amount_increase':
                        $metaData['price'] = max(0, $currentPrice + $priceValue);
                        break;
                    case 'amount_decrease':
                        $metaData['price'] = max(0, $currentPrice - $priceValue);
                        break;
                }
            }

            $effectiveRegularPrice = isset($metaData['price']) ? (float) $metaData['price'] : $currentPrice;

            // 3. Sale Price
            if ($salePriceMode === 'clear') {
                $metaData['sale_price'] = null;
            } elseif ($salePriceMode !== 'no_change' && $salePriceValue !== null) {
                switch ($salePriceMode) {
                    case 'fixed':
                        $metaData['sale_price'] = max(0, $salePriceValue);
                        break;
                    case 'percent_decrease_regular':
                        $metaData['sale_price'] = max(0, round($effectiveRegularPrice * (1 - $salePriceValue / 100)));
                        break;
                    case 'percent_increase':
                        $base = $currentSalePrice ?? $effectiveRegularPrice;
                        $metaData['sale_price'] = max(0, round($base * (1 + $salePriceValue / 100)));
                        break;
                    case 'percent_decrease':
                        $base = $currentSalePrice ?? $effectiveRegularPrice;
                        $metaData['sale_price'] = max(0, round($base * (1 - $salePriceValue / 100)));
                        break;
                    case 'amount_increase':
                        $base = $currentSalePrice ?? $effectiveRegularPrice;
                        $metaData['sale_price'] = max(0, $base + $salePriceValue);
                        break;
                    case 'amount_decrease':
                        $base = $currentSalePrice ?? $effectiveRegularPrice;
                        $metaData['sale_price'] = max(0, $base - $salePriceValue);
                        break;
                }
            }

            $post->meta_data = $metaData;
            $post->save();

            // 4. Taxonomies (categories & brands)
            $existingTaxonomyIds = $post->taxonomies->pluck('id')->all();
            $shouldSync = false;

            if (is_array($categories) && ! empty($categories)) {
                $shouldSync = true;
                $nonCategoryIds = $post->taxonomies
                    ->where('taxonomy', '!=', 'product_cat')
                    ->pluck('id')
                    ->all();
                $existingTaxonomyIds = array_unique(array_merge($nonCategoryIds, array_map('intval', $categories)));
            }

            if (is_array($brands) && ! empty($brands)) {
                $shouldSync = true;
                $nonBrandIds = collect($existingTaxonomyIds)->filter(function ($tid) use ($post) {
                    $tax = $post->taxonomies->firstWhere('id', $tid);
                    return $tax ? ! in_array($tax->taxonomy, ['brand', 'product_brand']) : true;
                })->all();
                $existingTaxonomyIds = array_unique(array_merge($nonBrandIds, array_map('intval', $brands)));
            }

            if ($shouldSync) {
                $post->taxonomies()->sync($existingTaxonomyIds);
            }
        }

        Cache::flush();

        return response()->json(['success' => true, 'message' => 'Đã cập nhật thành công']);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'order' => 'required|integer',
        ]);

        $post = Post::where('post_type', 'product')
            ->where(function ($q) use ($request) {
                $q->where('id', $request->id)->orWhere('slug', $request->id);
            })->first();

        if (! $post) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
        }

        $order = (int) $request->order;
        $post->order = $order;
        $metaData = is_string($post->meta_data) ? json_decode($post->meta_data, true) : ($post->meta_data ?? []);
        $metaData['order'] = $order;
        $post->meta_data = $metaData;
        $post->save();

        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật thứ tự sản phẩm '{$post->title}' thành {$order}",
            'order' => $order,
        ]);
    }

    public function syncWidgets(Request $request, $projectCode = null)
    {
        Cache::flush();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã làm mới bộ nhớ đệm cho các widget sản phẩm!',
            ]);
        }

        $this->alertSuccess('Đã làm mới bộ nhớ đệm cho các widget sản phẩm!');

        return redirect()->back();
    }
}
