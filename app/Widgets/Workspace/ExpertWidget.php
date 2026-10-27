<?php

declare(strict_types=1);

namespace App\Widgets\Workspace;

use App\Widgets\BaseWidget;

class ExpertWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Chuyên Gia & Đội Ngũ (Workspace Expert)',
            'description' => 'Màn hình giới thiệu chuyên gia chủ trì và các thông tin năng lực của đội ngũ kỹ sư, kiến trúc sư',
            'category' => 'Workspace',
            'version' => '1.0.0',
            'author' => 'Workspace',
            'icon' => 'user-group',
            'group' => 'workspace_expert',
            'fields' => [
                [
                    'name' => 'section_id',
                    'label' => 'ID định danh Section (VD: design-expert, build-expert)',
                    'type' => 'text',
                    'default' => 'design-expert',
                ],
                [
                    'name' => 'section_class',
                    'label' => 'CSS Class tùy biến (VD: expert-design)',
                    'type' => 'text',
                    'default' => 'expert-design',
                ],
                [
                    'name' => 'data_ui',
                    'label' => 'Giao diện nền sáng/tối (UI theme)',
                    'type' => 'select',
                    'default' => 'light',
                    'options' => [
                        'light' => 'Sáng (Light)',
                        'dark' => 'Tối (Dark)',
                    ],
                ],
                [
                    'name' => 'heading',
                    'label' => 'Tiêu đề khối chuyên gia',
                    'type' => 'textarea',
                    'default' => "ABOUT THE<br />EXPERT",
                ],
                [
                    'name' => 'image',
                    'label' => 'Hình ảnh chân dung chuyên gia',
                    'type' => 'image',
                    'default' => 'themes/workspace/assets/Man 1.png',
                ],
                [
                    'name' => 'bullets',
                    'label' => 'Thông tin năng lực / Thành viên',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'title',
                            'label' => 'Họ tên hoặc Chức danh',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'role',
                            'label' => 'Vai trò phụ trách',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'text',
                            'label' => 'Mô tả kinh nghiệm / Thành tựu',
                            'type' => 'textarea',
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
        return view('themes.workspace.widgets.expert', [
            'widget' => $this,
            'settings' => $this->settings,
        ])->render();
    }
}
