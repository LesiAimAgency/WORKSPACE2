<?php

namespace App\Widgets\Groups;

use App\Widgets\BaseWidget;

class BannerWidget extends BaseWidget
{
    /**
     * Get widget metadata
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'Banner Group',
            'description' => 'Khối quản lý các banner quảng cáo & khuyến mãi',
            'category' => 'Banners',
            'version' => '1.0.0',
            'icon' => 'image',
            'group' => 'banner',
            'fields' => [
                [
                    'name' => 'banners',
                    'label' => 'Danh sách Banner',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'image',
                            'label' => 'Hình ảnh Banner',
                            'type' => 'image',
                        ],
                        [
                            'name' => 'title',
                            'label' => 'Tiêu đề Banner',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'subtitle',
                            'label' => 'Phụ đề / Khuyến mãi',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'button_text',
                            'label' => 'Chữ nút bấm',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'link',
                            'label' => 'Đường dẫn (URL)',
                            'type' => 'text',
                        ],
                    ],
                ],
                [
                    'name' => 'grid_layout',
                    'label' => 'Cấu trúc Lưới (Grid Layout)',
                    'type' => 'select',
                    'options' => [
                        '1_col' => '1 Cột (Full Width)',
                        '2_col' => '2 Cột (50% - 50%)',
                        '3_col' => '3 Cột (33% - 33% - 33%)',
                        'masonry' => 'Lưới Masonry phức tạp',
                        'carousel' => 'Slider (Trượt)',
                    ],
                    'default' => '3_col',
                ],
                [
                    'name' => 'hover_effect',
                    'label' => 'Hiệu ứng khi di chuột (Hover Effect)',
                    'type' => 'select',
                    'options' => [
                        'zoom' => 'Phóng to ảnh (Zoom in)',
                        'darken' => 'Tối đi (Darken)',
                        'overlay' => 'Lớp phủ màu (Color Overlay)',
                        'none' => 'Không có hiệu ứng',
                    ],
                    'default' => 'zoom',
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
        $banners = $this->get('banners', $this->get('slides', []));
        if (\is_string($banners) && ! empty($banners)) {
            $decoded = json_decode($banners, true);
            if (json_last_error() === JSON_ERROR_NONE && \is_array($decoded)) {
                $banners = $decoded;
            }
        }
        if (! \is_array($banners)) {
            $banners = [];
        }

        return [
            'banners' => $banners,
            'slides' => $banners, // Fallback compatibility
            'debug_settings' => $this->settings,
        ];
    }
}
