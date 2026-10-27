<?php

declare(strict_types=1);

namespace App\Widgets\Workspace;

use App\Widgets\BaseWidget;

class ProjectWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Dự Án Kiến Trúc (Workspace Projects)',
            'description' => 'Trình chiếu slide dự án công trình chi tiết với thông số kỹ thuật (diện tích, vị trí, giải pháp) và Lightbox xem ảnh',
            'category' => 'Workspace',
            'version' => '1.0.0',
            'author' => 'Workspace',
            'icon' => 'building-office',
            'group' => 'workspace_project',
            'fields' => [
                [
                    'name' => 'section_id',
                    'label' => 'ID định danh Section (VD: design-project, build-project)',
                    'type' => 'text',
                    'default' => 'design-project',
                ],
                [
                    'name' => 'section_class',
                    'label' => 'CSS Class tùy biến (VD: project-design)',
                    'type' => 'text',
                    'default' => 'project-design',
                ],
                [
                    'name' => 'data_ui',
                    'label' => 'Giao diện nền sáng/tối (UI theme)',
                    'type' => 'select',
                    'default' => 'dark',
                    'options' => [
                        'dark' => 'Tối (Dark)',
                        'light' => 'Sáng (Light)',
                    ],
                ],
                [
                    'name' => 'bg_image',
                    'label' => 'Hình nền chính mặc định',
                    'type' => 'image',
                    'default' => 'themes/workspace/assets/build1.png',
                ],
                [
                    'name' => 'projects',
                    'label' => 'Danh sách các Dự án công trình',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'label',
                            'label' => 'Nhãn thứ tự (VD: PROJECT 01)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'name',
                            'label' => 'Tên công trình',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'area',
                            'label' => 'Diện tích (VD: 1,800 m²)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'location',
                            'label' => 'Địa điểm',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'typology',
                            'label' => 'Loại hình (VD: Corporate Office)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'style',
                            'label' => 'Phong cách thiết kế',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'tech',
                            'label' => 'Giải pháp kỹ thuật',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'team',
                            'label' => 'Đội ngũ thực hiện',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'image',
                            'label' => 'Hình ảnh đại diện',
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
        return view('themes.workspace.widgets.project', [
            'widget' => $this,
            'settings' => $this->settings,
        ])->render();
    }
}
