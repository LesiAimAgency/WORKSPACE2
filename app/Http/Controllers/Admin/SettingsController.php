<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectSetting;
use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        [$project, $tenantId] = $this->resolveProjectAndTenant($request);
        $boxes = $this->getSettingsBoxes();

        $viewData = [
            'project' => $project,
            'currentProject' => $project,
            'boxes' => $boxes,
        ];

        if (view()->exists('admin.settings.index')) {
            return view('admin.settings.index', $viewData);
        }

        return view('cms.settings.index', $viewData);
    }

    public function seo(Request $request)
    {
        return $this->group($request, null, 'seo');
    }

    public function scanTranslations()
    {
        try {
            $keys = [];
            $viewPath = resource_path('views');

            // Scan all blade files
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($viewPath)
            );

            foreach ($files as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $content = file_get_contents($file->getRealPath());

                    // Match __('key') and __("key") patterns
                    preg_match_all("/__\('([^']+)'\)/", $content, $matches1);
                    preg_match_all('/__\("([^"]+)"\)/', $content, $matches2);

                    // Match Lang('key') and Lang("key") patterns
                    preg_match_all("/Lang\('([^']+)'\)/", $content, $matches3);
                    preg_match_all('/Lang\("([^"]+)"\)/', $content, $matches4);

                    // Match trans_db('key') and trans_db("key") patterns
                    preg_match_all("/trans_db\('([^']+)'\)/", $content, $matches5);
                    preg_match_all('/trans_db\("([^"]+)"\)/', $content, $matches6);

                    foreach ([$matches1, $matches2, $matches3, $matches4, $matches5, $matches6] as $m) {
                        if (! empty($m[1])) {
                            $keys = array_merge($keys, $m[1]);
                        }
                    }
                }
            }

            $keys = array_unique($keys);
            sort($keys);

            return response()->json([
                'success' => true,
                'keys' => $keys,
                'count' => count($keys),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function save(Request $request)
    {
        try {
            // Debug: Log all request data
            \Log::info('Settings save - Request data:', [
                'all_data' => $request->all(),
                'url' => $request->url(),
                'method' => $request->method(),
            ]);

            [$project, $tenantId] = $this->resolveProjectAndTenant($request);
            if (! $project) {
                $project = Project::first();
            }
            $tenantId = $tenantId ?: ($project?->tenant_id ?? session('current_tenant_id') ?? 6);
            $savedCount = 0;

            // Handle direct file uploads (e.g. site_favicon_file, contact_modal_logo_file)
            if ($request->hasFile('site_favicon_file')) {
                $favFile = $request->file('site_favicon_file');
                if ($favFile && $favFile->isValid()) {
                    $ext = strtolower($favFile->getClientOriginalExtension());
                    if (in_array($ext, ['ico', 'png', 'svg', 'webp', 'jpg', 'jpeg', 'gif'])) {
                        $path = $favFile->store('favicons', 'public');
                        $request->merge(['site_favicon' => '/storage/'.$path]);
                    }
                }
            }

            if ($request->hasFile('contact_modal_logo_file')) {
                $logoFile = $request->file('contact_modal_logo_file');
                if ($logoFile && $logoFile->isValid()) {
                    $ext = strtolower($logoFile->getClientOriginalExtension());
                    if (in_array($ext, ['ico', 'png', 'svg', 'webp', 'jpg', 'jpeg', 'gif'])) {
                        $path = $logoFile->store('logos', 'public');
                        $request->merge(['contact_modal_logo' => '/storage/'.$path]);
                    }
                }
            }

            // Normalize site_favicon URL if it includes the current host
            if ($request->filled('site_favicon')) {
                $fav = $request->input('site_favicon');
                $appUrl = url('/');
                if (str_starts_with($fav, $appUrl)) {
                    $fav = substr($fav, strlen($appUrl));
                    if (! str_starts_with($fav, '/')) {
                        $fav = '/'.$fav;
                    }
                    $request->merge(['site_favicon' => $fav]);
                }
            }

            // Handle guarantee icon uploads if any
            if ($request->hasFile('guarantee_icons')) {
                $guaranteeData = $request->input('product_guarantees', []);
                if (is_string($guaranteeData)) {
                    $guaranteeData = json_decode($guaranteeData, true) ?? [];
                }
                foreach ($request->file('guarantee_icons') as $idx => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('guarantees', 'public');
                        if (isset($guaranteeData['items'][$idx])) {
                            $guaranteeData['items'][$idx]['icon'] = '/storage/'.$path;
                        }
                    }
                }
                $request->merge(['product_guarantees' => $guaranteeData]);
            }

            // Handle social icon uploads if any
            if ($request->hasFile('social_icons')) {
                $socialData = $request->input('social_items', []);
                if (is_string($socialData)) {
                    $socialData = json_decode($socialData, true) ?? [];
                }
                foreach ($request->file('social_icons') as $idx => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('social', 'public');
                        if (isset($socialData[$idx])) {
                            $socialData[$idx]['icon'] = '/storage/'.$path;
                            $socialData[$idx]['icon_type'] = 'image';
                        }
                    }
                }
                $request->merge(['social_items' => $socialData]);
            }

            // Handle partner image uploads if any
            if ($request->hasFile('partner_images')) {
                $partnerData = $request->input('partners_items', []);
                if (is_string($partnerData)) {
                    $partnerData = json_decode($partnerData, true) ?? [];
                }
                foreach ($request->file('partner_images') as $idx => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('partners', 'public');
                        if (isset($partnerData[$idx])) {
                            $partnerData[$idx]['image'] = '/storage/'.$path;
                        }
                    }
                }
                $request->merge(['partners_items' => $partnerData]);
            }

            // Sync traditional social_* keys if social_items is provided
            if ($request->has('social_items')) {
                $sItems = $request->input('social_items');
                if (is_string($sItems)) {
                    $sItems = json_decode($sItems, true) ?? [];
                }
                if (is_array($sItems)) {
                    $mapSocial = [
                        'facebook' => 'social_facebook',
                        'youtube' => 'social_youtube',
                        'zalo' => 'social_zalo',
                        'tiktok' => 'social_tiktok',
                        'instagram' => 'social_instagram',
                        'twitter' => 'social_twitter',
                    ];
                    foreach ($sItems as $s) {
                        $sName = strtolower($s['name'] ?? '');
                        $sLink = $s['link'] ?? '';
                        foreach ($mapSocial as $needle => $sKey) {
                            if (str_contains($sName, $needle) && !empty($sLink)) {
                                $request->merge([$sKey => $sLink]);
                            }
                        }
                    }
                }
            }

            // Sử dụng transaction để tránh duplicate
            \DB::transaction(function () use ($request, $project, $tenantId, &$savedCount) {
                foreach ($request->except('_token', '_method', 'page', 'site_favicon_file', 'contact_modal_logo_file', 'guarantee_icons', 'social_icons', 'partner_images') as $key => $value) {
                    if (is_string($value)) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $value = $decoded;
                        }
                    }

                    if ($project) {
                        // Xóa setting cũ trước (nếu có) để tránh duplicate
                        \DB::table('settings')
                            ->where('key', $key)
                            ->where(function ($q) use ($project, $tenantId) {
                                $q->where('project_id', $project->id);
                                if ($tenantId) {
                                    $q->orWhere('tenant_id', $tenantId);
                                }
                            })
                            ->delete();

                        // Insert setting mới (100% tenant_id + project_id)
                        \DB::table('settings')->insert([
                            'key' => $key,
                            'payload' => json_encode(is_array($value) ? $value : ['value' => $value]),
                            'value' => is_scalar($value) ? (string) $value : null,
                            'group' => 'general',
                            'project_id' => $project->id,
                            'tenant_id' => $tenantId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        if ($key === 'site_favicon') {
                            \DB::table('settings')
                                ->where('key', 'seo_favicon')
                                ->where(function ($q) use ($project, $tenantId) {
                                    $q->where('project_id', $project->id);
                                    if ($tenantId) {
                                        $q->orWhere('tenant_id', $tenantId);
                                    }
                                })
                                ->delete();

                            \DB::table('settings')->insert([
                                'key' => 'seo_favicon',
                                'payload' => json_encode(is_array($value) ? $value : ['value' => $value]),
                                'value' => is_scalar($value) ? (string) $value : null,
                                'group' => 'general',
                                'project_id' => $project->id,
                                'tenant_id' => $tenantId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    } else {
                        Setting::set($key, $value);
                        if ($key === 'site_favicon') {
                            Setting::set('seo_favicon', $value);
                        }
                    }

                    $savedCount++;
                }
            });

            SettingsService::getInstance()->clearCache();

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Lưu cấu hình thành công!',
                ]);
            }

            return back()->with('alert', [
                'type' => 'success',
                'message' => 'Lưu cấu hình thành công!',
            ])->with('success', 'Lưu cấu hình thành công!');
        } catch (\Exception $e) {
            \Log::error('Settings save error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi: '.$e->getMessage(),
            ]);
        }
    }

    public function projectSettings(Request $request)
    {
        return $this->group($request, null, 'seo');
    }

    public function saveProjectSettings(Request $request)
    {
        $project = $request->attributes->get('project') ?? Project::first();

        try {
            if ($request->has('permissions')) {
                foreach ($request->permissions as $module => $perms) {
                    $project->permissions()->updateOrCreate(
                        ['module' => $module],
                        $perms
                    );
                }
            }

            return back()->with('alert', [
                'type' => 'success',
                'message' => 'Cập nhật phân quyền thành công!',
            ]);
        } catch (\Exception $e) {
            return back()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi: '.$e->getMessage(),
            ]);
        }
    }

    public function appearance(Request $request)
    {
        [$project] = $this->resolveProjectAndTenant($request);
        if ($project && ($project->code === 'ehenho' || ($project->features['theme'] ?? '') === 'ehenho')) {
            return redirect(route('project.admin.ehenho.theme.header', ['projectCode' => $project->code]).'#design');
        }

        return $this->group($request, null, 'appearance');
    }

    public function modalBox(Request $request)
    {
        return $this->group($request, null, 'modal-box');
    }

    public function notifications(Request $request)
    {
        return $this->group($request, null, 'notifications');
    }

    public function filter(Request $request)
    {
        return $this->group($request, null, 'filter');
    }

    public function contactButtons(Request $request)
    {
        return $this->group($request, null, 'contact-buttons');
    }

    public function social(Request $request)
    {
        return $this->group($request, null, 'social');
    }

    public function partners(Request $request)
    {
        return $this->group($request, null, 'partners');
    }

    public function group(Request $request, $projectCodeOrGroup = null, $group = null)
    {
        $group = $group
            ?: ($request->route('group')
            ?: ($projectCodeOrGroup ?: 'seo'));

        if ($group === 'contact-modal') {
            $group = 'modal-box';
        }
        if ($group === 'fillter') {
            $group = 'filter';
        }

        [$project, $tenantId] = $this->resolveProjectAndTenant($request);

        $settingsMap = $this->getSettingsMap($project, $tenantId, $group);

        $languages = $settingsMap['languages'] ?? setting('languages', []);
        if (is_string($languages)) {
            $languages = json_decode($languages, true) ?: [];
        }
        $activeLanguages = collect($languages)
            ->filter(fn ($l) => is_array($l) ? ($l['is_active'] ?? false) : ($l->is_active ?? false))
            ->map(fn ($l) => (object) $l)
            ->values();

        if ($activeLanguages->isEmpty()) {
            $activeLanguages = collect([
                (object) ['code' => 'vi', 'name' => 'Tiếng Việt', 'flag_emoji' => '🇻🇳', 'flag_url' => '', 'is_default' => true, 'is_active' => true],
            ]);
        }

        $viewData = [
            'group' => $group,
            'settings' => $settingsMap,
            'settingsMap' => $settingsMap,
            'activeLanguages' => $activeLanguages,
            'project' => $project,
            'currentProject' => $project,
        ];

        if ($group === 'filter') {
            $rawCategories = \App\Models\Taxonomy::where('taxonomy', 'product_cat')
                ->with(['parent', 'children'])
                ->withCount('posts as products_count')
                ->orderBy('order')
                ->orderBy('id')
                ->get();

            $productCategories = $this->buildCategoryTreeAndFlatten($rawCategories);
            $productBrands = \App\Models\Taxonomy::whereIn('taxonomy', ['brand', 'product_brand'])
                ->withCount('posts as products_count')
                ->orderBy('name')
                ->get();

            $savedFilterSettings = setting('product_filter_settings');
            if (is_string($savedFilterSettings)) {
                $savedFilterSettings = json_decode($savedFilterSettings, true) ?: [];
            }
            $defaultFilterSettings = $this->getDefaultFilterSettings($productCategories, $productBrands);
            if (! is_array($savedFilterSettings) || empty($savedFilterSettings)) {
                $savedFilterSettings = $defaultFilterSettings;
            } else {
                $savedFilterSettings = array_replace_recursive($defaultFilterSettings, $savedFilterSettings);
            }

            $viewData['productCategories'] = $productCategories;
            $viewData['productBrands'] = $productBrands;
            $viewData['filterSettings'] = $savedFilterSettings;
        }

        if ($group === 'social') {
            $savedSocialItems = setting('social_items');
            if (is_string($savedSocialItems)) {
                $savedSocialItems = json_decode($savedSocialItems, true) ?: [];
            }
            if (! is_array($savedSocialItems) || empty($savedSocialItems)) {
                $savedSocialItems = [
                    [
                        'id' => 1,
                        'name' => 'Facebook',
                        'link' => setting('social_facebook') ?: 'https://facebook.com/',
                        'icon' => 'fa-brands fa-facebook',
                        'icon_type' => 'fa',
                        'color' => '#1877F2',
                        'target' => '_blank',
                        'is_active' => true,
                    ],
                    [
                        'id' => 2,
                        'name' => 'Zalo',
                        'link' => setting('social_zalo') ? (str_starts_with(setting('social_zalo'), 'http') ? setting('social_zalo') : 'https://zalo.me/'.setting('social_zalo')) : 'https://zalo.me/',
                        'icon' => 'fa-solid fa-comment-dots',
                        'icon_type' => 'fa',
                        'color' => '#0068FF',
                        'target' => '_blank',
                        'is_active' => true,
                    ],
                    [
                        'id' => 3,
                        'name' => 'YouTube',
                        'link' => setting('social_youtube') ?: 'https://youtube.com/',
                        'icon' => 'fa-brands fa-youtube',
                        'icon_type' => 'fa',
                        'color' => '#FF0000',
                        'target' => '_blank',
                        'is_active' => true,
                    ],
                    [
                        'id' => 4,
                        'name' => 'TikTok',
                        'link' => setting('social_tiktok') ?: 'https://tiktok.com/',
                        'icon' => 'fa-brands fa-tiktok',
                        'icon_type' => 'fa',
                        'color' => '#000000',
                        'target' => '_blank',
                        'is_active' => true,
                    ],
                    [
                        'id' => 5,
                        'name' => 'Instagram',
                        'link' => setting('social_instagram') ?: 'https://instagram.com/',
                        'icon' => 'fa-brands fa-instagram',
                        'icon_type' => 'fa',
                        'color' => '#E4405F',
                        'target' => '_blank',
                        'is_active' => true,
                    ],
                ];
            }
            $viewData['socialItems'] = $savedSocialItems;
        }

        if ($group === 'partners') {
            $savedPartnersHeading = setting('partners_heading') ?: 'OUR CUSTOMERS - PARTNERS';
            $savedPartnerItems = setting('partners_items', setting('partners'));
            if (is_string($savedPartnerItems)) {
                $savedPartnerItems = json_decode($savedPartnerItems, true) ?: [];
            }
            if (! is_array($savedPartnerItems) || empty($savedPartnerItems)) {
                $savedPartnerItems = [
                    [
                        'id' => 1,
                        'image' => 'themes/workspace/assets/parnert1.png',
                        'alt' => 'Strategic Partner 1',
                        'link' => '',
                        'is_active' => true,
                    ],
                    [
                        'id' => 2,
                        'image' => 'themes/workspace/assets/parnert2.png',
                        'alt' => 'Strategic Partner 2',
                        'link' => '',
                        'is_active' => true,
                    ],
                    [
                        'id' => 3,
                        'image' => 'themes/workspace/assets/parnert1.png',
                        'alt' => 'Strategic Partner 3',
                        'link' => '',
                        'is_active' => true,
                    ],
                    [
                        'id' => 4,
                        'image' => 'themes/workspace/assets/parnert2.png',
                        'alt' => 'Strategic Partner 4',
                        'link' => '',
                        'is_active' => true,
                    ],
                    [
                        'id' => 5,
                        'image' => 'themes/workspace/assets/parnert1.png',
                        'alt' => 'Strategic Partner 5',
                        'link' => '',
                        'is_active' => true,
                    ],
                ];
            }
            $viewData['partnersHeading'] = $savedPartnersHeading;
            $viewData['partnerItems'] = $savedPartnerItems;
        }

        // 1. Theme specific view
        $theme = ($project?->features['theme'] ?? null) ?: ($project?->code === 'viettinmart-eco' ? 'viettinmartdemo' : ($project?->code === 'wkcomputer' ? 'wkcomputerdemo' : setting('theme')));
        if ($theme && view()->exists("frontend.themes.{$theme}.admin.settings.{$group}")) {
            return view("frontend.themes.{$theme}.admin.settings.{$group}", $viewData);
        }
        if (view()->exists("admin.settings.{$group}")) {
            return view("admin.settings.{$group}", $viewData);
        }
        if (view()->exists("cms.settings.{$group}")) {
            return view("cms.settings.{$group}", $viewData);
        }

        if (view()->exists('cms.settings.group')) {
            return view('cms.settings.group', $viewData);
        }

        $redirectRoute = \Route::has('admin.settings.index')
            ? route('admin.settings.index')
            : (\Route::has('project.admin.settings.index') ? route('project.admin.settings.index', ['projectCode' => $project?->code ?? 'inbetween_v2']) : url('/admin/settings'));

        return redirect($redirectRoute)
            ->with('error', "Không tìm thấy giao diện cấu hình cho nhóm '{$group}'.");
    }

    public function updateAppearance(Request $request)
    {
        return $this->updateGroup($request, null, 'appearance');
    }

    public function updateGroup(Request $request, $projectCodeOrGroup = null, $group = null)
    {
        $group = $group
            ?: ($request->route('group')
            ?: ($projectCodeOrGroup ?: 'appearance'));

        [$project, $tenantId] = $this->resolveProjectAndTenant($request);

        $inputs = $request->input('settings', $request->except(['_token', '_method', 'active_tab']));

        // Handle file uploads if any
        if ($request->hasFile('settings')) {
            foreach ($request->file('settings') as $key => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('settings', 'public');
                    $inputs[$key] = '/storage/'.$path;
                }
            }
        }

        try {
            \DB::transaction(function () use ($inputs, $group, $project, $tenantId) {
                foreach ($inputs as $key => $value) {
                    $encodedVal = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value;
                    $payload = is_array($value) ? $value : ['value' => $value];

                    // Delete previous setting for tenant/project to ensure clean state
                    \DB::table('settings')
                        ->where('key', $key)
                        ->where(function ($q) use ($project, $tenantId) {
                            if ($tenantId) {
                                $q->where('tenant_id', $tenantId);
                            }
                            if ($project) {
                                $q->orWhere('project_id', $project->id);
                            }
                            if ($tenantId == 3) {
                                $q->orWhere('project_id', 10);
                            }
                        })
                        ->delete();

                    // Insert clean setting record
                    \DB::table('settings')->insert([
                        'key' => $key,
                        'value' => $encodedVal,
                        'payload' => json_encode($payload),
                        'group' => $group,
                        'project_id' => $project?->id,
                        'tenant_id' => $tenantId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });

            SettingsService::getInstance()->clearCache();
            \Cache::flush();

            $activeTab = $request->input('active_tab');
            $redirectUrl = url()->previous();
            if ($activeTab && ! str_contains($redirectUrl, '#')) {
                $redirectUrl .= '#'.ltrim($activeTab, '#');
            }

            return redirect($redirectUrl)->with('alert', [
                'type' => 'success',
                'message' => 'Cập nhật cấu hình thành công!',
            ])->with('success', 'Cập nhật cấu hình thành công!');
        } catch (\Throwable $e) {
            \DB::rollBack();

            return back()->withInput()->with('alert', [
                'type' => 'error',
                'message' => 'Lỗi lưu cấu hình: '.$e->getMessage(),
            ]);
        }
    }

    protected function resolveProjectAndTenant(Request $request): array
    {
        $project = $request->attributes->get('project');
        if (! $project && function_exists('current_project')) {
            $project = current_project();
        }
        if (! $project) {
            $project = Project::first();
        }

        $tenantId = $project?->tenant_id ?? 6;

        return [$project, (int) $tenantId];
    }

    protected function getSettingsMap(?Project $project, int $tenantId, string $group = 'seo'): array
    {
        $settings = Setting::withoutGlobalScopes()->get();

        $resolveValue = function ($s) {
            $val = $s->value;
            if ($val === null && ! empty($s->payload)) {
                if (is_array($s->payload)) {
                    $val = array_key_exists('value', $s->payload) ? $s->payload['value'] : $s->payload;
                } else {
                    $val = $s->payload;
                }
            }

            if (is_array($val) && array_key_exists('value', $val)) {
                $val = $val['value'];
            }

            return $val;
        };

        $map = [];
        foreach ($settings as $s) {
            $map[$s->key] = $resolveValue($s);
        }

        return $map;
    }

    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $res = \App\Services\MailConfigService::sendTestEmail($request->input('email'));

        return response()->json($res, $res['success'] ? 200 : 422);
    }

    public function getSettingsBoxes(): array
    {
        return [
            [
                'id' => 'seo',
                'title' => 'Cài Đặt SEO & Favicon',
                'category' => 'general',
                'category_name' => 'Cấu hình chung',
                'url' => route('admin.settings.seo'),
                'icon' => 'globe',
                'color' => 'blue',
            ],
            [
                'id' => 'modal-box',
                'title' => 'Modal Box Liên Hệ',
                'category' => 'appearance',
                'category_name' => 'Giao diện & Hiển thị',
                'url' => route('admin.settings.modal-box'),
                'icon' => 'window',
                'color' => 'orange',
            ],
            [
                'id' => 'company-profile',
                'title' => 'Hồ Sơ Năng Lực (Heyzine)',
                'category' => 'appearance',
                'category_name' => 'Giao diện & Hiển thị',
                'url' => route('admin.settings.group', 'company-profile'),
                'icon' => 'document-text',
                'color' => 'blue',
            ],
            [
                'id' => 'notifications',
                'title' => 'Cấu Hình Email SMTP',
                'category' => 'system',
                'category_name' => 'Hệ thống & Tích hợp',
                'url' => route('admin.settings.notifications'),
                'icon' => 'mail',
                'color' => 'indigo',
            ],
            [
                'id' => 'contact',
                'title' => 'Thông Tin Liên Hệ',
                'category' => 'general',
                'category_name' => 'Cấu hình chung',
                'url' => route('admin.settings.group', 'contact'),
                'icon' => 'phone',
                'color' => 'emerald',
            ],
            [
                'id' => 'social',
                'title' => 'Mạng Xã Hội',
                'category' => 'general',
                'category_name' => 'Cấu hình chung',
                'url' => route('admin.settings.group', 'social'),
                'icon' => 'share',
                'color' => 'sky',
            ],
            [
                'id' => 'partners',
                'title' => 'Đối Tác',
                'category' => 'general',
                'category_name' => 'Cấu hình chung',
                'url' => route('admin.settings.group', 'partners'),
                'icon' => 'users',
                'color' => 'amber',
            ],
            [
                'id' => 'contact-buttons',
                'title' => 'Nút Liên Hệ Nổi',
                'category' => 'marketing',
                'category_name' => 'Bán hàng & Chuyển đổi',
                'url' => route('admin.settings.group', 'contact-buttons'),
                'icon' => 'chat',
                'color' => 'teal',
            ],
            // [
            //     'id' => 'languages',
            //     'title' => 'Quản Lý Ngôn Ngữ',
            //     'category' => 'appearance',
            //     'category_name' => 'Giao diện & Hiển thị',
            //     'url' => route('admin.settings.group', 'languages'),
            //     'icon' => 'language',
            //     'color' => 'violet',
            // ],
            // [
            //     'id' => 'fonts',
            //     'title' => 'Quản Lý Font Chữ',
            //     'category' => 'appearance',
            //     'category_name' => 'Giao diện & Hiển thị',
            //     'url' => route('admin.settings.group', 'fonts'),
            //     'icon' => 'font',
            //     'color' => 'rose',
            // ],
            // [
            //     'id' => 'watermark',
            //     'title' => 'Đóng Dấu Ảnh (Watermark)',
            //     'category' => 'appearance',
            //     'category_name' => 'Giao diện & Hiển thị',
            //     'url' => route('admin.settings.group', 'watermark'),
            //     'icon' => 'stamp',
            //     'color' => 'amber',
            // ],
            [
                'id' => 'toc',
                'title' => 'Mục Lục Tự Động (TOC)',
                'category' => 'appearance',
                'category_name' => 'Giao diện & Hiển thị',
                'url' => route('admin.settings.group', 'toc'),
                'icon' => 'toc',
                'color' => 'slate',
            ],
            [
                'id' => 'popups',
                'title' => 'Cửa Sổ Bật Lên (Popups)',
                'category' => 'marketing',
                'category_name' => 'Bán hàng & Chuyển đổi',
                'url' => route('admin.settings.group', 'popups'),
                'icon' => 'megaphone',
                'color' => 'red',
            ],
            [
                'id' => 'guarantees',
                'title' => 'Cam Kết & Bảo Hành Sản Phẩm',
                'category' => 'marketing',
                'category_name' => 'Bán hàng & Chuyển đổi',
                'url' => route('admin.settings.group', 'guarantees'),
                'icon' => 'stamp',
                'color' => 'emerald',
            ],
            // [
            //     'id' => 'fake-notifications',
            //     'title' => 'Thông Báo Mua Hàng',
            //     'category' => 'marketing',
            //     'category_name' => 'Bán hàng & Chuyển đổi',
            //     'url' => route('admin.settings.group', 'fake-notifications'),
            //     'icon' => 'bell',
            //     'color' => 'yellow',
            // ],
            // [
            //     'id' => 'custom-css',
            //     'title' => 'Tùy Chỉnh CSS & Code',
            //     'category' => 'system',
            //     'category_name' => 'Hệ thống & Tích hợp',
            //     'url' => route('admin.settings.group', 'custom-css'),
            //     'icon' => 'code',
            //     'color' => 'gray',
            // ],
            // [
            //     'id' => 'payment',
            //     'title' => 'Phương Thức Thanh Toán',
            //     'category' => 'marketing',
            //     'category_name' => 'Bán hàng & Chuyển đổi',
            //     'url' => route('admin.settings.group', 'payment'),
            //     'icon' => 'credit-card',
            //     'color' => 'emerald',
            // ],
            // [
            //     'id' => 'shipping',
            //     'title' => 'Cấu Hình Vận Chuyển',
            //     'category' => 'marketing',
            //     'category_name' => 'Bán hàng & Chuyển đổi',
            //     'url' => route('admin.settings.group', 'shipping'),
            //     'icon' => 'truck',
            //     'color' => 'blue',
            // ],
            // [
            //     'id' => 'forms',
            //     'title' => 'Cấu Hình Biểu Mẫu',
            //     'category' => 'marketing',
            //     'category_name' => 'Bán hàng & Chuyển đổi',
            //     'url' => route('admin.settings.group', 'forms'),
            //     'icon' => 'forms',
            //     'color' => 'purple',
            // ],
            [
                'id' => 'ai',
                'title' => 'Trí Tuệ Nhân Tạo (AI)',
                'category' => 'system',
                'category_name' => 'Hệ thống & Tích hợp',
                'url' => route('admin.settings.group', 'ai'),
                'icon' => 'sparkles',
                'color' => 'fuchsia',
            ],
            [
                'id' => 'analytics',
                'title' => 'Thống Kê & Analytics',
                'category' => 'system',
                'category_name' => 'Hệ thống & Tích hợp',
                'url' => route('admin.settings.group', 'analytics'),
                'icon' => 'chart',
                'color' => 'cyan',
            ],
            [
                'id' => 'appearance',
                'title' => 'Giao Diện & Thiết Kế',
                'category' => 'appearance',
                'category_name' => 'Giao diện & Hiển thị',
                'url' => route('admin.settings.appearance'),
                'icon' => 'palette',
                'color' => 'pink',
            ],
            [
                'id' => 'filter',
                'title' => 'Bộ Lọc Sản Phẩm (Filter)',
                'category' => 'appearance',
                'category_name' => 'Giao diện & Hiển thị',
                'url' => route('admin.settings.group', 'filter'),
                'icon' => 'filter',
                'color' => 'blue',
            ],
            [
                'id' => 'users',
                'title' => 'Tài Khoản Quản Trị',
                'category' => 'system',
                'category_name' => 'Hệ thống & Tích hợp',
                'url' => route('admin.users.index'),
                'icon' => 'users',
                'color' => 'slate',
            ],
        ];
    }

    protected function getDefaultFilterSettings($productCategories, $productBrands): array
    {
        $defaultCatIds = [1, 2, 3];
        $selectedCatIds = [];
        foreach ($productCategories as $cat) {
            if (in_array($cat->id, $defaultCatIds)) {
                $selectedCatIds[] = $cat->id;
            }
        }
        if (empty($selectedCatIds) && $productCategories->isNotEmpty()) {
            $selectedCatIds = $productCategories->take(3)->pluck('id')->toArray();
        }

        return [
            'category_filter' => [
                'enabled' => true,
                'title' => 'LOẠI SẢN PHẨM',
                'type' => 'checkbox', // 'checkbox', 'radio', 'dropdown', 'tree'
                'mode' => 'custom', // 'all_root', 'all', 'custom'
                'selected_categories' => $selectedCatIds,
                'selection_type' => 'checkbox',
                'is_expanded' => true,
            ],
            'brand_filter' => [
                'enabled' => true,
                'title' => 'THƯƠNG HIỆU',
                'type' => 'logo', // 'logo', 'checkbox', 'dropdown'
                'mode' => 'all', // 'all', 'custom'
                'selected_brands' => $productBrands->pluck('id')->toArray(),
                'display_type' => 'logo',
                'is_expanded' => true,
            ],
            'price_filter' => [
                'enabled' => true,
                'title' => 'KHOẢNG GIÁ',
                'type' => 'milestones', // 'milestones' (mốc) or 'slider' (slider range)
                'min_price' => 0,
                'max_price' => 1000000,
                'step' => 50000,
                'allow_contact' => true,
                'is_expanded' => true,
                'ranges' => [
                    ['id' => 'under_200', 'label' => 'Dưới 200.000đ', 'min' => 0, 'max' => 200000, 'type' => 'range'],
                    ['id' => '200_400', 'label' => '200.000đ - 400.000đ', 'min' => 200000, 'max' => 400000, 'type' => 'range'],
                    ['id' => 'over_400', 'label' => 'Trên 400.000đ', 'min' => 400000, 'max' => null, 'type' => 'range'],
                    ['id' => 'contact', 'label' => 'Giá liên hệ dự án', 'min' => null, 'max' => null, 'type' => 'contact'],
                ],
            ],
            'featured_categories' => [1, 6, 7],
        ];
    }

    protected function buildCategoryTreeAndFlatten($categories)
    {
        $grouped = [];
        $byId = [];
        foreach ($categories as $cat) {
            $byId[$cat->id] = $cat;
            $pid = $cat->parent_id ?: 0;
            $grouped[$pid][] = $cat;
        }

        $roots = [];
        foreach ($categories as $cat) {
            $pid = $cat->parent_id ?: 0;
            if (! $pid || ! isset($byId[$pid])) {
                $roots[] = $cat;
            }
        }

        usort($roots, fn ($a, $b) => ($a->order ?? 0) <=> ($b->order ?? 0) ?: ($a->id <=> $b->id));

        $flattened = collect();
        $traverse = function ($items, $depth = 0, $ancestorIds = []) use (&$traverse, &$flattened, &$grouped, &$byId) {
            $total = count($items);
            foreach ($items as $idx => $item) {
                $item->tree_level = $depth;
                $item->is_last_child = ($idx === $total - 1);
                $children = $grouped[$item->id] ?? [];
                usort($children, fn ($a, $b) => ($a->order ?? 0) <=> ($b->order ?? 0) ?: ($a->id <=> $b->id));

                $item->children_list = $children;
                $item->children_count = count($children);
                $item->ancestor_ids = $ancestorIds;

                if ($item->parent_id && isset($byId[$item->parent_id])) {
                    $item->parent_name = $byId[$item->parent_id]->name;
                } else {
                    $item->parent_name = null;
                }

                $flattened->push($item);

                if (! empty($children)) {
                    $nextAncestors = array_merge($ancestorIds, [$item->id]);
                    $traverse($children, $depth + 1, $nextAncestors);
                }
            }
        };

        $traverse($roots, 0, []);

        return $flattened;
    }
}
