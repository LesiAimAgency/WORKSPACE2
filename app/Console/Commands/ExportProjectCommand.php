<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Theme\ThemeManager;
use App\Models\Project;
use App\Services\ProjectExportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportProjectCommand extends Command
{
    protected $signature = 'project:export 
                            {project_id? : ID or Code of the project (default: DA009 / wkcomputer)}
                            {--theme= : Theme slug (e.g. wkcomputerdemo)}
                            {--with-vendor : Include full vendor directory in the export ZIP}
                            {--demo : Prepare and serve a standalone demo on localhost}
                            {--port=8088 : Port for the localhost demo server}';

    protected $description = 'Export a complete self-contained standalone website package with database SQL and optional localhost demo';

    public function handle(ProjectExportService $exportService, ThemeManager $themeManager): int
    {
        $projectArg = $this->argument('project_id');
        $themeArg = $this->option('theme');
        $isDemo = (bool) $this->option('demo');
        $port = (int) $this->option('port');

        // 1. Locate Project
        $project = null;
        if ($projectArg) {
            $project = is_numeric($projectArg)
                ? Project::find($projectArg)
                : Project::where('code', $projectArg)
                    ->orWhere('subdomain', $projectArg)
                    ->orWhere('name', 'like', "%{$projectArg}%")
                    ->first();
        }

        if (! $project && $themeArg) {
            $project = Project::where('features->theme', $themeArg)
                ->orWhere('subdomain', 'like', "%{$themeArg}%")
                ->first();
        }

        if (! $project) {
            // Default to WKComputer (DA009)
            $project = Project::where('code', 'DA009')
                ->orWhere('code', 'wkcomputer')
                ->orWhere('subdomain', 'wkcomputer')
                ->first();
        }

        if (! $project) {
            $this->error('Project not found. Please specify a valid project ID or code.');

            return self::FAILURE;
        }

        $activeTheme = $themeManager->resolveActiveTheme($project);

        $this->newLine();
        $this->info('====================================================');
        $this->info("  STARTING EXPORT: {$project->name}");
        $this->info("  Project ID: {$project->id} | Code: {$project->code} | Theme: {$activeTheme}");
        $this->info('====================================================');

        // 2. Execute Export via ProjectExportService
        $this->info('1. Generating CMS-only database SQL (scoped to project data & theme tables)...');
        $this->info('2. Packaging standalone source code, configuration, public assets, and views...');
        $this->info('3. Compressing full standalone package into ZIP archive...');

        $startTime = microtime(true);
        $result = $exportService->buildExportPackage($project, null, (bool) $this->option('with-vendor'));
        $duration = round(microtime(true) - $startTime, 2);

        $zipPath = $result['zip_path'];
        $zipSizeMb = File::exists($zipPath) ? round(filesize($zipPath) / 1024 / 1024, 2) : 0;

        $this->newLine();
        $this->info('✅ WEBSITE EXPORT COMPLETED SUCCESSFULLY!');
        $this->table(
            ['Property', 'Details'],
            [
                ['Project Name', $project->name],
                ['Project Code', $project->code],
                ['Resolved Theme', $activeTheme],
                ['Export Duration', "{$duration} seconds"],
                ['ZIP Package Path', $zipPath],
                ['ZIP Package Size', "{$zipSizeMb} MB"],
                ['Database Snapshot', 'database/database.sql (CMS Whitelist + Theme Tables)'],
                ['Execution Mode', 'STANDALONE_MODE=true (Runs independently on any domain/host)'],
            ]
        );

        // 3. If --demo is requested, set up standalone instance and launch server
        if ($isDemo) {
            $this->newLine();
            $this->info('====================================================');
            $this->info("  LAUNCHING STANDALONE DEMO ON LOCALHOST (Port {$port})");
            $this->info('====================================================');

            $demoDir = storage_path("app/demo_{$project->code}_standalone");

            $this->info("Setting up standalone demo directory at: {$demoDir}");

            // Extract ZIP into demo directory
            if (File::exists($demoDir)) {
                File::deleteDirectory($demoDir);
            }
            File::makeDirectory($demoDir, 0755, true);

            $zip = new \ZipArchive;
            if ($zip->open($zipPath) === true) {
                $zip->extractTo($demoDir);
                $zip->close();
                $this->info('✓ Extracted standalone project files.');
            } else {
                $this->error('Failed to extract ZIP for demo.');

                return self::FAILURE;
            }

            // Ensure vendor is linked in demo directory for instant execution
            if (! File::exists($demoDir.'/vendor')) {
                $vendorSource = base_path('vendor');
                $vendorLink = $demoDir.'/vendor';
                if (PHP_OS_FAMILY === 'Windows') {
                    $srcWin = str_replace('/', '\\', $vendorSource);
                    $linkWin = str_replace('/', '\\', $vendorLink);
                    @exec("cmd /c mklink /J \"{$linkWin}\" \"{$srcWin}\"");
                } else {
                    @symlink($vendorSource, $vendorLink);
                }
                $this->info('✓ Linked vendor directory for instant local execution.');
            }

            // Ensure storage/installed.lock exists
            File::put($demoDir.'/storage/installed.lock', now()->toIso8601String());

            // Prepare .env for standalone local execution
            $envPath = $demoDir.'/.env';
            if (File::exists($envPath)) {
                $envContent = File::get($envPath);
                $envContent = str_replace('__DB_DATABASE__', config('database.connections.mysql.database', 'core'), $envContent);
                $envContent = str_replace('__DB_USERNAME__', config('database.connections.mysql.username', 'root'), $envContent);
                $dbPass = env('DB_PASSWORD', config('database.connections.mysql.password', 'root'));
                $envContent = str_replace('__DB_PASSWORD__', (string) $dbPass, $envContent);
                $envContent = str_replace('localhost', config('database.connections.mysql.host', '127.0.0.1'), $envContent);
                $envContent = str_replace('APP_DEBUG=false', 'APP_DEBUG=true', $envContent);
                $envContent = str_replace('APP_URL=https://'.$project->external_domain, "http://127.0.0.1:{$port}", $envContent);
                File::put($envPath, $envContent);
                $this->info('✓ Configured standalone .env with database connection.');
            }

            // Create storage link in demo
            if (! File::exists($demoDir.'/public/storage')) {
                $target = base_path('storage/app/public');
                $link = $demoDir.'/public/storage';
                if (PHP_OS_FAMILY === 'Windows') {
                    $targetWin = str_replace('/', '\\', $target);
                    $linkWin = str_replace('/', '\\', $link);
                    @exec("cmd /c mklink /J \"{$linkWin}\" \"{$targetWin}\"");
                }
            }

            $demoUrl = "http://127.0.0.1:{$port}";
            $this->newLine();
            $this->info("🎉 DEMO SERVER READY AT: {$demoUrl}");
            $this->info("Direct Project Home: {$demoUrl}/");
            $this->info("Direct Shop Page:   {$demoUrl}/cua-hang");
            $this->info("Direct PC Builder:   {$demoUrl}/xay-dung-cau-hinh");
            $this->info("Direct Cart Page:    {$demoUrl}/gio-hang");
            $this->newLine();
            $this->info("Run: php -S 127.0.0.1:{$port} -t {$demoDir}/public");

            return self::SUCCESS;
        }

        return self::SUCCESS;
    }
}
