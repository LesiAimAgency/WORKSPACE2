<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MaterialController extends Controller
{
    /**
     * Default Step 1 Typologies.
     */
    public static function getDefaultTypologies(): array
    {
        return [
            ['en' => 'Office', 'vi' => 'Văn phòng làm việc', 'icon' => '🏢', 'desc' => 'Không gian hiệu suất cao, cần tiêu âm và độ bền bề mặt.'],
            ['en' => 'School', 'vi' => 'Trường học', 'icon' => '🎓', 'desc' => 'An toàn, kháng khuẩn, bền và dễ bảo trì.'],
            ['en' => 'Townhouse', 'vi' => 'Nhà phố', 'icon' => '🏠', 'desc' => 'Không gian ở ấm áp, cân bằng thẩm mỹ nội thất.'],
            ['en' => 'Coffee Shop', 'vi' => 'Quán cà phê', 'icon' => '☕', 'desc' => 'Trải nghiệm xúc giác, chịu ẩm và biểu cảm vật liệu.'],
            ['en' => 'Retail', 'vi' => 'Cửa hàng bán lẻ', 'icon' => '🛍️', 'desc' => 'Dẫn dắt hành vi mua sắm, bền và tạo điểm nhấn.'],
            ['en' => 'Apartment', 'vi' => 'Căn hộ chung cư', 'icon' => '🏬', 'desc' => 'Tinh gọn, bền vững, cân bằng kỹ thuật và ở.'],
        ];
    }

    /**
     * Default Step 2 Surfaces.
     */
    public static function getDefaultSurfaces(): array
    {
        return [
            ['en' => 'Floor', 'vi' => 'Bề mặt sàn', 'viShort' => 'Bề mặt sàn', 'enLabel' => 'Floor'],
            ['en' => 'Wall', 'vi' => 'Bề mặt tường', 'viShort' => 'Bề mặt tường', 'enLabel' => 'Wall'],
            ['en' => 'Ceiling', 'vi' => 'Hệ trần', 'viShort' => 'Hệ trần', 'enLabel' => 'Ceiling'],
            ['en' => 'Loose Furniture', 'vi' => 'Đồ rời', 'viShort' => 'Đồ rời', 'enLabel' => 'Loose'],
            ['en' => 'Detail Surface', 'vi' => 'Chi tiết kỹ thuật', 'viShort' => 'Chi tiết kỹ thuật', 'enLabel' => 'Detail'],
        ];
    }

    /**
     * Default Step 3 Area Matrix.
     */
    public static function getDefaultAreaMatrix(): array
    {
        return [
            'Office' => [
                'Floor' => ['Reception', 'Open Work', 'Meeting Room', 'Executive', 'Corridor', 'Pantry', 'WC'],
                'Wall' => ['Reception', 'Open Work', 'Meeting Room', 'Corridor', 'Pantry', 'Executive'],
                'Ceiling' => ['Reception', 'Open Work', 'Meeting Room', 'Executive', 'Corridor'],
                'Loose Furniture' => ['Reception', 'Open Work', 'Meeting Room', 'Pantry', 'Lounge'],
                'Detail Surface' => ['Reception Desk', 'Pantry Counter', 'Meeting Table', 'Executive Desk', 'Corridor Handrail'],
            ],
            'School' => [
                'Floor' => ['Classroom', 'Corridor', 'Library', 'Canteen', 'Playground', 'WC', 'Admin'],
                'Wall' => ['Classroom', 'Corridor', 'Library', 'Canteen', 'Auditorium'],
                'Ceiling' => ['Classroom', 'Library', 'Auditorium', 'Corridor', 'Canteen'],
                'Loose Furniture' => ['Classroom', 'Library', 'Canteen', 'Admin', 'Auditorium'],
                'Detail Surface' => ['Lab Bench', 'Library Shelf', 'Canteen Counter', 'Classroom Desk'],
            ],
            'Townhouse' => [
                'Floor' => ['Living Room', 'Kitchen', 'Bedroom', 'WC', 'Stair', 'Terrace'],
                'Wall' => ['Living Room', 'Kitchen', 'Bedroom', 'Facade', 'WC', 'Stair'],
                'Ceiling' => ['Living Room', 'Kitchen', 'Bedroom', 'WC', 'Terrace'],
                'Loose Furniture' => ['Living Room', 'Bedroom', 'Kitchen', 'Terrace'],
                'Detail Surface' => ['Kitchen Counter', 'Stair Handrail', 'Wardrobe Surface', 'Bath Vanity'],
            ],
            'Coffee Shop' => [
                'Floor' => ['Serving Area', 'Seating Area', 'Bar Counter', 'WC', 'Terrace', 'Kitchen'],
                'Wall' => ['Bar Counter', 'Seating Area', 'Facade', 'WC', 'Kitchen', 'Serving Area'],
                'Ceiling' => ['Seating Area', 'Bar Counter', 'Serving Area', 'WC'],
                'Loose Furniture' => ['Seating Area', 'Bar Counter', 'Terrace', 'Serving Area'],
                'Detail Surface' => ['Bar Counter', 'Table Top', 'Facade Cladding', 'Menu Board'],
            ],
            'Retail' => [
                'Floor' => ['Sales Floor', 'Fitting Room', 'Cashier', 'Storage', 'WC', 'Entrance'],
                'Wall' => ['Sales Floor', 'Fitting Room', 'Facade', 'Cashier', 'Entrance'],
                'Ceiling' => ['Sales Floor', 'Fitting Room', 'Entrance', 'Cashier'],
                'Loose Furniture' => ['Sales Floor', 'Fitting Room', 'Cashier', 'Window Display'],
                'Detail Surface' => ['Cashier Counter', 'Display Shelf', 'Facade', 'Fitting Bench'],
            ],
            'Apartment' => [
                'Floor' => ['Living Room', 'Kitchen', 'Bedroom', 'Balcony', 'WC', 'Corridor'],
                'Wall' => ['Living Room', 'Kitchen', 'Bedroom', 'Balcony', 'WC', 'Corridor'],
                'Ceiling' => ['Living Room', 'Kitchen', 'Bedroom', 'Balcony', 'WC'],
                'Loose Furniture' => ['Living Room', 'Bedroom', 'Balcony', 'Kitchen', 'Corridor'],
                'Detail Surface' => ['Kitchen Counter', 'Wardrobe', 'Bath Vanity', 'Balcony Railing'],
            ],
        ];
    }

    /**
     * Default Step 3 Area Vietnamese Labels.
     */
    public static function getDefaultAreaLabels(): array
    {
        return [
            'Reception' => 'Khu lễ tân', 'Open Work' => 'Khu làm việc mở', 'Meeting Room' => 'Phòng họp',
            'Executive' => 'Phòng điều hành', 'Corridor' => 'Hành lang', 'Pantry' => 'Pantry',
            'WC' => 'Khu vệ sinh', 'Lounge' => 'Khu thư giãn (Lounge)', 'Classroom' => 'Phòng học', 'Library' => 'Thư viện',
            'Canteen' => 'Căn tin', 'Playground' => 'Sân chơi', 'Admin' => 'Hành chính',
            'Auditorium' => 'Hội trường', 'Living Room' => 'Phòng khách', 'Kitchen' => 'Bếp',
            'Bedroom' => 'Phòng ngủ', 'Stair' => 'Cầu thang', 'Terrace' => 'Sân thượng',
            'Facade' => 'Mặt dựng', 'Serving Area' => 'Khu phục vụ', 'Seating Area' => 'Khu chỗ ngồi',
            'Bar Counter' => 'Quầy bar', 'Sales Floor' => 'Sàn bán hàng', 'Fitting Room' => 'Phòng thử đồ',
            'Cashier' => 'Thu ngân', 'Storage' => 'Kho', 'Entrance' => 'Lối vào',
            'Window Display' => 'Tủ trưng bày', 'Reception Desk' => 'Quầy lễ tân', 'Pantry Counter' => 'Bàn pantry',
            'Meeting Table' => 'Bàn họp', 'Executive Desk' => 'Bàn điều hành', 'Corridor Handrail' => 'Tay vịn',
            'Lab Bench' => 'Bàn thí nghiệm', 'Library Shelf' => 'Kệ thư viện', 'Canteen Counter' => 'Quầy căn tin',
            'Classroom Desk' => 'Bàn học', 'Kitchen Counter' => 'Mặt bếp', 'Stair Handrail' => 'Tay vịn cầu thang',
            'Wardrobe Surface' => 'Bề mặt tủ áo', 'Bath Vanity' => 'Bàn lavabo', 'Table Top' => 'Mặt bàn',
            'Facade Cladding' => 'Ốp mặt dựng', 'Menu Board' => 'Bảng thực đơn', 'Cashier Counter' => 'Quầy thu ngân',
            'Display Shelf' => 'Kệ trưng bày', 'Fitting Bench' => 'Băng thử đồ', 'Wardrobe' => 'Tủ áo',
            'Balcony' => 'Ban công', 'Balcony Railing' => 'Lan can',
        ];
    }

    /**
     * Default Step 4 Life Cycles.
     */
    public static function getDefaultLifeCycles(): array
    {
        return [
            ['id' => 'short', 'years' => 3, 'vi' => '1–3 năm', 'en' => 'Short term', 'note' => 'Pop-up, thử nghiệm'],
            ['id' => 'medium', 'years' => 7, 'vi' => '5–10 năm', 'en' => 'Standard', 'note' => 'Văn phòng tiêu chuẩn'],
            ['id' => 'long', 'years' => 15, 'vi' => '10+ năm', 'en' => 'Long term', 'note' => 'Sảnh, lõi, WC'],
        ];
    }

    /**
     * Default Step 4 Budget Levels.
     */
    public static function getDefaultBudgetLevels(): array
    {
        return [
            ['level' => 1, 'vi' => 'Tiết kiệm', 'en' => 'Economical', 'vnd' => '85k – 300k'],
            ['level' => 2, 'vi' => 'Cân bằng', 'en' => 'Balanced', 'vnd' => '300k – 600k'],
            ['level' => 3, 'vi' => 'Cao cấp', 'en' => 'Premium', 'vnd' => '600k – 900k'],
            ['level' => 4, 'vi' => 'Siêu cao cấp', 'en' => 'Luxury', 'vnd' => '900k – 1.5tr+'],
            ['level' => 5, 'vi' => 'Bỏ qua', 'en' => 'Skip', 'vnd' => 'Mọi mức giá', 'isSkip' => true],
        ];
    }

    /**
     * Default Step 5 Left Column Insight Content.
     */
    public static function getDefaultStep5Insight(): array
    {
        return [
            'ai_standards' => "• QCVN 06:2022/BXD – An toàn cháy: lối thoát nạn yêu cầu vật liệu không cháy / chậm cháy.\n• QCVN 16:2023/BXD – Giới hạn VOC, formaldehyde, hấp thụ nước <0.5% cho porcelain.\n• TCVN 4474 + TCVN 8264 – Tiêu âm, chống trượt: sàn R9-R11, trần NRC ≥0.65.\n• WELL v2 – LEED v4.1 – S03 Movement, S04 Sound, A06 VOC <50g/L.",
            'ai_fire_floor' => 'Bfl-s1 / B1 cho thoát nạn',
            'ai_fire_ceiling' => 'Class A – 30 phút',
            'ai_fire_other' => 'B1 hành lang',
            'ai_metrics' => 'R9-11 • VOC <50 • NRC 0.45-0.70 • E0',
            'ws_experience' => "• Đã làm 23 phòng họp LVT, 41 văn phòng gạch Taicera, 12 sảnh Terrazzo.\n• Bài học: nền ẩm >75% → LVT phồng, cần đo ẩm trước.\n• Thẩm mỹ: ưu tiên vân mờ, trung tính ấm, không bóng gắt.\n• Đội quen: thợ ốp Taicera, thạch cao Knauf, inox TIG Sài Gòn.",
            'ws_common_errors' => 'Nền không phẳng • Ẩm >75% • Keo rẻ bong mép',
            'ws_tips' => 'Đo ẩm • Cán phẳng 3mm • Keo Wakol/Weber • Khe 5mm',
        ];
    }

    /**
     * Default Categories.
     */
    public static function getDefaultCategories(): array
    {
        return ['Vinyl', 'Terrazzo', 'Porcelain', 'HPL', 'Wool', 'Paint', 'Mineral', 'PET', 'Glass', 'Steel', 'Wood', 'Rubber', 'Gypsum', 'Solid Surface', 'Veneer', 'Carpet', 'Stainless'];
    }

    /**
     * Default Brands / Suppliers.
     */
    public static function getDefaultBrands(): array
    {
        return ['Taicera / Vietnam', 'Moduleo / Belgium', 'Vietbeton', 'LG Hausys / HQ', 'Formica / USA', 'Kvadrat / Denmark', 'Dulux / AkzoNobel VN', 'Armstrong / UK', 'Saint-Gobain', 'DuPont / USA', 'Alpi / An Cường', 'Interface / NL', 'Feltouch / Turkey', 'Posco / Korea-VN', 'Nora by Interface / DE', 'Knauf / Vietnam-DE', 'AGC / Japan', 'An Cường / Vietnam'];
    }

    /**
     * Get managed list of Categories (stored in settings + any distinct DB categories).
     */
    public static function getManagedCategories(): array
    {
        $saved = self::resolveArraySetting('material_lab_categories', self::getDefaultCategories());
        $db = Material::select('category')->whereNotNull('category')->distinct()->pluck('category')->filter()->values()->toArray();
        $merged = array_values(array_unique(array_filter(array_merge($saved, $db))));
        sort($merged);

        return $merged;
    }

    /**
     * Get managed list of Brands / Suppliers (stored in settings + any distinct DB brands).
     */
    public static function getManagedBrands(): array
    {
        $saved = self::resolveArraySetting('material_lab_brands', self::getDefaultBrands());
        $db = Material::select('brand')->whereNotNull('brand')->distinct()->pluck('brand')->filter()->values()->toArray();
        $merged = array_values(array_unique(array_filter(array_merge($saved, $db))));
        sort($merged);

        return $merged;
    }

    /**
     * Helper to resolve JSON/Array setting with fallback.
     */
    public static function resolveArraySetting(string $key, array $default): array
    {
        $val = setting($key);
        if (is_string($val)) {
            $decoded = json_decode($val, true);
            if (is_array($decoded) && ! empty($decoded)) {
                return $decoded;
            }
        }
        if (is_array($val) && ! empty($val)) {
            return $val;
        }

        return $default;
    }

    /**
     * Display a listing of materials in admin portal.
     */
    public function index(Request $request): View
    {
        $query = Material::query();

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%")
                    ->orWhere('brand', 'like', "%{$s}%")
                    ->orWhere('category', 'like', "%{$s}%");
            });
        }

        if ($request->filled('typology')) {
            $t = $request->typology;
            $query->whereJsonContains('suitable_typologies', $t);
        }

        if ($request->filled('surface')) {
            $surf = $request->surface;
            $query->whereJsonContains('suitable_locations', $surf);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('origin')) {
            $query->where('origin', $request->origin);
        }

        if ($request->filled('budget_level')) {
            $query->where('budget_level', $request->budget_level);
        }

        $materials = $query->orderBy('sort_order')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $categories = self::getManagedCategories();
        $typologies = self::resolveArraySetting('material_lab_typologies', self::getDefaultTypologies());
        $surfaces = self::resolveArraySetting('material_lab_surfaces', self::getDefaultSurfaces());

        return view('cms.material-lab.index', compact('materials', 'categories', 'typologies', 'surfaces'));
    }

    /**
     * Helper to retrieve dropdown options for Material forms.
     */
    protected function getDropdownOptions(?Material $current = null): array
    {
        $typologies = self::resolveArraySetting('material_lab_typologies', self::getDefaultTypologies());
        $surfaces = self::resolveArraySetting('material_lab_surfaces', self::getDefaultSurfaces());
        $areaMatrix = self::resolveArraySetting('material_lab_area_matrix', self::getDefaultAreaMatrix());
        $areaLabels = self::resolveArraySetting('material_lab_area_labels', self::getDefaultAreaLabels());
        $lifeCycles = self::resolveArraySetting('material_lab_lifecycles', self::getDefaultLifeCycles());
        $budgetLevels = self::resolveArraySetting('material_lab_budgets', self::getDefaultBudgetLevels());

        // Collect all unique areas across areaMatrix + areaLabels
        $allAreaKeys = array_keys($areaLabels);
        foreach ($areaMatrix as $surfMap) {
            if (is_array($surfMap)) {
                foreach ($surfMap as $areaList) {
                    if (is_array($areaList)) {
                        foreach ($areaList as $aKey) {
                            if (! in_array($aKey, $allAreaKeys, true)) {
                                $allAreaKeys[] = $aKey;
                            }
                        }
                    }
                }
            }
        }
        $allAreas = [];
        foreach ($allAreaKeys as $aKey) {
            $allAreas[] = [
                'en' => $aKey,
                'vi' => $areaLabels[$aKey] ?? $aKey,
            ];
        }

        $step1Title = setting('material_lab_step1_title', 'Công trình của bạn là gì?');
        $step1Desc = setting('material_lab_step1_desc', 'Chọn loại hình để gợi ý vật liệu phù hợp.');
        $step2Title = setting('material_lab_step2_title', 'Vị trí nào cần vật liệu?');
        $step2Desc = setting('material_lab_step2_desc', 'Chọn bề mặt để lọc vật liệu phù hợp.');
        $step3Title = setting('material_lab_step3_title', 'Khu vực chức năng nào?');
        $step4Title = setting('material_lab_step4_title', 'Nhu cầu thực tế?');
        $step4Desc = setting('material_lab_step4_desc', 'Chọn độ bền, ngân sách, nguồn gốc và tồn kho.');

        $categories = array_values(array_unique(array_filter(array_merge(
            self::getManagedCategories(),
            $current && $current->category ? [$current->category] : []
        ))));
        sort($categories);

        $brands = array_values(array_unique(array_filter(array_merge(
            self::getManagedBrands(),
            $current && $current->brand ? [$current->brand] : []
        ))));
        sort($brands);

        $defaultFinishes = ['Vân gỗ mờ', 'Mài bóng lộ đá', 'Giả đá mờ', 'Mờ mịn chống vân tay', 'Dệt nỉ len', 'Sơn mờ siêu mịn', 'Micro-perforated', 'Clear Low-Iron', 'Liền khối', 'Natural Oiled', 'Loop Pile', 'Nỉ ép', 'Hairline Brushed', 'Đàn hồi', 'Perforated'];
        $dbFinishes = Material::select('finish')->whereNotNull('finish')->distinct()->pluck('finish')->filter()->values()->toArray();
        $finishes = array_values(array_unique(array_filter(array_merge($defaultFinishes, $dbFinishes, $current && $current->finish ? [$current->finish] : []))));
        sort($finishes);

        return compact(
            'typologies',
            'surfaces',
            'areaMatrix',
            'areaLabels',
            'allAreas',
            'lifeCycles',
            'budgetLevels',
            'step1Title',
            'step1Desc',
            'step2Title',
            'step2Desc',
            'step3Title',
            'step4Title',
            'step4Desc',
            'categories',
            'brands',
            'finishes'
        );
    }

    /**
     * Show form to create a new material.
     */
    public function create(): View
    {
        $options = $this->getDropdownOptions();

        return view('cms.material-lab.create', $options);
    }

    /**
     * Store a newly created material.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:100|unique:materials,code',
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'origin' => 'required|string|in:Vietnam,Import,Both',
            'origin_label' => 'nullable|string|max:255',
            'availability' => 'required|string|in:Stock,2-3 weeks,6-8 weeks,Any',
            'durability_years' => 'required|integer|min:1|max:50',
            'cost_label' => 'nullable|string|max:100',
            'budget_level' => 'required|integer|in:1,2,3,4',
            'finish' => 'nullable|string|max:255',
            'tech_specs' => 'nullable|string|max:255',
            'application' => 'nullable|string|max:255',
            'suitable_typologies' => 'nullable|array',
            'suitable_locations' => 'nullable|array',
            'suitable_areas' => 'nullable|array',
            'tech_description' => 'nullable|string',
            'ai_standards' => 'nullable|string',
            'fire_requirement' => 'nullable|string|max:255',
            'metrics' => 'nullable|string|max:255',
            'experience_description' => 'nullable|string',
            'ws_experience' => 'nullable|string',
            'common_errors' => 'nullable|string|max:255',
            'tips' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Vui lòng nhập Tên vật liệu.',
            'brand.required' => 'Vui lòng chọn hoặc nhập Thương hiệu / Nhà cung cấp.',
            'category.required' => 'Vui lòng chọn hoặc nhập Phân loại danh mục vật liệu.',
            'origin.required' => 'Vui lòng chọn Xuất xứ.',
            'availability.required' => 'Vui lòng chọn Thời gian cung ứng.',
            'durability_years.required' => 'Vui lòng nhập Độ bền bảo hành (năm).',
            'budget_level.required' => 'Vui lòng chọn Mức ngân sách.',
        ]);

        // Tự động sinh mã định danh (Code) từ Tên vật liệu nếu không truyền vào
        if (empty($validated['code'])) {
            $baseCode = Str::slug($request->input('name') ?? 'material', '-');
            $code = $baseCode;
            $counter = 1;
            while (Material::where('code', $code)->exists()) {
                $code = "{$baseCode}-{$counter}";
                $counter++;
            }
            $validated['code'] = $code;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['origin_label'] = match ($validated['origin']) {
            'Vietnam' => 'Hàng Việt',
            'Import' => 'Nhập khẩu',
            'Both' => 'Liên doanh',
            default => 'Hàng Việt',
        };

        $budgetLevels = self::resolveArraySetting('material_lab_budgets', self::getDefaultBudgetLevels());
        $matchedBudget = collect($budgetLevels)->firstWhere('level', (int) $validated['budget_level']);
        $validated['cost_label'] = $matchedBudget['vnd'] ?? match ((int) $validated['budget_level']) {
            1 => '85k – 300k',
            2 => '300k – 600k',
            3 => '600k – 900k',
            4 => '900k – 1.5tr+',
            default => '300k – 600k',
        };

        $validated['tech_specs'] = $validated['metrics'] ?? null;
        $validated['tech_description'] = $validated['ai_standards'] ?? null;
        $validated['experience_description'] = $validated['ws_experience'] ?? null;
        $validated['application'] = isset($validated['ws_experience']) ? Str::limit(trim((string) $validated['ws_experience']), 250) : null;

        $validated['sort_order'] = $request->filled('sort_order')
            ? (int) $request->input('sort_order')
            : (int) (Material::max('sort_order') ?? 0) + 1;
        $validated['suitable_typologies'] = array_values(array_filter($request->input('suitable_typologies', [])));
        $validated['suitable_locations'] = array_values(array_filter($request->input('suitable_locations', [])));
        $validated['suitable_areas'] = array_values(array_filter($request->input('suitable_areas', [])));

        Material::create($validated);

        return redirect()->route('admin.material-lab.index')->with('success', 'Thêm mới vật liệu thành công!');
    }

    /**
     * Show form to edit an existing material.
     */
    public function edit(Material $material): View
    {
        $options = $this->getDropdownOptions($material);

        return view('cms.material-lab.edit', array_merge(['material' => $material], $options));
    }

    /**
     * Update material in storage.
     */
    public function update(Request $request, Material $material): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:100|unique:materials,code,' . $material->id,
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'origin' => 'required|string|in:Vietnam,Import,Both',
            'origin_label' => 'nullable|string|max:255',
            'availability' => 'required|string|in:Stock,2-3 weeks,6-8 weeks,Any',
            'durability_years' => 'required|integer|min:1|max:50',
            'cost_label' => 'nullable|string|max:100',
            'budget_level' => 'required|integer|in:1,2,3,4',
            'finish' => 'nullable|string|max:255',
            'tech_specs' => 'nullable|string|max:255',
            'application' => 'nullable|string|max:255',
            'suitable_typologies' => 'nullable|array',
            'suitable_locations' => 'nullable|array',
            'suitable_areas' => 'nullable|array',
            'tech_description' => 'nullable|string',
            'ai_standards' => 'nullable|string',
            'fire_requirement' => 'nullable|string|max:255',
            'metrics' => 'nullable|string|max:255',
            'experience_description' => 'nullable|string',
            'ws_experience' => 'nullable|string',
            'common_errors' => 'nullable|string|max:255',
            'tips' => 'nullable|string|max:255',
            'image_url' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Vui lòng nhập Tên vật liệu.',
            'brand.required' => 'Vui lòng chọn hoặc nhập Thương hiệu / Nhà cung cấp.',
            'category.required' => 'Vui lòng chọn hoặc nhập Phân loại danh mục vật liệu.',
            'origin.required' => 'Vui lòng chọn Xuất xứ.',
            'availability.required' => 'Vui lòng chọn Thời gian cung ứng.',
            'durability_years.required' => 'Vui lòng nhập Độ bền bảo hành (năm).',
            'budget_level.required' => 'Vui lòng chọn Mức ngân sách.',
        ]);

        // Giữ nguyên code nếu form không gửi lên
        if (empty($validated['code'])) {
            $validated['code'] = $material->code;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['origin_label'] = match ($validated['origin']) {
            'Vietnam' => 'Hàng Việt',
            'Import' => 'Nhập khẩu',
            'Both' => 'Liên doanh',
            default => 'Hàng Việt',
        };

        $budgetLevels = self::resolveArraySetting('material_lab_budgets', self::getDefaultBudgetLevels());
        $matchedBudget = collect($budgetLevels)->firstWhere('level', (int) $validated['budget_level']);
        $validated['cost_label'] = $matchedBudget['vnd'] ?? match ((int) $validated['budget_level']) {
            1 => '85k – 300k',
            2 => '300k – 600k',
            3 => '600k – 900k',
            4 => '900k – 1.5tr+',
            default => '300k – 600k',
        };

        $validated['tech_specs'] = $validated['metrics'] ?? null;
        $validated['tech_description'] = $validated['ai_standards'] ?? null;
        $validated['experience_description'] = $validated['ws_experience'] ?? null;
        $validated['application'] = isset($validated['ws_experience']) ? Str::limit(trim((string) $validated['ws_experience']), 250) : null;

        $validated['suitable_typologies'] = array_values(array_filter($request->input('suitable_typologies', [])));
        $validated['suitable_locations'] = array_values(array_filter($request->input('suitable_locations', [])));
        $validated['suitable_areas'] = array_values(array_filter($request->input('suitable_areas', [])));
        $validated['sort_order'] = $request->filled('sort_order')
            ? (int) $request->input('sort_order')
            : ($material->sort_order ?? 0);

        $material->update($validated);

        return redirect()->route('admin.material-lab.index')->with('success', 'Cập nhật vật liệu thành công!');
    }

    /**
     * Delete a material.
     */
    public function destroy(Material $material): RedirectResponse
    {
        $material->delete();

        return redirect()->route('admin.material-lab.index')->with('success', 'Đã xóa vật liệu thành công!');
    }

    /**
     * Toggle active state via AJAX/POST.
     */
    public function toggleActive(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $material = Material::findOrFail($id);
        $material->is_active = ! $material->is_active;
        $material->save();

        return response()->json([
            'success' => true,
            'is_active' => $material->is_active,
            'message' => $material->is_active ? 'Đã kích hoạt hiển thị' : 'Đã ẩn vật liệu',
        ]);
    }

    /**
     * View configuration page for Material Lab.
     */
    public function config(): View
    {
        $typologies = self::resolveArraySetting('material_lab_typologies', self::getDefaultTypologies());
        $surfaces = self::resolveArraySetting('material_lab_surfaces', self::getDefaultSurfaces());
        $areaMatrix = self::resolveArraySetting('material_lab_area_matrix', self::getDefaultAreaMatrix());
        $areaLabels = self::resolveArraySetting('material_lab_area_labels', self::getDefaultAreaLabels());
        $lifeCycles = self::resolveArraySetting('material_lab_lifecycles', self::getDefaultLifeCycles());
        $budgetLevels = self::resolveArraySetting('material_lab_budgets', self::getDefaultBudgetLevels());
        $step5Insight = array_merge(
            self::getDefaultStep5Insight(),
            self::resolveArraySetting('material_lab_step5_insight', [])
        );

        $title = setting('material_lab_title', 'Thư viện vật liệu');
        $subtitle = setting('material_lab_subtitle', 'Material Lab • V1.0');
        $step1Title = setting('material_lab_step1_title', 'Công trình của bạn là gì?');
        $step1Desc = setting('material_lab_step1_desc', 'Chọn loại hình để gợi ý vật liệu phù hợp.');
        $step2Title = setting('material_lab_step2_title', 'Vị trí nào cần vật liệu?');
        $step2Desc = setting('material_lab_step2_desc', 'Chọn bề mặt để lọc vật liệu phù hợp.');
        $step3Title = setting('material_lab_step3_title', 'Khu vực chức năng nào?');
        $step4Title = setting('material_lab_step4_title', 'Nhu cầu thực tế?');
        $step4Desc = setting('material_lab_step4_desc', 'Chọn độ bền, ngân sách, nguồn gốc và tồn kho.');

        return view('cms.material-lab.config', compact(
            'typologies',
            'surfaces',
            'areaMatrix',
            'areaLabels',
            'lifeCycles',
            'budgetLevels',
            'step5Insight',
            'title',
            'subtitle',
            'step1Title',
            'step1Desc',
            'step2Title',
            'step2Desc',
            'step3Title',
            'step4Title',
            'step4Desc'
        ));
    }

    /**
     * Save configuration for Material Lab.
     */
    public function saveConfig(Request $request): RedirectResponse
    {
        $settingsToSave = [
            'material_lab_title' => $request->input('material_lab_title', 'Thư viện vật liệu'),
            'material_lab_subtitle' => $request->input('material_lab_subtitle', 'Material Lab • V1.0'),
            'material_lab_step1_title' => $request->input('material_lab_step1_title', 'Công trình của bạn là gì?'),
            'material_lab_step1_desc' => $request->input('material_lab_step1_desc', 'Chọn loại hình để gợi ý vật liệu phù hợp.'),
            'material_lab_step2_title' => $request->input('material_lab_step2_title', 'Vị trí nào cần vật liệu?'),
            'material_lab_step2_desc' => $request->input('material_lab_step2_desc', 'Chọn bề mặt để lọc vật liệu phù hợp.'),
            'material_lab_step3_title' => $request->input('material_lab_step3_title', 'Khu vực chức năng nào?'),
            'material_lab_step4_title' => $request->input('material_lab_step4_title', 'Nhu cầu thực tế?'),
            'material_lab_step4_desc' => $request->input('material_lab_step4_desc', 'Chọn độ bền, ngân sách, nguồn gốc và tồn kho.'),
        ];

        // 1. Typologies (Step 1)
        if ($request->filled('typologies_json')) {
            $decoded = json_decode($request->input('typologies_json'), true);
            if (is_array($decoded) && ! empty($decoded)) {
                $cleanTypologies = [];
                foreach ($decoded as $item) {
                    $en = trim((string) ($item['en'] ?? ''));
                    $vi = trim((string) ($item['vi'] ?? ''));
                    if ($en !== '' && $vi !== '') {
                        $cleanTypologies[] = [
                            'en' => $en,
                            'vi' => $vi,
                            'icon' => trim((string) ($item['icon'] ?? '🏢')) ?: '🏢',
                            'desc' => trim((string) ($item['desc'] ?? '')),
                        ];
                    }
                }
                if (! empty($cleanTypologies)) {
                    $settingsToSave['material_lab_typologies'] = $cleanTypologies;
                }
            }
        }

        // 2. Surfaces (Step 2)
        if ($request->filled('surfaces_json')) {
            $decoded = json_decode($request->input('surfaces_json'), true);
            if (is_array($decoded) && ! empty($decoded)) {
                $cleanSurfaces = [];
                foreach ($decoded as $item) {
                    $en = trim((string) ($item['en'] ?? ''));
                    $vi = trim((string) ($item['vi'] ?? ''));
                    if ($en !== '' && $vi !== '') {
                        $cleanSurfaces[] = [
                            'en' => $en,
                            'vi' => $vi,
                            'viShort' => trim((string) ($item['viShort'] ?? $vi)) ?: $vi,
                            'enLabel' => trim((string) ($item['enLabel'] ?? $en)) ?: $en,
                        ];
                    }
                }
                if (! empty($cleanSurfaces)) {
                    $settingsToSave['material_lab_surfaces'] = $cleanSurfaces;
                }
            }
        }

        // 3. Area Labels & Area Matrix (Step 3)
        if ($request->filled('area_labels_json')) {
            $decoded = json_decode($request->input('area_labels_json'), true);
            if (is_array($decoded) && ! empty($decoded)) {
                $settingsToSave['material_lab_area_labels'] = $decoded;
            }
        }

        if ($request->filled('area_matrix_json')) {
            $decoded = json_decode($request->input('area_matrix_json'), true);
            if (is_array($decoded) && ! empty($decoded)) {
                $settingsToSave['material_lab_area_matrix'] = $decoded;
            }
        }

        // 4. LifeCycles & Budgets (Step 4)
        if ($request->filled('lifecycles_json')) {
            $decoded = json_decode($request->input('lifecycles_json'), true);
            if (is_array($decoded) && ! empty($decoded)) {
                $settingsToSave['material_lab_lifecycles'] = $decoded;
            }
        }

        if ($request->filled('budgets_json')) {
            $decoded = json_decode($request->input('budgets_json'), true);
            if (is_array($decoded) && ! empty($decoded)) {
                $settingsToSave['material_lab_budgets'] = $decoded;
            }
        }

        // 5. Step 5 Insight Config
        if ($request->has('step5_insight') && is_array($request->input('step5_insight'))) {
            $settingsToSave['material_lab_step5_insight'] = $request->input('step5_insight');
        }

        foreach ($settingsToSave as $key => $val) {
            if (function_exists('set_config')) {
                set_config($key, $val, 'general');
            }
            DB::table('settings')->updateOrInsert(
                ['key' => $key, 'project_id' => null],
                [
                    'payload' => json_encode(is_array($val) ? $val : ['value' => $val]),
                    'value' => is_scalar($val) ? (string) $val : json_encode($val),
                    'group' => 'general',
                    'updated_at' => now(),
                ]
            );
        }

        SettingsService::getInstance()->clearCache();

        return redirect()->route('admin.material-lab.config')->with('success', 'Đã lưu toàn bộ cấu hình Material Lab thành công!');
    }

    /**
     * Helper to persist an array setting to DB & clear cache.
     */
    protected function persistArraySetting(string $key, array $val): void
    {
        if (function_exists('set_config')) {
            set_config($key, $val, 'general');
        }
        DB::table('settings')->updateOrInsert(
            ['key' => $key, 'project_id' => null],
            [
                'payload' => json_encode($val),
                'value' => json_encode($val),
                'group' => 'general',
                'updated_at' => now(),
            ]
        );
        SettingsService::getInstance()->clearCache();
    }

    /**
     * Manage Brands / Suppliers sub-page.
     */
    public function brands(): View
    {
        $brands = self::getManagedBrands();
        $counts = Material::select('brand', DB::raw('count(*) as total'))
            ->whereNotNull('brand')
            ->groupBy('brand')
            ->pluck('total', 'brand')
            ->toArray();

        return view('cms.material-lab.brands', compact('brands', 'counts'));
    }

    /**
     * Save Brands / Suppliers actions (add, rename, delete).
     */
    public function saveBrands(Request $request): RedirectResponse
    {
        $action = $request->input('action', 'add');
        $brands = self::getManagedBrands();

        if ($action === 'add') {
            $newBrand = trim((string) $request->input('name', ''));
            if ($newBrand === '') {
                return redirect()->route('admin.material-lab.brands')->with('error', 'Vui lòng nhập tên thương hiệu / nhà cung cấp.');
            }
            if (! in_array($newBrand, $brands, true)) {
                $brands[] = $newBrand;
                sort($brands);
                $this->persistArraySetting('material_lab_brands', array_values($brands));
            }

            return redirect()->route('admin.material-lab.brands')->with('success', "Đã thêm thương hiệu \"{$newBrand}\" thành công!");
        }

        if ($action === 'rename') {
            $oldName = trim((string) $request->input('old_name', ''));
            $newName = trim((string) $request->input('new_name', ''));
            if ($oldName !== '' && $newName !== '' && $oldName !== $newName) {
                $brands = array_map(fn ($b) => $b === $oldName ? $newName : $b, $brands);
                $brands = array_values(array_unique(array_filter($brands)));
                sort($brands);
                Material::where('brand', $oldName)->update(['brand' => $newName]);
                $this->persistArraySetting('material_lab_brands', $brands);

                return redirect()->route('admin.material-lab.brands')->with('success', "Đã đổi tên thương hiệu \"{$oldName}\" thành \"{$newName}\"!");
            }
        }

        if ($action === 'delete') {
            $target = trim((string) $request->input('name', ''));
            $usedCount = Material::where('brand', $target)->count();
            if ($usedCount > 0) {
                return redirect()->route('admin.material-lab.brands')->with('error', "Không thể xóa thương hiệu \"{$target}\" vì đang có {$usedCount} vật liệu sử dụng. Hãy đổi tên hoặc chuyển vật liệu sang thương hiệu khác trước.");
            }
            $brands = array_values(array_filter($brands, fn ($b) => $b !== $target));
            $this->persistArraySetting('material_lab_brands', $brands);

            return redirect()->route('admin.material-lab.brands')->with('success', "Đã xóa thương hiệu \"{$target}\" khỏi danh sách!");
        }

        return redirect()->route('admin.material-lab.brands');
    }

    /**
     * Manage Categories sub-page.
     */
    public function categories(): View
    {
        $categories = self::getManagedCategories();
        $counts = Material::select('category', DB::raw('count(*) as total'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        return view('cms.material-lab.categories', compact('categories', 'counts'));
    }

    /**
     * Save Categories actions (add, rename, delete).
     */
    public function saveCategories(Request $request): RedirectResponse
    {
        $action = $request->input('action', 'add');
        $categories = self::getManagedCategories();

        if ($action === 'add') {
            $newCat = trim((string) $request->input('name', ''));
            if ($newCat === '') {
                return redirect()->route('admin.material-lab.categories')->with('error', 'Vui lòng nhập tên phân loại danh mục.');
            }
            if (! in_array($newCat, $categories, true)) {
                $categories[] = $newCat;
                sort($categories);
                $this->persistArraySetting('material_lab_categories', array_values($categories));
            }

            return redirect()->route('admin.material-lab.categories')->with('success', "Đã thêm phân loại danh mục \"{$newCat}\" thành công!");
        }

        if ($action === 'rename') {
            $oldName = trim((string) $request->input('old_name', ''));
            $newName = trim((string) $request->input('new_name', ''));
            if ($oldName !== '' && $newName !== '' && $oldName !== $newName) {
                $categories = array_map(fn ($c) => $c === $oldName ? $newName : $c, $categories);
                $categories = array_values(array_unique(array_filter($categories)));
                sort($categories);
                Material::where('category', $oldName)->update(['category' => $newName]);
                $this->persistArraySetting('material_lab_categories', $categories);

                return redirect()->route('admin.material-lab.categories')->with('success', "Đã đổi tên danh mục \"{$oldName}\" thành \"{$newName}\"!");
            }
        }

        if ($action === 'delete') {
            $target = trim((string) $request->input('name', ''));
            $usedCount = Material::where('category', $target)->count();
            if ($usedCount > 0) {
                return redirect()->route('admin.material-lab.categories')->with('error', "Không thể xóa danh mục \"{$target}\" vì đang có {$usedCount} vật liệu sử dụng. Hãy đổi tên hoặc chuyển vật liệu sang danh mục khác trước.");
            }
            $categories = array_values(array_filter($categories, fn ($c) => $c !== $target));
            $this->persistArraySetting('material_lab_categories', $categories);

            return redirect()->route('admin.material-lab.categories')->with('success', "Đã xóa danh mục \"{$target}\" khỏi danh sách!");
        }

        return redirect()->route('admin.material-lab.categories');
    }
}
