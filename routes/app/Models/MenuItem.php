<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MenuItem extends Model
{

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'target',
        'is_active',
        'icon',
        'css_class',
        'image',
        'badge',
        'badge_color',
        'settings',
        'linkable_type',
        'linkable_id',
        'order',
        '_lft',
        '_rgt',
        'tenant_id',
        'project_id',
    ];

    protected static function booted(): void
    {
        static::creating(function ($item) {
            if ($item->_lft === null) {
                $item->_lft = 0;
            }
            if ($item->_rgt === null) {
                $item->_rgt = 0;
            }
            if ($item->target === null) {
                $item->target = '_self';
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
            'settings' => 'array',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function getUrlAttribute($value): ?string
    {
        if ($value) {
            if (str_contains($value, '/san-pham/')) {
                $value = str_replace('/san-pham/', '/', $value);
            }

            return $value;
        }

        if ($this->linkable) {
            $project = function_exists('current_project') ? current_project() : null;
            $code = $project?->code;
            $prefix = $code ? '/'.$code : '';

            $slug = $this->linkable->slug ?? $this->linkable->id;

            return match ($this->linkable_type) {
                'App\\Models\\Post' => ($this->linkable->post_type === 'page') ? "{$prefix}/{$slug}" : "{$prefix}/blog/{$slug}",
                'App\\Models\\ProductCategory' => "{$prefix}/{$slug}",
                'App\\Models\\Product' => "{$prefix}/{$slug}",
                'App\\Models\\Taxonomy' => "{$prefix}/blog?category={$slug}",
                'App\\Models\\Brand' => "{$prefix}/cua-hang?brand={$slug}",
                default => '#',
            };
        }

        return '#';
    }
}
