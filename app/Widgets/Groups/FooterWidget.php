<?php

namespace App\Widgets\Groups;

use App\Widgets\BaseWidget;

class FooterWidget extends BaseWidget
{
    /**
     * Get widget metadata
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'Footer',
            'description' => 'Global Footer cho website',
            'category' => 'Global',
            'version' => '1.0.0',
            'icon' => 'layout-bottom',
            'group' => 'footer',
            'fields' => [
                // Các field config cơ bản sẽ được mở rộng trong tương lai
                [
                    'name' => 'footer_logo',
                    'label' => 'Logo Footer',
                    'type' => 'image',
                ],
                [
                    'name' => 'footer_text',
                    'label' => 'Đoạn giới thiệu ngắn',
                    'type' => 'textarea',
                ],
                [
                    'name' => 'copyright',
                    'label' => 'Dòng chữ bản quyền (Copyright)',
                    'type' => 'text',
                ],
            ],
            'settings' => [
                'cacheable' => true,
                'cache_duration' => 3600,
            ],
        ];
    }

    /**
     * Prepare data for the Blade view
     */
    public function getViewData(): array
    {
        return [];
    }
}
