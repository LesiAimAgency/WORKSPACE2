<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectPermission;
use App\Models\ProjectSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Widget;
use Database\Seeders\ViettinmartEcommerceEnhanceSeeder;
use Database\Seeders\ViettinmartFooterWidgetSeeder;
use Database\Seeders\ViettinmartFormTemplateSeeder;
use Database\Seeders\ViettinmartMenuSeeder;
use Database\Seeders\ViettinmartProductEnTranslationSeeder;
use Database\Seeders\ViettinmartProductsSeeder;
use Database\Seeders\ViettinmartSettingsSeeder;
use Database\Seeders\ViettinmartWidgetsSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ViettinmartDeployService
{
    /**
     * Deploy complete Viettinmart E-commerce solution into a Project.
     *
     * @return array<string, mixed>
     */
    public function deploy(Project $project, ?int $tenantId = null): array
    {
        Log::info("Starting 1-Click VTM Deployment for Project: {$project->code} (ID: {$project->id})");

        // 0. Ensure schema migrations are up-to-date (non-destructive)
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            Log::warning('Auto-migration during VTM deploy warning: '.$e->getMessage());
        }

        // 1. Ensure Tenant
        if (! $tenantId) {
            $tenant = Tenant::where('code', $project->code)
                ->orWhere('domain', $project->code)
                ->first();

            if (! $tenant) {
                $tenant = Tenant::create([
                    'name' => $project->name ?: 'Viettinmart',
                    'code' => $project->code,
                    'domain' => $project->code,
                    'database_name' => 'core',
                    'settings' => ['theme' => 'viettinmartdemo'],
                    'status' => 'active',
                ]);
            }
            $tenantId = $tenant->id;
        }

        // 2. Configure Project
        $project->project_type = 'website';
        $project->department_id = $project->department_id ?: 2;
        $project->status = 'active';
        $project->initialized_at = now();
        $project->cms_features = ['commerce', 'product_listing', 'blog', 'contact', 'gallery', 'agent'];
        $project->save();

        // 3. Configure Theme & Settings
        $this->configureThemeSettings($project->id, $tenantId, $project->name);

        // 4. Enable All 21 Core CMS Modules
        $this->enableCoreModules($project->id);

        // 5. Setup CMS Admin User
        $adminInfo = $this->setupAdminUser($project, $tenantId);

        // 6. Run Seeders for Widgets, Menus, Forms
        if (Schema::hasTable('widgets')) {
            try {
                Widget::where('project_id', $project->id)->where('area', 'homepage')->delete();
            } catch (\Throwable $e) {
                Log::warning('Could not clean existing widgets: '.$e->getMessage());
            }
        }

        if (Schema::hasTable('form_templates')) {
            try {
                (new ViettinmartFormTemplateSeeder)->run($project->id, $tenantId);
            } catch (\Throwable $e) {
                Log::warning('ViettinmartFormTemplateSeeder warning: '.$e->getMessage());
            }
        }

        try {
            (new ViettinmartWidgetsSeeder)->run($project->id, $tenantId);
        } catch (\Throwable $e) {
            Log::warning('ViettinmartWidgetsSeeder warning: '.$e->getMessage());
        }

        try {
            (new ViettinmartFooterWidgetSeeder)->run($project->id, $tenantId);
        } catch (\Throwable $e) {
            Log::warning('ViettinmartFooterWidgetSeeder warning: '.$e->getMessage());
        }

        try {
            (new ViettinmartMenuSeeder)->run($project->id, $tenantId);
        } catch (\Throwable $e) {
            Log::warning('ViettinmartMenuSeeder warning: '.$e->getMessage());
        }

        // 7. Seed Authentic Viettinmart Catalog (62 Products, 22 Taxonomies, Categories, Posts)
        try {
            (new ViettinmartProductsSeeder)->run($project->id, $tenantId);
        } catch (\Throwable $e) {
            Log::warning('ViettinmartProductsSeeder warning: '.$e->getMessage());
        }

        // 7b. Seed Settings, Theme Options & Ecommerce Enhancements
        try {
            (new ViettinmartSettingsSeeder)->run($project->id, $tenantId);
        } catch (\Throwable $e) {
            Log::warning('ViettinmartSettingsSeeder warning: '.$e->getMessage());
        }

        try {
            (new ViettinmartEcommerceEnhanceSeeder)->run($project->id);
        } catch (\Throwable $e) {
            Log::warning('ViettinmartEcommerceEnhanceSeeder warning: '.$e->getMessage());
        }

        // 8. Seed English Product Translations & UI Translations
        try {
            (new ViettinmartProductEnTranslationSeeder)->run($project->id);
        } catch (\Throwable $e) {
            Log::warning('ViettinmartProductEnTranslationSeeder warning: '.$e->getMessage());
        }

        try {
            $this->seedUiTranslations($project->id, $tenantId);
        } catch (\Throwable $e) {
            Log::warning('seedUiTranslations warning: '.$e->getMessage());
        }

        // 9. Clear cache
        try {
            Artisan::call('cache:clear');
        } catch (\Exception $e) {
            Log::warning('Could not clear cache: '.$e->getMessage());
        }

        Log::info("Completed 1-Click VTM Deployment for Project: {$project->code}");

        return [
            'success' => true,
            'project' => $project,
            'tenant_id' => $tenantId,
            'admin_username' => $adminInfo['username'],
            'admin_password' => $adminInfo['password'],
            'frontend_url' => url('/'.$project->code),
            'admin_url' => url('/'.$project->code.'/admin'),
            'languages_url' => url('/'.$project->code.'/admin/settings/languages'),
        ];
    }

    /**
     * Enable all 21 Core CMS modules.
     */
    protected function enableCoreModules(int $projectId): void
    {
        $allModules = [
            'settings.contact', 'settings.notifications', 'settings.fonts',
            'settings.logs', 'settings.analytics', 'settings.watermark',
            'settings.toc', 'settings.social', 'settings.payment',
            'settings.shipping', 'settings.ai', 'settings.reviews',
            'settings.forms', 'settings.contact_buttons', 'settings.redirects',
            'settings.seo', 'settings.popups', 'settings.permissions',
            'settings.fake_notifications', 'settings.orders', 'settings.languages',
        ];

        foreach ($allModules as $module) {
            ProjectSetting::set($projectId, $module, '1');
        }

        // Default project permissions
        try {
            $defaultPermissions = ProjectPermission::getDefaultPermissions();
            $project = Project::find($projectId);
            if ($project) {
                foreach ($defaultPermissions as $mod => $perms) {
                    $project->permissions()->updateOrCreate(['module' => $mod], $perms);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not set permissions: '.$e->getMessage());
        }
    }

    /**
     * Setup CMS Admin account.
     *
     * @return array{username: string, password: string}
     */
    protected function setupAdminUser(Project $project, int $tenantId): array
    {
        $username = $project->code;
        $password = 'admin123';
        $email = strtolower($project->code).'@viettinmart.local';

        // Check if user already exists
        $user = User::where('username', $username)->first();
        if ($user) {
            $user->role = 'cms';
            $user->level = 2;
            $user->tenant_id = $tenantId;
            $projectIds = is_array($user->project_ids) ? $user->project_ids : (json_decode($user->project_ids ?? '[]', true) ?: []);
            if (! in_array($project->id, $projectIds)) {
                $projectIds[] = $project->id;
            }
            $user->project_ids = $projectIds;
            $user->save();
        } else {
            User::create([
                'name' => 'CMS Admin - '.$project->name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'cms',
                'level' => 2,
                'tenant_id' => $tenantId,
                'project_ids' => [$project->id],
                'email_verified_at' => now(),
            ]);
        }

        $project->update([
            'project_admin_username' => $username,
            'project_admin_password' => Hash::make($password),
            'project_admin_password_plain' => encrypt($password),
            'password_updated_at' => now(),
        ]);

        return [
            'username' => $username,
            'password' => $password,
        ];
    }

    /**
     * Configure default Viettinmart theme options and settings.
     */
    protected function configureThemeSettings(int $projectId, int $tenantId, string $projectName): void
    {
        $defaultSettings = [
            'theme' => 'viettinmartdemo',
            'site_name' => $projectName ?: 'VietTinMart',
            'site_copyright' => '©2026 VNEXT GLOBAL TECH',
            'site_address' => '123 Đường Nguyễn Trãi, Quận 1, TP. HCM',
            'hotline' => '1900 1234',
            'facebook_url' => 'https://facebook.com/viettinmart',
            'instagram_url' => 'https://instagram.com/viettinmart',
            'youtube_url' => 'https://youtube.com/viettinmart',
            'company_legal_name' => 'CÔNG TY TNHH VIỆT TÍN MART',
            'company_license' => 'GPKD số 0319379713 do Sở Tài Chính TP Hồ Chí Minh cấp ngày 26/01/2026',
            'company_notice' => 'Website đang chạy thử, chờ cấp phép của Bộ Công Thương',
            'contact_hours' => '8:00 sáng – 6:00 chiều',
            'footer_company_title' => 'Giới thiệu về công ty',
            'footer_phone_label' => 'Số Điện Thoại',
            'footer_address_label' => 'Địa chỉ Công ty',
            'footer_hours_label' => 'Cả tuần',
            'contact_email_display' => 'phuong.pham@viettinmart.vn',
            'contact_email_2' => 'daniel.ng@viettinmart.vn',
            'color_primary' => '#31a7db',
            'color_secondary' => '#ffffff',
            'color_body' => '#6e777d',
            'color_heading_1' => '#2c3c28',
            'color_success' => '#3eb75e',
            'color_danger' => '#31a7db',
            'color_warning' => '#ff8f3c',
            'color_info' => '#000000',
            'font_main' => "'Barlow', sans-serif",
            'font_heading' => "'Barlow', sans-serif",
            'p_regular' => '400',
            'p_semi_bold' => '600',
            'p_bold' => '700',
            'font_size_b1' => '16px',
            'line_height_b1' => '1.3',
            'topbar_welcome_text' => 'Chào mừng bạn đến với VietTinMart!',
            'topbar_right_text' => 'Cần trợ giúp? Gọi cho chúng tôi: 1900 1234',
            'default_language' => 'vi',
            'multilingual_enabled' => '1',
            'languages' => json_encode([
                ['code' => 'vi', 'name' => 'Tiếng Việt', 'flag' => '🇻🇳', 'is_default' => true],
                ['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => false],
                ['code' => 'zh', 'name' => '中文', 'flag' => '🇨🇳', 'is_default' => false],
            ]),
            'icon_cart' => 'fa-sharp fa-regular fa-cart-shopping',
            'icon_user' => 'fa-light fa-user',
            'icon_wishlist' => 'fa-regular fa-heart',
            'icon_search' => 'fa-light fa-magnifying-glass',
            'icon_category' => 'fa-solid fa-bars',
            'icon_truck' => 'fa-solid fa-truck-fast',
            'icon_money' => 'fa-solid fa-dollar-sign',
            'icon_refresh' => 'fa-solid fa-rotate-left',
            'icon_shield' => 'fa-solid fa-shield-halved',
            'icon_headset' => 'fa-light fa-headset',
            'image_banner_default' => 'theme/images/banner/01.webp',
            'image_promo_default' => 'theme/images/feature/01.jpg',
        ];

        foreach ($defaultSettings as $key => $val) {
            DB::table('settings')
                ->where('key', $key)
                ->where(function ($q) use ($tenantId, $projectId) {
                    $q->where('tenant_id', $tenantId)
                        ->orWhere('project_id', $projectId);
                })
                ->delete();

            DB::table('settings')->insert([
                'key' => $key,
                'value' => $val,
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Pre-seed UI translations for the project into settings table.
     */
    protected function seedUiTranslations(int $projectId, int $tenantId): void
    {
        $frontendVi = resource_path('lang/vi/frontend.php');
        if (! file_exists($frontendVi)) {
            $frontendVi = base_path('lang/vi/frontend.php');
        }

        $existingSetting = DB::table('settings')
            ->where('project_id', $projectId)
            ->where('key', 'translations')
            ->value('value');

        $translations = [];
        if ($existingSetting) {
            $decoded = json_decode($existingSetting, true);
            if (is_array($decoded) && ! empty($decoded)) {
                $translations = $decoded;
            }
        }

        // If translations are empty, copy from project 10 or from frontend.php
        if (empty($translations)) {
            $vtmTranslations = DB::table('settings')
                ->where('key', 'translations')
                ->where(function ($q) {
                    $q->where('tenant_id', 3)
                        ->orWhere('project_id', 10);
                })
                ->value('value');

            if ($vtmTranslations) {
                $decoded = json_decode($vtmTranslations, true);
                if (is_array($decoded) && ! empty($decoded)) {
                    $translations = $decoded;
                }
            }
        }

        if (empty($translations) && file_exists($frontendVi)) {
            $strings = include $frontendVi;
            if (is_array($strings)) {
                foreach ($strings as $k => $v) {
                    if (is_string($v) && ! empty($v)) {
                        $translations[] = [
                            'key' => $k,
                            'values' => [
                                'vi' => $v,
                                'en' => '',
                                'zh' => '',
                            ],
                        ];
                    }
                }
            }
        }

        if (! empty($translations)) {
            DB::table('settings')
                ->where('key', 'translations')
                ->where(function ($q) use ($tenantId, $projectId) {
                    $q->where('tenant_id', $tenantId)
                        ->orWhere('project_id', $projectId);
                })
                ->delete();

            DB::table('settings')->insert([
                'key' => 'translations',
                'value' => json_encode($translations),
                'project_id' => $projectId,
                'tenant_id' => $tenantId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
