<?php

declare(strict_types=1);

namespace App\Services\Tenancy;

use App\Models\Project;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseResolver
{
    private string $originalDefaultConnection = 'mysql';

    public function __construct()
    {
        $this->originalDefaultConnection = config('database.default', 'mysql');
    }

    /**
     * Connect to project database (Isolated DB or Shared Scoped DB)
     */
    public function connect(Project $project): string
    {
        $this->originalDefaultConnection = config('database.default', 'mysql');

        $dbName = null;
        if (isset($project->deployment_config) && is_array($project->deployment_config)) {
            $dbName = $project->deployment_config['database']['name'] ?? null;
        }

        $defaultConnConfig = config("database.connections.{$this->originalDefaultConnection}", []);

        // Case 1: Project has an isolated database name (and it differs from central DB, non-sqlite testing)
        if ($dbName && ($defaultConnConfig['driver'] ?? '') !== 'sqlite' && $dbName !== ($defaultConnConfig['database'] ?? 'core')) {
            $projectDbConfig = array_merge($defaultConnConfig, [
                'database' => $dbName,
                'username' => $project->deployment_config['database']['user'] ?? ($defaultConnConfig['username'] ?? null),
                'password' => $project->deployment_config['database']['password'] ?? ($defaultConnConfig['password'] ?? null),
            ]);

            Config::set('database.connections.project', $projectDbConfig);

            try {
                DB::purge('project');
                DB::connection('project')->getPdo();
                Log::debug("Successfully connected to isolated database: {$dbName} for project: {$project->code}");

                return 'project';
            } catch (\Throwable $e) {
                Log::warning("Could not connect to isolated database '{$dbName}' for project '{$project->code}', falling back to shared central: ".$e->getMessage());
            }
        }

        // Case 2: Shared Central Database with Scoping (e.g. Viettinmart / WKComputer / default)
        Config::set('database.connections.project', $defaultConnConfig);
        try {
            DB::purge('project');
            // If sqlite in-memory in test environment, share PDO
            if (($defaultConnConfig['driver'] ?? '') === 'sqlite' && ($defaultConnConfig['database'] ?? '') === ':memory:') {
                DB::connection('project')->setPdo(DB::connection($this->originalDefaultConnection)->getPdo());
            } else {
                DB::connection('project')->getPdo();
            }
        } catch (\Throwable $e) {
            Log::error('Failed setting up shared project connection: '.$e->getMessage());
        }

        return 'project';
    }

    /**
     * Clean up and reset connection state to prevent leak between requests
     */
    public function disconnect(): void
    {
        try {
            DB::purge('project');
            DB::disconnect('project');
            Config::set('database.default', $this->originalDefaultConnection);
        } catch (\Throwable $e) {
            // Suppress error during shutdown
        }
    }
}
