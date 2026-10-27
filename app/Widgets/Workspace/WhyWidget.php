<?php

declare(strict_types=1);

namespace App\Widgets\Workspace;

use App\Widgets\BaseWidget;

class WhyWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Lý Do Chọn Chúng Tôi (Workspace Why Us & Connect)',
            'description' => 'Section lý do chọn Workspace với slider 4 tiêu chuẩn (Quality, Progress, Cost, Solving) kết hợp form đăng ký tư vấn và logo đối tác',
            'category' => 'Workspace',
            'version' => '1.0.0',
            'author' => 'Workspace',
            'icon' => 'star',
            'group' => 'workspace_why',
            'fields' => [
                [
                    'name' => 'heading1',
                    'label' => 'Tiêu đề dòng 1',
                    'type' => 'text',
                    'default' => 'WHY CHOOSING',
                ],
                [
                    'name' => 'heading2',
                    'label' => 'Tiêu đề dòng 2',
                    'type' => 'text',
                    'default' => 'WORKSPACE',
                ],
                [
                    'name' => 'reasons',
                    'label' => 'Danh sách các tiêu chuẩn cam kết (Slides)',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'prefix',
                            'label' => 'Từ mào đầu (VD: Because of the)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'title',
                            'label' => 'Tiêu đề nổi bật (VD: QUALITY, PROGRESS)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'bullet_1',
                            'label' => 'Ý cam kết 1',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'bullet_2',
                            'label' => 'Ý cam kết 2',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'bullet_3',
                            'label' => 'Ý cam kết 3',
                            'type' => 'text',
                        ],
                    ],
                ],
                [
                    'name' => 'connect_heading1',
                    'label' => 'Tiêu đề kết nối dòng 1',
                    'type' => 'text',
                    'default' => 'CONNECT',
                ],
                [
                    'name' => 'connect_heading2',
                    'label' => 'Tiêu đề kết nối dòng 2',
                    'type' => 'text',
                    'default' => 'WORKSPACE',
                ],
                [
                    'name' => 'button_text',
                    'label' => 'Chữ nút gửi liên hệ',
                    'type' => 'text',
                    'default' => 'CONNECT WITH US',
                ],
                [
                    'name' => 'partners_heading',
                    'label' => 'Tiêu đề khối đối tác',
                    'type' => 'text',
                    'default' => 'OUR CUSTOMERS - PARTNERS',
                ],
                [
                    'name' => 'partners',
                    'label' => 'Danh sách Logo đối tác / Khách hàng',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'image',
                            'label' => 'Hình ảnh Logo',
                            'type' => 'image',
                        ],
                        [
                            'name' => 'alt',
                            'label' => 'Tên đối tác',
                            'type' => 'text',
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
        return view('themes.workspace.widgets.why', [
            'widget' => $this,
            'settings' => $this->settings,
        ])->render();
    }
}
