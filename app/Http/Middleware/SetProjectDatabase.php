<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\SettingsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetProjectDatabase
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.standalone_mode') || env('STANDALONE_MODE')) {
            return $next($request);
        }

        $project = $request->attributes->get('project');

        if ($project) {
            // Enable database switching for project context
            $this->setProjectDatabase($project, $request);

            Log::debug("Project database context set for: {$project->code}");
        }

        $response = $next($request);

        // Reset database after request
        if ($project) {
            $this->resetToMainDatabase();
        }

        return $response instanceof Response ? $response : response($response);
    }

    private function setProjectDatabase($project, Request $request)
    {
        $code = $project->code;

        // Fallback to project ID if code is empty
        if (empty($code)) {
            $code = 'project_'.$project->id;
        }

        Log::debug("SetProjectDatabase: Setting up project context for {$code}, project_id: {$project->id}");

        // Store main database name for later reset
        $request->attributes->set('main_database', config('database.default'));

        // Set tenant ID và project ID cho session TRƯỚC KHI query (100% Tenant-based)
        $tenantId = $project->tenant_id ?? null;
        if ($project->code === 'viettinmart-eco' || str_contains($project->code, 'viettinmart')) {
            $tenantId = 3;
        } elseif (str_contains($project->code, 'wkcomputer')) {
            $tenantId = 4;
        } elseif (! $tenantId) {
            $matchedTenant = Tenant::where('code', $project->code)
                ->orWhere('code', str_replace(['-eco', '-ecommerce', '-demo'], '', $project->code))
                ->first();
            $tenantId = $matchedTenant?->id
                ?? \DB::table('widgets')->where('project_id', $project->id)->whereNotNull('tenant_id')->value('tenant_id')
                ?? \DB::table('products_enhanced')->where('project_id', $project->id)->whereNotNull('tenant_id')->value('tenant_id')
                ?? \DB::table('posts')->where('project_id', $project->id)->whereNotNull('tenant_id')->value('tenant_id')
                ?? $project->id;
        }

        if ($tenantId && Schema::hasColumn('projects', 'tenant_id')) {
            if ($project->tenant_id !== $tenantId) {
                try {
                    $project->update(['tenant_id' => $tenantId]);
                } catch (\Throwable $e) {
                }
            }
        }

        session(['current_tenant_id' => $tenantId]);
        session(['current_project_id' => $project->id]);
        app()->instance('current_project_id', $project->id);
        app()->instance('current_tenant_id', $tenantId);
        config(['app.current_tenant_id' => $tenantId]);
        config(['app.default_tenant_id' => $tenantId]);

        // Clear settings cache để load lại từ project database
        if (class_exists('\App\Services\SettingsService')) {
            SettingsService::getInstance()->clearCache();
        }

        // SHARED HOSTING MODE: Always use main database with project_id scoping
        $this->setupSharedDatabase($project, $code);
    }

    /**
     * Setup project using shared database (single database for all projects)
     */
    private function setupSharedDatabase($project, $code)
    {
        Log::info("Using shared database for project: {$code}");

        // Use the main database connection config (use actual default, not hardcoded 'mysql')
        $defaultConnection = config('database.default');
        Config::set('database.connections.project', config("database.connections.{$defaultConnection}"));

        try {
            DB::purge('project');

            // If sqlite in-memory database, share PDO instance so tables exist across connections
            if (config("database.connections.{$defaultConnection}.driver") === 'sqlite' && config("database.connections.{$defaultConnection}.database") === ':memory:') {
                DB::connection('project')->setPdo(DB::connection($defaultConnection)->getPdo());
                if (DB::connection($defaultConnection)->getPdo()->inTransaction()) {
                    $ref = new \ReflectionProperty(DB::connection('project'), 'transactions');
                    $ref->setAccessible(true);
                    $ref->setValue(DB::connection('project'), max(1, DB::connection($defaultConnection)->transactionLevel()));
                }
            }

            // Test connection before switching
            DB::connection('project')->getPdo();

            // Set default connection to project for this request
            DB::setDefaultConnection('project');
            Config::set('database.default', 'project');

            // Set project context for scoped queries
            app()->instance('current_project_id', $project->id);
            session(['current_project_id' => $project->id]);

            Log::info("Successfully using shared database for project: {$code}");

        } catch (\Exception $e) {
            Log::error("Failed to setup shared database for project {$code}: ".$e->getMessage());
            $this->fallbackToMainDatabase($project);
        }
    }

    // COMMENTED OUT: Legacy multisite database setup
    // This was trying to create separate databases which doesn't work on shared hosting
    /*
    private function setupMultisiteDatabase($project, $code)
    {
        Log::info("Using multisite database for project: {$code}");

        // Use fixed multisite database configuration
        Config::set('database.connections.project', [
            'driver' => 'mysql',
            'host' => env('MULTISITE_DB_HOST', '127.0.0.1'),
            'port' => env('MULTISITE_DB_PORT', '3306'),
            'database' => env('MULTISITE_DB_DATABASE', 'u712054581_Database_01'),
            'username' => env('MULTISITE_DB_USERNAME', 'u712054581_Database_01'),
            'password' => env('MULTISITE_DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        try {
            DB::purge('project');

            // Test connection before switching
            DB::connection('project')->getPdo();

            // Set default connection to project for this request
            DB::setDefaultConnection('project');
            Config::set('database.default', 'project');

            // Set project context for multisite queries
            app()->instance('current_project_id', $project->id);
            session(['current_project_id' => $project->id]);

            Log::info("Successfully connected to multisite database for project: {$code}");

        } catch (\Exception $e) {
            Log::error("Failed to connect to multisite database for project {$code}: " . $e->getMessage());
            $this->fallbackToMainDatabase($project);
        }
    }
    */

    private function fallbackToMainDatabase($project)
    {
        Log::warning("Falling back to main database with project scoping for project: {$project->code}");

        // Use main database but set project context
        Config::set('database.connections.project', config('database.connections.mysql'));
        DB::purge('project');

        // Set project ID for scoping queries
        app()->instance('current_project_id', $project->id);
        session(['fallback_project_id' => $project->id]);
    }

    private function resetToMainDatabase()
    {
        // Restore to the original main connection (saved before switching)
        // Use 'mysql' as safe default fallback, but prefer what was saved in request
        $mainConnection = request()->attributes->get('main_database', config('database.default', 'mysql'));

        // If we're already on the main connection, nothing to do
        if (DB::getDefaultConnection() !== 'project') {
            return;
        }

        DB::setDefaultConnection($mainConnection);
        Config::set('database.default', $mainConnection);
        DB::purge('project');

        Log::debug("SetProjectDatabase: Reset to main database ({$mainConnection})");
    }
}
