<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SyncEhenhoToHostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ehenho:sync-host-db {--sql=sync_ehenho_to_host.sql : Path to delta SQL file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Ehenho dating schema, master provinces, project configuration and migrations into host database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Ehenho Host Database Synchronization...');

        $sqlPath = base_path((string) $this->option('sql'));
        if (File::exists($sqlPath)) {
            $this->info("Reading SQL delta file: {$sqlPath}");
            $sql = File::get($sqlPath);

            try {
                DB::unprepared($sql);
                $this->info('✔ Successfully synchronized Ehenho tables, provinces, project and migrations via SQL!');
            } catch (\Throwable $e) {
                $this->warn('SQL execution notice: '.$e->getMessage().'. Falling back to Laravel migrations...');
                $this->call('migrate', ['--force' => true]);
                $this->call('db:seed', ['--class' => 'EhenhoMasterSeeder', '--force' => true]);
            }
        } else {
            $this->info('SQL file not present on host. Running native idempotent migrations and seeder...');
            $this->call('migrate', ['--force' => true]);
            $this->call('db:seed', ['--class' => 'EhenhoMasterSeeder', '--force' => true]);
        }

        // Verify tables
        $tables = ['provinces', 'profiles', 'conversations', 'messages', 'social_connections'];
        $this->table(
            ['Table', 'Status', 'Row Count'],
            array_map(function ($table) {
                try {
                    $count = DB::table($table)->count();

                    return [$table, 'EXISTS', $count];
                } catch (\Throwable) {
                    return [$table, 'MISSING', 0];
                }
            }, $tables)
        );

        $project = DB::table('projects')->where('code', 'ehenho')->first();
        if ($project) {
            $this->info("✔ Project 'ehenho' is verified in projects table (ID: {$project->id}, domain: {$project->external_domain})");
        } else {
            $this->warn("⚠ Project 'ehenho' not found in projects table.");
        }

        return self::SUCCESS;
    }
}
