<?php

namespace App\Widgets\Groups;

use App\Widgets\BaseWidget;

class HeaderWidget extends BaseWidget
{
    /**
     * Get widget metadata
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'Header',
            'description' => 'Global Header cho website',
            'category' => 'Global',
            'version' => '1.0.0',
            'icon' => 'layout-top',
            'group' => 'header',
            'fields' => [
                // Các field config cơ bản sẽ được mở rộng trong tương lai
                [
                    'name' => 'logo',
                    'label' => 'Logo',
                    'type' => 'image',
                ],
                [
                    'name' => 'contact_phone',
                    'label' => 'Số điện thoại liên hệ',
                    'type' => 'text',
                ],
                [
                    'name' => 'contact_email',
                    'label' => 'Email liên hệ',
                    'type' => 'text',
                ],
                [
                    'name' => 'promotional_text',
                    'label' => 'Câu chữ khuyến mãi (Top bar)',
                    'type' => 'wysiwyg',
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
