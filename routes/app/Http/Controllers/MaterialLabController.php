<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
        // 1. Get all active materials ordered by sort_order
        $materials = Material::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->code,
                    'name' => $m->name,
                    'brand' => $m->brand,
                    'suitableLocations' => $m->suitable_locations ?? [],
                    'category' => $m->category,
                    'origin' => $m->origin,
                    'originLabel' => $m->origin_label ?: ($m->origin === 'Vietnam' ? 'Hàng Việt' : 'Nhập khẩu'),
                    'availability' => $m->availability,
                    'durabilityYears' => (int) $m->durability_years,
                    'costLabel' => $m->cost_label,
                    'budgetLevel' => (int) $m->budget_level,
                    'finish' => $m->finish,
                    'tech' => $m->tech_specs,
                    'application' => $m->application,
                    'tech_desc' => $m->tech_description,
                    'experience_desc' => $m->experience_description,
                    'image_url' => $m->image_url,
                ];
            });

        // 2. Fetch configurations from settings or defaults
        $typologies = setting('material_lab_typologies') ?: [
            ['en' => 'Office', 'vi' => 'Văn phòng làm việc', 'icon' => '🏢', 'desc' => 'Không gian hiệu suất cao, cần tiêu âm và độ bền bề mặt.'],
            ['en' => 'School', 'vi' => 'Trường học', 'icon' => '🎓', 'desc' => 'An toàn, kháng khuẩn, bền và dễ bảo trì.'],
            ['en' => 'Townhouse', 'vi' => 'Nhà phố', 'icon' => '🏠', 'desc' => 'Không gian ở ấm áp, cân bằng thẩm mỹ nội thất.'],
            ['en' => 'Coffee Shop', 'vi' => 'Quán cà phê', 'icon' => '☕', 'desc' => 'Trải nghiệm xúc giác, chịu ẩm và biểu cảm vật liệu.'],
            ['en' => 'Retail', 'vi' => 'Cửa hàng bán lẻ', 'icon' => '🛍️', 'desc' => 'Dẫn dắt hành vi mua sắm, bền và tạo điểm nhấn.'],
            ['en' => 'Apartment', 'vi' => 'Căn hộ chung cư', 'icon' => '🏬', 'desc' => 'Tinh gọn, bền vững, cân bằng kỹ thuật và ở.'],
        ];
        if (is_string($typologies)) {
            $typologies = json_decode($typologies, true) ?: [];
        }

        $surfaces = setting('material_lab_surfaces') ?: [
            ['en' => 'Floor', 'vi' => 'Bề mặt sàn', 'viShort' => 'Bề mặt sàn', 'enLabel' => 'Floor'],
            ['en' => 'Wall', 'vi' => 'Bề mặt tường', 'viShort' => 'Bề mặt tường', 'enLabel' => 'Wall'],
            ['en' => 'Ceiling', 'vi' => 'Hệ trần', 'viShort' => 'Hệ trần', 'enLabel' => 'Ceiling'],
            ['en' => 'Loose Furniture', 'vi' => 'Đồ rời', 'viShort' => 'Đồ rời', 'enLabel' => 'Loose'],
            ['en' => 'Detail Surface', 'vi' => 'Chi tiết kỹ thuật', 'viShort' => 'Chi tiết kỹ thuật', 'enLabel' => 'Detail'],
        ];
        if (is_string($surfaces)) {
            $surfaces = json_decode($surfaces, true) ?: [];
        }

        $areaMatrix = setting('material_lab_area_matrix') ?: [];
        if (is_string($areaMatrix)) {
            $areaMatrix = json_decode($areaMatrix, true) ?: [];
        }

        $areaLabels = setting('material_lab_area_labels') ?: [];
        if (is_string($areaLabels)) {
            $areaLabels = json_decode($areaLabels, true) ?: [];
        }

        $lifeCycles = [
            ['id' => 'short', 'years' => 3, 'vi' => '1–3 năm', 'en' => 'Short term', 'note' => 'Pop-up, thử nghiệm'],
            ['id' => 'medium', 'years' => 7, 'vi' => '5–10 năm', 'en' => 'Standard', 'note' => 'Văn phòng tiêu chuẩn'],
            ['id' => 'long', 'years' => 15, 'vi' => '10+ năm', 'en' => 'Long term', 'note' => 'Sảnh, lõi, WC'],
        ];

        $budgetLevels = [
            ['level' => 1, 'vi' => 'Tiết kiệm', 'en' => 'Economical', 'vnd' => '85k – 300k'],
            ['level' => 2, 'vi' => 'Cân bằng', 'en' => 'Balanced', 'vnd' => '300k – 600k'],
            ['level' => 3, 'vi' => 'Cao cấp', 'en' => 'Premium', 'vnd' => '600k – 900k'],
            ['level' => 4, 'vi' => 'Siêu cao cấp', 'en' => 'Luxury', 'vnd' => '900k – 1.5tr+'],
            ['level' => 5, 'vi' => 'Bỏ qua', 'en' => 'Skip', 'vnd' => 'Mọi mức giá', 'isSkip' => true],
        ];

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
