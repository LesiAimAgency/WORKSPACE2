<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectPermission;
use App\Models\ProjectSetting;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\WkcomputerMenuSeeder;
use Database\Seeders\WkcomputerProductsSeeder;
use Database\Seeders\WkcomputerSettingsSeeder;
use Database\Seeders\WkcomputerWidgetsSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WkcomputerDeployService
{
    /**
     * Deploy complete WKComputer Gaming & PC solution into a Project.
     *
     * @return array<string, mixed>
     */
    public function deploy(?Project $project = null, ?int $tenantId = null): array
    {
        Log::info('Starting 1-Click WKComputer Deployment...');

        // 0. Ensure schema migrations
        try {
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            Log::warning('Auto-migration warning: '.$e->getMessage());
        }

        // 1. Ensure Tenant
        $tenantCode = 'wkcomputer';
        $tenant = Tenant::where('code', $tenantCode)
            ->orWhere('domain', $tenantCode)
            ->first();

        if (! $tenant) {
            $tenant = Tenant::create([
                'name' => 'WKComputer',
                'code' => $tenantCode,
                'domain' => $tenantCode,
                'database_name' => 'core',
                'settings' => ['theme' => 'wkcomputerdemo'],
                'status' => 'active',
            ]);
        }
        $tenantId = $tenant->id;

        // 2. Ensure Project
        if (! $project) {
            $project = Project::where('code', 'wkcomputer')->first();
            if (! $project) {
                $project = Project::create([
                    'name' => 'WKComputer - Gaming PC & Gear',
                    'code' => 'wkcomputer',
                    'subdomain' => 'wkcomputer',
                    'project_type' => 'website',
                    'department_id' => 2,
                    'status' => 'active',
                    'initialized_at' => now(),
                    'cms_features' => ['commerce', 'product_listing', 'blog', 'contact', 'gallery', 'pc_builder'],
                ]);
            }
        }

        $project->project_type = 'website';
        $project->status = 'active';
        $project->initialized_at = now();
        $project->cms_features = ['commerce', 'product_listing', 'blog', 'contact', 'gallery', 'pc_builder'];
        $project->save();

        // 3. Configure Theme & Core Modules
        $this->enableCoreModules($project->id);

        // 4. Setup CMS Admin User
        $adminInfo = $this->setupAdminUser($project, $tenantId);

        // 5. Run Seeders
        try {
            (new WkcomputerSettingsSeeder)->run($project->id, $tenantId);
        } catch (\Throwable $e) {
            Log::warning('WkcomputerSettingsSeeder warning: '.$e->getMessage());
        }

        try {
            (new WkcomputerProductsSeeder)->run($project->id, $tenantId);
        } catch (\Throwable $e) {
            Log::warning('WkcomputerProductsSeeder warning: '.$e->getMessage());
        }

        try {
            (new WkcomputerMenuSeeder)->run($project->id, $tenantId);
        } catch (\Throwable $e) {
            Log::warning('WkcomputerMenuSeeder warning: '.$e->getMessage());
        }

        try {
            (new WkcomputerWidgetsSeeder)->run($project->id, $tenantId);
        } catch (\Throwable $e) {
            Log::warning('WkcomputerWidgetsSeeder warning: '.$e->getMessage());
        }

        Log::info("WKComputer deployment completed successfully for Project ID {$project->id}");

        return [
            'success' => true,
            'project_id' => $project->id,
            'project_code' => $project->code,
            'theme' => 'wkcomputerdemo',
            'admin_url' => url("/{$project->code}/admin"),
            'site_url' => url("/{$project->code}"),
            'admin_username' => $adminInfo['username'],
            'admin_password' => $adminInfo['password'],
        ];
    }

    /**
     * Setup CMS Admin account.
     */
    protected function setupAdminUser(Project $project, int $tenantId): array
    {
        $username = $project->code;
        $password = 'admin123';
        $email = strtolower($project->code).'@wkcomputer.local';

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
     * Enable all 21 Core CMS modules for this project.
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

        try {
            $defaultPermissions = ProjectPermission::getDefaultPermissions();
            $project = Project::find($projectId);
            if ($project) {
                foreach ($defaultPermissions as $mod => $perms) {
                    $project->permissions()->updateOrCreate(['module' => $mod], $perms);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Could not set permissions: '.$e->getMessage());
        }
    }
}
