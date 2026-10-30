<?php

declare(strict_types=1);

namespace App\Widgets\Workspace;

use App\Widgets\BaseWidget;

class HomeWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Home Banner (Workspace)',
            'description' => 'Màn hình mở đầu với logo lớn thương hiệu Workspace và hiệu ứng Zoom In',
            'category' => 'Workspace',
            'version' => '1.0.0',
            'author' => 'Workspace',
            'icon' => 'home',
            'group' => 'workspace_home',
            'sort_order' => 1,
            'fields' => [
                [
                    'name' => 'logo',
                    'label' => 'Hình ảnh Logo thương hiệu',
                    'type' => 'image',
                    'default' => 'themes/workspace/assets/logo.png',
                    'help' => 'Chọn hoặc tải ảnh logo (khuyến nghị định dạng PNG trong suốt)',
                ],
                [
                    'name' => 'alt',
                    'label' => 'Văn bản thay thế (Alt text)',
                    'type' => 'text',
                    'default' => 'Workspace Interior design and build',
                ],
                [
                    'name' => 'aria_label',
                    'label' => 'Nhãn trợ năng (Aria Label)',
                    'type' => 'text',
                    'default' => 'Workspace',
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
        return view('themes.workspace.widgets.home', [
            'widget' => $this,
            'settings' => $this->settings,
        ])->render();
    }
}
