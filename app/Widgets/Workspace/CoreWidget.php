<?php

declare(strict_types=1);

namespace App\Widgets\Workspace;

use App\Widgets\BaseWidget;

class CoreWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Giá Trị Cốt Lõi (Workspace Core Values)',
            'description' => 'Section giới thiệu giá trị cốt lõi công ty với đồ họa quỹ đạo SVG độc quyền',
            'category' => 'Workspace',
            'version' => '1.0.0',
            'author' => 'Workspace',
            'icon' => 'sparkles',
            'group' => 'workspace_core',
            'sort_order' => 2,
            'fields' => [
                [
                    'name' => 'sub_heading',
                    'label' => 'Tiêu đề phụ (Sub Heading)',
                    'type' => 'text',
                    'default' => 'WORKSPACE',
                ],
                [
                    'name' => 'heading',
                    'label' => 'Tiêu đề chính',
                    'type' => 'text',
                    'default' => 'CORE VALUES',
                ],
                [
                    'name' => 'description',
                    'label' => 'Đoạn văn mô tả',
                    'type' => 'textarea',
                    'editor' => false,
                    'rows' => 3,
                    'default' => 'This combination of core values is flexibly applied by us at each stage of the company, in personnel training, dealing with partners & serving customers!',
                ],
                [
                    'name' => 'image',
                    'label' => 'Hình ảnh đồ họa cốt lõi (SVG/PNG)',
                    'type' => 'image',
                    'default' => 'themes/workspace/assets/main1.svg',
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
        return view('themes.workspace.widgets.core', [
            'widget' => $this,
            'settings' => $this->settings,
        ])->render();
    }
}
