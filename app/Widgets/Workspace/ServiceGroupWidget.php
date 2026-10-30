<?php

declare(strict_types=1);

namespace App\Widgets\Workspace;

use App\Widgets\BaseWidget;

class ServiceGroupWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Nhóm Dịch Vụ Tổng Hợp (Workspace Service Group)',
            'description' => 'Hiển thị chuỗi màn hình liên tiếp (Workflow -> Dự Án -> Chuyên Gia) cho từng gói dịch vụ',
            'category' => 'Workspace',
            'version' => '1.0.0',
            'author' => 'Workspace',
            'icon' => 'squares-plus',
            'group' => 'workspace_service_group',
            'hidden' => true,
            'fields' => [
                [
                    'name' => 'services',
                    'label' => 'Danh sách chuỗi Dịch vụ',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'title',
                            'label' => 'Tên dịch vụ (VD: DESIGN, BUILD)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'desc',
                            'label' => 'Mô tả dịch vụ',
                            'type' => 'textarea',
                        ],
                        [
                            'name' => 'image',
                            'label' => 'Hình ảnh nền Workflow',
                            'type' => 'image',
                        ],
                        [
                            'name' => 'project_bg_image',
                            'label' => 'Hình ảnh nền Project',
                            'type' => 'image',
                        ],
                        [
                            'name' => 'expert_heading',
                            'label' => 'Tiêu đề khối chuyên gia',
                            'type' => 'text',
                            'default' => "ABOUT THE<br />EXPERT",
                        ],
                        [
                            'name' => 'expert_image',
                            'label' => 'Hình ảnh chân dung chuyên gia',
                            'type' => 'image',
                        ],
                    ],
                ],
            ],
            'settings' => [
                'cacheable' => false,
                'cache_duration' => 0,
            ],
        ];
    }

    public function render(): string
    {
        $settings = $this->settings;
        if (empty($settings['services'])) {
            $settings['services'] = [
                [
                    'title' => 'Design',
                    'desc' => 'We create architectural and interior design concepts that inspire and elevate everyday experiences.',
                    'workflow_kicker' => 'We',
                    'workflow_suffix' => 'Spaces',
                    'image' => asset('themes/workspace/assets/build1.png'),
                    'project_bg_image' => asset('themes/workspace/assets/build1.png'),
                    'expert_heading' => "ABOUT THE<br />EXPERT",
                    'expert_image' => asset('themes/workspace/assets/Man 1.png'),
                    'expert_bullets' => [
                        '15+ Years Experience in Architecture',
                        'Led 100+ Commercial & Residential Projects',
                        'Awarded Top Interior Design 2024',
                    ],
                    'projects' => [
                        [
                            'label' => 'THE HORIZON VILLA',
                            'area' => '850 sqm',
                            'location' => 'Da Nang, Vietnam',
                            'typology' => 'Residential Villa',
                            'style' => 'Modern Minimalist',
                            'tech' => 'Smart Home Automation',
                            'team' => 'Workspace Studio',
                            'image' => asset('themes/workspace/assets/build1.png'),
                        ],
                    ],
                ],
            ];
        }

        return view('themes.workspace.widgets.service_group', [
            'widget' => $this,
            'settings' => $settings,
        ])->render();
    }
}

