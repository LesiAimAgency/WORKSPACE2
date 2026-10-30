<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Http\Controllers\CoverflowController;
use App\Models\Project;
use App\Models\Widget;
use Illuminate\Database\Seeder;

class WorkspaceWidgetsSeeder extends Seeder
{
    public function run(?int $overrideProjectId = null, ?int $overrideTenantId = null): void
    {
        $project = Project::where('code', 'daiphuc68')->first() ?? Project::first();
        $projectId = $overrideProjectId ?? $project?->id;
        $tenantId = $overrideTenantId ?? $project?->tenant_id;

        // Xóa toàn bộ các widget cũ / dư thừa ở homepage-main và nhóm workspace_* để đồng bộ mới hoàn toàn
        Widget::withoutGlobalScopes()
            ->where(function ($query) use ($projectId) {
                $query->where('area', 'homepage-main')
                    ->orWhere('type', 'like', 'workspace_%');
                if ($projectId) {
                    $query->orWhere('project_id', $projectId);
                }
            })
            ->delete();

        $defaultServices = CoverflowController::getDefaultServices();

        $coverflowOverviewServices = array_map(static fn (array $s): array => [
            'number' => $s['number'],
            'title' => $s['title'],
            'chip_line_1' => $s['chip_line_1'] ?? '',
            'chip_line_2' => $s['chip_line_2'] ?? '',
            'slug' => $s['slug'],
            'desc' => $s['desc'],
            'image' => $s['image'],
        ], $defaultServices);

        $coverflowDetailServices = array_map(static fn (array $s): array => [
            'title' => $s['title'],
            'slug' => $s['slug'],
            'image' => $s['image'],
            'intro_items' => $s['intro_items'] ?? [],
            'projects' => $s['projects'] ?? [],
        ], $defaultServices);

        $widgets = [
            // 1. Home Banner (#home)
            [
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
                'name' => '1. Home Banner (Logo Workspace)',
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

            // 2. Giá trị cốt lõi (#core-values)
            [
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
                'name' => '2. Giá Trị Cốt Lõi (Core Values)',
                'type' => 'workspace_core',
                'area' => 'homepage-main',
                'sort_order' => 2,
                'is_active' => true,
                'settings' => [
                    'sub_heading' => 'WORKSPACE',
                    'heading' => 'CORE VALUES',
                    'description' => 'This combination of core values is flexibly applied by us at each stage of the company, in personnel training, dealing with partners & serving customers!',
                    'image' => 'themes/workspace/assets/main1.svg',
                ],
            ],

            // 3. Bộ sưu tập Dịch vụ Coverflow (#coverflow)
            [
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
                'name' => '3. Bộ Sưu Tập Dịch Vụ Coverflow (Our Services)',
                'type' => 'workspace_service',
                'area' => 'homepage-main',
                'sort_order' => 3,
                'is_active' => true,
                'settings' => [
                    'view_more_text' => 'View more',
                    'services' => $coverflowOverviewServices,
                ],
            ],

            // 4. Chi tiết Dịch vụ & Dự án (#coverflowService)
            [
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
                'name' => '4. Chi Tiết Dịch Vụ & Dự Án (Service Introductions & Projects)',
                'type' => 'workspace_coverflow_service',
                'area' => 'homepage-main',
                'sort_order' => 4,
                'is_active' => true,
                'settings' => [
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
                    'services' => $coverflowDetailServices,
                ],
            ],

            // 5. Chuyên gia & Đội ngũ (#design-expert)
            [
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
                'name' => '5. Chuyên Gia & Đội Ngũ (About The Expert)',
                'type' => 'workspace_expert',
                'area' => 'homepage-main',
                'sort_order' => 5,
                'is_active' => true,
                'settings' => [
                    'section_id' => 'design-expert',
                    'section_class' => 'expert-design',
                    'data_ui' => 'light',
                    'heading' => 'ABOUT THE<br />EXPERT',
                    'desc' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
                    'expert_name' => 'Kiến trúc sư Võ Đức Minh',
                    'image' => 'themes/workspace/assets/Man 1.png',
                    'skills' => [
                        ['name' => 'Thiết kế văn phòng', 'pct' => '77'],
                        ['name' => 'Thiết kế F&B', 'pct' => '56'],
                        ['name' => 'Thiết kế nhà ở', 'pct' => '88'],
                    ],
                    'contact_button_text' => 'Liên hệ',
                    'contact_link' => '#connect',
                    'projects_button_text' => 'Xem dự án',
                    'projects_link' => '#design-project',
                ],
            ],

            // 6. Lý do chọn chúng tôi (#why-workspace)
            [
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
                'name' => '6. Lý Do Chọn Chúng Tôi (Why Choosing Workspace)',
                'type' => 'workspace_why',
                'area' => 'homepage-main',
                'sort_order' => 6,
                'is_active' => true,
                'settings' => [
                    'heading1' => 'WHY CHOOSING',
                    'heading2' => 'WORKSPACE',
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
                ],
            ],

            // 7. Form kết nối & Đối tác (#connect)
            [
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
                'name' => '7. Kết Nối & Đối Tác (Connect Workspace)',
                'type' => 'workspace_connect',
                'area' => 'homepage-main',
                'sort_order' => 7,
                'is_active' => true,
                'settings' => [
                    'heading1' => 'CONNECT',
                    'heading2' => 'WORKSPACE',
                    'label_name' => 'Your Name',
                    'label_phone' => 'Phone Number (*)',
                    'label_email' => 'Email',
                    'label_note' => 'Note for us',
                    'button_text' => 'CONNECT WITH US',
                ],
            ],
        ];

        foreach ($widgets as $widgetData) {
            Widget::withoutGlobalScopes()->create($widgetData);
        }

        if ($this->command) {
            $this->command->info('✅ Đã seed thành công ' . count($widgets) . ' Widgets chuẩn giao diện Workspace Homepage mới nhất!');
        }
    }
}
