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
        'compatibility_matrix',
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
        'compatibility_matrix' => 'array',
        'durability_years' => 'integer',
        'budget_level' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Resolve the structured 3-tier compatibility matrix for this material.
     * Ensures all 6 typologies and 5 surfaces are present, populated with their configuration.
     */
    public function resolveCompatibilityMatrix(): array
    {
        $typologies = \App\Http\Controllers\Admin\MaterialController::getDefaultTypologies();
        $surfaces = \App\Http\Controllers\Admin\MaterialController::getDefaultSurfaces();
        $areaMatrix = \App\Http\Controllers\Admin\MaterialController::getDefaultAreaMatrix();

        $matrix = is_array($this->compatibility_matrix) ? $this->compatibility_matrix : [];

        $typoKeys = array_map(fn ($t) => $t['en'], $typologies);
        $surfKeys = array_map(fn ($s) => $s['en'], $surfaces);

        $suitableTypo = is_array($this->suitable_typologies) ? $this->suitable_typologies : $typoKeys;
        $suitableSurf = is_array($this->suitable_locations) ? $this->suitable_locations : [];
        $suitableAreas = is_array($this->suitable_areas) ? array_map(fn ($a) => \App\Http\Controllers\Admin\MaterialController::canonicalizeArea((string) $a), $this->suitable_areas) : [];

        $resolved = [];

        foreach ($typoKeys as $tKey) {
            $existingTypo = $matrix[$tKey] ?? null;
            $isTypoEnabled = isset($existingTypo['enabled'])
                ? (bool) $existingTypo['enabled']
                : in_array($tKey, $suitableTypo, true);

            $resolved[$tKey] = [
                'enabled' => $isTypoEnabled,
                'surfaces' => [],
            ];

            foreach ($surfKeys as $sKey) {
                $existingSurf = $existingTypo['surfaces'][$sKey] ?? null;
                $isSurfEnabled = isset($existingSurf['enabled'])
                    ? (bool) $existingSurf['enabled']
                    : in_array($sKey, $suitableSurf, true);

                $pairAreas = [];
                if (isset($existingSurf['areas']) && is_array($existingSurf['areas'])) {
                    $pairAreas = array_values(array_unique(array_map(fn ($a) => \App\Http\Controllers\Admin\MaterialController::canonicalizeArea((string) $a), $existingSurf['areas'])));
                } elseif (! empty($suitableAreas)) {
                    $defaultPairList = $areaMatrix[$tKey][$sKey] ?? [];
                    $pairAreas = array_values(array_intersect($suitableAreas, $defaultPairList));
                }

                $resolved[$tKey]['surfaces'][$sKey] = [
                    'enabled' => $isSurfEnabled,
                    'areas' => $pairAreas, // [] means applies to ALL areas of this surface
                ];
            }
        }

        return $resolved;
    }
}
