<?php

namespace App\Widgets\General\BladeWidget;

use App\Widgets\BaseWidget;
use Illuminate\Support\Facades\Blade;

class BladeWidgetWidget extends BaseWidget
{
    public function render(): string
    {
        $content = $this->get('content', '');

        if (empty($content)) {
            return '';
        }

        // Evaluate the Blade string
        try {
            return Blade::render($content, ['widget' => $this]);
        } catch (\Exception $e) {
            if (config('app.debug')) {
                return '<!-- BladeWidget Error: '.$e->getMessage()." -->\n".$content;
            }

            return '<!-- Widget Render Error -->';
        }
    }

    public function css(): string
    {
        return '';
    }

    public function js(): string
    {
        return '';
    }

    /**
     * Legacy method for backward compatibility
     */
    public static function getConfig(): array
    {
        return [
            'name' => 'BladeWidget',
            'description' => 'A widget that renders dynamic Blade template code',
            'category' => 'general',
            'version' => '1.0.0',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" /></svg>',
            'fields' => [
                ['name' => 'content', 'label' => 'Blade Content', 'type' => 'textarea', 'default' => ''],
            ],
        ];
    }
}
