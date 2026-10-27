<?php

namespace App\Widgets;

class HTMLWidget extends BaseWidget
{
    public static function getConfig(): array
    {
        return [
            'name' => 'Custom HTML',
            'description' => 'Chèn mã HTML, văn bản tùy chỉnh hoặc banner',
            'category' => 'general',
            'version' => '1.0.0',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" /></svg>',
            'fields' => [
                [
                    'name' => 'content',
                    'label' => 'Nội dung HTML',
                    'type' => 'textarea',
                    'rows' => 8,
                    'default' => '',
                    'description' => 'Hỗ trợ mã HTML, CSS hoặc JS tùy biến',
                ],
            ],
        ];
    }

    public function render(): string
    {
        $content = (string) $this->get('content', '');

        return $this->wrapWithCodeInjections($content);
    }

    public function getData(): array
    {
        return [
            'content' => (string) $this->get('content', ''),
        ];
    }
}
