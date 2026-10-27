<?php

namespace App\Services;

use App\Models\Post;
use App\Models\ProductVariation;
use App\Models\Taxonomy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductService
{
    /**
     * Create or update a product (Simple or Variable).
     *
     * @param  array  $data
     * @param  Post|null  $post
     * @return Post
     * @throws ValidationException
     */
    public function saveProduct(array $data, Post|int|null $post = null): Post
    {
        if (is_numeric($post)) {
            $post = Post::find($post);
        }

        return DB::transaction(function () use ($data, $post) {
            $isCreate = ! $post;
            $productType = $data['product_type'] ?? ($post ? $post->product_type : 'simple');
            if (! in_array($productType, ['simple', 'variable'])) {
                $productType = 'simple';
            }

            // Slug handling
            $name = trim($data['name'] ?? ($data['title'] ?? ($post ? $post->title : '')));
            if (empty($name)) {
                throw ValidationException::withMessages(['name' => 'Tên sản phẩm không được để trống.']);
            }

            $rawSlug = ! empty($data['slug']) ? Str::slug($data['slug']) : ($post && ! empty($post->slug) ? $post->slug : Str::slug($name));
            $slug = $this->generateUniqueSlug($rawSlug, $post?->id);

            // Existing meta
            $currentMeta = $post ? (is_array($post->meta_data) ? $post->meta_data : json_decode($post->meta_data ?? '[]', true)) : [];

            // Simple product pricing & stock
            $regularPrice = isset($data['price']) && $data['price'] !== '' ? (float) str_replace(['.', ','], ['', '.'], (string) $data['price']) : 0.0;
            $salePrice = isset($data['sale_price']) && $data['sale_price'] !== '' && (float) $data['sale_price'] > 0
                ? (float) str_replace(['.', ','], ['', '.'], (string) $data['sale_price'])
                : null;

            if ($salePrice !== null && $salePrice >= $regularPrice && $regularPrice > 0) {
                // Sale price should be less than regular price if both exist
                // We keep it or allow equal, but if greater, we can set it to null or throw
                if ($salePrice > $regularPrice) {
                    throw ValidationException::withMessages(['sale_price' => 'Giá khuyến mãi không được lớn hơn giá gốc.']);
                }
            }

            $manageStock = ! empty($data['manage_stock']);
            $stockQuantity = isset($data['stock_quantity']) ? (int) $data['stock_quantity'] : 0;

            $rawStatus = $data['stock_status'] ?? null;
            if ($rawStatus === 'instock' || $rawStatus === 'in_stock') {
                $stockStatus = 'in_stock';
            } elseif ($rawStatus === 'outofstock' || $rawStatus === 'out_of_stock') {
                $stockStatus = 'out_of_stock';
            } else {
                $stockStatus = $manageStock ? ($stockQuantity > 0 ? 'in_stock' : 'out_of_stock') : 'in_stock';
            }

            // Format gallery
            $gallery = $data['gallery'] ?? ($currentMeta['gallery'] ?? []);
            if (is_string($gallery)) {
                $gallery = json_decode($gallery, true) ?: array_filter(explode(',', $gallery));
            }

            // Format specs (descriptive attributes)
            $specs = $this->sanitizeSpecs($data['specs'] ?? ($currentMeta['specs'] ?? []));

            // Format all attributes (Variation & Descriptive)
            $rawAttrs = $data['attributes'] ?? ($currentMeta['attributes'] ?? []);
            $attributes = $this->sanitizeAttributes($rawAttrs);

            // Extract variation attributes from $attributes or fallback
            $variationAttributes = collect($attributes)->filter(fn ($a) => ! empty($a['variation']))->values()->all();
            if (empty($variationAttributes)) {
                $variationAttributes = $this->sanitizeVariationAttributes($data['variation_attributes'] ?? ($currentMeta['variation_attributes'] ?? []));
            }

            $metaData = array_merge($currentMeta, [
                'product_type' => $productType,
                'sku' => trim($data['sku'] ?? ($currentMeta['sku'] ?? '')),
                'price' => $regularPrice,
                'sale_price' => $salePrice,
                'date_on_sale_from' => $data['date_on_sale_from'] ?? null,
                'date_on_sale_to' => $data['date_on_sale_to'] ?? null,
                'manage_stock' => $manageStock,
                'stock_quantity' => $stockQuantity,
                'stock_status' => $stockStatus,
                'gallery' => array_values(array_filter($gallery)),
                'specs' => $specs,
                'attributes' => $attributes,
                'variation_attributes' => $variationAttributes,
                'is_featured' => ! empty($data['is_featured']),
                'brand' => $data['brand_name'] ?? ($currentMeta['brand'] ?? null),
                'voltage' => $data['voltage'] ?? ($currentMeta['voltage'] ?? null),
                'standard' => $data['standard'] ?? ($currentMeta['standard'] ?? null),
                'badge' => $data['badge'] ?? ($currentMeta['badge'] ?? null),
                'guarantee_override' => $data['guarantee_override'] ?? ($currentMeta['guarantee_override'] ?? 'default'),
                'custom_guarantees' => isset($data['custom_guarantees']) ? (is_array($data['custom_guarantees']) ? array_values(array_filter($data['custom_guarantees'])) : (json_decode($data['custom_guarantees'], true) ?: [])) : ($currentMeta['custom_guarantees'] ?? []),
                'order' => isset($data['order']) && $data['order'] !== '' ? (int) $data['order'] : (isset($data['sort_order']) && $data['sort_order'] !== '' ? (int) $data['sort_order'] : ($post?->order ?? 0)),
            ]);

            $order = isset($data['order']) && $data['order'] !== '' ? (int) $data['order'] : (isset($data['sort_order']) && $data['sort_order'] !== '' ? (int) $data['sort_order'] : ($post?->order ?? 0));

            $postAttributes = [
                'title' => $name,
                'slug' => $slug,
                'excerpt' => function_exists('sanitize_editor_html') ? sanitize_editor_html($data['short_description'] ?? ($data['excerpt'] ?? null)) : ($data['short_description'] ?? ($data['excerpt'] ?? null)),
                'content' => function_exists('sanitize_editor_html') ? sanitize_editor_html($data['description'] ?? ($data['content'] ?? '')) : ($data['description'] ?? ($data['content'] ?? '')),
                'featured_image' => $data['featured_image'] ?? null,
                'post_type' => 'product',
                'order' => $order,
                'status' => in_array($data['status'] ?? '', ['draft', 'published', 'archived']) ? $data['status'] : 'draft',
                'meta_title' => !empty(trim((string)($data['meta_title'] ?? ''))) ? trim($data['meta_title']) : $name,
                'meta_description' => !empty(trim((string)($data['meta_description'] ?? ''))) ? trim($data['meta_description']) : (!empty(trim((string)($data['short_description'] ?? ''))) ? trim($data['short_description']) : Str::limit(strip_tags($data['description'] ?? ''), 160)),
                'seo_data' => [
                    'focus_keyword' => !empty(trim((string)($data['focus_keyword'] ?? ''))) ? trim($data['focus_keyword']) : null,
                    'meta_keywords' => !empty(trim((string)($data['meta_keywords'] ?? ''))) ? trim($data['meta_keywords']) : null,
                    'schema_type' => !empty(trim((string)($data['schema_type'] ?? ''))) ? trim($data['schema_type']) : 'Product',
                    'canonical_url' => !empty(trim((string)($data['canonical_url'] ?? ''))) ? trim($data['canonical_url']) : null,
                    'custom_schema' => !empty(trim((string)($data['custom_schema'] ?? ''))) ? trim($data['custom_schema']) : null,
                    'noindex' => ! empty($data['noindex']),
                ],
                'meta_data' => $metaData,
            ];

            if ($isCreate) {
                $postAttributes['author_id'] = auth()->id();
                $post = Post::create($postAttributes);
            } else {
                $post->update($postAttributes);
            }

            // Sync Categories & Brands via term_relationships
            $termIds = [];
            if (! empty($data['categories'])) {
                foreach ((array) $data['categories'] as $cat) {
                    if (is_numeric($cat)) {
                        $termIds[] = (int) $cat;
                    } elseif (is_string($cat) && trim($cat) !== '') {
                        $catName = trim($cat);
                        $catSlug = Str::slug($catName);
                        $taxonomyRecord = Taxonomy::where('taxonomy', 'product_cat')
                            ->where(function ($q) use ($catName, $catSlug) {
                                $q->where('name', $catName)->orWhere('slug', $catSlug);
                            })->first();
                        if (! $taxonomyRecord) {
                            $taxonomyRecord = Taxonomy::create([
                                'name' => $catName,
                                'slug' => $catSlug,
                                'taxonomy' => 'product_cat',
                                'status' => 'active',
                            ]);
                        }
                        $termIds[] = $taxonomyRecord->id;
                    }
                }
            }

            if (! empty($data['brands'])) {
                foreach ((array) $data['brands'] as $brand) {
                    if (is_numeric($brand)) {
                        $termIds[] = (int) $brand;
                    } elseif (is_string($brand) && trim($brand) !== '') {
                        $brandName = trim($brand);
                        $brandSlug = Str::slug($brandName);
                        $taxonomyRecord = Taxonomy::whereIn('taxonomy', ['brand', 'product_brand'])
                            ->where(function ($q) use ($brandName, $brandSlug) {
                                $q->where('name', $brandName)->orWhere('slug', $brandSlug);
                            })->first();
                        if (! $taxonomyRecord) {
                            $taxonomyRecord = Taxonomy::create([
                                'name' => $brandName,
                                'slug' => $brandSlug,
                                'taxonomy' => 'brand',
                                'status' => 'active',
                            ]);
                        }
                        $termIds[] = $taxonomyRecord->id;
                    }
                }
            } elseif (! empty($data['brand_id'])) {
                $termIds[] = (int) $data['brand_id'];
            }

            // Sync product attributes & values via taxonomies (taxonomy: product_attribute)
            if (! empty($variationAttributes)) {
                foreach ($variationAttributes as $attr) {
                    $attrName = trim($attr['name'] ?? '');
                    if ($attrName === '') {
                        continue;
                    }

                    $attrSlug = ! empty($attr['slug']) ? Str::slug($attr['slug']) : Str::slug($attrName);
                    $parentAttr = Taxonomy::firstOrCreate(
                        ['slug' => $attrSlug, 'taxonomy' => 'product_attribute'],
                        [
                            'name' => $attrName,
                            'parent_id' => null,
                            'status' => 'published',
                            'meta_data' => ['type' => 'button'],
                        ]
                    );
                    $termIds[] = $parentAttr->id;

                    $values = $attr['values'] ?? [];
                    if (is_string($values)) {
                        $values = array_map('trim', explode(',', $values));
                    }
                    foreach ((array) $values as $val) {
                        $valName = trim((string) $val);
                        if ($valName === '') {
                            continue;
                        }

                        $valSlug = $attrSlug.'-'.Str::slug($valName);
                        $childAttr = Taxonomy::firstOrCreate(
                            ['slug' => $valSlug, 'taxonomy' => 'product_attribute', 'parent_id' => $parentAttr->id],
                            [
                                'name' => $valName,
                                'status' => 'published',
                            ]
                        );
                        $termIds[] = $childAttr->id;
                    }
                }
            }

            $post->taxonomies()->sync(array_unique(array_filter($termIds)));

            // If Variable Product, synchronize variations safely
            if ($productType === 'variable') {
                $this->syncVariations($post, $data['variations'] ?? []);
            } else {
                // If converted to simple product, soft delete or disable existing variations
                $post->variations()->update(['status' => 'inactive']);
            }

            return $post->fresh(['taxonomies', 'variations']);
        });
    }

    /**
     * Synchronize variations for a variable product with duplication prevention and transaction safety.
     *
     * @param  Post  $post
     * @param  array  $variationsData
     * @return void
     * @throws ValidationException
     */
    public function syncVariations(Post $post, array $variationsData): void
    {
        $existingVariations = $post->variations()->get()->keyBy('id');
        $processedIds = [];
        $seenHashes = [];

        foreach ($variationsData as $index => $vData) {
            $attributes = $vData['attributes'] ?? [];
            if (is_string($attributes)) {
                $attributes = json_decode($attributes, true) ?: [];
            }

            // Clean attribute values
            $cleanedAttrs = [];
            foreach ($attributes as $k => $v) {
                $kClean = trim((string) $k);
                $vClean = trim((string) $v);
                if ($kClean !== '' && $vClean !== '') {
                    $cleanedAttrs[$kClean] = $vClean;
                }
            }

            if (empty($cleanedAttrs)) {
                continue; // Skip invalid variation without attribute combinations
            }

            $hash = ProductVariation::computeAttributeHash($cleanedAttrs);

            // Duplicate combination check in same batch
            if (isset($seenHashes[$hash])) {
                throw ValidationException::withMessages([
                    "variations.{$index}" => "Tổ hợp thuộc tính '".implode(' - ', array_values($cleanedAttrs))."' đã bị trùng lặp trong sản phẩm này.",
                ]);
            }
            $seenHashes[$hash] = true;

            $vPrice = isset($vData['price']) && $vData['price'] !== '' ? (float) str_replace(['.', ','], ['', '.'], (string) $vData['price']) : 0.0;
            $vSalePrice = isset($vData['sale_price']) && $vData['sale_price'] !== '' && (float) $vData['sale_price'] > 0
                ? (float) str_replace(['.', ','], ['', '.'], (string) $vData['sale_price'])
                : null;

            if ($vSalePrice !== null && $vSalePrice > $vPrice && $vPrice > 0) {
                throw ValidationException::withMessages([
                    "variations.{$index}.sale_price" => "Giá khuyến mãi của biến thể (".implode(' - ', array_values($cleanedAttrs)).") không được lớn hơn giá gốc.",
                ]);
            }

            $vManageStock = ! empty($vData['manage_stock']);
            $vStockQty = isset($vData['stock_quantity']) ? (int) $vData['stock_quantity'] : 0;
            $vStockStatus = $vData['stock_status'] ?? ($vManageStock ? ($vStockQty > 0 ? 'in_stock' : 'out_of_stock') : 'in_stock');
            $vStatus = in_array($vData['status'] ?? '', ['active', 'inactive']) ? $vData['status'] : 'active';
            $vSku = ! empty($vData['sku']) ? trim($vData['sku']) : null;
            $vImage = $vData['image'] ?? null;

            $variationPayload = [
                'post_id' => $post->id,
                'sku' => $vSku,
                'price' => $vPrice,
                'sale_price' => $vSalePrice,
                'manage_stock' => $vManageStock,
                'stock_quantity' => $vStockQty,
                'stock_status' => $vStockStatus,
                'image' => $vImage,
                'attributes' => $cleanedAttrs,
                'attribute_hash' => $hash,
                'status' => $vStatus,
                'order' => (int) ($vData['order'] ?? $index),
            ];

            $variationId = ! empty($vData['id']) ? (int) $vData['id'] : null;

            if ($variationId && $existingVariations->has($variationId)) {
                /** @var ProductVariation $varModel */
                $varModel = $existingVariations->get($variationId);
                $varModel->update($variationPayload);
                $processedIds[] = $varModel->id;
            } else {
                // Check if variation with this attribute_hash already exists on this product
                $existingByHash = $post->variations()->where('attribute_hash', $hash)->first();
                if ($existingByHash) {
                    $existingByHash->update($variationPayload);
                    $processedIds[] = $existingByHash->id;
                } else {
                    $created = ProductVariation::create($variationPayload);
                    $processedIds[] = $created->id;
                }
            }
        }

        // Only soft-delete variations that were not included in the payload (explicitly deleted by admin)
        if (! empty($processedIds)) {
            $post->variations()->whereNotIn('id', $processedIds)->delete();
        }
    }

    /**
     * Sanitize descriptive specs array.
     */
    private function sanitizeSpecs(mixed $specs): array
    {
        if (is_string($specs)) {
            $specs = json_decode($specs, true) ?: [];
        }

        if (! is_array($specs)) {
            return [];
        }

        $cleaned = [];
        foreach ($specs as $item) {
            if (is_array($item)) {
                $name = trim($item['name'] ?? ($item['label'] ?? ''));
                $value = trim($item['value'] ?? '');
                if ($name !== '' && $value !== '') {
                    $cleaned[] = [
                        'name' => $name,
                        'value' => $value,
                    ];
                }
            }
        }

        return $cleaned;
    }

    /**
     * Sanitize variation attributes configuration.
     * e.g. [['name' => 'Màu sắc', 'slug' => 'color', 'values' => ['Đỏ', 'Xanh']]]
     */
    private function sanitizeVariationAttributes(mixed $attributes): array
    {
        if (is_string($attributes)) {
            $attributes = json_decode($attributes, true) ?: [];
        }

        if (! is_array($attributes)) {
            return [];
        }

        $cleaned = [];
        foreach ($attributes as $attr) {
            if (is_array($attr)) {
                $name = trim($attr['name'] ?? '');
                $slug = ! empty($attr['slug']) ? Str::slug($attr['slug']) : Str::slug($name);
                $values = $attr['values'] ?? [];
                if (is_string($values)) {
                    $values = array_map('trim', explode(',', $values));
                }
                $values = array_values(array_unique(array_filter($values)));

                if ($name !== '' && ! empty($values)) {
                    $cleaned[] = [
                        'name' => $name,
                        'slug' => $slug,
                        'values' => $values,
                    ];
                }
            }
        }

        return $cleaned;
    }

    /**
     * Sanitize all attributes (both variation and descriptive attributes).
     */
    private function sanitizeAttributes(mixed $attributes): array
    {
        if (is_string($attributes)) {
            $attributes = json_decode($attributes, true) ?: [];
        }

        if (! is_array($attributes)) {
            return [];
        }

        $cleaned = [];
        foreach ($attributes as $attr) {
            if (is_array($attr)) {
                $name = trim($attr['name'] ?? '');
                $values = $attr['values'] ?? [];
                if (is_string($values)) {
                    $values = array_map('trim', explode(',', $values));
                }
                $values = array_values(array_unique(array_filter($values)));

                if ($name !== '' && ! empty($values)) {
                    $cleaned[] = [
                        'name' => $name,
                        'slug' => ! empty($attr['slug']) ? Str::slug($attr['slug']) : Str::slug($name),
                        'values' => $values,
                        'variation' => ! empty($attr['variation']),
                    ];
                }
            }
        }

        return $cleaned;
    }

    /**
     * Generate unique slug for posts.
     */
    private function generateUniqueSlug(string $baseSlug, ?int $excludeId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = Post::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (! $query->exists()) {
                break;
            }

            $counter++;
            $slug = "{$baseSlug}-{$counter}";
        }

        return $slug;
    }

    /**
     * Get categories list for products.
     */
    public function getCategories(): Collection
    {
        return Taxonomy::where('taxonomy', 'product_cat')->orderBy('order')->get();
    }

    /**
     * Get brands list for products.
     */
    public function getBrands(): Collection
    {
        return Taxonomy::whereIn('taxonomy', ['brand', 'product_brand'])->orderBy('name')->get();
    }

    /**
     * Get global pre-defined attributes.
     */
    public function getGlobalAttributes(): Collection
    {
        return Taxonomy::where('taxonomy', 'product_attribute')->whereNull('parent_id')->with('children')->get();
    }
}
