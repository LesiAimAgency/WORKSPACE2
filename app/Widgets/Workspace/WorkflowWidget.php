<?php

declare(strict_types=1);

namespace App\Widgets\Workspace;

use App\Widgets\BaseWidget;

class WorkflowWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Quy Trình Triển Khai (Workspace Workflow)',
            'description' => 'Màn hình giới thiệu chi tiết từng phân nhánh dịch vụ với font chữ viết tay nghệ thuật và hình ảnh toàn màn hình',
            'category' => 'Workspace',
            'version' => '1.0.0',
            'author' => 'Workspace',
            'icon' => 'arrow-path',
            'group' => 'workspace_workflow',
            'hidden' => true,
            'fields' => [
                [
                    'name' => 'section_id',
                    'label' => 'ID định danh Section (VD: about, build, furniture-design)',
                    'type' => 'text',
                    'default' => 'about',
                ],
                [
                    'name' => 'section_class',
                    'label' => 'CSS Class tùy biến (VD: workflow-design)',
                    'type' => 'text',
                    'default' => 'workflow-design',
                ],
                [
                    'name' => 'data_ui',
                    'label' => 'Giao diện nền sáng/tối (UI theme)',
                    'type' => 'select',
                    'default' => 'dark',
                    'options' => [
                        'dark' => 'Tối (Dark)',
                        'light' => 'Sáng (Light)',
                    ],
                ],
                [
                    'name' => 'kicker',
                    'label' => 'Từ mào đầu (Kicker text)',
                    'type' => 'text',
                    'default' => 'We',
                ],
                [
                    'name' => 'script',
                    'label' => 'Từ viết tay nghệ thuật (Script title)',
                    'type' => 'text',
                    'default' => 'Design',
                    'help' => 'Từ này sẽ hiển thị bằng font nghệ thuật SVN-Snell Roundhand Script',
                ],
                [
                    'name' => 'suffix',
                    'label' => 'Từ tiếp nối (Suffix text)',
                    'type' => 'text',
                    'default' => 'Spaces',
                ],
                [
                    'name' => 'desc',
                    'label' => 'Đoạn mô tả dịch vụ',
                    'type' => 'textarea',
                    'default' => 'Transforming empty spaces into high-performance, human-centric working environments that stimulate creativity and productivity.',
                ],
                [
                    'name' => 'image',
                    'label' => 'Hình ảnh nền Workflow',
                    'type' => 'image',
                    'default' => 'themes/workspace/assets/build1.png',
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
        return view('themes.workspace.widgets.workflow', [
            'widget' => $this,
            'settings' => $this->settings,
        ])->render();
    }
}
