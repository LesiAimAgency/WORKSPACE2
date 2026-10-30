<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

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
     * Resolve and normalize services from widget settings or fallback.
     */
    public static function getServicesData(): array
    {
        $widget = Widget::where('id', 18)->orWhere('type', 'workspace_service')->first();
        $settings = $widget ? ($widget->settings ?? []) : [];
        $rawServices = $settings['services'] ?? [];

        $defaults = self::getDefaultServices();

        if (empty($rawServices) || ! is_array($rawServices)) {
            return $defaults;
        }

        $normalized = [];
        foreach ($rawServices as $idx => $s) {
            $num = $s['number'] ?? sprintf('%02d', $idx + 1);
            $title = $s['title'] ?? ($defaults[$idx]['title'] ?? 'Service ' . $num);
            $slug = ! empty($s['slug']) ? Str::slug($s['slug']) : Str::slug($title);
            $desc = $s['desc'] ?? ($defaults[$idx]['desc'] ?? '');
            $image = ! empty($s['image']) ? $s['image'] : ($defaults[$idx]['image'] ?? 'themes/workspace/coverflow/frame1.png');

            $introItems = ! empty($s['intro_items']) && is_array($s['intro_items'])
                ? $s['intro_items']
                : ($defaults[$idx]['intro_items'] ?? [
                    ['title' => $title, 'desc' => $desc],
                ]);

            $projects = ! empty($s['projects']) && is_array($s['projects'])
                ? $s['projects']
                : ($defaults[$idx]['projects'] ?? $defaults[0]['projects']);

            $normalized[] = [
                'number' => $num,
                'title' => $title,
                'slug' => $slug,
                'desc' => $desc,
                'image' => $image,
                'intro_items' => $introItems,
                'projects' => $projects,
            ];
        }

        return $normalized;
    }

    /**
     * View 1: Coverflow Overview -> Chuyển về Homepage section #coverflow
     */
    public function coverflow(Request $request)
    {
        if ($request->ajax() || $request->query('embed')) {
            $services = self::getServicesData();
            $activeSlug = $request->query('service') ?: ($services[0]['slug'] ?? '');
            $activeServiceIndex = 0;
            foreach ($services as $idx => $s) {
                if ($s['slug'] === $activeSlug) {
                    $activeServiceIndex = $idx;
                    break;
                }
            }
            $activeService = $services[$activeServiceIndex] ?? $services[0];
            return view('themes.workspace.widgets.coverflow', compact('services', 'activeService', 'activeServiceIndex'));
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
