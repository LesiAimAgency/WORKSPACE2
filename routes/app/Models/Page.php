<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     * Pages are stored in posts table with post_type = 'page'
     */
    protected $table = 'posts';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image',
        'post_type',
        'custom_css',
        'template',
        'status',
        'meta_title',
        'meta_description',
        'seo_data',
        'views',
        'published_at',
        'author_id',
    ];

    protected $casts = [
        'seo_data' => 'array',
        'published_at' => 'datetime',
    ];

    protected $attributes = [
        'post_type' => 'page',
    ];

    /**
     * Boot the model - auto filter by post_type = 'page'
     */
    protected static function booted(): void
    {
        static::addGlobalScope('page', function (Builder $builder) {
            $builder->where('posts.post_type', 'page');
        });

        static::creating(function ($page) {
            $page->post_type = 'page';
        });
    }

    /**
     * Get the sections for the page
     */
    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->ordered();
    }

    /**
     * Get active sections for the page
     */
    public function activeSections(): HasMany
    {
        return $this->hasMany(PageSection::class)->active()->ordered();
    }

    /**
     * Scope for published pages
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Get rendered page content
     */
    public function getRenderedContent(): string
    {
        $content = $this->content ?? '';

        foreach ($this->activeSections as $section) {
            $content .= $section->getRenderedContent();
        }

        return $content;
    }

    /**
     * Get custom CSS
     */
    public function getCustomCss(): string
    {
        return $this->custom_css ?? '';
    }

    /**
     * Add a section to the page
     */
    public function addSection(string $type, array $settings = [], ?int $order = null): PageSection
    {
        if ($order === null) {
            $order = $this->sections()->max('order') + 1;
        }

        return $this->sections()->create([
            'type' => $type,
            'settings' => $settings,
            'order' => $order,
            'is_active' => true,
        ]);
    }
}
