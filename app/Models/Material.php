<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materials';

    protected $fillable = [
        'code',
        'name',
        'brand',
        'category',
        'origin',
        'origin_label',
        'availability',
        'durability_years',
        'cost_label',
        'budget_level',
        'finish',
        'tech_specs',
        'application',
        'suitable_typologies',
        'suitable_locations',
        'suitable_areas',
        'tech_description',
        'ai_standards',
        'fire_requirement',
        'metrics',
        'experience_description',
        'ws_experience',
        'common_errors',
        'tips',
        'image_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'suitable_typologies' => 'array',
        'suitable_locations' => 'array',
        'suitable_areas' => 'array',
        'durability_years' => 'integer',
        'budget_level' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
