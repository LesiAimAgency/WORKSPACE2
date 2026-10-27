<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Ehenho;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectSettingModel;
use App\Models\Widget;
use App\Services\MenuService;
use App\Services\SettingsService;
use App\Widgets\Ehenho\EhenhoHeroSliderWidget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminThemeSettingController extends Controller
{
    /**
     * Resolve project and tenant context
     */
    protected function getProjectAndTenant(Request $request, string $projectCode): array
    {
        $project = Project::where('code', $projectCode)->first();
        if (! $project) {
            $project = $request->attributes->get('project');
        }
        $tenantId = $project?->tenant_id ?? session('current_tenant_id') ?? $project?->id;

        return [$project, $tenantId];
    }

    /**
     * Default Category Bar Navlinks for eHenho Header
     */
    public static function getDefaultNavlinks(): array
    {
        return [
            ['title' => 'Tìm bạn bốn phương', 'url' => '/ehenho/tim-kiem', 'is_active' => true],
            ['title' => 'Tìm người kết hôn', 'url' => '/ehenho/tim-kiem?looking_for=ket_hon', 'is_active' => true],
            ['title' => 'Tìm người yêu', 'url' => '/ehenho/tim-kiem?looking_for=nguoi_yeu', 'is_active' => true],
            ['title' => 'Tìm bạn gái', 'url' => '/ehenho/tim-kiem?gender=female', 'is_active' => true],
            ['title' => 'Tìm bạn trai', 'url' => '/ehenho/tim-kiem?gender=male', 'is_active' => true],
            ['title' => 'Tìm bạn đời', 'url' => '/ehenho/tim-kiem?looking_for=ban_doi', 'is_active' => true],
            ['title' => 'Tìm bạn tâm sự', 'url' => '/ehenho/tim-kiem?looking_for=tam_su', 'is_active' => true],
            ['title' => 'Tìm bạn bè mới', 'url' => '/ehenho/tim-kiem?looking_for=ban_be', 'is_active' => true],
        ];
    }

    /**
     * Get All Ehenho Theme Settings Data for Unified View
     */
    protected function getAllThemeSettings(Request $request, string $projectCode): array
    {
        [$project, $tenantId] = $this->getProjectAndTenant($request, $projectCode);

        $settings = [
            'site_name' => setting('site_name', 'eHenho.com - Hẹn hò Online & Tìm bạn Bốn phương'),
            'site_copyright' => setting('site_copyright', 'Email: hi@ehenho.com'),
            'site_logo' => setting('site_logo', ''),
            'site_favicon' => setting('site_favicon', ''),
            'logo_height' => setting('logo_height', '40'),
            'theme_color' => setting('theme_color', '#007cae'),
            'category_bar_color' => setting('category_bar_color', '#e85151'),
            'header_bg_color' => setting('header_bg_color', '#202020'),
            'header_text_color' => setting('header_text_color', '#f0f0f0'),
            'header_button_color' => setting('header_button_color', '#d9534f'),
            'header_height' => setting('header_height', '54'),
            'header_sticky' => setting('header_sticky', '1'),
            'header_show_search' => setting('header_show_search', '1'),
            'header_show_help' => setting('header_show_help', '1'),
            'header_show_auth' => setting('header_show_auth', '1'),
            'social_facebook' => setting('social_facebook', 'http://www.facebook.com/ehenho'),
            'social_twitter' => setting('social_twitter', 'http://www.twitter.com/ehenho'),
            'social_instagram' => setting('social_instagram', ''),
            'social_youtube' => setting('social_youtube', ''),
            'social_tiktok' => setting('social_tiktok', ''),
            'social_zalo' => setting('social_zalo', ''),
        ];

        $rawNavlinks = setting('ehenho_header_navlinks');
        if (is_string($rawNavlinks) && ! empty($rawNavlinks)) {
            $navlinks = json_decode($rawNavlinks, true) ?: static::getDefaultNavlinks();
        } elseif (is_array($rawNavlinks) && ! empty($rawNavlinks)) {
            $navlinks = $rawNavlinks;
        } else {
            $navlinks = static::getDefaultNavlinks();
        }

        $widget = Widget::withoutGlobalScope('tenant')
            ->where(function ($q) use ($project, $tenantId) {
                if ($project?->id) {
                    $q->where('project_id', $project->id);
                }
                if ($tenantId) {
                    $q->orWhere('tenant_id', $tenantId);
                }
            })
            ->where(function ($q) {
                $q->where('type', 'ehenho_hero_slider')
                    ->orWhere('type', 'SliderWidget');
            })
            ->first();

        $slides = $widget?->settings['slides'] ?? [];
        $interval = $widget?->settings['interval'] ?? (int) setting('ehenho_slider_interval', 5000);

        if (empty($slides)) {
            $settingSlides = setting('ehenho_slider_data');
            if (is_string($settingSlides) && ! empty($settingSlides)) {
                $slides = json_decode($settingSlides, true) ?: [];
            } elseif (is_array($settingSlides)) {
                $slides = $settingSlides;
            }
        }

        if (empty($slides)) {
            $slides = EhenhoHeroSliderWidget::getDefaultSlides();
        }

        $menus = MenuService::getMenusForProject($project);
        $availableLocations = MenuService::getAvailableLocations();

        return [
            'project' => $project,
            'currentProject' => $project,
            'projectCode' => $projectCode,
            'settings' => $settings,
            'settingsMap' => $settings,
            'navlinks' => $navlinks,
            'slides' => $slides,
            'interval' => $interval,
            'widget' => $widget,
            'menus' => $menus,
            'availableLocations' => $availableLocations,
        ];
    }

    /**
     * 1. Appearance, Color & Logo Management
     */
    public function appearance(Request $request, string $projectCode): View
    {
        $data = $this->getAllThemeSettings($request, $projectCode);
        $data['initialTab'] = $request->query('tab', 'design');

        return view('frontend.themes.ehenho.admin.settings.appearance', $data);
    }

    /**
     * Save Appearance Settings
     */
    public function updateAppearance(Request $request, string $projectCode): RedirectResponse
    {
        [$project, $tenantId] = $this->getProjectAndTenant($request, $projectCode);
        $activeTab = $request->input('active_tab', 'design');

        $settingsInput = $request->input('settings', []);
        $getVal = function ($key) use ($request, $settingsInput) {
            return $request->input($key, $settingsInput[$key] ?? null);
        };
        $hasVal = function ($key) use ($request, $settingsInput) {
            return $request->has($key) || isset($settingsInput[$key]);
        };

        // 1. Process Colors & Logo if present
        if ($hasVal('theme_color') || $hasVal('site_name') || $hasVal('site_copyright') || $hasVal('social_facebook') || $hasVal('social_twitter') || $hasVal('social_instagram') || $hasVal('social_youtube') || $hasVal('social_tiktok') || $hasVal('social_zalo') || $request->hasFile('site_logo_file') || $request->filled('site_logo_url') || $hasVal('site_logo')) {
            $logoValue = setting('site_logo', '');
            if ($request->hasFile('site_logo_file')) {
                $file = $request->file('site_logo_file');
                $path = $file->store('themes/ehenho/logos', 'public');
                $logoValue = '/storage/'.$path;
            } elseif ($request->filled('site_logo_url')) {
                $logoValue = $request->input('site_logo_url');
            } elseif ($getVal('site_logo')) {
                $logoValue = $getVal('site_logo');
            }

            $faviconValue = setting('site_favicon', '');
            if ($request->hasFile('site_favicon_file')) {
                $favFile = $request->file('site_favicon_file');
                $favPath = $favFile->store('themes/ehenho/favicons', 'public');
                $faviconValue = '/storage/'.$favPath;
            } elseif ($getVal('site_favicon')) {
                $faviconValue = $getVal('site_favicon');
            }

            if ($hasVal('site_name')) {
                ProjectSettingModel::set('site_name', $getVal('site_name') ?? '', 'general');
            }
            if ($hasVal('site_copyright')) {
                ProjectSettingModel::set('site_copyright', $getVal('site_copyright') ?? '', 'general');
            }
            if ($hasVal('social_facebook')) {
                ProjectSettingModel::set('social_facebook', $getVal('social_facebook') ?? '', 'social');
            }
            if ($hasVal('social_twitter')) {
                ProjectSettingModel::set('social_twitter', $getVal('social_twitter') ?? '', 'social');
            }
            if ($hasVal('social_instagram')) {
                ProjectSettingModel::set('social_instagram', $getVal('social_instagram') ?? '', 'social');
            }
            if ($hasVal('social_youtube')) {
                ProjectSettingModel::set('social_youtube', $getVal('social_youtube') ?? '', 'social');
            }
            if ($hasVal('social_tiktok')) {
                ProjectSettingModel::set('social_tiktok', $getVal('social_tiktok') ?? '', 'social');
            }
            if ($hasVal('social_zalo')) {
                ProjectSettingModel::set('social_zalo', $getVal('social_zalo') ?? '', 'social');
            }
            if (! empty($logoValue)) {
                ProjectSettingModel::set('site_logo', $logoValue, 'general');
            }
            if (! empty($faviconValue)) {
                ProjectSettingModel::set('site_favicon', $faviconValue, 'general');
            }
            if (! empty($getVal('logo_height'))) {
                ProjectSettingModel::set('logo_height', (string) $getVal('logo_height'), 'appearance');
            }
            if (! empty($getVal('theme_color'))) {
                ProjectSettingModel::set('theme_color', $getVal('theme_color'), 'appearance');
            }
            if (! empty($getVal('category_bar_color'))) {
                ProjectSettingModel::set('category_bar_color', $getVal('category_bar_color'), 'appearance');
            }
            if (! empty($getVal('header_bg_color'))) {
                ProjectSettingModel::set('header_bg_color', $getVal('header_bg_color'), 'appearance');
            }
            if (! empty($getVal('header_text_color'))) {
                ProjectSettingModel::set('header_text_color', $getVal('header_text_color'), 'appearance');
            }
            if (! empty($getVal('header_button_color'))) {
                ProjectSettingModel::set('header_button_color', $getVal('header_button_color'), 'appearance');
            }
        }

        // 2. Process Header Settings & Navlinks if present
        if ($request->has('header_height') || $request->has('navlinks')) {
            if ($request->filled('header_height')) {
                ProjectSettingModel::set('header_height', (string) $request->input('header_height'), 'header');
            }
            ProjectSettingModel::set('header_sticky', $request->input('header_sticky') === '1' ? '1' : '0', 'header');
            ProjectSettingModel::set('header_show_search', $request->input('header_show_search') === '1' ? '1' : '0', 'header');
            ProjectSettingModel::set('header_show_help', $request->input('header_show_help') === '1' ? '1' : '0', 'header');
            ProjectSettingModel::set('header_show_auth', $request->input('header_show_auth') === '1' ? '1' : '0', 'header');

            $submittedNavlinks = $request->input('navlinks', []);
            $cleanNavlinks = [];
            foreach ($submittedNavlinks as $link) {
                if (! empty($link['title']) && ! empty($link['url'])) {
                    $cleanNavlinks[] = [
                        'title' => trim($link['title']),
                        'url' => trim($link['url']),
                        'is_active' => ! empty($link['is_active']),
                    ];
                }
            }
            if (! empty($cleanNavlinks)) {
                ProjectSettingModel::set('ehenho_header_navlinks', $cleanNavlinks, 'header');
            }
        }

        // 3. Process Slider / Widgets if present
        if ($request->has('slides') || $request->has('interval')) {
            $submittedSlides = $request->input('slides', []);
            $slideFiles = $request->file('slide_files', []);
            $interval = (int) ($request->input('interval') ?: 5000);

            $processedSlides = [];
            foreach ($submittedSlides as $index => $item) {
                $img = $item['image_url'] ?? '';
                if (isset($slideFiles[$index]) && $slideFiles[$index]->isValid()) {
                    $path = $slideFiles[$index]->store('themes/ehenho/slides', 'public');
                    $img = '/storage/'.$path;
                }
                if (empty($img)) {
                    $img = '/themes/ehenho/images/tim-ban-bon-phuong.jpg';
                }

                $processedSlides[] = [
                    'image' => $img,
                    'title' => $item['title'] ?? '',
                    'subtitle' => $item['subtitle'] ?? '',
                    'button_text' => $item['button_text'] ?? 'TẠO HỒ SƠ CỦA BẠN!',
                    'button_link' => $item['button_link'] ?? '/ehenho/dang-ky',
                ];
            }

            if (! empty($processedSlides)) {
                $widgetSettings = [
                    'slides' => $processedSlides,
                    'interval' => $interval,
                ];

                $widget = Widget::withoutGlobalScope('tenant')
                    ->where(function ($q) use ($project, $tenantId) {
                        if ($project?->id) {
                            $q->where('project_id', $project->id);
                        }
                        if ($tenantId) {
                            $q->orWhere('tenant_id', $tenantId);
                        }
                    })
                    ->where(function ($q) {
                        $q->where('type', 'ehenho_hero_slider')
                            ->orWhere('type', 'SliderWidget');
                    })
                    ->first();

                if ($widget) {
                    $widget->settings = $widgetSettings;
                    $widget->type = 'ehenho_hero_slider';
                    $widget->is_active = true;
                    $widget->save();
                } else {
                    Widget::withoutGlobalScope('tenant')->create([
                        'project_id' => $project?->id,
                        'tenant_id' => $tenantId,
                        'name' => 'eHenho Hero Carousel',
                        'type' => 'ehenho_hero_slider',
                        'area' => 'homepage-main',
                        'sort_order' => 1,
                        'is_active' => true,
                        'settings' => $widgetSettings,
                    ]);
                }

                ProjectSettingModel::set('ehenho_slider_data', $processedSlides, 'widgets');
                ProjectSettingModel::set('ehenho_slider_interval', (string) $interval, 'widgets');

                if (function_exists('clear_widget_cache')) {
                    clear_widget_cache();
                }
            }
        }

        if (class_exists(SettingsService::class)) {
            SettingsService::getInstance()->clearCache();
        }

        return redirect(route('project.admin.ehenho.theme.header', $projectCode).'#'.$activeTab)
            ->with('success', 'Đã lưu toàn bộ cấu hình giao diện & slider thành công!');
    }

    /**
     * 2. Header Management View
     */
    public function header(Request $request, string $projectCode): View
    {
        $data = $this->getAllThemeSettings($request, $projectCode);
        $data['initialTab'] = $request->query('tab', 'design');

        return view('frontend.themes.ehenho.admin.settings.appearance', $data);
    }

    /**
     * Save Header Settings
     */
    public function updateHeader(Request $request, string $projectCode): RedirectResponse
    {
        [$project] = $this->getProjectAndTenant($request, $projectCode);

        $data = $request->validate([
            'header_height' => 'nullable|numeric|min:40|max:120',
            'header_sticky' => 'nullable|string',
            'header_show_search' => 'nullable|string',
            'header_show_help' => 'nullable|string',
            'header_show_auth' => 'nullable|string',
            'navlinks' => 'nullable|array',
            'navlinks.*.title' => 'required|string|max:100',
            'navlinks.*.url' => 'required|string|max:500',
            'navlinks.*.is_active' => 'nullable',
        ]);

        ProjectSettingModel::set('header_height', (string) ($data['header_height'] ?? '54'), 'header');
        ProjectSettingModel::set('header_sticky', $request->has('header_sticky') ? '1' : '0', 'header');
        ProjectSettingModel::set('header_show_search', $request->has('header_show_search') ? '1' : '0', 'header');
        ProjectSettingModel::set('header_show_help', $request->has('header_show_help') ? '1' : '0', 'header');
        ProjectSettingModel::set('header_show_auth', $request->has('header_show_auth') ? '1' : '0', 'header');

        // Process navlinks
        $cleanNavlinks = [];
        if (! empty($data['navlinks']) && is_array($data['navlinks'])) {
            foreach ($data['navlinks'] as $item) {
                if (! empty($item['title']) && ! empty($item['url'])) {
                    $cleanNavlinks[] = [
                        'title' => trim($item['title']),
                        'url' => trim($item['url']),
                        'is_active' => isset($item['is_active']) && ($item['is_active'] === '1' || $item['is_active'] === true),
                    ];
                }
            }
        }

        if (empty($cleanNavlinks)) {
            $cleanNavlinks = static::getDefaultNavlinks();
        }

        ProjectSettingModel::set('ehenho_header_navlinks', $cleanNavlinks, 'header');

        if (class_exists(SettingsService::class)) {
            SettingsService::getInstance()->clearCache();
        }

        return redirect()
            ->route('project.admin.ehenho.theme.header', $projectCode)
            ->with('success', 'Đã lưu cấu hình Header và danh mục điều hướng thành công!');
    }

    /**
     * 3. Slider & Widgets Management View
     */
    public function widgets(Request $request, string $projectCode): View
    {
        $data = $this->getAllThemeSettings($request, $projectCode);
        $data['initialTab'] = 'widgets';

        return view('frontend.themes.ehenho.admin.settings.appearance', $data);
    }

    /**
     * Save Slider & Widget Settings
     */
    public function updateWidgets(Request $request, string $projectCode): RedirectResponse
    {
        [$project, $tenantId] = $this->getProjectAndTenant($request, $projectCode);

        $request->validate([
            'interval' => 'nullable|numeric|min:1000|max:30000',
            'slides' => 'required|array|min:1',
            'slides.*.title' => 'nullable|string|max:255',
            'slides.*.subtitle' => 'nullable|string|max:1000',
            'slides.*.button_text' => 'nullable|string|max:100',
            'slides.*.button_link' => 'nullable|string|max:500',
            'slides.*.image_url' => 'nullable|string|max:1000',
            'slide_files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $submittedSlides = $request->input('slides', []);
        $slideFiles = $request->file('slide_files', []);
        $interval = (int) ($request->input('interval') ?: 5000);

        $processedSlides = [];
        foreach ($submittedSlides as $index => $item) {
            $img = $item['image_url'] ?? '';

            // If a file was uploaded for this slide index
            if (isset($slideFiles[$index]) && $slideFiles[$index]->isValid()) {
                $path = $slideFiles[$index]->store('themes/ehenho/slides', 'public');
                $img = '/storage/'.$path;
            }

            // Fallback image if empty
            if (empty($img)) {
                $img = '/themes/ehenho/images/tim-ban-bon-phuong.jpg';
            }

            $processedSlides[] = [
                'image' => $img,
                'title' => $item['title'] ?? '',
                'subtitle' => $item['subtitle'] ?? '',
                'button_text' => $item['button_text'] ?? 'TẠO HỒ SƠ CỦA BẠN!',
                'button_link' => $item['button_link'] ?? '/ehenho/dang-ky',
            ];
        }

        $widgetSettings = [
            'slides' => $processedSlides,
            'interval' => $interval,
        ];

        // Save or update in widgets table
        $widget = Widget::withoutGlobalScope('tenant')
            ->where(function ($q) use ($project, $tenantId) {
                if ($project?->id) {
                    $q->where('project_id', $project->id);
                }
                if ($tenantId) {
                    $q->orWhere('tenant_id', $tenantId);
                }
            })
            ->where(function ($q) {
                $q->where('type', 'ehenho_hero_slider')
                    ->orWhere('type', 'SliderWidget');
            })
            ->first();

        if ($widget) {
            $widget->settings = $widgetSettings;
            $widget->type = 'ehenho_hero_slider';
            $widget->is_active = true;
            $widget->save();
        } else {
            Widget::withoutGlobalScope('tenant')->create([
                'project_id' => $project?->id,
                'tenant_id' => $tenantId,
                'name' => 'eHenho Hero Carousel',
                'type' => 'ehenho_hero_slider',
                'area' => 'homepage-main',
                'sort_order' => 1,
                'is_active' => true,
                'settings' => $widgetSettings,
            ]);
        }

        // Also save to settings table as redundant backup
        ProjectSettingModel::set('ehenho_slider_data', $processedSlides, 'widgets');
        ProjectSettingModel::set('ehenho_slider_interval', (string) $interval, 'widgets');

        // Clear caches
        if (function_exists('clear_widget_cache')) {
            clear_widget_cache();
        }
        if (class_exists(SettingsService::class)) {
            SettingsService::getInstance()->clearCache();
        }

        return redirect()
            ->route('project.admin.ehenho.theme.widgets', $projectCode)
            ->with('success', 'Đã lưu cấu hình Hero Slider thành công!');
    }
}
