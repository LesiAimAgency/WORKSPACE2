<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CoverflowController;
use App\Models\Widget;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Get the 5 standard Service Categories.
     */
    public static function getDefaultCategories(): array
    {
        return [
            [
                'name' => 'Interior Advisory and Project Setup',
                'chip_line_1' => 'Interior Advisory',
                'chip_line_2' => 'and Project Setup',
            ],
            [
                'name' => 'Interior Design and FF&E Consultancy',
                'chip_line_1' => 'Interior Design and',
                'chip_line_2' => 'FF&E Consultancy',
            ],
            [
                'name' => 'Fit-Out and Finishing Works',
                'chip_line_1' => 'Fit-Out and',
                'chip_line_2' => 'Finishing Works',
            ],
            [
                'name' => 'Joinery Manufacturer and FF&E Installation',
                'chip_line_1' => 'Joinery Manufacturer and',
                'chip_line_2' => 'FF&E Installation',
            ],
            [
                'name' => 'Material Library and Competency Management',
                'chip_line_1' => 'Material Library and',
                'chip_line_2' => 'Competency Management',
            ],
        ];
    }

    /**
     * Get managed service categories from settings or defaults.
     */
    public static function getManagedCategories(): array
    {
        $raw = setting('workspace_service_categories');
        $decoded = null;
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
        } elseif (is_array($raw)) {
            $decoded = $raw;
        }

        if (! is_array($decoded) || empty($decoded)) {
            return self::getDefaultCategories();
        }

        $result = [];
        foreach ($decoded as $item) {
            if (is_string($item) && trim($item) !== '') {
                $name = trim($item);
                [$line1, $line2] = self::splitTitleIntoLines($name);
                $result[] = [
                    'name' => $name,
                    'chip_line_1' => $line1,
                    'chip_line_2' => $line2,
                ];
            } elseif (is_array($item) && ! empty($item['name'])) {
                $name = trim((string) $item['name']);
                [$defLine1, $defLine2] = self::splitTitleIntoLines($name);
                $result[] = [
                    'name' => $name,
                    'chip_line_1' => ! empty($item['chip_line_1']) ? trim((string) $item['chip_line_1']) : $defLine1,
                    'chip_line_2' => ! empty($item['chip_line_2']) ? trim((string) $item['chip_line_2']) : $defLine2,
                ];
            }
        }

        return ! empty($result) ? array_values($result) : self::getDefaultCategories();
    }

    /**
     * Split a category/service title into 2 balanced lines for Coverflow chips.
     */
    public static function splitTitleIntoLines(string $title): array
    {
        foreach (self::getDefaultCategories() as $def) {
            if (strcasecmp($def['name'], $title) === 0) {
                return [$def['chip_line_1'], $def['chip_line_2']];
            }
        }

        $words = preg_split('/\s+/', trim($title)) ?: [$title];
        if (count($words) <= 1) {
            return [$title, ''];
        }
        $mid = (int) ceil(count($words) / 2);

        return [
            implode(' ', array_slice($words, 0, $mid)),
            implode(' ', array_slice($words, $mid)),
        ];
    }

    /**
     * Retrieve all normalized services (removing accidental test placeholders if unedited).
     */
    public static function getAllServices(): array
    {
        $rawServices = CoverflowController::getServicesData(null, false);
        $defaults = CoverflowController::getDefaultServices();
        $result = [];

        foreach ($rawServices as $idx => $s) {
            // Skip accidental test placeholder "New Interior Service 06" from previous UI test
            if (
                isset($s['title']) &&
                preg_match('/^New Interior Service \d+$/i', trim((string) $s['title']))
            ) {
                continue;
            }

            $def = $defaults[$idx] ?? $defaults[0];
            $num = sprintf('%02d', count($result) + 1);
            $title = ! empty($s['title']) ? trim((string) $s['title']) : ($def['title'] ?? 'Service '.$num);
            $category = ! empty($s['category']) ? trim((string) $s['category']) : $title;
            $slug = ! empty($s['slug']) ? Str::slug((string) $s['slug']) : Str::slug($title);

            [$defLine1, $defLine2] = self::splitTitleIntoLines($category);
            $chipLine1 = ! empty($s['chip_line_1']) ? trim((string) $s['chip_line_1']) : $defLine1;
            $chipLine2 = ! empty($s['chip_line_2']) ? trim((string) $s['chip_line_2']) : $defLine2;

            $result[] = [
                'id' => count($result) + 1,
                'code' => 'SVC-'.$num,
                'number' => $num,
                'title' => $title,
                'category' => $category,
                'chip_line_1' => $chipLine1,
                'chip_line_2' => $chipLine2,
                'slug' => $slug,
                'desc' => isset($s['desc']) ? trim((string) $s['desc']) : ($def['desc'] ?? ''),
                'image' => ! empty($s['image']) ? trim((string) $s['image']) : ($def['image'] ?? 'themes/workspace/coverflow/frame1.png'),
                'detail_image' => ! empty($s['detail_image']) ? trim((string) $s['detail_image']) : (! empty($s['image']) ? trim((string) $s['image']) : ($def['detail_image'] ?? 'themes/workspace/coverflow/frame1.png')),
                'is_active' => isset($s['is_active']) ? (bool) $s['is_active'] : true,
                'intro_items' => (! empty($s['intro_items']) && is_array($s['intro_items'])) ? array_values($s['intro_items']) : ($def['intro_items'] ?? []),
                'projects' => (! empty($s['projects']) && is_array($s['projects'])) ? array_values($s['projects']) : ($def['projects'] ?? []),
            ];
        }

        return $result;
    }

    /**
     * Get shared UI labels.
     */
    public static function getLabels(): array
    {
        $coverflowSettings = CoverflowController::getCoverflowSettings();
        $detailSettings = CoverflowController::getServiceDetailSettings();

        return [
            'view_more_text' => $coverflowSettings['view_more_text'] ?? 'View more',
            'pill_text' => $detailSettings['pill_text'] ?? 'Services',
            'tab_intro_text' => $detailSettings['tab_intro_text'] ?? 'Introductions',
            'tab_project_text' => $detailSettings['tab_project_text'] ?? 'Projects',
            'back_text' => $detailSettings['back_text'] ?? 'Back to Services',
            'compare_button_text' => $detailSettings['compare_button_text'] ?? 'Comparison Design and Result',
            'label_area' => $detailSettings['label_area'] ?? 'Total Area',
            'label_location' => $detailSettings['label_location'] ?? 'Location',
            'label_typology' => $detailSettings['label_typology'] ?? 'Typology',
            'label_style' => $detailSettings['label_style'] ?? 'Style',
            'label_tech' => $detailSettings['label_tech'] ?? 'Tech Solution',
        ];
    }

    /**
     * Default repeatable project specification fields.
     */
    public static function getDefaultSpecFields(?array $labels = null): array
    {
        $labels = $labels ?? self::getLabels();

        return [
            [
                'key' => 'area',
                'label' => $labels['label_area'] ?? 'Total Area',
                'placeholder' => 'VD: 1,250 m2',
                'default_value' => '850 m2',
            ],
            [
                'key' => 'location',
                'label' => $labels['label_location'] ?? 'Location',
                'placeholder' => 'VD: District 1, Ho Chi Minh City',
                'default_value' => 'Ho Chi Minh City',
            ],
            [
                'key' => 'typology',
                'label' => $labels['label_typology'] ?? 'Typology',
                'placeholder' => 'VD: Technology Workspace',
                'default_value' => 'Commercial Office',
            ],
            [
                'key' => 'style',
                'label' => $labels['label_style'] ?? 'Style',
                'placeholder' => 'VD: Contemporary Minimalist',
                'default_value' => 'Modern Minimalist',
            ],
            [
                'key' => 'tech',
                'label' => $labels['label_tech'] ?? 'Tech Solution',
                'placeholder' => 'VD: Acoustic Baffles & Integrated MEP',
                'default_value' => 'Smart Lighting & Acoustic Control',
            ],
        ];
    }

    /**
     * Get configured repeatable project specification fields.
     */
    public static function getSpecFields(): array
    {
        $raw = setting('workspace_project_spec_fields');
        $decoded = null;
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
        } elseif (is_array($raw)) {
            $decoded = $raw;
        }

        if (! is_array($decoded) || empty($decoded)) {
            return self::getDefaultSpecFields();
        }

        $fields = [];
        foreach (array_values($decoded) as $idx => $item) {
            if (! is_array($item)) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $rawKey = trim((string) ($item['key'] ?? ''));
            $key = $rawKey !== '' ? Str::slug($rawKey, '_') : Str::slug($label, '_');
            if ($key === '') {
                $key = 'spec_'.($idx + 1);
            }
            $fields[] = [
                'key' => $key,
                'label' => $label,
                'placeholder' => trim((string) ($item['placeholder'] ?? '')),
                'default_value' => trim((string) ($item['default_value'] ?? '')),
            ];
        }

        return ! empty($fields) ? $fields : self::getDefaultSpecFields();
    }

    /**
     * Normalize a project's repeatable specs array while keeping legacy 5 fields in sync.
     */
    public static function normalizeProjectSpecs(array $proj, ?array $specFields = null): array
    {
        $specFields = $specFields ?? self::getSpecFields();
        $fieldMap = [];
        foreach ($specFields as $f) {
            $fieldMap[$f['key']] = $f;
        }

        $specs = [];
        $seenKeys = [];

        if (! empty($proj['specs']) && is_array($proj['specs'])) {
            foreach (array_values($proj['specs']) as $idx => $row) {
                if (! is_array($row)) {
                    continue;
                }
                $label = trim((string) ($row['label'] ?? ''));
                $value = trim((string) ($row['value'] ?? ''));
                $rawKey = trim((string) ($row['key'] ?? ''));

                if ($label === '' && $value === '') {
                    continue;
                }

                $key = $rawKey !== '' ? Str::slug($rawKey, '_') : Str::slug($label, '_');
                if ($key === '') {
                    $key = 'spec_'.($idx + 1);
                }

                if ($label === '' && isset($fieldMap[$key])) {
                    $label = $fieldMap[$key]['label'];
                }

                $specs[] = [
                    'key' => $key,
                    'label' => $label !== '' ? $label : ('Spec '.($idx + 1)),
                    'value' => $value,
                ];
                $seenKeys[$key] = true;
            }
        }

        // If project had no explicit specs array yet, or if new global spec fields were configured, ensure global fields are represented
        if (empty($specs)) {
            foreach ($specFields as $f) {
                $k = $f['key'];
                $val = isset($proj[$k]) ? trim((string) $proj[$k]) : '';
                $specs[] = [
                    'key' => $k,
                    'label' => $f['label'],
                    'value' => $val,
                ];
            }
        }

        return $specs;
    }

    /**
     * Persist services list and labels to DB settings + synchronize Homepage widgets.
     */
    protected function persistServicesAndSync(array $servicesList, ?array $labels = null): void
    {
        $labels = $labels ?? self::getLabels();
        $defaults = CoverflowController::getDefaultServices();

        $normalizedServices = [];
        $overviewWidgetServices = [];
        $detailWidgetServices = [];

        foreach (array_values($servicesList) as $idx => $s) {
            if (! is_array($s)) {
                continue;
            }
            $def = $defaults[$idx] ?? $defaults[0];
            $num = ! empty($s['number']) ? trim((string) $s['number']) : sprintf('%02d', $idx + 1);
            $title = ! empty($s['title']) ? trim((string) $s['title']) : ($def['title'] ?? 'Service '.$num);
            $category = ! empty($s['category']) ? trim((string) $s['category']) : $title;
            $slug = ! empty($s['slug']) ? Str::slug((string) $s['slug']) : Str::slug($title);

            [$defLine1, $defLine2] = self::splitTitleIntoLines($category);
            $chipLine1 = isset($s['chip_line_1']) && trim((string) $s['chip_line_1']) !== ''
                ? trim((string) $s['chip_line_1'])
                : $defLine1;
            $chipLine2 = isset($s['chip_line_2']) && trim((string) $s['chip_line_2']) !== ''
                ? trim((string) $s['chip_line_2'])
                : $defLine2;

            $desc = isset($s['desc']) ? trim(strip_tags((string) $s['desc'])) : ($def['desc'] ?? '');
            $image = ! empty($s['image']) ? trim((string) $s['image']) : ($def['image'] ?? 'themes/workspace/coverflow/frame1.png');
            $detailImage = ! empty($s['detail_image']) ? trim((string) $s['detail_image']) : $image;
            $isActive = isset($s['is_active']) ? (bool) $s['is_active'] : true;

            $rawIntros = (! empty($s['intro_items']) && is_array($s['intro_items']))
                ? array_values($s['intro_items'])
                : [];

            $introItems = [];
            foreach ($rawIntros as $intro) {
                if (! is_array($intro)) {
                    continue;
                }
                $iTitle = trim((string) ($intro['title'] ?? ''));
                $iDesc = trim(strip_tags((string) ($intro['desc'] ?? '')));
                if ($iTitle === '' && $iDesc === '') {
                    continue;
                }
                $introItems[] = [
                    'title' => $iTitle !== '' ? $iTitle : $title,
                    'desc' => $iDesc,
                ];
            }
            if (empty($introItems)) {
                $introItems[] = [
                    'title' => $title,
                    'desc' => $desc,
                ];
            }

            $rawProjects = (isset($s['projects']) && is_array($s['projects']))
                ? array_values($s['projects'])
                : [];

            $projects = [];
            $specFields = self::getSpecFields();
            foreach ($rawProjects as $pIdx => $proj) {
                if (! is_array($proj)) {
                    continue;
                }
                $pTitle = trim((string) ($proj['title'] ?? ''));
                if ($pTitle === '' && empty($proj['cover_image']) && empty($proj['area']) && empty($proj['specs'])) {
                    continue;
                }
                $defProj = $def['projects'][$pIdx] ?? ($def['projects'][0] ?? $defaults[0]['projects'][0]);
                $projSpecs = self::normalizeProjectSpecs($proj, $specFields);
                $projects[] = [
                    'title' => $pTitle !== '' ? $pTitle : ('PROJECT '.sprintf('%02d', $pIdx + 1)),
                    'area' => trim((string) ($proj['area'] ?? '')),
                    'location' => trim((string) ($proj['location'] ?? '')),
                    'typology' => trim((string) ($proj['typology'] ?? '')),
                    'style' => trim((string) ($proj['style'] ?? '')),
                    'tech' => trim((string) ($proj['tech'] ?? '')),
                    'specs' => $projSpecs,
                    'cover_image' => ! empty($proj['cover_image']) ? trim((string) $proj['cover_image']) : ($defProj['cover_image'] ?? $image),
                    'design_image' => ! empty($proj['design_image']) ? trim((string) $proj['design_image']) : ($defProj['design_image'] ?? 'themes/workspace/workflow/design.png'),
                    'result_image' => ! empty($proj['result_image']) ? trim((string) $proj['result_image']) : ($defProj['result_image'] ?? 'themes/workspace/workflow/build.png'),
                ];
            }

            $serviceRecord = [
                'id' => $idx + 1,
                'code' => 'SVC-'.$num,
                'number' => $num,
                'title' => $title,
                'category' => $category,
                'chip_line_1' => $chipLine1,
                'chip_line_2' => $chipLine2,
                'slug' => $slug,
                'desc' => $desc,
                'image' => $image,
                'detail_image' => $detailImage,
                'is_active' => $isActive,
                'intro_items' => $introItems,
                'projects' => $projects,
            ];

            $normalizedServices[] = $serviceRecord;

            if ($isActive) {
                $overviewWidgetServices[] = [
                    'number' => $num,
                    'title' => $title,
                    'category' => $category,
                    'chip_line_1' => $chipLine1,
                    'chip_line_2' => $chipLine2,
                    'slug' => $slug,
                    'desc' => $desc,
                    'image' => $image,
                ];

                $detailWidgetServices[] = [
                    'title' => $title,
                    'category' => $category,
                    'slug' => $slug,
                    'image' => $detailImage,
                    'intro_items' => $introItems,
                    'projects' => $projects,
                ];
            }
        }

        // Save to settings table
        $servicesJson = json_encode($normalizedServices, JSON_UNESCAPED_UNICODE);
        $labelsJson = json_encode($labels, JSON_UNESCAPED_UNICODE);

        setting([
            'workspace_services_data' => $servicesJson,
            'workspace_services_labels' => $labelsJson,
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'workspace_services_data', 'project_id' => null],
            [
                'payload' => $servicesJson,
                'value' => $servicesJson,
                'group' => 'general',
                'updated_at' => now(),
            ]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'workspace_services_labels', 'project_id' => null],
            [
                'payload' => $labelsJson,
                'value' => $labelsJson,
                'group' => 'general',
                'updated_at' => now(),
            ]
        );

        // Synchronize with Homepage Widgets
        $serviceWidgets = Widget::withoutGlobalScopes()
            ->where('type', 'workspace_service')
            ->get();

        foreach ($serviceWidgets as $sw) {
            $sSettings = is_array($sw->settings) ? $sw->settings : [];
            $sSettings['view_more_text'] = $labels['view_more_text'];
            $sSettings['services'] = $overviewWidgetServices;
            $sw->update(['settings' => $sSettings]);
        }

        $detailWidgets = Widget::withoutGlobalScopes()
            ->where('type', 'workspace_coverflow_service')
            ->get();

        $specFields = self::getSpecFields();
        foreach ($detailWidgets as $dw) {
            $dSettings = is_array($dw->settings) ? $dw->settings : [];
            $dSettings['pill_text'] = $labels['pill_text'];
            $dSettings['tab_intro_text'] = $labels['tab_intro_text'];
            $dSettings['tab_project_text'] = $labels['tab_project_text'];
            $dSettings['back_text'] = $labels['back_text'];
            $dSettings['compare_button_text'] = $labels['compare_button_text'];
            $dSettings['label_area'] = $labels['label_area'];
            $dSettings['label_location'] = $labels['label_location'];
            $dSettings['label_typology'] = $labels['label_typology'];
            $dSettings['label_style'] = $labels['label_style'];
            $dSettings['label_tech'] = $labels['label_tech'];
            $dSettings['spec_fields'] = $specFields;
            $dSettings['services'] = $detailWidgetServices;
            $dw->update(['settings' => $dSettings]);
        }

        if (function_exists('clear_widget_cache')) {
            clear_widget_cache('homepage-main');
            clear_widget_cache('homepage');
        }
        SettingsService::getInstance()->clearCache();
        Cache::flush();
        try {
            Artisan::call('view:clear');
        } catch (\Throwable $e) {
            // Ignore
        }
    }

    /**
     * Public method called by ProjectController to sync Services & Homepage Widgets.
     */
    public function syncServicesFromProjectManager(array $servicesList): void
    {
        $this->persistServicesAndSync($servicesList);
    }

    /**
     * Find a service index in array by id, number, code, or slug.
     */
    protected function findServiceIndex(array $services, string $identifier): ?int
    {
        foreach ($services as $idx => $s) {
            if (
                (string) ($s['id'] ?? ($idx + 1)) === $identifier ||
                (string) ($s['number'] ?? '') === $identifier ||
                strcasecmp((string) ($s['code'] ?? ''), $identifier) === 0 ||
                strcasecmp((string) ($s['slug'] ?? ''), $identifier) === 0
            ) {
                return $idx;
            }
        }

        return null;
    }

    /**
     * 1. LIST ALL SERVICES (Table UI identical to Material Lab index).
     */
    public function index(Request $request): View
    {
        $allServices = self::getAllServices();

        // Sync cleaned list if accidental test item was stripped
        $rawSaved = CoverflowController::getServicesData(null, false);
        if (count($rawSaved) !== count($allServices)) {
            $this->persistServicesAndSync($allServices);
        }

        $categories = self::getManagedCategories();
        $labels = self::getLabels();

        $filtered = collect($allServices);

        if ($request->filled('search')) {
            $q = mb_strtolower(trim((string) $request->input('search')));
            $filtered = $filtered->filter(function (array $item) use ($q) {
                $inTitle = str_contains(mb_strtolower($item['title'] ?? ''), $q);
                $inCat = str_contains(mb_strtolower($item['category'] ?? ''), $q);
                $inCode = str_contains(mb_strtolower($item['code'] ?? ''), $q);
                $inDesc = str_contains(mb_strtolower($item['desc'] ?? ''), $q);
                $inIntros = collect($item['intro_items'] ?? [])->contains(function ($intro) use ($q) {
                    return str_contains(mb_strtolower($intro['title'] ?? ''), $q)
                        || str_contains(mb_strtolower($intro['desc'] ?? ''), $q);
                });

                return $inTitle || $inCat || $inCode || $inDesc || $inIntros;
            });
        }

        if ($request->filled('category')) {
            $catFilter = trim((string) $request->input('category'));
            $filtered = $filtered->filter(fn (array $item) => ($item['category'] ?? $item['title']) === $catFilter);
        }

        if ($request->filled('status')) {
            $statusFilter = $request->input('status');
            if ($statusFilter === 'active') {
                $filtered = $filtered->filter(fn (array $item) => ! empty($item['is_active']));
            } elseif ($statusFilter === 'inactive') {
                $filtered = $filtered->filter(fn (array $item) => empty($item['is_active']));
            }
        }

        $services = $filtered->values()->all();
        $totalCount = count($allServices);

        return view('cms.services.index', compact(
            'services',
            'totalCount',
            'categories',
            'labels'
        ));
    }

    /**
     * 2. SHOW CREATE SERVICE FORM (2-column UI identical to Material Lab create).
     */
    public function create(): View
    {
        $allServices = self::getAllServices();
        $categories = self::getManagedCategories();
        $labels = self::getLabels();
        $nextNumber = sprintf('%02d', count($allServices) + 1);
        $managedProjects = ProjectController::getAllProjects();

        return view('cms.services.create', compact(
            'categories',
            'labels',
            'nextNumber',
            'managedProjects'
        ));
    }

    /**
     * 3. STORE NEW SERVICE.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ], [
            'title.required' => 'Vui lòng nhập Tiêu đề Danh mục / Dịch vụ.',
        ]);

        $allServices = self::getAllServices();
        $newItem = $this->buildServiceItemFromRequest($request, count($allServices));
        $allServices[] = $newItem;

        // Also ensure category exists in managed categories
        $this->ensureCategoryExists($newItem['category'], $newItem['chip_line_1'], $newItem['chip_line_2']);

        $this->persistServicesAndSync($allServices);

        // Sync selected project IDs relationship
        if ($request->has('project_ids_submitted')) {
            $this->syncServiceProjectRelationships($newItem['slug'], (array) $request->input('project_ids', []));
        }

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Đã thêm mới dịch vụ "'.$newItem['title'].'" và đồng bộ ra Trang chủ thành công!');
    }

    /**
     * 4. SHOW EDIT SERVICE FORM (2-column UI identical to Material Lab edit).
     */
    public function edit(string $service): View
    {
        $allServices = self::getAllServices();
        $idx = $this->findServiceIndex($allServices, $service);

        if ($idx === null) {
            abort(404, 'Không tìm thấy dịch vụ này.');
        }

        $serviceItem = $allServices[$idx];
        $categories = self::getManagedCategories();
        $labels = self::getLabels();
        $managedProjects = ProjectController::getAllProjects();

        return view('cms.services.edit', [
            'service' => $serviceItem,
            'categories' => $categories,
            'labels' => $labels,
            'allServices' => $allServices,
            'managedProjects' => $managedProjects,
        ]);
    }

    /**
     * 5. UPDATE EXISTING SERVICE.
     */
    public function update(Request $request, string $service): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ], [
            'title.required' => 'Vui lòng nhập Tiêu đề Danh mục / Dịch vụ.',
        ]);

        $allServices = self::getAllServices();
        $idx = $this->findServiceIndex($allServices, $service);

        if ($idx === null) {
            return redirect()->route('admin.services.index')->with('error', 'Không tìm thấy dịch vụ cần cập nhật.');
        }

        $oldSlug = $allServices[$idx]['slug'] ?? '';
        $updatedItem = $this->buildServiceItemFromRequest($request, $idx, $allServices[$idx]);
        $allServices[$idx] = $updatedItem;

        $this->ensureCategoryExists($updatedItem['category'], $updatedItem['chip_line_1'], $updatedItem['chip_line_2']);

        // If slug changed, keep linked projects in sync with the new slug
        if ($oldSlug !== '' && $oldSlug !== $updatedItem['slug']) {
            $allProjects = ProjectController::getAllProjects();
            $changedAnyProject = false;
            foreach ($allProjects as &$proj) {
                $slugs = $proj['service_slugs'] ?? [];
                if (in_array($oldSlug, $slugs, true)) {
                    $slugs = array_map(fn ($s) => $s === $oldSlug ? $updatedItem['slug'] : $s, $slugs);
                    $proj['service_slugs'] = array_values(array_unique($slugs));
                    $changedAnyProject = true;
                }
            }
            unset($proj);
            if ($changedAnyProject) {
                ProjectController::persistProjectsAndSyncServices($allProjects, false);
            }
        }

        $this->persistServicesAndSync($allServices);

        if ($request->has('project_ids_submitted')) {
            $this->syncServiceProjectRelationships(
                $updatedItem['slug'],
                (array) $request->input('project_ids', []),
                $oldSlug
            );
        }

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Đã cập nhật dịch vụ "'.$updatedItem['title'].'" và đồng bộ ra Trang chủ thành công!');
    }

    /**
     * 6. DELETE SERVICE.
     */
    public function destroy(string $service): RedirectResponse
    {
        $allServices = self::getAllServices();
        $idx = $this->findServiceIndex($allServices, $service);

        if ($idx === null) {
            return redirect()->route('admin.services.index')->with('error', 'Không tìm thấy dịch vụ cần xóa.');
        }

        $removedTitle = $allServices[$idx]['title'] ?? '';
        array_splice($allServices, $idx, 1);

        // Re-number remaining services
        foreach ($allServices as $i => &$item) {
            $item['id'] = $i + 1;
            $item['number'] = sprintf('%02d', $i + 1);
            $item['code'] = 'SVC-'.$item['number'];
        }
        unset($item);

        $this->persistServicesAndSync($allServices);

        return redirect()
            ->route('admin.services.index')
            ->with('success', "Đã xóa dịch vụ \"{$removedTitle}\" thành công!");
    }

    /**
     * 7. TOGGLE ACTIVE STATUS VIA AJAX.
     */
    public function toggleActive(Request $request): JsonResponse
    {
        $id = (string) $request->input('id', '');
        $allServices = self::getAllServices();
        $idx = $this->findServiceIndex($allServices, $id);

        if ($idx === null) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dịch vụ.',
            ], 404);
        }

        $allServices[$idx]['is_active'] = ! ($allServices[$idx]['is_active'] ?? true);
        $this->persistServicesAndSync($allServices);

        return response()->json([
            'success' => true,
            'is_active' => $allServices[$idx]['is_active'],
            'message' => $allServices[$idx]['is_active'] ? 'Đã kích hoạt hiển thị dịch vụ' : 'Đã ẩn dịch vụ trên trang chủ',
        ]);
    }

    /**
     * 8. MANAGE SERVICE CATEGORIES PAGE -> Redirect to Services Index (since Services ARE Project Categories).
     */
    public function categories(): RedirectResponse
    {
        return redirect()->route('admin.services.index');
    }

    /**
     * 9. SAVE SERVICE CATEGORIES (add, rename, delete, reset).
     */
    public function saveCategories(Request $request): RedirectResponse
    {
        $action = $request->input('action', 'add');
        $categories = self::getManagedCategories();
        $allServices = self::getAllServices();

        if ($action === 'reset') {
            $defaultCats = self::getDefaultCategories();
            $this->persistCategoriesSetting($defaultCats);

            foreach ($defaultCats as $cIdx => $dCat) {
                if (isset($allServices[$cIdx])) {
                    $allServices[$cIdx]['category'] = $dCat['name'];
                    $allServices[$cIdx]['chip_line_1'] = $dCat['chip_line_1'];
                    $allServices[$cIdx]['chip_line_2'] = $dCat['chip_line_2'];
                }
            }
            $this->persistServicesAndSync($allServices);

            return redirect()
                ->route('admin.services.categories')
                ->with('success', 'Đã khôi phục 5 danh mục dịch vụ chuẩn và đồng bộ ra Coverflow thành công!');
        }

        if ($action === 'add') {
            $name = trim((string) $request->input('name', ''));
            if ($name === '') {
                return redirect()->route('admin.services.categories')->with('error', 'Vui lòng nhập tên phân loại danh mục dịch vụ.');
            }
            [$defLine1, $defLine2] = self::splitTitleIntoLines($name);
            $line1 = trim((string) $request->input('chip_line_1', '')) ?: $defLine1;
            $line2 = trim((string) $request->input('chip_line_2', '')) ?: $defLine2;
            $image = trim((string) $request->input('image', '')) ?: 'themes/workspace/coverflow/frame1.png';
            $desc = trim((string) $request->input('desc', ''));

            $exists = collect($categories)->contains(fn ($c) => strcasecmp($c['name'], $name) === 0);
            if (! $exists) {
                $categories[] = [
                    'name' => $name,
                    'chip_line_1' => $line1,
                    'chip_line_2' => $line2,
                ];
                $this->persistCategoriesSetting($categories);

                // Also create corresponding Coverflow service entry if not present
                $num = sprintf('%02d', count($allServices) + 1);
                $allServices[] = [
                    'id' => count($allServices) + 1,
                    'code' => 'SVC-'.$num,
                    'number' => $num,
                    'title' => $name,
                    'category' => $name,
                    'chip_line_1' => $line1,
                    'chip_line_2' => $line2,
                    'slug' => Str::slug($name),
                    'desc' => $desc,
                    'image' => $image,
                    'detail_image' => $image,
                    'is_active' => true,
                    'intro_items' => [
                        ['title' => $name, 'desc' => $desc],
                    ],
                    'projects' => [],
                ];
                $this->persistServicesAndSync($allServices);
            }

            return redirect()
                ->route('admin.services.categories')
                ->with('success', "Đã thêm danh mục dịch vụ \"{$name}\" thành công!");
        }

        if ($action === 'rename') {
            $oldName = trim((string) $request->input('old_name', ''));
            $newName = trim((string) $request->input('new_name', ''));
            if ($oldName !== '' && $newName !== '') {
                [$defLine1, $defLine2] = self::splitTitleIntoLines($newName);
                $line1 = trim((string) $request->input('chip_line_1', '')) ?: $defLine1;
                $line2 = trim((string) $request->input('chip_line_2', '')) ?: $defLine2;
                $newDesc = $request->has('desc') ? trim((string) $request->input('desc')) : null;
                $newImage = $request->filled('image') ? trim((string) $request->input('image')) : null;

                foreach ($categories as &$cat) {
                    if ($cat['name'] === $oldName) {
                        $cat['name'] = $newName;
                        $cat['chip_line_1'] = $line1;
                        $cat['chip_line_2'] = $line2;
                    }
                }
                unset($cat);
                $this->persistCategoriesSetting($categories);

                // Sync services belonging to this category
                $updatedAny = false;
                foreach ($allServices as &$s) {
                    if (($s['category'] ?? $s['title']) === $oldName) {
                        $s['category'] = $newName;
                        if ($s['title'] === $oldName) {
                            $s['title'] = $newName;
                            $s['slug'] = Str::slug($newName);
                        }
                        $s['chip_line_1'] = $line1;
                        $s['chip_line_2'] = $line2;
                        if ($newDesc !== null) {
                            $s['desc'] = $newDesc;
                        }
                        if ($newImage !== null) {
                            $s['image'] = $newImage;
                            $s['detail_image'] = $newImage;
                        }
                        $updatedAny = true;
                    }
                }
                unset($s);

                if ($updatedAny) {
                    $this->persistServicesAndSync($allServices);
                }

                return redirect()
                    ->route('admin.services.categories')
                    ->with('success', "Đã cập nhật danh mục \"{$newName}\" và đồng bộ ra Coverflow Trang chủ!");
            }
        }

        if ($action === 'delete') {
            $target = trim((string) $request->input('name', ''));
            $usedCount = collect($allServices)->filter(fn ($s) => ($s['category'] ?? $s['title']) === $target)->count();
            if ($usedCount > 0) {
                return redirect()
                    ->route('admin.services.categories')
                    ->with('error', "Không thể xóa danh mục \"{$target}\" vì đang có {$usedCount} dịch vụ sử dụng. Hãy chuyển dịch vụ sang danh mục khác trước.");
            }

            $categories = array_values(array_filter($categories, fn ($c) => $c['name'] !== $target));
            $this->persistCategoriesSetting($categories);

            return redirect()
                ->route('admin.services.categories')
                ->with('success', "Đã xóa danh mục \"{$target}\" khỏi danh sách!");
        }

        return redirect()->route('admin.services.categories');
    }

    /**
     * 10. DISPLAY CONFIG PAGE (Identical to Material Lab config).
     */
    public function config(): View
    {
        $labels = self::getLabels();
        $specFields = self::getSpecFields();
        $totalServices = count(self::getAllServices());

        return view('cms.services.config', compact('labels', 'specFields', 'totalServices'));
    }

    /**
     * 11. SAVE CONFIG PAGE.
     */
    public function saveConfig(Request $request): RedirectResponse
    {
        // Parse repeatable spec fields from JSON or array input
        $rawSpecsJson = $request->input('spec_fields_json');
        $decodedSpecs = [];
        if (is_string($rawSpecsJson) && trim($rawSpecsJson) !== '') {
            $parsed = json_decode($rawSpecsJson, true);
            if (is_array($parsed)) {
                $decodedSpecs = $parsed;
            }
        } elseif (is_array($request->input('spec_fields'))) {
            $decodedSpecs = $request->input('spec_fields');
        }

        $specFields = [];
        foreach (array_values($decodedSpecs) as $idx => $item) {
            if (! is_array($item)) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $rawKey = trim((string) ($item['key'] ?? ''));
            $key = $rawKey !== '' ? Str::slug($rawKey, '_') : Str::slug($label, '_');
            if ($key === '') {
                $key = 'spec_'.($idx + 1);
            }
            $specFields[] = [
                'key' => $key,
                'label' => $label,
                'placeholder' => trim((string) ($item['placeholder'] ?? '')),
                'default_value' => trim((string) ($item['default_value'] ?? '')),
            ];
        }

        if (empty($specFields)) {
            $specFields = self::getDefaultSpecFields();
        }

        // Map standard 5 keys if present in repeatable specFields
        $specByKey = [];
        foreach ($specFields as $sf) {
            $specByKey[$sf['key']] = $sf['label'];
        }

        $labels = [
            'view_more_text' => trim((string) $request->input('view_more_text', 'View more')) ?: 'View more',
            'pill_text' => trim((string) $request->input('pill_text', 'Services')) ?: 'Services',
            'tab_intro_text' => trim((string) $request->input('tab_intro_text', 'Introductions')) ?: 'Introductions',
            'tab_project_text' => trim((string) $request->input('tab_project_text', 'Projects')) ?: 'Projects',
            'back_text' => trim((string) $request->input('back_text', 'Back to Services')) ?: 'Back to Services',
            'compare_button_text' => trim((string) $request->input('compare_button_text', 'Comparison Design and Result')) ?: 'Comparison Design and Result',
            'label_area' => $specByKey['area'] ?? trim((string) $request->input('label_area', $specFields[0]['label'] ?? 'Total Area')) ?: 'Total Area',
            'label_location' => $specByKey['location'] ?? trim((string) $request->input('label_location', $specFields[1]['label'] ?? 'Location')) ?: 'Location',
            'label_typology' => $specByKey['typology'] ?? trim((string) $request->input('label_typology', $specFields[2]['label'] ?? 'Typology')) ?: 'Typology',
            'label_style' => $specByKey['style'] ?? trim((string) $request->input('label_style', $specFields[3]['label'] ?? 'Style')) ?: 'Style',
            'label_tech' => $specByKey['tech'] ?? trim((string) $request->input('label_tech', $specFields[4]['label'] ?? 'Tech Solution')) ?: 'Tech Solution',
        ];

        $specJson = json_encode($specFields, JSON_UNESCAPED_UNICODE);
        setting(['workspace_project_spec_fields' => $specJson]);
        \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(
            ['key' => 'workspace_project_spec_fields', 'project_id' => null],
            [
                'payload' => $specJson,
                'value' => $specJson,
                'group' => 'general',
                'updated_at' => now(),
            ]
        );
        SettingsService::getInstance()->clearCache();

        // Sync updated spec labels to all existing Projects and Services
        $allProjects = ProjectController::getAllProjects();
        foreach ($allProjects as &$proj) {
            $existingSpecs = is_array($proj['specs'] ?? null) ? $proj['specs'] : [];
            $existingByKey = [];
            foreach ($existingSpecs as $es) {
                if (! empty($es['key'])) {
                    $existingByKey[$es['key']] = $es;
                }
            }

            $mergedSpecs = [];
            $usedKeys = [];
            foreach ($specFields as $sf) {
                $k = $sf['key'];
                $usedKeys[$k] = true;
                $val = $existingByKey[$k]['value'] ?? ($proj[$k] ?? '');
                $mergedSpecs[] = [
                    'key' => $k,
                    'label' => $sf['label'],
                    'value' => trim((string) $val),
                ];
            }
            // Keep any extra custom per-project specs that the admin added on individual projects
            foreach ($existingSpecs as $es) {
                $ek = $es['key'] ?? '';
                if ($ek !== '' && ! isset($usedKeys[$ek])) {
                    $mergedSpecs[] = $es;
                }
            }
            $proj['specs'] = $mergedSpecs;
        }
        unset($proj);

        ProjectController::persistProjectsAndSyncServices($allProjects, true);

        $allServices = self::getAllServices();
        $this->persistServicesAndSync($allServices, $labels);

        return redirect()
            ->route('admin.services.config')
            ->with('success', 'Đã lưu cấu hình hiển thị, danh sách nhãn Thông số Kỹ thuật Dự án và đồng bộ ra Trang chủ thành công!');
    }

    /**
     * 12. RESET SERVICES TO STANDARD 5 DEFAULTS.
     */
    public function resetDefaults(): RedirectResponse
    {
        $defaults = CoverflowController::getDefaultServices();
        $this->persistCategoriesSetting(self::getDefaultCategories());
        $this->persistServicesAndSync($defaults);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Đã khôi phục 5 dịch vụ tiêu chuẩn thành công!');
    }

    /**
     * Legacy bulk save endpoint compatibility.
     */
    public function save(Request $request): RedirectResponse
    {
        return $this->saveConfig($request);
    }

    /**
     * Build a single service item array from form request.
     */
    protected function buildServiceItemFromRequest(Request $request, int $idx, array $existing = []): array
    {
        $defaults = CoverflowController::getDefaultServices();
        $def = $defaults[$idx] ?? $defaults[0];

        $number = trim((string) $request->input('number', $existing['number'] ?? sprintf('%02d', $idx + 1)));
        if ($number === '') {
            $number = sprintf('%02d', $idx + 1);
        }

        $title = trim((string) $request->input('title', $existing['title'] ?? $def['title']));
        $category = trim((string) $request->input('category', '')) ?: $title;
        $slug = trim((string) $request->input('slug', ''));
        $slug = $slug !== '' ? Str::slug($slug) : Str::slug($title);

        [$defLine1, $defLine2] = self::splitTitleIntoLines($category);
        $chipLine1 = trim((string) $request->input('chip_line_1', '')) ?: $defLine1;
        $chipLine2 = trim((string) $request->input('chip_line_2', '')) ?: $defLine2;

        $desc = trim((string) $request->input('desc', $existing['desc'] ?? ''));
        $image = trim((string) $request->input('image', $existing['image'] ?? 'themes/workspace/coverflow/frame1.png'));
        $detailImage = trim((string) $request->input('detail_image', '')) ?: $image;
        $isActive = $request->has('is_active');

        // Parse intro_items (JSON or array)
        $rawIntros = [];
        if ($request->filled('intro_items_json')) {
            $decoded = json_decode((string) $request->input('intro_items_json'), true);
            if (is_array($decoded)) {
                $rawIntros = $decoded;
            }
        } elseif (is_array($request->input('intro_items'))) {
            $rawIntros = $request->input('intro_items');
        } else {
            $rawIntros = $existing['intro_items'] ?? [];
        }

        $introItems = [];
        foreach ($rawIntros as $intro) {
            if (! is_array($intro)) {
                continue;
            }
            $iTitle = trim((string) ($intro['title'] ?? ''));
            $iDesc = trim((string) ($intro['desc'] ?? ''));
            if ($iTitle === '' && $iDesc === '') {
                continue;
            }
            $introItems[] = [
                'title' => $iTitle !== '' ? $iTitle : $title,
                'desc' => $iDesc,
            ];
        }

        // Parse projects (JSON or array)
        $rawProjects = [];
        if ($request->filled('projects_json')) {
            $decoded = json_decode((string) $request->input('projects_json'), true);
            if (is_array($decoded)) {
                $rawProjects = $decoded;
            }
        } elseif (is_array($request->input('projects'))) {
            $rawProjects = $request->input('projects');
        } else {
            $rawProjects = $existing['projects'] ?? [];
        }

        $projects = [];
        foreach ($rawProjects as $pIdx => $proj) {
            if (! is_array($proj)) {
                continue;
            }
            $pTitle = trim((string) ($proj['title'] ?? ''));
            if ($pTitle === '' && empty($proj['cover_image']) && empty($proj['area'])) {
                continue;
            }
            $projects[] = [
                'title' => $pTitle !== '' ? $pTitle : ('PROJECT '.sprintf('%02d', $pIdx + 1)),
                'area' => trim((string) ($proj['area'] ?? '')),
                'location' => trim((string) ($proj['location'] ?? '')),
                'typology' => trim((string) ($proj['typology'] ?? '')),
                'style' => trim((string) ($proj['style'] ?? '')),
                'tech' => trim((string) ($proj['tech'] ?? '')),
                'cover_image' => ! empty($proj['cover_image']) ? trim((string) $proj['cover_image']) : $image,
                'design_image' => ! empty($proj['design_image']) ? trim((string) $proj['design_image']) : 'themes/workspace/workflow/design.png',
                'result_image' => ! empty($proj['result_image']) ? trim((string) $proj['result_image']) : 'themes/workspace/workflow/build.png',
            ];
        }

        return [
            'id' => $idx + 1,
            'code' => 'SVC-'.$number,
            'number' => $number,
            'title' => $title,
            'category' => $category,
            'chip_line_1' => $chipLine1,
            'chip_line_2' => $chipLine2,
            'slug' => $slug,
            'desc' => $desc,
            'image' => $image,
            'detail_image' => $detailImage,
            'is_active' => $isActive,
            'intro_items' => $introItems,
            'projects' => $projects,
        ];
    }

    /**
     * Persist categories array to DB settings.
     */
    protected function persistCategoriesSetting(array $categories): void
    {
        $val = array_values($categories);
        setting(['workspace_service_categories' => json_encode($val, JSON_UNESCAPED_UNICODE)]);
        DB::table('settings')->updateOrInsert(
            ['key' => 'workspace_service_categories', 'project_id' => null],
            [
                'payload' => json_encode($val, JSON_UNESCAPED_UNICODE),
                'value' => json_encode($val, JSON_UNESCAPED_UNICODE),
                'group' => 'general',
                'updated_at' => now(),
            ]
        );
        SettingsService::getInstance()->clearCache();
    }

    /**
     * Synchronize Service <-> Project relationship when saving from Service form.
     */
    protected function syncServiceProjectRelationships(string $serviceSlug, array $selectedProjectIds, string $oldSlug = ''): void
    {
        $selectedIds = array_map('intval', $selectedProjectIds);
        $allProjects = ProjectController::getAllProjects();

        foreach ($allProjects as &$proj) {
            $slugs = $proj['service_slugs'] ?? [];
            if ($oldSlug !== '' && $oldSlug !== $serviceSlug) {
                $slugs = array_values(array_filter($slugs, fn ($s) => $s !== $oldSlug));
            }
            if (in_array((int) $proj['id'], $selectedIds, true)) {
                if (! in_array($serviceSlug, $slugs, true)) {
                    $slugs[] = $serviceSlug;
                }
            } else {
                $slugs = array_values(array_filter($slugs, fn ($s) => $s !== $serviceSlug));
            }
            $proj['service_slugs'] = array_values(array_unique($slugs));
        }
        unset($proj);

        ProjectController::persistProjectsAndSyncServices($allProjects, true);
    }

    /**
     * Ensure a category exists in the managed categories list.
     */
    protected function ensureCategoryExists(string $categoryName, string $line1, string $line2): void
    {
        $categories = self::getManagedCategories();
        $exists = collect($categories)->contains(fn ($c) => strcasecmp($c['name'], $categoryName) === 0);
        if (! $exists && $categoryName !== '') {
            $categories[] = [
                'name' => $categoryName,
                'chip_line_1' => $line1,
                'chip_line_2' => $line2,
            ];
            $this->persistCategoriesSetting($categories);
        }
    }
}
