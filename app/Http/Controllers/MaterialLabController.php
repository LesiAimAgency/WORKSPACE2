<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\MaterialController;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaterialLabController extends Controller
{
    /**
     * Display the Material Lab interactive wizard.
     */
    public function index(Request $request): View
    {
        $budgetLevels = MaterialController::resolveArraySetting(
            'material_lab_budgets',
            MaterialController::getDefaultBudgetLevels()
        );
        $budgetMap = collect($budgetLevels)->mapWithKeys(fn ($b) => [(int) ($b['level'] ?? 0) => ($b['vnd'] ?? '')])->toArray();

        // 1. Get all active materials ordered by sort_order
        $materials = Material::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($m) use ($budgetMap) {
                $unifiedMetrics = $m->metrics ?: $m->tech_specs;
                $unifiedStandards = $m->ai_standards ?: $m->tech_description;
                $unifiedExperience = $m->ws_experience ?: ($m->experience_description ?: $m->application);
                $unifiedCost = $budgetMap[(int) $m->budget_level] ?? ($m->cost_label ?: '300k – 600k');

                return [
                    'id' => $m->code,
                    'name' => $m->name,
                    'brand' => $m->brand,
                    'suitableTypologies' => is_array($m->suitable_typologies) ? $m->suitable_typologies : [],
                    'suitableLocations' => is_array($m->suitable_locations) ? $m->suitable_locations : [],
                    'suitableAreas' => is_array($m->suitable_areas) ? $m->suitable_areas : [],
                    'compatibility' => $m->resolveCompatibilityMatrix(),
                    'category' => $m->category,
                    'origin' => $m->origin,
                    'originLabel' => match ($m->origin) {
                        'Vietnam' => 'Hàng Việt',
                        'Import' => 'Nhập khẩu',
                        'Both' => 'Liên doanh',
                        default => $m->origin_label ?: 'Hàng Việt',
                    },
                    'availability' => $m->availability,
                    'durabilityYears' => (int) $m->durability_years,
                    'costLabel' => $unifiedCost,
                    'budgetLevel' => (int) $m->budget_level,
                    'finish' => $m->finish,
                    'tech' => $unifiedMetrics,
                    'application' => $unifiedExperience,
                    'tech_desc' => $unifiedStandards,
                    'ai_standards' => $unifiedStandards,
                    'fire_requirement' => $m->fire_requirement,
                    'metrics' => $unifiedMetrics,
                    'experience_desc' => $unifiedExperience,
                    'ws_experience' => $unifiedExperience,
                    'common_errors' => $m->common_errors,
                    'tips' => $m->tips,
                    'image_url' => $m->image_url,
                ];
            });

        // 2. Fetch configurations from settings or defaults
        $typologies = MaterialController::resolveArraySetting(
            'material_lab_typologies',
            MaterialController::getDefaultTypologies()
        );

        $surfaces = MaterialController::resolveArraySetting(
            'material_lab_surfaces',
            MaterialController::getDefaultSurfaces()
        );

        $areaMatrix = MaterialController::resolveArraySetting(
            'material_lab_area_matrix',
            MaterialController::getDefaultAreaMatrix()
        );

        $areaLabels = MaterialController::resolveArraySetting(
            'material_lab_area_labels',
            MaterialController::getDefaultAreaLabels()
        );

        $lifeCycles = MaterialController::resolveArraySetting(
            'material_lab_lifecycles',
            MaterialController::getDefaultLifeCycles()
        );

        $budgetLevels = MaterialController::resolveArraySetting(
            'material_lab_budgets',
            MaterialController::getDefaultBudgetLevels()
        );

        $step5Insight = array_merge(
            MaterialController::getDefaultStep5Insight(),
            MaterialController::resolveArraySetting('material_lab_step5_insight', [])
        );

        $pageTitle = setting('material_lab_title', 'Thư viện vật liệu');
        $pageSubtitle = setting('material_lab_subtitle', 'Material Lab • V1.0');
        $step1Title = setting('material_lab_step1_title', 'Công trình của bạn là gì?');
        $step1Desc = setting('material_lab_step1_desc', 'Chọn loại hình để gợi ý vật liệu phù hợp.');
        $step2Title = setting('material_lab_step2_title', 'Vị trí nào cần vật liệu?');
        $step2Desc = setting('material_lab_step2_desc', 'Chọn bề mặt để lọc vật liệu phù hợp.');
        $step3Title = setting('material_lab_step3_title', 'Khu vực chức năng nào?');
        $step4Title = setting('material_lab_step4_title', 'Nhu cầu thực tế?');
        $step4Desc = setting('material_lab_step4_desc', 'Chọn độ bền, ngân sách, nguồn gốc và tồn kho.');

        return view('pages.material-lab', compact(
            'materials',
            'typologies',
            'surfaces',
            'areaMatrix',
            'areaLabels',
            'lifeCycles',
            'budgetLevels',
            'step5Insight',
            'pageTitle',
            'pageSubtitle',
            'step1Title',
            'step1Desc',
            'step2Title',
            'step2Desc',
            'step3Title',
            'step4Title',
            'step4Desc'
        ));
    }
}
