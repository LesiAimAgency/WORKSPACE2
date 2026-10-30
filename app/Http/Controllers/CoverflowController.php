<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CoverflowController extends Controller
{
    /**
     * Get default structured services data with full fallback.
     */
    public static function getDefaultServices(): array
    {
        return [
            [
                'number' => '01',
                'title' => 'Interior Advisory and Project Setup',
                'chip_line_1' => 'Interior Advisory',
                'chip_line_2' => 'and Project Setup',
                'slug' => 'interior-advisory-and-project-setup',
                'desc' => 'Providing comprehensive strategic solutions aimed at orienting design concepts, optimizing budget, allocating implementation personnel, and establishing operational standards for interior projects.',
                'image' => 'themes/workspace/coverflow/frame1.png',
                'intro_items' => [
                    [
                        'title' => 'Interior Project Advisory',
                        'desc' => 'Providing comprehensive strategic solutions aimed at orienting design concepts, optimizing budget, allocating implementation personnel, and establishing operational standards for interior projects.',
                    ],
                    [
                        'title' => 'Project Setup & Technical Review',
                        'desc' => 'Strategic preparation phase reviewing design documentation, cost estimation and construction schedule to ensure feasibility and risk control.',
                    ],
                ],
                'projects' => [
                    [
                        'title' => 'PROJECT 01',
                        'area' => '125 m²',
                        'location' => 'District 2, Ho Chi Minh City',
                        'typology' => 'Office',
                        'style' => 'Modern',
                        'tech' => 'Integrated Smart-home system',
                        'cover_image' => 'themes/workspace/coverflow/frame1.png',
                        'design_image' => 'themes/workspace/workflow/design.png',
                        'result_image' => 'themes/workspace/workflow/build.png',
                    ],
                    [
                        'title' => 'PROJECT 02',
                        'area' => '210 m²',
                        'location' => 'District 1, Ho Chi Minh City',
                        'typology' => 'Penthouse',
                        'style' => 'Minimalist Luxury',
                        'tech' => 'Smart Lighting & HVAC Control',
                        'cover_image' => 'themes/workspace/build2.png',
                        'design_image' => 'themes/workspace/workflow/furniture-design.png',
                        'result_image' => 'themes/workspace/build2.png',
                    ],
                    [
                        'title' => 'PROJECT 03',
                        'area' => '350 m²',
                        'location' => 'Thu Duc City, Ho Chi Minh City',
                        'typology' => 'Commercial Retail',
                        'style' => 'Industrial Chic',
                        'tech' => 'Automated Energy Saving & Sound',
                        'cover_image' => 'themes/workspace/build3.png',
                        'design_image' => 'themes/workspace/workflow/design.png',
                        'result_image' => 'themes/workspace/build1.png',
                    ],
                ],
            ],
            [
                'number' => '02',
                'title' => 'Interior Design and FF&E Consultancy',
                'chip_line_1' => 'Interior Design and',
                'chip_line_2' => 'FF&E Consultancy',
                'slug' => 'interior-design-and-ffe-consultancy',
                'desc' => 'Creating bespoke interior design concepts, ergonomic space planning, and curated FF&E selection tailored to brand identity and user well-being.',
                'image' => 'themes/workspace/build2.png',
                'intro_items' => [
                    [
                        'title' => 'Spatial Concept & 3D Visualization',
                        'desc' => 'Translating brand ethos and operational workflows into stunning, photorealistic 3D interior design spaces.',
                    ],
                    [
                        'title' => 'FF&E Specification & Sourcing',
                        'desc' => 'Meticulously curating furniture, fixtures, lighting, and materials that balance aesthetics, durability, and budget.',
                    ],
                ],
                'projects' => [
                    [
                        'title' => 'PROJECT 01',
                        'area' => '280 m²',
                        'location' => 'District 7, Ho Chi Minh City',
                        'typology' => 'Creative Agency Studio',
                        'style' => 'Scandinavian Warmth',
                        'tech' => 'Acoustic Ceiling & Smart Scene Lighting',
                        'cover_image' => 'themes/workspace/build2.png',
                        'design_image' => 'themes/workspace/workflow/design.png',
                        'result_image' => 'themes/workspace/build2.png',
                    ],
                    [
                        'title' => 'PROJECT 02',
                        'area' => '420 m²',
                        'location' => 'Binh Thanh, Ho Chi Minh City',
                        'typology' => 'Tech Headquarters',
                        'style' => 'High-Tech Minimalist',
                        'tech' => 'IoT Occupancy Sensors & Dynamic Shading',
                        'cover_image' => 'themes/workspace/coverflow/frame1.png',
                        'design_image' => 'themes/workspace/workflow/furniture-design.png',
                        'result_image' => 'themes/workspace/workflow/build.png',
                    ],
                ],
            ],
            [
                'number' => '03',
                'title' => 'Fit-Out and Finishing Works',
                'chip_line_1' => 'Fit-Out and',
                'chip_line_2' => 'Finishing Works',
                'slug' => 'fit-out-and-finishing-works',
                'desc' => 'Turnkey fit-out construction, advanced MEP engineering, and rigorous site management delivering projects on schedule with zero tolerance for defects.',
                'image' => 'themes/workspace/build3.png',
                'intro_items' => [
                    [
                        'title' => 'Turnkey Fit-Out Execution',
                        'desc' => 'Executing civil works, MEP, HVAC, drywall, flooring, and ceiling systems with uncompromising precision.',
                    ],
                    [
                        'title' => 'Site Supervision & Quality Assurance',
                        'desc' => 'Strict QA/QC protocols ensuring every millimeter matches the approved construction drawing set.',
                    ],
                ],
                'projects' => [
                    [
                        'title' => 'PROJECT 01',
                        'area' => '650 m²',
                        'location' => 'District 1, Ho Chi Minh City',
                        'typology' => 'Financial Banking Hub',
                        'style' => 'Modern Luxury',
                        'tech' => 'Fire-resistant Glass & BMS Automation',
                        'cover_image' => 'themes/workspace/build3.png',
                        'design_image' => 'themes/workspace/workflow/design.png',
                        'result_image' => 'themes/workspace/build1.png',
                    ],
                ],
            ],
            [
                'number' => '04',
                'title' => 'Joinery Manufacturer and FF&E Installation',
                'chip_line_1' => 'Joinery Manufacturer and',
                'chip_line_2' => 'FF&E Installation',
                'slug' => 'joinery-manufacturer-and-ffe-installation',
                'desc' => 'State-of-the-art joinery manufacturing and seamless on-site installation of custom cabinetry, acoustic panels, and premium loose furniture.',
                'image' => 'themes/workspace/assets/build2.png',
                'intro_items' => [
                    [
                        'title' => 'Custom Joinery & Millwork',
                        'desc' => 'In-house precision factory crafting bespoke wooden cabinets, wall paneling, and reception counters.',
                    ],
                    [
                        'title' => 'White-Glove FF&E Delivery',
                        'desc' => 'Careful packaging, transport, and flawless assembly by certified interior installation specialists.',
                    ],
                ],
                'projects' => [
                    [
                        'title' => 'PROJECT 01',
                        'area' => '500 m²',
                        'location' => 'Thu Duc City, Ho Chi Minh City',
                        'typology' => 'Corporate Villa Headquarters',
                        'style' => 'Contemporary Executive',
                        'tech' => 'Acoustic Wall Paneling & Ergonomic Desking',
                        'cover_image' => 'themes/workspace/assets/build2.png',
                        'design_image' => 'themes/workspace/workflow/furniture-design.png',
                        'result_image' => 'themes/workspace/assets/build2.png',
                    ],
                ],
            ],
            [
                'number' => '05',
                'title' => 'Material Library and Competency Management',
                'chip_line_1' => 'Material Library and',
                'chip_line_2' => 'Competency Management',
                'slug' => 'material-library-and-competency-management',
                'desc' => 'Curated library of over 100+ sustainable, certified construction materials tested for acoustic, fire safety, and aesthetic standards.',
                'image' => 'themes/workspace/assets/maintain-crop.png',
                'intro_items' => [
                    [
                        'title' => 'Material Research & Tactile Sourcing',
                        'desc' => 'Guiding clients through tactile samples, technical data sheets, and lifecycle durability assessments.',
                    ],
                    [
                        'title' => 'Standards & Compliance Consulting',
                        'desc' => 'Ensuring all specified interior surfaces meet fire safety (QCVN 06) and green building certifications (LEED/WELL).',
                    ],
                ],
                'projects' => [
                    [
                        'title' => 'PROJECT 01',
                        'area' => '180 m²',
                        'location' => 'District 3, Ho Chi Minh City',
                        'typology' => 'Material Exhibition Lab',
                        'style' => 'Biophilic Sustainable',
                        'tech' => 'Low-VOC Finishes & IoT Environmental Monitoring',
                        'cover_image' => 'themes/workspace/assets/maintain-crop.png',
                        'design_image' => 'themes/workspace/workflow/design.png',
                        'result_image' => 'themes/workspace/workflow/build.png',
                    ],
                ],
            ],
        ];
    }

    /**
     * Resolve Coverflow overview widget settings from DB or override.
     */
    public static function getCoverflowSettings(?array $overrideSettings = null): array
    {
        $widget = Widget::withoutGlobalScopes()
            ->where('type', 'workspace_service')
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->first();

        $dbSettings = $widget && is_array($widget->settings) ? $widget->settings : [];

        return array_merge([
            'view_more_text' => 'View more',
        ], $dbSettings, is_array($overrideSettings) ? $overrideSettings : []);
    }

    /**
     * Resolve Service Detail & Projects widget settings from DB or override.
     */
    public static function getServiceDetailSettings(?array $overrideSettings = null): array
    {
        $widget = Widget::withoutGlobalScopes()
            ->where('type', 'workspace_coverflow_service')
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->first();

        $dbSettings = $widget && is_array($widget->settings) ? $widget->settings : [];

        return array_merge([
            'pill_text' => 'Services',
            'tab_intro_text' => 'Introductions',
            'tab_project_text' => 'Projects',
            'back_text' => 'Back to Services',
            'compare_button_text' => 'Comparison Design and Result',
            'label_area' => 'Total Area',
            'label_location' => 'Location',
            'label_typology' => 'Typology',
            'label_style' => 'Style',
            'label_tech' => 'Tech Solution',
        ], $dbSettings, is_array($overrideSettings) ? $overrideSettings : []);
    }

    /**
     * Resolve and normalize services from widget settings in DB or fallback.
     */
    public static function getServicesData(?array $overrideSettings = null): array
    {
        $serviceWidget = Widget::withoutGlobalScopes()
            ->where('type', 'workspace_service')
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->first();

        $detailWidget = Widget::withoutGlobalScopes()
            ->where('type', 'workspace_coverflow_service')
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->first();

        $overviewServices = ($serviceWidget && ! empty($serviceWidget->settings['services']) && is_array($serviceWidget->settings['services']))
            ? array_values($serviceWidget->settings['services'])
            : [];

        $detailServices = ($detailWidget && ! empty($detailWidget->settings['services']) && is_array($detailWidget->settings['services']))
            ? array_values($detailWidget->settings['services'])
            : [];

        if (! empty($overrideSettings['services']) && is_array($overrideSettings['services'])) {
            $overrideList = array_values($overrideSettings['services']);
            // Check if override comes from CoverflowServiceWidget (has intro_items or projects) or ServiceWidget
            $firstOverride = $overrideList[0] ?? [];
            if (isset($firstOverride['intro_items']) || isset($firstOverride['projects'])) {
                $detailServices = $overrideList;
            } else {
                $overviewServices = $overrideList;
            }
        }

        $defaults = self::getDefaultServices();

        if (empty($overviewServices) && empty($detailServices)) {
            return $defaults;
        }

        // Index detail services by slug and by position for seamless merging
        $detailBySlug = [];
        foreach ($detailServices as $dIdx => $dService) {
            if (! is_array($dService)) {
                continue;
            }
            $dSlug = ! empty($dService['slug'])
                ? Str::slug((string) $dService['slug'])
                : (! empty($dService['title']) ? Str::slug((string) $dService['title']) : '');
            if ($dSlug !== '') {
                $detailBySlug[$dSlug] = $dService;
            }
        }

        $baseList = ! empty($overviewServices) ? $overviewServices : $detailServices;
        $normalized = [];

        foreach ($baseList as $idx => $s) {
            if (! is_array($s)) {
                continue;
            }
            $def = $defaults[$idx] ?? $defaults[0];
            $num = ! empty($s['number']) ? (string) $s['number'] : sprintf('%02d', $idx + 1);
            $title = ! empty($s['title']) ? (string) $s['title'] : ($def['title'] ?? 'Service '.$num);
            $slug = ! empty($s['slug']) ? Str::slug((string) $s['slug']) : Str::slug($title);

            $detailMatch = $detailBySlug[$slug] ?? ($detailServices[$idx] ?? []);
            if (! is_array($detailMatch)) {
                $detailMatch = [];
            }

            $desc = ! empty($s['desc'])
                ? strip_tags((string) $s['desc'])
                : (! empty($detailMatch['desc']) ? strip_tags((string) $detailMatch['desc']) : ($def['desc'] ?? ''));

            $image = ! empty($s['image'])
                ? (string) $s['image']
                : (! empty($detailMatch['image']) ? (string) $detailMatch['image'] : ($def['image'] ?? 'themes/workspace/coverflow/frame1.png'));

            $detailImage = ! empty($detailMatch['image'])
                ? (string) $detailMatch['image']
                : $image;

            $chipLine1 = ! empty($s['chip_line_1']) ? (string) $s['chip_line_1'] : ($def['chip_line_1'] ?? '');
            $chipLine2 = ! empty($s['chip_line_2']) ? (string) $s['chip_line_2'] : ($def['chip_line_2'] ?? '');

            $rawIntros = ! empty($detailMatch['intro_items']) && is_array($detailMatch['intro_items'])
                ? $detailMatch['intro_items']
                : (! empty($s['intro_items']) && is_array($s['intro_items']) ? $s['intro_items'] : ($def['intro_items'] ?? []));

            $introItems = [];
            foreach ($rawIntros as $intro) {
                if (! is_array($intro)) {
                    continue;
                }
                $introItems[] = [
                    'title' => (string) ($intro['title'] ?? $title),
                    'desc' => strip_tags((string) ($intro['desc'] ?? $desc)),
                ];
            }
            if (empty($introItems)) {
                $introItems = [
                    ['title' => $title, 'desc' => $desc],
                ];
            }

            $rawProjects = ! empty($detailMatch['projects']) && is_array($detailMatch['projects'])
                ? $detailMatch['projects']
                : (! empty($s['projects']) && is_array($s['projects']) ? $s['projects'] : ($def['projects'] ?? $defaults[0]['projects']));

            $projects = [];
            foreach ($rawProjects as $pIdx => $proj) {
                if (! is_array($proj)) {
                    continue;
                }
                $defProj = $def['projects'][$pIdx] ?? ($def['projects'][0] ?? $defaults[0]['projects'][0]);
                $projects[] = [
                    'title' => (string) ($proj['title'] ?? $defProj['title'] ?? 'PROJECT '.sprintf('%02d', $pIdx + 1)),
                    'area' => (string) ($proj['area'] ?? $defProj['area'] ?? ''),
                    'location' => (string) ($proj['location'] ?? $defProj['location'] ?? ''),
                    'typology' => (string) ($proj['typology'] ?? $defProj['typology'] ?? ''),
                    'style' => (string) ($proj['style'] ?? $defProj['style'] ?? ''),
                    'tech' => (string) ($proj['tech'] ?? $defProj['tech'] ?? ''),
                    'cover_image' => ! empty($proj['cover_image']) ? (string) $proj['cover_image'] : ($defProj['cover_image'] ?? $image),
                    'design_image' => ! empty($proj['design_image']) ? (string) $proj['design_image'] : ($defProj['design_image'] ?? $image),
                    'result_image' => ! empty($proj['result_image']) ? (string) $proj['result_image'] : ($defProj['result_image'] ?? $image),
                ];
            }

            $normalized[] = [
                'number' => $num,
                'title' => $title,
                'chip_line_1' => $chipLine1,
                'chip_line_2' => $chipLine2,
                'slug' => $slug,
                'desc' => $desc,
                'image' => $image,
                'detail_image' => $detailImage,
                'intro_items' => $introItems,
                'projects' => $projects,
            ];
        }

        return ! empty($normalized) ? $normalized : $defaults;
    }

    /**
     * View 1: Coverflow Overview -> Chuyển về Homepage section #coverflow
     */
    public function coverflow(Request $request)
    {
        if ($request->ajax() || $request->query('embed')) {
            $services = self::getServicesData();
            $settings = self::getCoverflowSettings();
            $activeSlug = $request->query('service') ?: ($services[0]['slug'] ?? '');
            $activeServiceIndex = 0;
            foreach ($services as $idx => $s) {
                if ($s['slug'] === $activeSlug) {
                    $activeServiceIndex = $idx;
                    break;
                }
            }
            $activeService = $services[$activeServiceIndex] ?? $services[0];

            return view('themes.workspace.widgets.coverflow', compact('services', 'settings', 'activeService', 'activeServiceIndex'));
        }

        $slug = $request->query('service');
        if ($slug) {
            session(['active_service_slug' => $slug]);
        }

        return redirect()->to(url('/#coverflow'));
    }

    /**
     * View 2: Coverflow Service INTRODUCTIONS -> Chuyển về Homepage section #coverflowService
     */
    public function service(Request $request, ?string $slug = null)
    {
        $targetSlug = $slug ?: $request->query('service');
        if ($request->ajax() || $request->query('embed')) {
            $services = self::getServicesData();
            $settings = self::getServiceDetailSettings();
            $activeService = null;
            foreach ($services as $s) {
                if ($s['slug'] === $targetSlug) {
                    $activeService = $s;
                    break;
                }
            }
            $activeService = $activeService ?? ($services[0] ?? []);

            return view('themes.workspace.widgets.coverflow_service', [
                'services' => $services,
                'settings' => $settings,
                'activeService' => $activeService,
                'slug' => $activeService['slug'] ?? '',
            ]);
        }

        if ($targetSlug) {
            session(['active_service_slug' => $targetSlug]);
        }

        return redirect()->to(url('/#coverflowService'));
    }

    /**
     * View 3: Coverflow Service PROJECTS -> Chuyển về Homepage section #coverflowProject
     */
    public function project(Request $request, ?string $slug = null)
    {
        $targetSlug = $slug ?: $request->query('service');
        if ($request->ajax() || $request->query('embed')) {
            $services = self::getServicesData();
            $settings = self::getServiceDetailSettings();
            $activeService = null;
            foreach ($services as $s) {
                if ($s['slug'] === $targetSlug) {
                    $activeService = $s;
                    break;
                }
            }
            $activeService = $activeService ?? ($services[0] ?? []);
            $projects = $activeService['projects'] ?? [];

            return view('themes.workspace.widgets.coverflow_project', [
                'services' => $services,
                'settings' => $settings,
                'activeService' => $activeService,
                'projects' => $projects,
                'slug' => $activeService['slug'] ?? '',
            ]);
        }

        if ($targetSlug) {
            session(['active_service_slug' => $targetSlug]);
        }

        return redirect()->to(url('/#coverflowProject'));
    }
}
