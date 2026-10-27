<?php

namespace App\Widgets\Groups;

use App\Widgets\BaseWidget;

class SliderWidget extends BaseWidget
{
    /**
     * Get widget metadata
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'Slider Group',
            'description' => 'Khối trình chiếu Slide Banner lớn đầu trang',
            'category' => 'Sliders',
            'version' => '1.0.0',
            'icon' => 'presentation-chart-bar',
            'group' => 'slider',
            'fields' => [
                [
                    'name' => 'slides',
                    'label' => 'Danh sách Hero Slide',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'image',
                            'label' => 'Ảnh nền Slide',
                            'type' => 'image',
                        ],
                        [
                            'name' => 'title',
                            'label' => 'Tiêu đề Slide',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'subtitle',
                            'label' => 'Phụ đề / Nội dung mô tả',
                            'type' => 'wysiwyg',
                        ],
                        [
                            'name' => 'button_text',
                            'label' => 'Chữ trên nút bấm',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'button_link',
                            'label' => 'Đường dẫn nút bấm (URL)',
                            'type' => 'text',
                        ],
                    ],
                ],
                [
                    'name' => 'slider_height',
                    'label' => 'Chiều cao Slide (Slider Height)',
                    'type' => 'select',
                    'options' => [
                        'full' => 'Full Screen (100vh)',
                        'auto' => 'Auto (Tự động theo ảnh)',
                        'custom' => 'Tuỳ chỉnh (Custom CSS)',
                    ],
                    'default' => 'auto',
                ],
                [
                    'name' => 'text_animation',
                    'label' => 'Hiệu ứng chữ (Text Animation)',
                    'type' => 'select',
                    'options' => [
                        'fade' => 'Mờ dần (Fade)',
                        'slide_up' => 'Trượt lên (Slide Up)',
                        'zoom' => 'Phóng to (Zoom)',
                    ],
                    'default' => 'slide_up',
                ],
                [
                    'name' => 'arrows_visible',
                    'label' => 'Hiển thị nút điều hướng (Arrows)',
                    'type' => 'select',
                    'options' => [
                        'yes' => 'Có (Yes)',
                        'no' => 'Không (No)',
                        'hover' => 'Chỉ hiện khi di chuột (Hover)',
                    ],
                    'default' => 'yes',
                ],
            ],
            'settings' => [
                'cacheable' => false,
                'cache_duration' => 0,
            ],
        ];
    }

    /**
     * Prepare data for the Blade view
     */
    public function getViewData(): array
    {
        $slides = $this->get('slides', []);
        if (\is_string($slides) && ! empty($slides)) {
            $decoded = json_decode($slides, true);
            if (json_last_error() === JSON_ERROR_NONE && \is_array($decoded)) {
                $slides = $decoded;
            }
        }
        if (! \is_array($slides)) {
            $slides = [];
        }

        return [
            'slides' => $slides,
        ];
    }
}
