<?php

declare(strict_types=1);

namespace App\Widgets\Workspace;

use App\Http\Controllers\CoverflowController;
use App\Widgets\BaseWidget;

class CoverflowServiceWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Chi Tiết Dịch Vụ & Dự Án (Workspace Service Detail & Projects)',
            'description' => 'Section chi tiết dịch vụ 2 bước (Step 1: Introductions & Step 2: Projects kèm Modal so sánh Thiết kế - Thực tế)',
            'category' => 'Workspace',
            'version' => '1.0.0',
            'author' => 'Workspace',
            'icon' => 'squares-plus',
            'group' => 'workspace_coverflow_service',
            'sort_order' => 4,
            'fields' => [
                [
                    'name' => 'pill_text',
                    'label' => 'Nhãn thẻ danh mục (Category Pill)',
                    'type' => 'text',
                    'default' => 'Services',
                ],
                [
                    'name' => 'tab_intro_text',
                    'label' => 'Nhãn Tab Bước 1 (Introductions)',
                    'type' => 'text',
                    'default' => 'Introductions',
                ],
                [
                    'name' => 'tab_project_text',
                    'label' => 'Nhãn Tab Bước 2 (Projects)',
                    'type' => 'text',
                    'default' => 'Projects',
                ],
                [
                    'name' => 'back_text',
                    'label' => 'Nhãn nút Quay lại danh sách dịch vụ',
                    'type' => 'text',
                    'default' => 'Back to Services',
                ],
                [
                    'name' => 'compare_button_text',
                    'label' => 'Nhãn nút So sánh Thiết kế & Thực tế',
                    'type' => 'text',
                    'default' => 'Comparison Design and Result',
                ],
                [
                    'name' => 'label_area',
                    'label' => 'Nhãn thông số Diện tích',
                    'type' => 'text',
                    'default' => 'Total Area',
                ],
                [
                    'name' => 'label_location',
                    'label' => 'Nhãn thông số Địa điểm',
                    'type' => 'text',
                    'default' => 'Location',
                ],
                [
                    'name' => 'label_typology',
                    'label' => 'Nhãn thông số Loại hình',
                    'type' => 'text',
                    'default' => 'Typology',
                ],
                [
                    'name' => 'label_style',
                    'label' => 'Nhãn thông số Phong cách',
                    'type' => 'text',
                    'default' => 'Style',
                ],
                [
                    'name' => 'label_tech',
                    'label' => 'Nhãn thông số Giải pháp Kỹ thuật',
                    'type' => 'text',
                    'default' => 'Tech Solution',
                ],
                [
                    'name' => 'services',
                    'label' => 'Dữ liệu Chi tiết Dịch vụ & Dự án theo từng Dịch vụ',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'title',
                            'label' => 'Tên dịch vụ',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'slug',
                            'label' => 'Định danh Slug (Khớp với Coverflow)',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'image',
                            'label' => 'Hình ảnh đại diện Bước 1 (Introduction)',
                            'type' => 'image',
                        ],
                        [
                            'name' => 'intro_items',
                            'label' => 'Các mục giới thiệu (Bước 1 - Introductions)',
                            'type' => 'repeatable',
                            'fields' => [
                                [
                                    'name' => 'title',
                                    'label' => 'Tiêu đề mục giới thiệu',
                                    'type' => 'text',
                                ],
                                [
                                    'name' => 'desc',
                                    'label' => 'Nội dung mô tả',
                                    'type' => 'textarea',
                                    'editor' => false,
                                    'rows' => 2,
                                ],
                            ],
                        ],
                        [
                            'name' => 'projects',
                            'label' => 'Danh sách Dự án thuộc Dịch vụ này (Bước 2 - Projects)',
                            'type' => 'repeatable',
                            'fields' => [
                                [
                                    'name' => 'title',
                                    'label' => 'Tên dự án',
                                    'type' => 'text',
                                ],
                                [
                                    'name' => 'area',
                                    'label' => 'Diện tích (Total Area)',
                                    'type' => 'text',
                                ],
                                [
                                    'name' => 'location',
                                    'label' => 'Địa điểm (Location)',
                                    'type' => 'text',
                                ],
                                [
                                    'name' => 'typology',
                                    'label' => 'Loại hình (Typology)',
                                    'type' => 'text',
                                ],
                                [
                                    'name' => 'style',
                                    'label' => 'Phong cách (Style)',
                                    'type' => 'text',
                                ],
                                [
                                    'name' => 'tech',
                                    'label' => 'Giải pháp kỹ thuật (Tech Solution)',
                                    'type' => 'text',
                                ],
                                [
                                    'name' => 'cover_image',
                                    'label' => 'Ảnh bìa dự án (Cover Image)',
                                    'type' => 'image',
                                ],
                                [
                                    'name' => 'design_image',
                                    'label' => 'Ảnh bản vẽ thiết kế (Design Image - So sánh Before)',
                                    'type' => 'image',
                                ],
                                [
                                    'name' => 'result_image',
                                    'label' => 'Ảnh hoàn thiện thực tế (Result Image - So sánh After)',
                                    'type' => 'image',
                                ],
                            ],
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

        return view('themes.workspace.widgets.coverflow_service', [
            'widget' => $this,
            'settings' => $this->settings,
            'services' => $services,
        ])->render();
    }
}
