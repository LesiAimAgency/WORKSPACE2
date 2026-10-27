<?php

declare(strict_types=1);

namespace App\Widgets\Workspace;

use App\Widgets\BaseWidget;

class ServiceWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Dịch Vụ Nổi Bật (Workspace Services)',
            'description' => 'Bộ sưu tập các dịch vụ kiến trúc & thi công với thanh điều hướng slider ngang',
            'category' => 'Workspace',
            'version' => '1.0.0',
            'author' => 'Workspace',
            'icon' => 'briefcase',
            'group' => 'workspace_service',
            'fields' => [
                [
                    'name' => 'heading',
                    'label' => 'Tiêu đề chính',
                    'type' => 'text',
                    'default' => 'OUR SERVICES',
                ],
                [
                    'name' => 'description',
                    'label' => 'Mô tả dịch vụ',
                    'type' => 'textarea',
                    'default' => 'Explore our service categories and completed workspace projects.',
                ],
                [
                    'name' => 'services',
                    'label' => 'Danh sách các Dịch vụ',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'title',
                            'label' => 'Tên dịch vụ (VD: DESIGN, BUILD)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'desc',
                            'label' => 'Mô tả ngắn',
                            'type' => 'textarea',
                        ],
                        [
                            'name' => 'image',
                            'label' => 'Hình ảnh dịch vụ',
                            'type' => 'image',
                        ],
                        [
                            'name' => 'link',
                            'label' => 'Đường dẫn liên kết (VD: #about, #build)',
                            'type' => 'text',
                            'default' => '#',
                        ],
                        [
                            'name' => 'data_section',
                            'label' => 'Mã Section liên kết (VD: about, build)',
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
        return view('themes.workspace.widgets.service', [
            'widget' => $this,
            'settings' => $this->settings,
        ])->render();
    }
}
