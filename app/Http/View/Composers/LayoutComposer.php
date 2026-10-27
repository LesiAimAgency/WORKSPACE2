<?php

namespace App\Http\View\Composers;

use App\Services\SettingsService;
use Illuminate\View\View;

class LayoutComposer
{
    protected $settings;

    public function __construct()
    {
        $this->settings = SettingsService::getInstance();
    }

    public function compose(View $view)
    {
        $headerStyle = $this->settings->get('header_style', ['template' => 'default']);
        $footerStyle = $this->settings->get('footer_style', ['template' => 'default']);

        $headerPath = 'layouts.headers.'.($headerStyle['template'] ?? 'default');
        $footerPath = 'layouts.footers.'.($footerStyle['template'] ?? 'default');

        $view->with([
            'headerPath' => $headerPath,
            'footerPath' => $footerPath,
        ]);
    }
}
