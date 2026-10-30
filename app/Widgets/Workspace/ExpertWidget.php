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
            'description' => 'Màn hình giới thiệu chuyên gia chủ trì (About The Expert) kèm biểu đồ thanh kỹ năng chuyên môn và nút điều hướng',
            'category' => 'Workspace',
            'version' => '2.0.0',
            'author' => 'Workspace',
            'icon' => 'user-group',
            'group' => 'workspace_expert',
            'sort_order' => 5,
            'fields' => [
                [
                    'name' => 'heading',
                    'label' => 'Tiêu đề khối chuyên gia (Cho phép thẻ <br />)',
                    'type' => 'textarea',
                    'editor' => false,
                    'rows' => 2,
                    'default' => 'ABOUT THE<br />EXPERT',
                ],
                [
                    'name' => 'desc',
                    'label' => 'Đoạn văn mô tả chuyên gia',
                    'type' => 'textarea',
                    'editor' => false,
                    'rows' => 3,
                    'default' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
                ],
                [
                    'name' => 'expert_name',
                    'label' => 'Họ tên & Chức danh Chuyên gia',
                    'type' => 'text',
                    'default' => 'Kiến trúc sư Võ Đức Minh',
                ],
                [
                    'name' => 'image',
                    'label' => 'Hình ảnh chân dung chuyên gia',
                    'type' => 'image',
                    'default' => 'themes/workspace/assets/Man 1.png',
                ],
                [
                    'name' => 'skills',
                    'label' => 'Danh sách Kỹ năng / Năng lực chuyên môn',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'name',
                            'label' => 'Tên lĩnh vực / Kỹ năng (VD: Thiết kế văn phòng)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'pct',
                            'label' => 'Tỷ lệ phần trăm thanh năng lực (0 - 100)',
                            'type' => 'text',
                            'default' => '80',
                        ],
                    ],
                ],
                [
                    'name' => 'contact_button_text',
                    'label' => 'Nhãn nút Liên hệ',
                    'type' => 'text',
                    'default' => 'Liên hệ',
                ],
                [
                    'name' => 'contact_link',
                    'label' => 'Đường dẫn nút Liên hệ',
                    'type' => 'text',
                    'default' => '#connect',
                ],
                [
                    'name' => 'projects_button_text',
                    'label' => 'Nhãn nút Xem dự án',
                    'type' => 'text',
                    'default' => 'Xem dự án',
                ],
                [
                    'name' => 'projects_link',
                    'label' => 'Đường dẫn nút Xem dự án',
                    'type' => 'text',
                    'default' => '#design-project',
                ],
                [
                    'name' => 'section_id',
                    'label' => 'ID định danh Section',
                    'type' => 'text',
                    'default' => 'design-expert',
                ],
                [
                    'name' => 'section_class',
                    'label' => 'CSS Class tùy biến',
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
