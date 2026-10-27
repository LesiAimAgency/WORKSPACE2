<?php

namespace App\Widgets\Groups;

use App\Widgets\BaseWidget;

class FeatureWidget extends BaseWidget
{
    /**
     * Get widget metadata
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'Feature Group',
            'description' => 'Khối đặc điểm nổi bật / Tính năng / Dịch vụ',
            'category' => 'Features',
            'version' => '1.0.0',
            'icon' => 'star',
            'group' => 'feature',
            'fields' => [
                [
                    'name' => 'section_title',
                    'label' => 'Tiêu đề khối Tính năng',
                    'type' => 'text',
                ],
                [
                    'name' => 'features',
                    'label' => 'Danh sách Tính năng / Dịch vụ',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'icon',
                            'label' => 'Icon / Biểu tượng (Class hoặc Ảnh)',
                            'type' => 'image',
                        ],
                        [
                            'name' => 'title',
                            'label' => 'Tiêu đề tính năng',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'description',
                            'label' => 'Mô tả ngắn tính năng',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'link',
                            'label' => 'Đường dẫn liên kết (nếu có)',
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

    /**
     * Prepare data for the Blade view
     */
    public function getViewData(): array
    {
        $features = $this->get('features', []);
        $sectionTitle = $this->get('section_title', '');

        return [
            'section_title' => $sectionTitle,
            'features' => $features,
            'slides' => $features, // Fallback
        ];
    }
}
