<?php

namespace App\Models;

use App\Traits\ProjectScoped;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taxonomy extends Model
{
    use HasFactory, ProjectScoped, SoftDeletes, Translatable;

    protected $translatable = [
        'name',
        'description',
    ];

    protected $fillable = [
        'project_id',
        'tenant_id',
        'name',
        'slug',
        'taxonomy',
        'description',
        'parent_id',
        'order',
        'status',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Taxonomy::class, 'parent_id')->orderBy('order');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'term_relationships', 'term_taxonomy_id', 'object_id')
            ->withPivot('order');
    }

    // Scopes for specific taxonomies
    public function scopeCategories($query)
    {
        return $query->where('taxonomy', 'category');
    }

    public function scopeBrands($query)
    {
        return $query->where('taxonomy', 'brand');
    }

    public function scopeTags($query)
    {
        return $query->whereIn('taxonomy', ['post_tag', 'tag']);
    }

    public function scopeAttributes($query)
    {
        return $query->where('taxonomy', 'product_attribute')->whereNull('parent_id');
    }

    public function scopeAttributeValues($query)
    {
        return $query->where('taxonomy', 'product_attribute')->whereNotNull('parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'term_relationships', 'term_taxonomy_id', 'object_id');
    }

    // Helper methods
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getLevelAttribute(): int
    {
        return $this->meta_data['level'] ?? 0;
    }

    public function getImageAttribute(): ?string
    {
        return $this->meta_data['image'] ?? null;
    }

    public function getIsActiveAttribute(): bool
    {
        if (isset($this->meta_data['is_active'])) {
            return (bool) $this->meta_data['is_active'];
        }

        return $this->status === 'published';
    }
}
