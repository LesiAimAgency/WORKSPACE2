<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\User;
use App\Services\ViettinmartDeployService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class DeployViettinmartCommand extends Command
{
    protected $signature = 'project:deploy-vtm 
                            {project_id? : ID or Code of the project to deploy Viettinmart template to (e.g. viettinmart-eco)} 
                            {--tenant_id= : Optional tenant ID}';

    protected $description = 'Deploy and configure a complete Viettinmart E-commerce solution (1-Click VTM) to a project';

    public function handle(ViettinmartDeployService $deployService): int
    {
        $projectId = $this->argument('project_id');
        $tenantId = $this->option('tenant_id') ? (int) $this->option('tenant_id') : null;

        if (! $projectId) {
            $project = Project::where('code', 'viettinmart-eco')
                ->orWhere('code', 'viettinmart')
                ->orWhere('tenant_id', 3)
                ->orWhere('name', 'like', '%Viettinmart%')
                ->first();

            if (! $project) {
                $this->info('Chưa có dự án Viettinmart trong hệ thống. Đang tự động tạo dự án mới: Viettinmart (code: viettinmart-eco)...');
                $baseUrl = config('app.url', 'http://127.0.0.1:8000');
                $adminId = User::where('role', 'superadmin')->value('id') ?? User::first()?->id ?? 1;

                $project = Project::create([
                    'name' => 'Viettinmart E-commerce',
                    'code' => 'viettinmart-eco',
                    'subdomain' => rtrim($baseUrl, '/').'/viettinmart-eco',
                    'client_name' => 'VietTin Mart',
                    'department_id' => 2,
                    'status' => 'active',
                    'project_type' => 'website',
                    'admin_id' => $adminId,
                    'created_by' => $adminId,
                    'cms_features' => ['commerce', 'product_listing', 'blog', 'contact', 'gallery', 'agent'],
                ]);
                $this->info("✓ Đã tạo thành công dự án mới với ID: {$project->id}");
            }
        } else {
            $project = is_numeric($projectId) ? Project::find($projectId) : Project::where('code', $projectId)->first();

            if (! $project && ! is_numeric($projectId)) {
                $code = Str::slug($projectId);
                $this->info("Đang tạo dự án mới với mã: {$code}...");
                $baseUrl = config('app.url', 'http://127.0.0.1:8000');
                $adminId = User::where('role', 'superadmin')->value('id') ?? User::first()?->id ?? 1;

                $project = Project::create([
                    'name' => 'Viettinmart - '.ucfirst($code),
                    'code' => $code,
                    'subdomain' => rtrim($baseUrl, '/').'/'.$code,
                    'client_name' => 'VietTin Mart',
                    'department_id' => 2,
                    'status' => 'active',
                    'project_type' => 'website',
                    'admin_id' => $adminId,
                    'created_by' => $adminId,
                    'cms_features' => ['commerce', 'product_listing', 'blog', 'contact', 'gallery', 'agent'],
                ]);
                $this->info("✓ Đã tạo thành công dự án mới với ID: {$project->id}");
            }
        }

        if (! $project) {
            $this->error('Không tìm thấy hoặc không thể tạo dự án với ID hoặc Code đã chỉ định!');

            return self::FAILURE;
        }

        $this->info("Bắt đầu triển khai mẫu Viettinmart cho dự án: {$project->name} ({$project->code}, ID: {$project->id})...");

        // Auto-migrate to guarantee all tables exist (non-destructive)
        try {
            $this->line('• Kiểm tra cấu trúc database (migrating)...');
            Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {
            $this->warn('Migration warning: '.$e->getMessage());
        }

        try {
            $result = $deployService->deploy($project, $tenantId);

            $this->newLine();
            $this->info('==================================================');
            $this->info('TRIỂN KHAI VIETTINMART (VTM) THÀNH CÔNG!');
            $this->info('==================================================');
            $this->line("• Tên dự án:        <comment>{$result['project']->name}</comment>");
            $this->line("• Mã dự án (Code):  <comment>{$result['project']->code}</comment>");
            $this->line("• Tenant ID:        <comment>{$result['tenant_id']}</comment>");
            $this->line("• CMS Username:     <info>{$result['admin_username']}</info>");
            $this->line("• CMS Password:     <info>{$result['admin_password']}</info>");
            $this->line("• Frontend URL:     <href={$result['frontend_url']}>{$result['frontend_url']}</>");
            $this->line("• CMS Admin URL:    <href={$result['admin_url']}>{$result['admin_url']}</>");
            $this->line("• Cài đặt ngôn ngữ: <href={$result['languages_url']}>{$result['languages_url']}</>");
            $this->info('==================================================');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Lỗi trong quá trình triển khai VTM: {$e->getMessage()}");
            $this->error($e->getTraceAsString());

            return self::FAILURE;
        }
    }
}
