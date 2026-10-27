<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\User;
use App\Services\WkcomputerDeployService;
use Illuminate\Console\Command;

class DeployWkcomputerCommand extends Command
{
    protected $signature = 'project:deploy-wkcomputer 
                            {project_id? : ID or Code of the project (default: wkcomputer)} 
                            {--tenant_id= : Optional tenant ID}';

    protected $description = 'Deploy and configure a complete WKComputer Gaming & PC solution into a project';

    public function handle(WkcomputerDeployService $deployService): int
    {
        $projectArg = $this->argument('project_id');
        $tenantId = $this->option('tenant_id') ? (int) $this->option('tenant_id') : null;

        $project = null;
        if ($projectArg) {
            $project = is_numeric($projectArg)
                ? Project::find($projectArg)
                : Project::where('code', $projectArg)->first();
        }

        if (! $project) {
            $project = Project::where('code', 'wkcomputer')->first();
        }

        if (! $project) {
            $this->info('Creating new Project: WKComputer Gaming & PC (code: wkcomputer)...');
            $adminId = User::where('role', 'superadmin')->value('id') ?? User::first()?->id ?? 1;

            $project = Project::create([
                'name' => 'WKComputer Gaming & PC',
                'code' => 'wkcomputer',
                'subdomain' => 'wkcomputer',
                'department_id' => 2,
                'status' => 'active',
                'project_type' => 'website',
                'admin_id' => $adminId,
                'created_by' => $adminId,
                'cms_features' => ['commerce', 'product_listing', 'blog', 'contact', 'gallery', 'pc_builder'],
            ]);
            $this->info("✓ Created project with ID: {$project->id}");
        }

        $this->info("Deploying WKComputer into Project ID: {$project->id} ({$project->name})...");

        $result = $deployService->deploy($project, $tenantId);

        $this->newLine();
        $this->info('====================================================');
        $this->info('  WKCOMPUTER DEPLOYMENT SUCCESSFUL!                 ');
        $this->info('====================================================');
        $this->table(
            ['Key', 'Value'],
            [
                ['Project ID', $result['project_id']],
                ['Project Code', $result['project_code']],
                ['Theme', $result['theme']],
                ['Site URL', $result['site_url']],
                ['Admin URL', $result['admin_url']],
                ['CMS Username', $result['admin_username']],
                ['CMS Password', $result['admin_password']],
            ]
        );
        $this->newLine();

        return self::SUCCESS;
    }
}
