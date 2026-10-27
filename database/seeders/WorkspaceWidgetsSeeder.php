<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Widget;
use Illuminate\Database\Seeder;

class WorkspaceWidgetsSeeder extends Seeder
{
    public function run(?int $projectId = null, ?int $tenantId = null): void
    {
        if (! $projectId) {
            $project = Project::where('code', 'workspace')->orWhere('code', 'daiphuc68')->first();
            $projectId = $project?->id ?? 1;
            $tenantId = $project?->tenant_id ?? 6;
        }

        $widgets = [
            [
                'name' => 'Home Banner (Workspace)',
                'type' => 'workspace_home',
                'area' => 'homepage-main',
                'sort_order' => 1,
                'is_active' => true,
                'settings' => [
                    'logo' => 'themes/workspace/assets/logo.png',
                    'alt' => 'Workspace Interior design and build',
                    'aria_label' => 'Workspace',
                ],
            ],
            [
                'name' => 'Giá Trị Cốt Lõi (Workspace Core Values)',
                'type' => 'workspace_core',
                'area' => 'homepage-main',
                'sort_order' => 2,
                'is_active' => true,
                'settings' => [
                    'sub_heading' => 'WORKSPACE',
                    'heading' => 'CORE VALUES',
                    'description' => 'This combination of core values is flexibly applied by us at each stage of the company, in personnel training, dealing with partners & serving customers!',
                    'image' => 'themes/workspace/assets/main1.svg',
                    'nav_items' => [
                        ['label' => 'Core Values', 'link' => '#core-values', 'is_current' => true],
                        ['label' => 'Our Services', 'link' => '#service', 'is_current' => false],
                        ['label' => 'Why Choose Us', 'link' => '#why-workspace', 'is_current' => false],
                    ],
                ],
            ],
            [
                'name' => 'Dịch Vụ Nổi Bật (Workspace Services)',
                'type' => 'workspace_service',
                'area' => 'homepage-main',
                'sort_order' => 3,
                'is_active' => true,
                'settings' => [
                    'heading' => 'OUR SERVICES',
                    'description' => 'Explore our service categories and completed workspace projects.',
                    'nav_items' => [
                        ['label' => 'Core Values', 'link' => '#core-values', 'is_current' => false],
                        ['label' => 'Our Services', 'link' => '#service', 'is_current' => true],
                        ['label' => 'Why Choose Us', 'link' => '#why-workspace', 'is_current' => false],
                    ],
                    'services' => [
                        [
                            'title' => 'DESIGN',
                            'desc' => 'Office, commercial, and residential interior architectural concepts and detailed technical drawings.',
                            'image' => 'themes/workspace/assets/build1.png',
                            'link' => '#about',
                            'data_section' => 'about',
                        ],
                        [
                            'title' => 'BUILD',
                            'desc' => 'Turnkey fit-out, MEP engineering, high-end construction management, and quality control.',
                            'image' => 'themes/workspace/assets/build3.png',
                            'link' => '#build',
                            'data_section' => 'build',
                        ],
                        [
                            'title' => 'FURNITURE DESIGN',
                            'desc' => 'Bespoke loose furniture and custom joinery manufacturing with exquisite craftsmanship.',
                            'image' => 'themes/workspace/assets/build2.png',
                            'link' => '#furniture-design',
                            'data_section' => 'furniture-design',
                        ],
                        [
                            'title' => 'MAINTAIN',
                            'desc' => 'Comprehensive facility maintenance, preventative inspections, and swift warranty support.',
                            'image' => 'themes/workspace/assets/maintain-crop.png',
                            'link' => '#maintain',
                            'data_section' => 'maintain',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Quy Trình Triển Khai (Workspace Workflow)',
                'type' => 'workspace_workflow',
                'area' => 'homepage-main',
                'sort_order' => 4,
                'is_active' => true,
                'settings' => [
                    'section_id' => 'about',
                    'section_class' => 'workflow-design',
                    'data_ui' => 'dark',
                    'kicker' => 'We',
                    'script' => 'Design',
                    'suffix' => 'Spaces',
                    'desc' => 'Transforming empty spaces into high-performance, human-centric working environments that stimulate creativity and productivity.',
                    'image' => 'themes/workspace/assets/build1.png',
                    'nav_items' => [
                        ['label' => 'What we do', 'is_current' => true, 'data_workflow_slide' => '0'],
                        ['label' => 'Projects', 'is_current' => false, 'data_workflow_slide' => '1'],
                        ['label' => 'About the expert', 'is_current' => false, 'data_workflow_slide' => '2'],
                    ],
                ],
            ],
            [
                'name' => 'Dự Án Kiến Trúc (Workspace Projects)',
                'type' => 'workspace_project',
                'area' => 'homepage-main',
                'sort_order' => 5,
                'is_active' => true,
                'settings' => [
                    'section_id' => 'design-project',
                    'section_class' => 'project-design',
                    'data_ui' => 'dark',
                    'bg_image' => 'themes/workspace/assets/build1.png',
                    'nav_items' => [
                        ['label' => 'What we do', 'is_current' => false],
                        ['label' => 'Projects', 'is_current' => true],
                        ['label' => 'About the expert', 'is_current' => false],
                    ],
                    'projects' => [
                        [
                            'label' => 'PROJECT 01',
                            'name' => 'Tech Hub Headquarters',
                            'area' => '1,800 m²',
                            'location' => 'Keangnam Landmark, Hanoi',
                            'typology' => 'Modern Corporate Office',
                            'style' => 'Industrial Minimalism',
                            'tech' => 'Acoustic baffle ceilings, IoT lighting control, modular workstations',
                            'team' => 'Lead Architect Nguyen & Technical Specialists',
                            'image' => 'themes/workspace/assets/build1.png',
                            'gallery' => [
                                'themes/workspace/assets/build1.png',
                                'themes/workspace/assets/build3.png',
                                'themes/workspace/assets/build2.png',
                            ],
                        ],
                        [
                            'label' => 'PROJECT 02',
                            'name' => 'Fintech Innovation Center',
                            'area' => '2,400 m²',
                            'location' => 'Lotte Center, Ba Dinh, Hanoi',
                            'typology' => 'Creative Co-working & Studio',
                            'style' => 'Biophilic Scandinavian',
                            'tech' => 'Green wall air-filtering, soundproof phone booths, ergonomic sit-stand desks',
                            'team' => 'Senior Designer Tran & Interior Team',
                            'image' => 'themes/workspace/assets/build3.png',
                            'gallery' => [
                                'themes/workspace/assets/build3.png',
                                'themes/workspace/assets/build2.png',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Chuyên Gia & Đội Ngũ (Workspace Expert)',
                'type' => 'workspace_expert',
                'area' => 'homepage-main',
                'sort_order' => 6,
                'is_active' => true,
                'settings' => [
                    'section_id' => 'design-expert',
                    'section_class' => 'expert-design',
                    'data_ui' => 'light',
                    'heading' => "ABOUT THE<br />EXPERT",
                    'image' => 'themes/workspace/assets/Man 1.png',
                    'nav_items' => [
                        ['label' => 'What we do', 'is_current' => false],
                        ['label' => 'Projects', 'is_current' => false],
                        ['label' => 'About the expert', 'is_current' => true],
                    ],
                    'bullets' => [
                        ['title' => 'Chief Architect', 'text' => 'Over 15 years directing enterprise workplace transformation projects across Southeast Asia.'],
                        ['title' => 'Technical Director', 'text' => 'Pioneer in BIM coordination, LEED certified green building compliance, and smart office technology.'],
                        ['title' => 'Master Craftsman', 'text' => 'Specialized in bespoke joinery manufacturing and high-end executive furniture fit-out.'],
                    ],
                ],
            ],
            [
                'name' => 'Lý Do Chọn Chúng Tôi (Workspace Why Us & Connect)',
                'type' => 'workspace_why',
                'area' => 'homepage-main',
                'sort_order' => 7,
                'is_active' => true,
                'settings' => [
                    'heading1' => 'WHY CHOOSING',
                    'heading2' => 'WORKSPACE',
                    'nav_items' => [
                        ['label' => 'Core Values', 'link' => '#core-values', 'is_current' => false],
                        ['label' => 'Our Services', 'link' => '#service', 'is_current' => false],
                        ['label' => 'Why Choose Us', 'link' => '#why-workspace', 'is_current' => true],
                    ],
                    'reasons' => [
                        [
                            'prefix' => 'Because of the',
                            'title' => 'QUALITY',
                            'bullet_1' => 'Design and engineering team directly involved in production & construction.',
                            'bullet_2' => 'Skilled workers are trained from the WorkSpace center.',
                            'bullet_3' => 'Key personnel directly involved in the quality control process.',
                        ],
                        [
                            'prefix' => 'Because of the',
                            'title' => 'PROGRESS',
                            'bullet_1' => 'Workers have trained to multitask from the centers of WS.',
                            'bullet_2' => 'Cooperation in accomplishing goals with strategic partners is the core value.',
                            'bullet_3' => 'Flexible culture is highly valued by the WorkSpace management team.',
                        ],
                        [
                            'prefix' => 'Because of the',
                            'title' => 'COST EFFECTIVE',
                            'bullet_1' => 'The production and construction process minimizes intermediate costs.',
                            'bullet_2' => 'Key material and performance are managed for stronger efficiency.',
                            'bullet_3' => 'WorkSpace brings practical solutions before the production and construction stage.',
                        ],
                        [
                            'prefix' => 'Because of the',
                            'title' => 'PROBLEM-SOLVING',
                            'bullet_1' => 'Answer to handling design objectives for customers within agreed budgets.',
                            'bullet_2' => 'The system keeps technical and site control in one working flow.',
                            'bullet_3' => 'Real solutions focus on fast delivery improvement.',
                        ],
                    ],
                    'connect_heading1' => 'CONNECT',
                    'connect_heading2' => 'WORKSPACE',
                    'button_text' => 'CONNECT WITH US',
                    'partners_heading' => 'OUR CUSTOMERS - PARTNERS',
                    'partners' => [
                        ['image' => 'themes/workspace/assets/parnert1.png', 'alt' => 'Strategic Partner 1'],
                        ['image' => 'themes/workspace/assets/parnert2.png', 'alt' => 'Strategic Partner 2'],
                        ['image' => 'themes/workspace/assets/parnert1.png', 'alt' => 'Strategic Partner 3'],
                        ['image' => 'themes/workspace/assets/parnert2.png', 'alt' => 'Strategic Partner 4'],
                        ['image' => 'themes/workspace/assets/parnert1.png', 'alt' => 'Strategic Partner 5'],
                    ],
                ],
            ],
            [
                'name' => 'Form Kết Nối & Đối Tác (Workspace Connect)',
                'type' => 'workspace_connect',
                'area' => 'homepage-main',
                'sort_order' => 8,
                'is_active' => true,
                'settings' => [
                    'heading1' => 'CONNECT',
                    'heading2' => 'WORKSPACE',
                    'label_name' => 'Your Name',
                    'label_phone' => 'Phone Number (*)',
                    'label_email' => 'Email',
                    'label_note' => 'Note for us',
                    'button_text' => 'CONNECT WITH US',
                    'partners_heading' => 'OUR CUSTOMERS - PARTNERS',
                    'partners' => [
                        ['image' => 'themes/workspace/assets/parnert1.png', 'alt' => 'Strategic Partner 1'],
                        ['image' => 'themes/workspace/assets/parnert2.png', 'alt' => 'Strategic Partner 2'],
                        ['image' => 'themes/workspace/assets/parnert1.png', 'alt' => 'Strategic Partner 3'],
                        ['image' => 'themes/workspace/assets/parnert2.png', 'alt' => 'Strategic Partner 4'],
                        ['image' => 'themes/workspace/assets/parnert1.png', 'alt' => 'Strategic Partner 5'],
                    ],
                ],
            ],
        ];

        foreach ($widgets as $item) {
            Widget::withoutGlobalScope('tenant')->updateOrCreate(
                [
                    'project_id' => $projectId,
                    'area' => $item['area'],
                    'type' => $item['type'],
                ],
                [
                    'name' => $item['name'],
                    'sort_order' => $item['sort_order'],
                    'is_active' => $item['is_active'],
                    'settings' => $item['settings'],
                    'tenant_id' => $tenantId,
                    'variant' => 'default',
                ]
            );
        }
    }
}
