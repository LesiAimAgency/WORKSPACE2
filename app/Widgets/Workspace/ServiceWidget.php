<?php

declare(strict_types=1);

namespace App\Widgets\Workspace;

use App\Http\Controllers\CoverflowController;
use App\Widgets\BaseWidget;

class ServiceWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Bộ Sưu Tập Dịch Vụ Coverflow (Workspace Services)',
            'description' => 'Trình chiếu Coverflow 5 dịch vụ kiến trúc & thi công nội thất với thanh chọn chip dịch vụ tương tác',
            'category' => 'Workspace',
            'version' => '2.0.0',
            'author' => 'Workspace',
            'icon' => 'briefcase',
            'group' => 'workspace_service',
            'sort_order' => 3,
            'fields' => [
                [
                    'name' => 'view_more_text',
                    'label' => 'Nhãn nút Xem chi tiết (CTA Button)',
                    'type' => 'text',
                    'default' => 'View more',
                ],
                [
                    'name' => 'services',
                    'label' => 'Danh sách Dịch vụ Coverflow (5 dịch vụ)',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'number',
                            'label' => 'Số thứ tự (VD: 01, 02...)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'title',
                            'label' => 'Tên dịch vụ chính',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'chip_line_1',
                            'label' => 'Nhãn nút chuyển dòng 1 (Chip Line 1)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'chip_line_2',
                            'label' => 'Nhãn nút chuyển dòng 2 (Chip Line 2)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'slug',
                            'label' => 'Định danh Slug (Dùng để liên kết chi tiết dịch vụ)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'desc',
                            'label' => 'Mô tả ngắn hiển thị trên thẻ Coverflow',
                            'type' => 'textarea',
                            'editor' => false,
                            'rows' => 3,
                        ],
                        [
                            'name' => 'image',
                            'label' => 'Hình ảnh nền thẻ Coverflow',
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
        $services = CoverflowController::getServicesData($this->settings);

        return view('themes.workspace.widgets.coverflow', [
            'widget' => $this,
            'settings' => $this->settings,
            'services' => $services,
        ])->render();
    }
}
