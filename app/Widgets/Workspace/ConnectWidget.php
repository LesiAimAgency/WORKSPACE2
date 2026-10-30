<?php

declare(strict_types=1);

namespace App\Widgets\Workspace;

use App\Widgets\BaseWidget;

class ConnectWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Form Kết Nối & Đối Tác (Workspace Connect)',
            'description' => 'Màn hình Form đăng ký tư vấn trực tiếp thu thập thông tin khách hàng và thanh trượt thương hiệu đối tác',
            'category' => 'Workspace',
            'version' => '1.0.0',
            'author' => 'Workspace',
            'icon' => 'envelope',
            'group' => 'workspace_connect',
            'sort_order' => 7,
            'fields' => [
                [
                    'name' => 'heading1',
                    'label' => 'Tiêu đề dòng 1',
                    'type' => 'text',
                    'default' => 'CONNECT',
                ],
                [
                    'name' => 'heading2',
                    'label' => 'Tiêu đề dòng 2',
                    'type' => 'text',
                    'default' => 'WORKSPACE',
                ],
                [
                    'name' => 'label_name',
                    'label' => 'Nhãn trường Họ & Tên',
                    'type' => 'text',
                    'default' => 'Your Name',
                ],
                [
                    'name' => 'label_phone',
                    'label' => 'Nhãn trường Số điện thoại',
                    'type' => 'text',
                    'default' => 'Phone Number (*)',
                ],
                [
                    'name' => 'label_email',
                    'label' => 'Nhãn trường Email',
                    'type' => 'text',
                    'default' => 'Email',
                ],
                [
                    'name' => 'label_note',
                    'label' => 'Nhãn trường Lời nhắn',
                    'type' => 'text',
                    'default' => 'Note for us',
                ],
                [
                    'name' => 'button_text',
                    'label' => 'Chữ nút gửi',
                    'type' => 'text',
                    'default' => 'CONNECT WITH US',
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
        return view('themes.workspace.widgets.connect', [
            'widget' => $this,
            'settings' => $this->settings,
        ])->render();
    }
}
