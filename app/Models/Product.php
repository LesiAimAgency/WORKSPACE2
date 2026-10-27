<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Product Model (Unified Single Posts Table Architecture)
 *
 * Represents an eCommerce Product with full support for:
 * - Simple & Variable products
 * - Variations / Variants
 * - Attributes & Attribute Values (Taxonomies: product_attribute)
 * - Categories (Taxonomies: product_cat) & Brands (Taxonomies: brand)
 */
class Product extends Post
{
    /**
     * The table associated with the model.
     * Products are stored in posts table with post_type = 'product'
     */
    protected $table = 'posts';

    /**
     * Default attributes.
     */
    protected $attributes = [
        'post_type' => 'product',
        'status' => 'published',
    ];

    /**
     * Boot the model - auto scope by post_type = 'product'
     */
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('product', function (Builder $builder) {
            $builder->where('posts.post_type', 'product');
        });

        static::creating(function ($product) {
            $product->post_type = 'product';
        });
    }

    // ==========================================
    // RELATIONSHIPS FOR VARIANT PRODUCTS
    // ==========================================

    /**
     * Product variations (Biến thể sản phẩm)
     */
    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'post_id')->orderBy('order');
    }

    /**
     * Alias for variations
     */
    public function variants(): HasMany
    {
        return $this->variations();
    }

    /**
     * Active variations only
     */
    public function activeVariations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'post_id')
            ->where('status', 'active')
            ->orderBy('order');
    }

    /**
     * Product Attribute sets / Types (e.g. Màu sắc, Kích thước, Cấp điện áp)
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id')
            ->where('taxonomies.taxonomy', 'product_attribute')
            ->whereNull('taxonomies.parent_id')
            ->withPivot('order');
    }

    /**
     * Specific attribute values assigned to this product (e.g. Đỏ, Xanh, 24kV, 100A)
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id')
            ->where('taxonomies.taxonomy', 'product_attribute')
            ->whereNotNull('taxonomies.parent_id')
            ->withPivot('order');
    }

    /**
     * Product Categories
     */
    public function categories(): BelongsToMany
    {
        return $this->productCategories();
    }

    /**
     * Product Brands
     */
    public function brands(): BelongsToMany
    {
        return $this->productBrands();
    }

    // ==========================================
    // HELPER METHODS FOR VARIATIONS & ATTRIBUTES
    // ==========================================

    /**
     * Check if product has any variations
     */
    public function hasVariations(): bool
    {
        return $this->isVariable() && $this->variations()->exists();
    }

    /**
     * Find a variation matching an exact set of attributes.
     * e.g. $product->findVariation(['Màu sắc' => 'Đỏ', 'Kích thước' => 'XL'])
     */
    public function findVariation(array $attributes): ?ProductVariation
    {
        $hash = ProductVariation::computeAttributeHash($attributes);

        return $this->variations()->where('attribute_hash', $hash)->first();
    }

    /**
     * Get all variation attributes formatted with their available values
     * e.g. [
     *   ['name' => 'Màu sắc', 'slug' => 'color', 'values' => ['Đỏ', 'Xanh']],
     *   ['name' => 'Kích thước', 'slug' => 'size', 'values' => ['M', 'L', 'XL']]
     * ]
     */
    public function getVariationAttributesList(): array
    {
        $meta = $this->meta_data;
        if (is_string($meta)) {
            $meta = json_decode($meta, true) ?: [];
        }

        if (! empty($meta['variation_attributes']) && is_array($meta['variation_attributes'])) {
            return $meta['variation_attributes'];
        }

        // Fallback: extract from variations
        $collected = [];
        $vars = $this->relationLoaded('variations') ? $this->variations : $this->variations()->get();
        foreach ($vars as $var) {
            $attrs = (array) ($var->attributes ?? []);
            foreach ($attrs as $name => $val) {
                if (! isset($collected[$name])) {
                    $collected[$name] = [];
                }
                if ($val !== '' && ! in_array($val, $collected[$name])) {
                    $collected[$name][] = $val;
                }
            }
        }

        $result = [];
        foreach ($collected as $name => $vals) {
            $result[] = [
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
                'values' => $vals,
            ];
        }

        return $result;
    }

    /**
     * Get price range for variable product
     */
    public function getPriceRange(): array
    {
        if (! $this->isVariable()) {
            $price = (float) ($this->sale_price ?: $this->price ?: 0);
            return ['min' => $price, 'max' => $price];
        }

        $variations = $this->relationLoaded('variations')
            ? $this->variations->where('status', 'active')
            : $this->variations()->where('status', 'active')->get();

        if ($variations->isEmpty()) {
            $price = (float) ($this->sale_price ?: $this->price ?: 0);
            return ['min' => $price, 'max' => $price];
        }

        $min = (float) $variations->min('effective_price');
        $max = (float) $variations->max('effective_price');

        return ['min' => $min, 'max' => $max];
    }
}
