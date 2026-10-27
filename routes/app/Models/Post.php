<?php

namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    // Các field có thể dịch
    protected $translatable = [
        'title',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'project_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'post_type',
        'template',
        'status',
        'meta_title',
        'meta_description',
        'seo_data',
        'views',
        'published_at',
        'author_id',
        'order',
        'meta_data',
    ];

    protected $casts = [
        'order' => 'integer',
        'seo_data' => 'array',
        'meta_data' => 'array',
        'published_at' => 'datetime',
    ];

    // Scopes cho post type
    public function scopePosts($query)
    {
        return $query->where('post_type', 'post');
    }

    public function scopePages($query)
    {
        return $query->where('post_type', 'page');
    }

    public function scopeProducts($query)
    {
        return $query->where('post_type', 'product');
    }

    public function scopeProjects($query)
    {
        return $query->where('post_type', 'project');
    }

    public function scopeOfType($query, string|array $type)
    {
        return is_array($type) ? $query->whereIn('post_type', $type) : $query->where('post_type', $type);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeWithStandardRelations($query)
    {
        return $query->with(['translations', 'taxonomies.translations']);
    }

    public function category()
    {
        return $this->belongsToMany(Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id')
            ->where('taxonomy', 'category');
    }

    // Relationships
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id')->withoutGlobalScope('tenant');
    }



    public function taxonomies(): BelongsToMany
    {
        return $this->belongsToMany(Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id')
            ->withPivot('order');
    }

  

    public function tags()
    {
        return $this->belongsToMany(Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id')
            ->whereIn('taxonomy', ['post_tag', 'tag']);
    }

    // Helper methods
    public function isPost(): bool
    {
        return $this->post_type === 'post';
    }

    public function isPage(): bool
    {
        return $this->post_type === 'page';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSortOrderAttribute(): int
    {
        return (int) ($this->attributes['order'] ?? ($this->getMetaArray()['order'] ?? 0));
    }

    public function setSortOrderAttribute($value): void
    {
        $this->attributes['order'] = (int) $value;
    }

    private function getMetaArray(): array
    {
        $meta = $this->meta_data;
        if (is_string($meta)) {
            $decoded = json_decode($meta, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return is_array($meta) ? $meta : [];
    }

    public function getNameAttribute(): string
    {
        return $this->title ?? '';
    }

    public function getSkuAttribute(): ?string
    {
        return $this->getMetaArray()['sku'] ?? null;
    }

    public function getShortDescriptionAttribute(): ?string
    {
        return $this->excerpt;
    }

    public function getSeoDataAttribute($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    public function getFocusKeywordAttribute(): ?string
    {
        $seo = is_array($this->seo_data) ? $this->seo_data : [];
        return $seo['focus_keyword'] ?? ($this->meta_data['focus_keyword'] ?? null);
    }

    public function getMetaKeywordsAttribute(): ?string
    {
        $seo = is_array($this->seo_data) ? $this->seo_data : [];
        if (!empty($seo['meta_keywords'])) {
            return $seo['meta_keywords'];
        }
        if (!empty($this->meta_data['meta_keywords'])) {
            return $this->meta_data['meta_keywords'];
        }
        $keywords = [];
        if (!empty($seo['focus_keyword'])) {
            $keywords[] = $seo['focus_keyword'];
        }
        if (!empty($this->brand)) {
            $keywords[] = $this->brand;
        }
        if (!empty($this->category_name)) {
            $keywords[] = $this->category_name;
        }
        return !empty($keywords) ? implode(', ', array_unique($keywords)) : null;
    }

    public function getSchemaTypeAttribute(): ?string
    {
        $seo = is_array($this->seo_data) ? $this->seo_data : [];
        return $seo['schema_type'] ?? ($this->meta_data['schema_type'] ?? ($this->post_type === 'product' ? 'Product' : null));
    }

    public function getCanonicalUrlAttribute(): ?string
    {
        $seo = is_array($this->seo_data) ? $this->seo_data : [];
        return $seo['canonical_url'] ?? ($this->meta_data['canonical_url'] ?? null);
    }

    public function getCustomSchemaAttribute(): ?string
    {
        $seo = is_array($this->seo_data) ? $this->seo_data : [];
        return $seo['custom_schema'] ?? ($this->meta_data['custom_schema'] ?? null);
    }

    public function getNoindexAttribute(): bool
    {
        $seo = is_array($this->seo_data) ? $this->seo_data : [];
        return ! empty($seo['noindex']) || ! empty($this->meta_data['noindex']);
    }

    public function getPriceAttribute()
    {
        return $this->getMetaArray()['price'] ?? 0;
    }

    public function getSalePriceAttribute()
    {
        return $this->getMetaArray()['sale_price'] ?? null;
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'post_id')->orderBy('order');
    }

    public function variants(): HasMany
    {
        return $this->variations();
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id')
            ->where('taxonomies.taxonomy', 'product_attribute')
            ->whereNull('taxonomies.parent_id')
            ->withPivot('order');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id')
            ->where('taxonomies.taxonomy', 'product_attribute')
            ->whereNotNull('taxonomies.parent_id')
            ->withPivot('order');
    }

    public function productCategories(): BelongsToMany
    {
        return $this->belongsToMany(Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id')
            ->where('taxonomy', 'product_cat')
            ->withPivot('order');
    }

    public function productBrands(): BelongsToMany
    {
        return $this->belongsToMany(Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id')
            ->whereIn('taxonomy', ['brand', 'product_brand'])
            ->withPivot('order');
    }

    public function getProductTypeAttribute(): string
    {
        return $this->getMetaArray()['product_type'] ?? 'simple';
    }

    public function isSimple(): bool
    {
        return $this->product_type === 'simple';
    }

    public function isVariable(): bool
    {
        return $this->product_type === 'variable';
    }

    public function getManageStockAttribute(): bool
    {
        return (bool) ($this->getMetaArray()['manage_stock'] ?? false);
    }

    public function getVariationAttributesAttribute(): array
    {
        return $this->getMetaArray()['variation_attributes'] ?? [];
    }

    public function getMinPriceAttribute(): float
    {
        if ($this->isVariable() && $this->relationLoaded('variations') && $this->variations->isNotEmpty()) {
            return (float) $this->variations->where('status', 'active')->min('effective_price') ?: 0;
        }

        return (float) ($this->sale_price ?: $this->price ?: 0);
    }

    public function getMaxPriceAttribute(): float
    {
        if ($this->isVariable() && $this->relationLoaded('variations') && $this->variations->isNotEmpty()) {
            return (float) $this->variations->where('status', 'active')->max('effective_price') ?: 0;
        }

        return (float) ($this->sale_price ?: $this->price ?: 0);
    }

    public function getDisplayPriceAttribute(): string
    {
        if ($this->isVariable()) {
            $variations = $this->relationLoaded('variations') ? $this->variations : $this->variations()->where('status', 'active')->get();
            if ($variations->isNotEmpty()) {
                $min = (float) $variations->min('effective_price');
                $max = (float) $variations->max('effective_price');

                if ($min <= 0 && $max <= 0) {
                    return 'Liên hệ';
                }

                if ($min === $max) {
                    return number_format($min, 0, ',', '.').' ₫';
                }

                return number_format($min, 0, ',', '.').' ₫ - '.number_format($max, 0, ',', '.').' ₫';
            }
        }

        $meta = $this->getMetaArray();
        $price = $meta['sale_price'] ?? $meta['price'] ?? 0;
        if (! $price) {
            return 'Liên hệ';
        }

        return number_format((float) $price, 0, ',', '.').' ₫';
    }

    public function getPriceFormattedAttribute(): string
    {
        return $this->display_price;
    }

    public function getStockQuantityAttribute(): int
    {
        if ($this->isVariable()) {
            $variations = $this->relationLoaded('variations') ? $this->variations : $this->variations()->where('status', 'active')->get();
            if ($variations->isNotEmpty()) {
                return (int) $variations->where('manage_stock', true)->sum('stock_quantity');
            }
        }

        return (int) ($this->getMetaArray()['stock_quantity'] ?? 0);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->isVariable()) {
            $variations = $this->relationLoaded('variations') ? $this->variations : $this->variations()->where('status', 'active')->get();
            if ($variations->isNotEmpty()) {
                $hasInStock = $variations->some(fn ($v) => $v->is_in_stock);

                return $hasInStock ? 'in_stock' : 'out_of_stock';
            }
        }

        $meta = $this->getMetaArray();
        $status = $meta['stock_status'] ?? null;
        if ($status === 'instock' || $status === 'in_stock') {
            return 'in_stock';
        }
        if ($status === 'outofstock' || $status === 'out_of_stock') {
            return 'out_of_stock';
        }

        return $this->stock_quantity > 0 ? 'in_stock' : 'out_of_stock';
    }

    public function getCategoryAttribute()
    {
        return $this->taxonomies->whereIn('taxonomy', ['category', 'product_cat', 'project_cat'])->first();
    }

    public function getCategoryNameAttribute(): ?string
    {
        $cat = $this->productCategories->first() ?? $this->taxonomies->whereIn('taxonomy', ['category', 'product_cat', 'project_cat'])->first();
        if ($cat) {
            return $cat->name;
        }

        return $this->getMetaArray()['category'] ?? null;
    }

    public function getBrandAttribute(): ?string
    {
        $brand = $this->productBrands->first() ?? $this->taxonomies->whereIn('taxonomy', ['brand', 'product_brand'])->first();
        if ($brand) {
            return $brand->name;
        }

        $metaBrand = $this->getMetaArray()['brand'] ?? null;
        if (!empty($metaBrand)) {
            return $metaBrand;
        }

        // Auto-detect brand from product title if not explicitly set
        $title = $this->title ?? '';
        if ($title) {
            $knownBrands = ['Schneider Electric', 'Shihlin Electric', '3M Electrical', 'Vina Electric', 'Thibidi', 'Cadivi', 'EMIC', 'MBT', 'ABB', '3M'];
            foreach ($knownBrands as $kb) {
                if (stripos($title, $kb) !== false) {
                    return $kb === '3M' ? '3M Electrical' : $kb;
                }
            }
        }

        return null;
    }

    public function getBrandLogoAttribute(): ?string
    {
        // 1. Try resolving via linked brand taxonomy
        $brand = $this->productBrands->first() ?? $this->taxonomies->whereIn('taxonomy', ['brand', 'product_brand'])->first();
        if ($brand) {
            $brandMeta = is_array($brand->meta_data) ? $brand->meta_data : json_decode($brand->meta_data ?? '[]', true);
            $logo = $brandMeta['logo'] ?? $brand->image ?? null;
            if ($logo) {
                return str_starts_with($logo, 'http') ? $logo : asset($logo);
            }
            if (!empty($brand->slug)) {
                $candidateSvg = "storage/media/brands/brand-{$brand->slug}.svg";
                if (file_exists(public_path($candidateSvg))) {
                    return asset($candidateSvg);
                }
            }
        }

        // 2. Check meta_data['brand_logo'] on product
        $logo = $this->getMetaArray()['brand_logo'] ?? null;
        if ($logo) {
            return str_starts_with($logo, 'http') ? $logo : asset($logo);
        }

        // 3. Fallback matching by brand name
        $brandName = $this->brand;
        if ($brandName) {
            $matchedBrand = Taxonomy::whereIn('taxonomy', ['brand', 'product_brand'])
                ->where(function ($q) use ($brandName) {
                    $slug = \Illuminate\Support\Str::slug($brandName);
                    $q->where('name', $brandName)->orWhere('slug', $slug);
                })->first();

            if ($matchedBrand) {
                $bMeta = is_array($matchedBrand->meta_data) ? $matchedBrand->meta_data : json_decode($matchedBrand->meta_data ?? '[]', true);
                $bLogo = $bMeta['logo'] ?? $matchedBrand->image ?? null;
                if ($bLogo) {
                    return str_starts_with($bLogo, 'http') ? $bLogo : asset($bLogo);
                }
            }

            $slug = \Illuminate\Support\Str::slug($brandName);
            $candidateSvg = "storage/media/brands/brand-{$slug}.svg";
            if (file_exists(public_path($candidateSvg))) {
                return asset($candidateSvg);
            }
        }

        return null;
    }

    public function getVoltageAttribute(): ?string
    {
        return $this->getMetaArray()['voltage'] ?? null;
    }

    public function getStandardAttribute(): ?string
    {
        return $this->getMetaArray()['standard'] ?? null;
    }

    public function getBadgeAttribute(): ?string
    {
        return $this->getMetaArray()['badge'] ?? null;
    }

    public function getProjectCodeAttribute(): ?string
    {
        return $this->getMetaArray()['project_code'] ?? null;
    }

    public function getClientAttribute(): ?string
    {
        return $this->getMetaArray()['client'] ?? null;
    }

    public function getLocationAttribute(): ?string
    {
        return $this->getMetaArray()['location'] ?? null;
    }

    public function getScaleAttribute(): ?string
    {
        return $this->getMetaArray()['scale'] ?? null;
    }

    public function getYearAttribute(): ?string
    {
        return $this->getMetaArray()['year'] ?? ($this->getMetaArray()['completion_year'] ?? null);
    }

    public function getStyleAttribute(): ?string
    {
        return $this->getMetaArray()['style'] ?? null;
    }

    public function getScopeAttribute(): ?string
    {
        return $this->getMetaArray()['scope'] ?? null;
    }

    public function getChallengesAttribute(): ?string
    {
        return $this->getMetaArray()['challenges'] ?? null;
    }

    public function getReadingTimeAttribute(): ?string
    {
        return $this->getMetaArray()['reading_time'] ?? null;
    }

    public function getSpecsAttribute(): array
    {
        return $this->getMetaArray()['specs'] ?? [];
    }

    public function getDocumentsAttribute(): array
    {
        return $this->getMetaArray()['documents'] ?? [];
    }

    public function getFeaturedImageAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '//') || str_starts_with($value, 'data:')) {
            return $value;
        }

        return asset(ltrim($value, '/'));
    }

    public function getGalleryAttribute(): array
    {
        $meta = $this->getMetaArray();

        // If meta_data has gallery key (even if empty array)
        if (array_key_exists('gallery', $meta)) {
            $gallery = $meta['gallery'];
            if (is_string($gallery)) {
                $decoded = json_decode($gallery, true);
                $gallery = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode("\n", $gallery)));
            }
            if (is_array($gallery)) {
                return array_values(array_filter(array_map(function ($img) {
                    $img = trim((string) $img);
                    if (empty($img)) return null;
                    if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://') || str_starts_with($img, '//') || str_starts_with($img, 'data:')) {
                        return $img;
                    }
                    return asset(ltrim($img, '/'));
                }, $gallery)));
            }
        }

        // Default fallback for products vs other post types
        if ($this->post_type === 'product') {
            $feat = $this->featured_image;
            return $feat ? [$feat] : [
                asset('storage/media/products/product1.png'),
                asset('storage/media/products/product2.png'),
                asset('storage/media/products/product3.png'),
                asset('storage/media/products/product4.png'),
            ];
        }

        return [];
    }

    /**
     * Clean SEO URL for post/page/product/project
     */
    public function getUrlAttribute(): string
    {
        if ($this->post_type === 'product') {
            return route('products.show', $this->slug);
        }

        if ($this->post_type === 'project') {
            return route('projects.show', $this->slug);
        }

        if ($this->post_type === 'post') {
            return route('news.show', $this->slug);
        }

        if ($this->post_type === 'page') {
            if ($this->slug === 'about-us') {
                return route('about-us');
            }
            if ($this->slug === 'contact') {
                return route('contact.page');
            }
        }

        if ($this->post_type === 'faq') {
            return route('contact.page') . '#faq';
        }

        return url($this->slug);
    }
}
