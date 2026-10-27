<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\ViettinmartDataSyncService;
use Illuminate\Console\Command;

class SyncViettinmartDataCommand extends Command
{
    protected $signature = 'vtm:sync-data {project_id? : Target Project ID (defaults to viettinmart-eco project)} {--tenant_id= : Optional Tenant ID}';

    protected $description = 'Synchronize and auto-heal Viettinmart data across project IDs and tenant IDs';

    public function handle(ViettinmartDataSyncService $syncService): int
    {
        $projectId = $this->argument('project_id');
        $tenantId = $this->option('tenant_id') ? (int) $this->option('tenant_id') : null;

        if (! $projectId) {
            $project = Project::where('code', 'viettinmart-eco')->orWhere('code', 'viettinmart')->first();
            $projectId = $project?->id ?? 10;
        } else {
            $projectId = is_numeric($projectId) ? (int) $projectId : Project::where('code', $projectId)->value('id') ?? (int) $projectId;
        }

        $this->info("Starting Viettinmart data synchronization for Project ID: {$projectId}...");

        $result = $syncService->syncProjectId($projectId, $tenantId);

        $this->info("✓ Target Project ID: {$result['project_id']}");
        $this->info("✓ Target Tenant ID: {$result['tenant_id']}");
        $this->info("✓ Active Widgets: {$result['widget_count']}");

        if (! empty($result['remapped_tables'])) {
            $this->info('✓ Remapped tables:');
            foreach ($result['remapped_tables'] as $tbl => $cnt) {
                $this->line("  - {$tbl}: {$cnt} rows");
            }
        }

        foreach ($result['log'] as $item) {
            $this->comment("  > {$item}");
        }

        $this->info('✓ Viettinmart data is fully synchronized and auto-healed!');

        return 0;
    }
}
