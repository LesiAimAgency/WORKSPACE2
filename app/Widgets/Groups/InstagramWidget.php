<?php

namespace App\Widgets\Groups;

use App\Widgets\BaseWidget;

class InstagramWidget extends BaseWidget
{
    /**
     * Get widget metadata
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'Instagram Group',
            'description' => 'Khối hiển thị thư viện ảnh Instagram / Mạng xã hội',
            'category' => 'Social Media',
            'version' => '1.0.0',
            'icon' => 'camera',
            'group' => 'instagram',
            'fields' => [
                [
                    'name' => 'title',
                    'label' => 'Tiêu đề khối Instagram',
                    'type' => 'text',
                ],
                [
                    'name' => 'username',
                    'label' => 'Tài khoản Instagram (@username)',
                    'type' => 'text',
                ],
                [
                    'name' => 'photos',
                    'label' => 'Danh sách Hình ảnh',
                    'type' => 'repeatable',
                    'fields' => [
                        [
                            'name' => 'image',
                            'label' => 'Hình ảnh',
                            'type' => 'image',
                        ],
                        [
                            'name' => 'caption',
                            'label' => 'Chú thích / Hashtag',
                            'type' => 'text',
                        ],
                        [
                            'name' => 'link',
                            'label' => 'Đường dẫn bài đăng Instagram',
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
        $photos = $this->get('photos', []);
        $title = $this->get('title', 'Follow Us On Instagram');
        $username = $this->get('username', '');

        return [
            'title' => $title,
            'username' => $username,
            'photos' => $photos,
            'slides' => $photos, // Fallback
        ];
    }
}
