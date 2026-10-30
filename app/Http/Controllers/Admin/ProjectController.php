<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Retrieve all managed projects, initializing from Services if not yet stored separately.
     */
    public static function getAllProjects(): array
    {
        $raw = setting('workspace_projects_data');
        $decoded = null;
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
        } elseif (is_array($raw)) {
            $decoded = $raw;
        }

        if (is_array($decoded) && ! empty($decoded)) {
            return self::normalizeProjectList($decoded);
        }

        // Seed from existing services' projects
        $services = ServiceController::getAllServices();
        $seeded = [];
        foreach ($services as $svc) {
            $svcSlug = $svc['slug'] ?? Str::slug($svc['title'] ?? '');
            foreach (($svc['projects'] ?? []) as $proj) {
                if (! is_array($proj)) {
                    continue;
                }
                $title = trim((string) ($proj['title'] ?? ''));
                if ($title === '') {
                    continue;
                }

                // Check if project with same title & area already exists to merge service_slugs
                $existingIdx = null;
                foreach ($seeded as $k => $ep) {
                    if (
                        strcasecmp($ep['title'], $title) === 0 &&
                        strcasecmp($ep['area'] ?? '', (string) ($proj['area'] ?? '')) === 0
                    ) {
                        $existingIdx = $k;
                        break;
                    }
                }

                if ($existingIdx !== null) {
                    if (! in_array($svcSlug, $seeded[$existingIdx]['service_slugs'], true)) {
                        $seeded[$existingIdx]['service_slugs'][] = $svcSlug;
                    }
                } else {
                    $num = sprintf('%02d', count($seeded) + 1);
                    $seeded[] = [
                        'id' => count($seeded) + 1,
                        'code' => 'PRJ-'.$num,
                        'title' => $title,
                        'slug' => Str::slug($title.'-'.$num),
                        'service_slugs' => [$svcSlug],
                        'area' => trim((string) ($proj['area'] ?? '')),
                        'location' => trim((string) ($proj['location'] ?? '')),
                        'typology' => trim((string) ($proj['typology'] ?? '')),
                        'style' => trim((string) ($proj['style'] ?? '')),
                        'tech' => trim((string) ($proj['tech'] ?? '')),
                        'specs' => $proj['specs'] ?? [],
                        'cover_image' => ! empty($proj['cover_image']) ? trim((string) $proj['cover_image']) : ($svc['image'] ?? 'themes/workspace/coverflow/frame1.png'),
                        'design_image' => ! empty($proj['design_image']) ? trim((string) $proj['design_image']) : 'themes/workspace/workflow/design.png',
                        'result_image' => ! empty($proj['result_image']) ? trim((string) $proj['result_image']) : 'themes/workspace/workflow/build.png',
                        'is_active' => true,
                    ];
                }
            }
        }

        return self::normalizeProjectList($seeded);
    }

    /**
     * Normalize project records with sequential IDs, codes, and repeatable specs.
     */
    public static function normalizeProjectList(array $list): array
    {
        $specFields = ServiceController::getSpecFields();
        $result = [];

        foreach (array_values($list) as $idx => $p) {
            if (! is_array($p)) {
                continue;
            }
            $num = sprintf('%02d', count($result) + 1);
            $title = trim((string) ($p['title'] ?? 'PROJECT '.$num));
            $slug = ! empty($p['slug']) ? Str::slug((string) $p['slug']) : Str::slug($title.'-'.$num);

            $serviceSlugs = [];
            if (! empty($p['service_slugs']) && is_array($p['service_slugs'])) {
                $serviceSlugs = array_values(array_unique(array_filter(array_map('strval', $p['service_slugs']))));
            } elseif (! empty($p['service_slug'])) {
                $serviceSlugs = [(string) $p['service_slug']];
            }

            $specs = ServiceController::normalizeProjectSpecs($p, $specFields);

            // Sync standard 5 keys from specs if present
            $specValueByKey = [];
            foreach ($specs as $sRow) {
                if (! empty($sRow['key'])) {
                    $specValueByKey[$sRow['key']] = (string) ($sRow['value'] ?? '');
                }
            }

            $area = $specValueByKey['area'] ?? trim((string) ($p['area'] ?? ($specs[0]['value'] ?? '')));
            $location = $specValueByKey['location'] ?? trim((string) ($p['location'] ?? ($specs[1]['value'] ?? '')));
            $typology = $specValueByKey['typology'] ?? trim((string) ($p['typology'] ?? ($specs[2]['value'] ?? '')));
            $style = $specValueByKey['style'] ?? trim((string) ($p['style'] ?? ($specs[3]['value'] ?? '')));
            $tech = $specValueByKey['tech'] ?? trim((string) ($p['tech'] ?? ($specs[4]['value'] ?? '')));

            $result[] = [
                'id' => count($result) + 1,
                'code' => ! empty($p['code']) ? trim((string) $p['code']) : ('PRJ-'.$num),
                'title' => $title,
                'slug' => $slug,
                'service_slugs' => $serviceSlugs,
                'area' => $area,
                'location' => $location,
                'typology' => $typology,
                'style' => $style,
                'tech' => $tech,
                'specs' => $specs,
                'cover_image' => ! empty($p['cover_image']) ? trim((string) $p['cover_image']) : 'themes/workspace/coverflow/frame1.png',
                'design_image' => ! empty($p['design_image']) ? trim((string) $p['design_image']) : 'themes/workspace/workflow/design.png',
                'result_image' => ! empty($p['result_image']) ? trim((string) $p['result_image']) : 'themes/workspace/workflow/build.png',
                'is_active' => isset($p['is_active']) ? (bool) $p['is_active'] : true,
            ];
        }

        return $result;
    }

    /**
     * Extract repeatable specs array from Request.
     */
    protected function extractSpecsFromRequest(Request $request): array
    {
        $specFields = ServiceController::getSpecFields();
        $rawJson = $request->input('specs_json');
        $decoded = [];

        if (is_string($rawJson) && trim($rawJson) !== '') {
            $parsed = json_decode($rawJson, true);
            if (is_array($parsed)) {
                $decoded = $parsed;
            }
        } elseif (is_array($request->input('specs'))) {
            $decoded = $request->input('specs');
        }

        $specs = [];
        foreach (array_values($decoded) as $idx => $row) {
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

            $specs[] = [
                'key' => $key,
                'label' => $label !== '' ? $label : ('Thông số '.($idx + 1)),
                'value' => $value,
            ];
        }

        if (empty($specs)) {
            foreach ($specFields as $sf) {
                $k = $sf['key'];
                $specs[] = [
                    'key' => $k,
                    'label' => $sf['label'],
                    'value' => trim((string) $request->input($k, '')),
                ];
            }
        }

        return $specs;
    }

    /**
     * Save projects to DB settings and synchronize bidirectional relationship with Services & Homepage Widgets.
     */
    public static function persistProjectsAndSyncServices(array $projects, bool $syncToServices = true): void
    {
        $normalized = self::normalizeProjectList($projects);
        $json = json_encode($normalized, JSON_UNESCAPED_UNICODE);

        setting(['workspace_projects_data' => $json]);
        DB::table('settings')->updateOrInsert(
            ['key' => 'workspace_projects_data', 'project_id' => null],
            [
                'payload' => $json,
                'value' => $json,
                'group' => 'general',
                'updated_at' => now(),
            ]
        );
        SettingsService::getInstance()->clearCache();

        if ($syncToServices) {
            $services = ServiceController::getAllServices();
            foreach ($services as &$svc) {
                $svcSlug = $svc['slug'] ?? '';
                $matchedProjects = [];
                foreach ($normalized as $proj) {
                    if (empty($proj['is_active'])) {
                        continue;
                    }
                    if (in_array($svcSlug, $proj['service_slugs'] ?? [], true)) {
                        $matchedProjects[] = [
                            'id' => $proj['id'],
                            'code' => $proj['code'],
                            'title' => $proj['title'],
                            'area' => $proj['area'],
                            'location' => $proj['location'],
                            'typology' => $proj['typology'],
                            'style' => $proj['style'],
                            'tech' => $proj['tech'],
                            'specs' => $proj['specs'] ?? [],
                            'cover_image' => $proj['cover_image'],
                            'design_image' => $proj['design_image'],
                            'result_image' => $proj['result_image'],
                        ];
                    }
                }
                $svc['projects'] = $matchedProjects;
            }
            unset($svc);

            app(ServiceController::class)->syncServicesFromProjectManager($services);
        }
    }

    /**
     * Find project index by id, code, or slug.
     */
    protected function findProjectIndex(array $projects, string $identifier): ?int
    {
        foreach ($projects as $idx => $p) {
            if (
                (string) ($p['id'] ?? ($idx + 1)) === $identifier ||
                strcasecmp((string) ($p['code'] ?? ''), $identifier) === 0 ||
                strcasecmp((string) ($p['slug'] ?? ''), $identifier) === 0
            ) {
                return $idx;
            }
        }

        return null;
    }

    /**
     * 1. LIST ALL PROJECTS (Table UI identical to Material Lab).
     */
    public function index(Request $request): View
    {
        $allProjects = self::getAllProjects();
        $services = ServiceController::getAllServices();
        $labels = ServiceController::getLabels();
        $specFields = ServiceController::getSpecFields();

        // Map service slug -> service item for quick display
        $serviceMap = [];
        foreach ($services as $svc) {
            $serviceMap[$svc['slug']] = $svc;
        }

        $filtered = collect($allProjects);

        if ($request->filled('search')) {
            $q = mb_strtolower(trim((string) $request->input('search')));
            $filtered = $filtered->filter(function (array $item) use ($q) {
                if (
                    str_contains(mb_strtolower($item['title'] ?? ''), $q) ||
                    str_contains(mb_strtolower($item['code'] ?? ''), $q) ||
                    str_contains(mb_strtolower($item['location'] ?? ''), $q) ||
                    str_contains(mb_strtolower($item['typology'] ?? ''), $q) ||
                    str_contains(mb_strtolower($item['style'] ?? ''), $q) ||
                    str_contains(mb_strtolower($item['tech'] ?? ''), $q)
                ) {
                    return true;
                }
                foreach (($item['specs'] ?? []) as $sp) {
                    if (
                        str_contains(mb_strtolower($sp['label'] ?? ''), $q) ||
                        str_contains(mb_strtolower($sp['value'] ?? ''), $q)
                    ) {
                        return true;
                    }
                }

                return false;
            });
        }

        if ($request->filled('service')) {
            $svcSlug = trim((string) $request->input('service'));
            $filtered = $filtered->filter(fn (array $item) => in_array($svcSlug, $item['service_slugs'] ?? [], true));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $filtered = $filtered->filter(fn (array $item) => ! empty($item['is_active']));
            } elseif ($status === 'inactive') {
                $filtered = $filtered->filter(fn (array $item) => empty($item['is_active']));
            }
        }

        $projects = $filtered->values()->all();
        $totalCount = count($allProjects);

        return view('cms.projects.index', compact(
            'projects',
            'totalCount',
            'services',
            'serviceMap',
            'labels',
            'specFields'
        ));
    }

    /**
     * 2. SHOW CREATE PROJECT FORM (2-column UI identical to Material Lab create).
     */
    public function create(Request $request): View
    {
        $allProjects = self::getAllProjects();
        $services = ServiceController::getAllServices();
        $labels = ServiceController::getLabels();
        $specFields = ServiceController::getSpecFields();
        $nextCode = 'PRJ-'.sprintf('%02d', count($allProjects) + 1);
        $preselectedService = trim((string) $request->query('service', ''));

        return view('cms.projects.create', compact(
            'services',
            'labels',
            'specFields',
            'nextCode',
            'preselectedService'
        ));
    }

    /**
     * 3. STORE NEW PROJECT.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'service_slugs' => 'required|array|min:1',
        ], [
            'title.required' => 'Vui lòng nhập Tên dự án.',
            'service_slugs.required' => 'Vui lòng chọn ít nhất 1 Dịch vụ liên kết với dự án này.',
        ]);

        $allProjects = self::getAllProjects();
        $num = sprintf('%02d', count($allProjects) + 1);
        $title = trim((string) $request->input('title'));
        $code = trim((string) $request->input('code', '')) ?: ('PRJ-'.$num);
        $specs = $this->extractSpecsFromRequest($request);

        $specByKey = [];
        foreach ($specs as $sp) {
            if (! empty($sp['key'])) {
                $specByKey[$sp['key']] = $sp['value'];
            }
        }

        $newProject = [
            'id' => count($allProjects) + 1,
            'code' => $code,
            'title' => $title,
            'slug' => Str::slug($title.'-'.$num),
            'service_slugs' => array_values(array_filter((array) $request->input('service_slugs', []))),
            'area' => $specByKey['area'] ?? trim((string) $request->input('area', $specs[0]['value'] ?? '')),
            'location' => $specByKey['location'] ?? trim((string) $request->input('location', $specs[1]['value'] ?? '')),
            'typology' => $specByKey['typology'] ?? trim((string) $request->input('typology', $specs[2]['value'] ?? '')),
            'style' => $specByKey['style'] ?? trim((string) $request->input('style', $specs[3]['value'] ?? '')),
            'tech' => $specByKey['tech'] ?? trim((string) $request->input('tech', $specs[4]['value'] ?? '')),
            'specs' => $specs,
            'cover_image' => trim((string) $request->input('cover_image', '')) ?: 'themes/workspace/coverflow/frame1.png',
            'design_image' => trim((string) $request->input('design_image', '')) ?: 'themes/workspace/workflow/design.png',
            'result_image' => trim((string) $request->input('result_image', '')) ?: 'themes/workspace/workflow/build.png',
            'is_active' => $request->has('is_active'),
        ];

        $allProjects[] = $newProject;
        self::persistProjectsAndSyncServices($allProjects, true);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Đã thêm dự án "'.$newProject['title'].'" và đồng bộ với Dịch vụ thành công!');
    }

    /**
     * 4. SHOW EDIT PROJECT FORM (2-column UI identical to Material Lab edit).
     */
    public function edit(string $project): View
    {
        $allProjects = self::getAllProjects();
        $idx = $this->findProjectIndex($allProjects, $project);

        if ($idx === null) {
            abort(404, 'Không tìm thấy dự án này.');
        }

        $projectItem = $allProjects[$idx];
        $services = ServiceController::getAllServices();
        $labels = ServiceController::getLabels();
        $specFields = ServiceController::getSpecFields();

        return view('cms.projects.edit', [
            'project' => $projectItem,
            'services' => $services,
            'labels' => $labels,
            'specFields' => $specFields,
        ]);
    }

    /**
     * 5. UPDATE EXISTING PROJECT.
     */
    public function update(Request $request, string $project): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'service_slugs' => 'required|array|min:1',
        ], [
            'title.required' => 'Vui lòng nhập Tên dự án.',
            'service_slugs.required' => 'Vui lòng chọn ít nhất 1 Dịch vụ liên kết với dự án này.',
        ]);

        $allProjects = self::getAllProjects();
        $idx = $this->findProjectIndex($allProjects, $project);

        if ($idx === null) {
            return redirect()->route('admin.projects.index')->with('error', 'Không tìm thấy dự án cần cập nhật.');
        }

        $existing = $allProjects[$idx];
        $title = trim((string) $request->input('title'));
        $code = trim((string) $request->input('code', $existing['code'] ?? ''));
        $specs = $this->extractSpecsFromRequest($request);

        $specByKey = [];
        foreach ($specs as $sp) {
            if (! empty($sp['key'])) {
                $specByKey[$sp['key']] = $sp['value'];
            }
        }

        $allProjects[$idx] = [
            'id' => $existing['id'],
            'code' => $code,
            'title' => $title,
            'slug' => Str::slug($title.'-'.$existing['id']),
            'service_slugs' => array_values(array_filter((array) $request->input('service_slugs', []))),
            'area' => $specByKey['area'] ?? trim((string) $request->input('area', $specs[0]['value'] ?? '')),
            'location' => $specByKey['location'] ?? trim((string) $request->input('location', $specs[1]['value'] ?? '')),
            'typology' => $specByKey['typology'] ?? trim((string) $request->input('typology', $specs[2]['value'] ?? '')),
            'style' => $specByKey['style'] ?? trim((string) $request->input('style', $specs[3]['value'] ?? '')),
            'tech' => $specByKey['tech'] ?? trim((string) $request->input('tech', $specs[4]['value'] ?? '')),
            'specs' => $specs,
            'cover_image' => trim((string) $request->input('cover_image', '')) ?: ($existing['cover_image'] ?? 'themes/workspace/coverflow/frame1.png'),
            'design_image' => trim((string) $request->input('design_image', '')) ?: ($existing['design_image'] ?? 'themes/workspace/workflow/design.png'),
            'result_image' => trim((string) $request->input('result_image', '')) ?: ($existing['result_image'] ?? 'themes/workspace/workflow/build.png'),
            'is_active' => $request->has('is_active'),
        ];

        self::persistProjectsAndSyncServices($allProjects, true);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Đã cập nhật dự án "'.$title.'" và đồng bộ với Dịch vụ thành công!');
    }

    /**
     * 6. DELETE PROJECT.
     */
    public function destroy(string $project): RedirectResponse
    {
        $allProjects = self::getAllProjects();
        $idx = $this->findProjectIndex($allProjects, $project);

        if ($idx === null) {
            return redirect()->route('admin.projects.index')->with('error', 'Không tìm thấy dự án cần xóa.');
        }

        $removedTitle = $allProjects[$idx]['title'] ?? '';
        array_splice($allProjects, $idx, 1);

        self::persistProjectsAndSyncServices($allProjects, true);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', "Đã xóa dự án \"{$removedTitle}\" thành công!");
    }

    /**
     * 7. TOGGLE PROJECT ACTIVE STATUS VIA AJAX.
     */
    public function toggleActive(Request $request): JsonResponse
    {
        $id = (string) $request->input('id', '');
        $allProjects = self::getAllProjects();
        $idx = $this->findProjectIndex($allProjects, $id);

        if ($idx === null) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dự án.',
            ], 404);
        }

        $allProjects[$idx]['is_active'] = ! ($allProjects[$idx]['is_active'] ?? true);
        self::persistProjectsAndSyncServices($allProjects, true);

        return response()->json([
            'success' => true,
            'is_active' => $allProjects[$idx]['is_active'],
            'message' => $allProjects[$idx]['is_active'] ? 'Đã kích hoạt hiển thị dự án' : 'Đã ẩn dự án',
        ]);
    }
}
