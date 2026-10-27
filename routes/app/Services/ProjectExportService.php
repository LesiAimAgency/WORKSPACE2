<?php

namespace App\Services;

use App\Core\Theme\ThemeManager;
use App\Models\HostingProfile;
use App\Models\Project;
use App\Services\Export\ExportValidationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ZipArchive;

/**
 * ProjectExportService
 *
 * Central service responsible for building a deployable ZIP package from a Project.
 * Used by both:
 *  - ProjectExportController (manual "Download ZIP" flow)
 *  - DeploymentService (automated "Deploy to Hosting" flow)
 *
 * The package produced is a self-contained Laravel application with:
 *  - All source files (excluding SuperAdmin controllers/routes)
 *  - A CMS-only database snapshot (only whitelist tables, scoped to project)
 *  - A pre-configured .env file
 *  - A bootstrap installer PHP script
 */
class ProjectExportService
{
    /**
     * Build a full export package for a project.
     *
     * Returns an array with:
     *   'zip_path'        => Absolute path to the generated ZIP file
     *   'db_sql_content'  => The SQL content string (for injection into .env step)
     *   'env_content'     => The .env content string (template, DB credentials added by caller)
     *
     * @throws \Exception on any failure
     */
    public function buildExportPackage(Project $project, ?HostingProfile $profile = null, bool $includeVendor = false): array
    {
        set_time_limit(0);
        ini_set('memory_limit', '1G');

        $exportBaseDir = storage_path("app/deployments/project_{$project->id}");
        $exportSourceDir = $exportBaseDir.'/source';
        $zipPath = $exportBaseDir.'/source.zip';

        // Clean up any previous attempt
        if (File::exists($exportBaseDir)) {
            File::deleteDirectory($exportBaseDir);
        }
        if (! File::exists($exportSourceDir)) {
            File::makeDirectory($exportSourceDir, 0755, true, true);
        }

        Log::info("[Export] Starting export for project {$project->code}");

        // Pre-flight validation
        $validator = app(ExportValidationService::class);
        $validation = $validator->validateProject($project);
        if (! $validation['is_valid']) {
            Log::warning("[Export] Project {$project->code} validation warnings/errors: ".implode('; ', $validation['errors']));
        }

        // 1. Copy source files (minus SuperAdmin)
        $this->exportEssentialFiles($project, $exportSourceDir, $includeVendor);
        Log::info('[Export] Source files copied.');

        // 2. Generate CMS-only database SQL
        $dbSqlContent = $this->generateDatabaseSQL($project);
        File::ensureDirectoryExists($exportSourceDir.'/database');
        File::put($exportSourceDir.'/database/database.sql', $dbSqlContent);
        Log::info('[Export] Database SQL generated ('.strlen($dbSqlContent).' bytes).');

        // 3. Generate .env template
        $envContent = $this->generateEnvTemplate($project, $profile);
        File::put($exportSourceDir.'/.env', $envContent);

        // 4. Generate bootstrap installer script
        $bootstrapContent = $this->generateBootstrapInstaller();
        File::put($exportSourceDir.'/deploy_setup.php', $bootstrapContent);

        // 5. Generate Phase 2 Package Manifest
        $themeManager = app(ThemeManager::class);
        $activeTheme = $themeManager->resolveActiveTheme($project);

        $manifestData = [
            'name' => 'website-package',
            'package_type' => 'standalone_website_application',
            'theme' => $activeTheme,
            'cms_version' => '2.0.0',
            'version' => '2.0.0',
            'exported_at' => now()->toIso8601String(),
            'platform' => 'VGT Platform Control Plane',
            'routing' => [
                'frontend' => '/',
                'admin' => '/admin',
                'installer' => '/install',
            ],
            'isolation' => [
                'database' => 'independent_website_db',
                'authentication' => 'independent_cms_auth',
                'control_plane' => 'vgt_remote_api',
            ],
        ];
        File::put($exportSourceDir.'/manifest.json', json_encode($manifestData, JSON_PRETTY_PRINT));

        // 6. ZIP everything
        $this->createZipFromDirectory($exportSourceDir, $zipPath, $project);
        Log::info("[Export] ZIP created at {$zipPath} (".round(filesize($zipPath) / 1024 / 1024, 2).' MB).');

        // Clean up source dir, keep ZIP
        File::deleteDirectory($exportSourceDir);

        return [
            'zip_path' => $zipPath,
            'db_sql_content' => $dbSqlContent,
            'env_content' => $envContent,
            'validation' => $validation,
        ];
    }

    private function exportEssentialFiles(Project $project, string $exportPath, bool $includeVendor = false): void
    {
        $basePath = base_path();

        // Directories to copy verbatim
        $directories = [
            'bootstrap' => 'bootstrap',
            'config' => 'config',
            'database' => 'database',
            'public' => 'public',
            'resources' => 'resources',
            'storage/app/public' => 'storage/app/public',
            'storage/framework/cache' => 'storage/framework/cache',
            'storage/framework/sessions' => 'storage/framework/sessions',
            'storage/framework/views' => 'storage/framework/views',
        ];

        if ($includeVendor) {
            $directories['vendor'] = 'vendor';
        }

        $themeManager = app(ThemeManager::class);
        $activeTheme = strtolower($themeManager->resolveActiveTheme($project));
        $projectCode = strtolower($project->code);

        // Determine public exclusions (skip heavy unneeded media and other themes)
        $publicExcludes = [
            'storage',
            'media-files',
            'Front-end',
            'theme',
        ];

        if ($activeTheme !== 'ehenho' && ! str_contains($projectCode, 'da010') && ! str_contains($projectCode, 'ehenho')) {
            $publicExcludes[] = 'e-henho';
        }

        if ($activeTheme !== 'viettinmartdemo' && ! str_contains($projectCode, 'viettinmart')) {
            $publicExcludes[] = 'viettinmartdemo';
        }

        $themesDir = $basePath.'/public/themes';
        if (File::isDirectory($themesDir)) {
            foreach (File::directories($themesDir) as $themePath) {
                $themeName = strtolower(basename($themePath));
                $matchesTheme = ($themeName === $activeTheme);
                $matchesProject = ($themeName === $projectCode);
                if (! $matchesTheme && ! $matchesProject) {
                    $publicExcludes[] = 'themes/'.basename($themePath);
                }
            }
        }

        $resourceExcludes = [];
        $resourceThemesDir = $basePath.'/resources/views/themes';
        if (File::isDirectory($resourceThemesDir)) {
            foreach (File::directories($resourceThemesDir) as $themePath) {
                $themeName = strtolower(basename($themePath));
                if ($themeName !== $activeTheme) {
                    $resourceExcludes[] = 'views/themes/'.basename($themePath);
                }
            }
        }
        $frontendThemesDir = $basePath.'/resources/views/frontend/themes';
        if (File::isDirectory($frontendThemesDir)) {
            foreach (File::directories($frontendThemesDir) as $themePath) {
                $themeName = strtolower(basename($themePath));
                if ($themeName !== $activeTheme) {
                    $resourceExcludes[] = 'views/frontend/themes/'.basename($themePath);
                }
            }
        }

        foreach ($directories as $source => $dest) {
            $sourcePath = $basePath.'/'.$source;
            if (File::exists($sourcePath)) {
                $excludes = match ($source) {
                    'public' => $publicExcludes,
                    'resources' => $resourceExcludes,
                    default => []
                };
                $this->robustCopyDirectory($sourcePath, $exportPath.'/'.$dest, $excludes);
            }
        }

        // Copy /app but strip SuperAdmin
        $this->copyAppWithoutSuperAdmin($basePath, $exportPath);

        // Copy /routes but strip superadmin.php and patch project.php
        $this->copyRoutesWithoutSuperAdmin($basePath, $exportPath, $project);

        // Remove installed.lock from exported storage so web installer can execute on first boot
        if (File::exists($exportPath.'/storage/installed.lock')) {
            File::delete($exportPath.'/storage/installed.lock');
        }

        // Ensure storage dirs exist cleanly with .gitkeep
        $storageDirs = [
            'storage/framework/cache/data',
            'storage/framework/testing',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/app/public',
            'storage/logs',
        ];
        foreach ($storageDirs as $dir) {
            if (! File::exists($exportPath.'/'.$dir)) {
                File::makeDirectory($exportPath.'/'.$dir, 0755, true, true);
            }
            File::put($exportPath.'/'.$dir.'/.gitkeep', '');
        }

        // Copy root-level files
        $rootFiles = ['artisan', 'composer.json', 'composer.lock', 'package.json', '.env.example', '.gitignore', '.htaccess'];
        foreach ($rootFiles as $file) {
            if (File::exists($basePath.'/'.$file)) {
                File::copy($basePath.'/'.$file, $exportPath.'/'.$file);
            }
        }

        // Create root index.php so requests to /home/{user}/{domain}/ work out-of-the-box
        if (! File::exists($exportPath.'/index.php')) {
            $rootIndex = <<<'PHP'
<?php

/**
 * Laravel - Root Entry Point for Shared Hosting
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
PHP;
            File::put($exportPath.'/index.php', $rootIndex);
        }
    }

    private function robustCopyDirectory(string $source, string $dest, array $excludePatterns = []): void
    {
        if (! is_dir($dest)) {
            @mkdir($dest, 0755, true);
        }

        // High-speed native copy on Windows using Robocopy
        if (PHP_OS_FAMILY === 'Windows' && function_exists('exec')) {
            $srcWin = str_replace('/', '\\', $source);
            $destWin = str_replace('/', '\\', $dest);
            $cmd = "robocopy \"{$srcWin}\" \"{$destWin}\" /E /NFL /NDL /NJH /NJS /nc /ns /np";
            if (! empty($excludePatterns)) {
                $excludeDirs = [];
                foreach ($excludePatterns as $pattern) {
                    $patternWin = str_replace('/', '\\', $pattern);
                    $excludeDirs[] = '"'.basename($patternWin).'"';
                }
                $cmd .= ' /XD '.implode(' ', array_unique($excludeDirs));
            }
            @exec($cmd, $out, $code);
            // In robocopy, exit codes 0-7 indicate success (files copied, extras, etc.)
            if ($code <= 7) {
                return;
            }
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        foreach ($iterator as $item) {
            if ($item->isLink()) {
                continue; // Skip symlinks to avoid recursion or broken links
            }

            $subPath = str_replace('\\', '/', $iterator->getSubPathname());

            // Check exclusion patterns
            foreach ($excludePatterns as $pattern) {
                $pattern = str_replace('\\', '/', $pattern);
                if ($subPath === $pattern || str_starts_with($subPath, rtrim($pattern, '/').'/')) {
                    continue 2;
                }
            }

            $target = $dest.DIRECTORY_SEPARATOR.$iterator->getSubPathname();
            if ($item->isDir()) {
                if (! is_dir($target)) {
                    @mkdir($target, 0755, true);
                }
            } else {
                @copy($item->getPathname(), $target);
            }
        }
    }

    private function copyAppWithoutSuperAdmin(string $basePath, string $exportPath): void
    {
        $appSource = $basePath.'/app';
        $appDest = $exportPath.'/app';

        if (! File::exists($appSource)) {
            return;
        }

        File::copyDirectory($appSource, $appDest);

        // Remove SuperAdmin controllers and middleware
        $superAdminPaths = [
            $appDest.'/Http/Controllers/SuperAdmin',
            $appDest.'/Http/Middleware/SuperAdminMiddleware.php',
        ];

        foreach ($superAdminPaths as $path) {
            if (File::exists($path)) {
                File::isDirectory($path) ? File::deleteDirectory($path) : File::delete($path);
            }
        }
    }

    private function copyRoutesWithoutSuperAdmin(string $basePath, string $exportPath, ?Project $project = null): void
    {
        $routesSource = $basePath.'/routes';
        $routesDest = $exportPath.'/routes';

        if (! File::exists($routesSource)) {
            return;
        }

        File::copyDirectory($routesSource, $routesDest);

        // Remove superadmin.php route file
        $superAdminRoute = $routesDest.'/superadmin.php';
        if (File::exists($superAdminRoute)) {
            File::delete($superAdminRoute);
        }

        // Determine destination redirect for root route
        $themeManager = app(ThemeManager::class);
        $themeSlug = $project ? $themeManager->resolveActiveTheme($project) : 'inbetween';

        $destination = '/'.($project?->code ?: $themeSlug);
        if ($themeSlug === 'inbetween_v2' || $themeSlug === 'inbetween') {
            $destination = '/';
        } elseif ($themeSlug === 'ehenho') {
            $destination = '/ehenho';
        } elseif ($themeSlug === 'wkcomputerdemo') {
            $destination = '/wkcomputer';
        } elseif ($themeSlug === 'viettinmartdemo') {
            $destination = '/viettinmart-eco';
        }

        // Patch web.php: remove superadmin require & ensure root route redirects to storefront
        $webRoute = $routesDest.'/web.php';
        if (File::exists($webRoute)) {
            $content = File::get($webRoute);
            $content = str_replace(
                "require __DIR__.'/superadmin.php';",
                '// SuperAdmin routes removed for standalone deployment',
                $content
            );
            if ($destination === '/') {
                $rootReplacement = "if (! file_exists(storage_path('installed.lock'))) {\n        return redirect('/install');\n    }\n\n    return app(\\App\\Http\\Controllers\\Themes\\InbetweenV2\\InbetweenV2Controller::class)->index(request());";
            } else {
                $rootReplacement = "if (! file_exists(storage_path('installed.lock'))) {\n        return redirect('/install');\n    }\n\n    return redirect('{$destination}');";
            }
            $content = str_replace(
                "return view('coming-soon');",
                $rootReplacement,
                $content
            );
            $content = preg_replace(
                "/if\s*\(\s*app\(\)->environment\('local'\)\s*\)\s*\{\s*return redirect\('\/superadmin'\);\s*\}/",
                "if (! file_exists(storage_path('installed.lock'))) { return redirect('/install'); }",
                $content
            );
            File::put($webRoute, $content);
        }

        // Patch project.php: strip {projectCode} prefix so routes work on standalone domain
        $projectRoute = $routesDest.'/project.php';
        if (File::exists($projectRoute)) {
            $content = File::get($projectRoute);
            $content = str_replace("Route::prefix('{projectCode}')", "Route::prefix('')", $content);
            $content = str_replace("Route::prefix('{projectCode}/admin')", "Route::prefix('admin')", $content);
            $content = str_replace("Route::prefix('{projectCode}/api')", "Route::prefix('api')", $content);
            $content = str_replace("Route::get('/{projectCode}/{slug}'", "Route::get('/{slug}'", $content);
            $content = preg_replace("/->where\s*\(\s*['\"]projectCode['\"]\s*,[^)]+\)/", '', $content);
            File::put($projectRoute, $content);
        }
    }

    // =========================================================================
    // DATABASE SQL EXPORT (CMS WHITELIST + THEME MANIFEST TABLES)
    // =========================================================================

    /**
     * CMS table whitelist with dynamic Theme Manifest tables inclusion.
     */
    public function getCmsTableWhitelist(?Project $project = null): array
    {
        $tables = [
            // Content
            'posts', 'page_sections', 'taxonomies', 'term_relationships', 'translations',
            'archive_templates', 'form_submissions', 'form_templates', 'modal_forms',
            // Widgets & Navigation
            'widgets', 'widget_templates', 'menus', 'menu_items',
            // Media / Fonts / Settings
            'fonts', 'settings', 'languages',
            // Users & Roles (project-level + admin accounts)
            'users', 'roles', 'permissions',
            'user_roles', 'user_permissions', 'role_permissions', 'user_addresses',
            // E-commerce
            'products_enhanced', 'product_categories',
            'product_attributes', 'attribute_groups',
            'product_attribute_values', 'product_attribute_value_mappings',
            'product_variations', 'brands', 'product_combos',
            'brand_product', 'product_attribute_product', 'product_category_product',
            'reviews', 'product_reviews',
            'coupons', 'flash_sale_campaigns', 'flash_sale_items',
            // Orders
            'orders', 'order_items', 'order_status_history', 'order_status_histories',
            // Shipping
            'shipping_carriers', 'shipping_zones', 'shipping_zone_locations',
            'shipping_rules', 'shipping_rule_conditions', 'shipping_rate_versions',
            // System Framework
            'sessions', 'cache', 'cache_locks', 'visitor_logs',
        ];

        if ($project) {
            $themeManager = app(ThemeManager::class);
            $themeSlug = $themeManager->resolveActiveTheme($project);
            $manifest = $themeManager->getManifest($themeSlug);
            if ($manifest) {
                $tables = array_unique(array_merge($tables, $manifest->getDatabaseTables()));
            }
        }

        return $tables;
    }

    public function generateDatabaseSQL(Project $project): string
    {
        $sql = "-- CMS Database snapshot for {$project->name} (Project: {$project->code})\n";
        $sql .= '-- Generated on: '.now()->format('Y-m-d H:i:s')."\n";
        $sql .= "-- NOTE: Includes project data and shared test/template data.\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET UNIQUE_CHECKS=0;\n";
        $sql .= "SET NAMES utf8mb4;\n\n";

        $existingTables = array_map(
            fn ($t) => array_values((array) $t)[0],
            DB::select('SHOW TABLES')
        );

        $tablesToExport = array_intersect($this->getCmsTableWhitelist($project), $existingTables);

        foreach ($tablesToExport as $table) {
            $sql .= $this->exportTableSQL($table, $project);
        }

        $sql .= "\nSET FOREIGN_KEY_CHECKS=1;\n";
        $sql .= "SET UNIQUE_CHECKS=1;\n";

        return $sql;
    }

    private function exportTableSQL(string $table, Project $project): string
    {
        $sql = "-- Table: {$table}\n";

        try {
            $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createTable[0]->{'Create Table'}.";\n\n";
            $sql .= $this->getTableData($table, $project);
        } catch (\Exception $e) {
            $sql .= "-- Error exporting table {$table}: ".$e->getMessage()."\n\n";
        }

        return $sql."\n";
    }

    private function getTableData(string $table, Project $project): string
    {
        $sql = "-- Data for {$table}\n";
        $columns = Schema::getColumnListing($table);
        $query = DB::table($table);

        // Schema-only tables (never export temporary session, cache, or visitor logs)
        if (in_array($table, ['cache', 'cache_locks', 'sessions', 'visitor_logs', 'failed_jobs', 'jobs', 'job_batches'])) {
            return $sql;
        }

        // Scope to CMS Data Plane
        if ($table === 'users') {
            // Include project admin/cms accounts only (never superadmin)
            $query->where(function ($q) {
                $q->where('role', 'admin')
                    ->orWhere('role', 'cms')
                    ->orWhere('username', 'admin');
            });
        } elseif (in_array($table, ['roles', 'permissions', 'role_permissions', 'languages', 'fonts', 'term_relationships', 'product_attributes', 'attribute_groups', 'product_reviews', 'coupons', 'flash_sale_campaigns', 'flash_sale_items'])) {
            // Shared system and e-commerce test tables: export all
        } elseif ($table === 'brand_product' || $table === 'product_attribute_product' || $table === 'product_category_product') {
            // Pivot tables for products
            $productIds = DB::table('products_enhanced')
                ->where('project_id', $project->id)
                ->orWhereNull('project_id')
                ->pluck('id');
            $query = DB::table($table)->whereIn('product_id', $productIds);
        } elseif ($table === 'user_roles' || $table === 'user_permissions') {
            $userIds = DB::table('users')
                ->where(function ($q) use ($project) {
                    if ($project->tenant_id) {
                        $q->where('tenant_id', $project->tenant_id);
                    }
                    $q->orWhere('role', 'superadmin')
                        ->orWhere('username', 'admin');
                })
                ->pluck('id');
            $query = DB::table($table)->whereIn('user_id', $userIds);
        } elseif (in_array('project_id', $columns)) {
            // Project data + all shared/default test data (where project_id is null or 0)
            $query->where(function ($q) use ($project) {
                $q->where('project_id', $project->id)
                    ->orWhereNull('project_id')
                    ->orWhere('project_id', 0);
            });
        } elseif (in_array('tenant_id', $columns)) {
            $query->where(function ($q) use ($project) {
                if ($project->tenant_id) {
                    $q->where('tenant_id', $project->tenant_id);
                }
                $q->orWhere('tenant_id', $project->id)
                    ->orWhereNull('tenant_id')
                    ->orWhere('tenant_id', 0);
            });
        }

        try {
            $data = $query->get();
            if ($data->isEmpty()) {
                return $sql;
            }

            $values = [];

            foreach ($data as $row) {
                $rowData = array_map(function ($value) {
                    if (is_null($value)) {
                        return 'NULL';
                    }
                    if (is_numeric($value) && ! is_string($value)) {
                        return $value;
                    }

                    return DB::getPdo()->quote($value);
                }, (array) $row);
                $values[] = '('.implode(', ', $rowData).')';
            }

            $chunks = array_chunk($values, 50);
            foreach ($chunks as $chunk) {
                $sql .= "INSERT INTO `{$table}` VALUES\n".implode(",\n", $chunk).";\n";
            }
        } catch (\Exception $e) {
            $sql .= '-- Error exporting data: '.$e->getMessage()."\n";
        }

        return $sql;
    }

    // =========================================================================
    // CONFIG / ENV GENERATION
    // =========================================================================

    /**
     * Generate a .env template with placeholder DB values.
     * The actual DB credentials are substituted by DeploymentService after DB creation.
     */
    public function generateEnvTemplate(Project $project, ?HostingProfile $profile = null): string
    {
        if (empty($project->api_token)) {
            $project->update(['api_token' => bin2hex(random_bytes(32))]);
            $project->refresh();
        }

        $domain = $project->external_domain ?? ($profile?->domain ?? 'your-domain.com');

        return 'APP_NAME="'.$project->name.'"
APP_ENV=production
APP_KEY=base64:'.base64_encode(random_bytes(32)).'
APP_DEBUG=false
APP_URL=https://'.$domain.'

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=__DB_DATABASE__
DB_USERNAME=__DB_USERNAME__
DB_PASSWORD=__DB_PASSWORD__

BROADCAST_CONNECTION=log
CACHE_STORE=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.your-host.com
MAIL_PORT=465
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="hello@'.$domain.'"
MAIL_FROM_NAME="'.$project->name.'"

# Project-specific settings
PROJECT_CODE='.$project->code.'
PROJECT_NAME="'.$project->name.'"

# Remote Sync API – token provided by SuperAdmin
SYNC_API_TOKEN="'.$project->api_token.'"
SUPERADMIN_URL="'.config('app.url').'"

# Standalone mode – website runs independently on its own domain
STANDALONE_MODE=true
';
    }

    // =========================================================================
    // BOOTSTRAP INSTALLER SCRIPT
    // =========================================================================

    /**
     * Generate a PHP installer script that is uploaded alongside the ZIP.
     * It is invoked once via HTTP to import the database and run artisan commands,
     * then self-destructs for security.
     *
     * The token placeholder __BOOTSTRAP_TOKEN__ must be replaced by the caller
     * before uploading the script.
     */
    public function generateBootstrapInstaller(): string
    {
        return <<<'PHP'
<?php
/**
 * Deploy Bootstrap Installer
 * This file self-destructs after successful execution.
 * DO NOT LEAVE THIS FILE ON THE SERVER.
 */

// Token protection – replaced by DeploymentService before upload
$token = '__BOOTSTRAP_TOKEN__';
if (! isset($_GET['token']) || $_GET['token'] !== $token) {
    http_response_code(403);
    die('Forbidden');
}

set_time_limit(300);
echo '<pre>';

// 1. Locate base directory and .env
$baseDir = file_exists(__DIR__ . '/../.env') ? dirname(__DIR__) : __DIR__;
$envPath = $baseDir . '/.env';
if (! file_exists($envPath)) {
    die('ERROR: .env file not found at ' . $envPath);
}

$env = [];
foreach (file($envPath) as $line) {
    $line = trim($line);
    if (str_starts_with($line, '#') || ! str_contains($line, '=')) {
        continue;
    }
    [$key, $value] = explode('=', $line, 2);
    $env[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
}

$dbHost = $env['DB_HOST'] ?? 'localhost';
$dbName = $env['DB_DATABASE'] ?? '';
$dbUser = $env['DB_USERNAME'] ?? '';
$dbPass = $env['DB_PASSWORD'] ?? '';

echo "Connecting to database {$dbName} on {$dbHost}...\n";

// 2. Import database SQL
$sqlFile = $baseDir . '/database/database.sql';
if (file_exists($sqlFile)) {
    $mysqli = null;
    $hostsToTry = array_unique([$dbHost, 'localhost', '127.0.0.1']);
    foreach ($hostsToTry as $h) {
        try {
            $mysqli = @new mysqli($h, $dbUser, $dbPass, $dbName);
            if (! $mysqli->connect_error) {
                echo "Connected successfully using host '{$h}'.\n";
                break;
            }
        } catch (\Throwable $ex) {
            // try next host
        }
    }

    if (! $mysqli || $mysqli->connect_error) {
        echo "WARN: Could not connect to MySQL: " . ($mysqli ? $mysqli->connect_error : 'Connection error') . "\n";
    } else {
        $mysqli->set_charset('utf8mb4');
        mysqli_report(MYSQLI_REPORT_OFF);

        $sql = file_get_contents($sqlFile);
        if ($mysqli->multi_query($sql)) {
            $imported = 0;
            do {
                $imported++;
                if ($res = $mysqli->store_result()) {
                    $res->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());
            echo "Database imported: {$imported} query batches executed.\n";
        } else {
            echo "WARN: multi_query failed: " . $mysqli->error . "\n";
        }
        $mysqli->close();
    }
} else {
    echo "WARN: database/database.sql not found, skipping DB import.\n";
}

// 3. Run Artisan commands if exec() is available
if (function_exists('exec')) {
    $php = PHP_BINARY ?: 'php';
    $artisan = $baseDir . '/artisan';
    if (file_exists($artisan)) {
        $commands = [
            'key:generate --force',
            'storage:link',
            'config:cache',
            'route:cache',
            'view:cache',
        ];
        foreach ($commands as $cmd) {
            exec("{$php} {$artisan} {$cmd} 2>&1", $out, $code);
            echo "artisan {$cmd}: " . ($code === 0 ? 'OK' : 'FAILED') . "\n";
            if (! empty($out)) {
                echo implode("\n", $out) . "\n";
            }
            $out = [];
        }
    }
} else {
    echo "NOTICE: exec() is disabled. Run these commands manually:\n";
    echo "  php artisan key:generate --force\n";
    echo "  php artisan storage:link\n";
    echo "  php artisan config:cache\n";
    echo "  php artisan route:cache\n";
    echo "  php artisan view:cache\n";
}

// 4. Self-destruct for security
echo "\nCleaning up...\n";
@unlink(__FILE__);
foreach (glob(__DIR__ . '/deploy_*.zip') as $zip) {
    @unlink($zip);
}
// Keep database.sql until manually confirmed, then user can delete
// @unlink(__DIR__ . '/database/database.sql');

echo "\n✅ Bootstrap completed. This script has been deleted.\n";
echo '</pre>';
PHP;
    }

    // =========================================================================
    // THEME STANDALONE EXPORT (WORDPRESS-STYLE)
    // =========================================================================

    /**
     * Export standalone Theme package (WordPress-style ZIP).
     * Includes theme.json, views, assets, widgets, controllers, seeders, routes, and demo SQL.
     */
    public function exportTheme(string $themeSlug): array
    {
        set_time_limit(0);
        ini_set('memory_limit', '1G');

        $themeManager = app(ThemeManager::class);
        $manifest = $themeManager->getManifest($themeSlug);
        if (! $manifest) {
            throw new \InvalidArgumentException("Theme '{$themeSlug}' not found or lacks a valid theme.json.");
        }

        $exportBaseDir = storage_path("app/theme_exports/{$themeSlug}");
        $exportSourceDir = $exportBaseDir.'/theme';
        $zipPath = $exportBaseDir."/{$themeSlug}_theme.zip";

        if (File::exists($exportBaseDir)) {
            File::deleteDirectory($exportBaseDir);
        }
        File::makeDirectory($exportSourceDir, 0755, true, true);

        // 1. theme.json
        $themeJsonPath = $manifest->getPath();
        File::copy($themeJsonPath, $exportSourceDir.'/theme.json');

        // 2. Views
        $viewPaths = [
            resource_path("views/themes/{$themeSlug}"),
            resource_path("views/frontend/themes/{$themeSlug}"),
        ];
        foreach ($viewPaths as $vp) {
            if (File::isDirectory($vp)) {
                $this->robustCopyDirectory($vp, $exportSourceDir.'/views');
            }
        }

        // 3. Public Assets
        $assetPath = public_path("themes/{$themeSlug}");
        if (File::isDirectory($assetPath)) {
            $this->robustCopyDirectory($assetPath, $exportSourceDir.'/assets');
        }

        // 4. Dedicated Widgets
        $studlyTheme = Str::studly($themeSlug);
        $widgetClassDir = app_path("Widgets/{$studlyTheme}");
        if (File::isDirectory($widgetClassDir)) {
            $this->robustCopyDirectory($widgetClassDir, $exportSourceDir.'/widgets/classes');
        }
        $widgetViewDir = resource_path("views/widgets/{$themeSlug}");
        if (File::isDirectory($widgetViewDir)) {
            $this->robustCopyDirectory($widgetViewDir, $exportSourceDir.'/widgets/views');
        }

        // 5. Dedicated Controllers
        $controllerDir = app_path("Http/Controllers/Themes/{$studlyTheme}");
        if (File::isDirectory($controllerDir)) {
            $this->robustCopyDirectory($controllerDir, $exportSourceDir.'/controllers');
        }

        // 6. Dedicated Seeders
        $seedersDir = database_path('seeders');
        if (File::isDirectory($seedersDir)) {
            foreach (File::files($seedersDir) as $file) {
                if (str_contains(strtolower($file->getFilename()), strtolower($themeSlug))) {
                    File::ensureDirectoryExists($exportSourceDir.'/seeders');
                    File::copy($file->getPathname(), $exportSourceDir.'/seeders/'.$file->getFilename());
                }
            }
        }

        // 7. Route declarations
        $routeFile = base_path("routes/{$themeSlug}.php");
        if (File::exists($routeFile)) {
            File::ensureDirectoryExists($exportSourceDir.'/routes');
            File::copy($routeFile, $exportSourceDir."/routes/{$themeSlug}.php");
        }

        // 8. Theme Database Schema & Demo SQL
        $themeTables = $manifest->getDatabaseTables();
        if (! empty($themeTables)) {
            $sql = "-- Theme {$themeSlug} Schema & Demo SQL\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
            $sql .= "SET UNIQUE_CHECKS=0;\n";
            $sql .= "SET NAMES utf8mb4;\n\n";

            foreach ($themeTables as $table) {
                if (Schema::hasTable($table)) {
                    $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                    $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                    $sql .= $createTable[0]->{'Create Table'}.";\n\n";

                    $rows = DB::table($table)->limit(100)->get();
                    if ($rows->isNotEmpty()) {
                        $sql .= "INSERT INTO `{$table}` VALUES\n";
                        $valStrings = [];
                        foreach ($rows as $row) {
                            $quoted = array_map(function ($val) {
                                if (is_null($val)) {
                                    return 'NULL';
                                }
                                if (is_numeric($val) && ! is_string($val)) {
                                    return $val;
                                }

                                return DB::getPdo()->quote($val);
                            }, (array) $row);
                            $valStrings[] = '('.implode(', ', $quoted).')';
                        }
                        $sql .= implode(",\n", $valStrings).";\n\n";
                    }
                }
            }
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            $sql .= "SET UNIQUE_CHECKS=1;\n";
            File::ensureDirectoryExists($exportSourceDir.'/data');
            File::put($exportSourceDir.'/data/demo.sql', $sql);
        }

        // 9. Create ZIP
        $this->createZipFromDirectory($exportSourceDir, $zipPath);

        // Clean up source dir, keep ZIP
        File::deleteDirectory($exportSourceDir);

        return [
            'zip_path' => $zipPath,
            'filename' => "{$themeSlug}_theme.zip",
            'size' => filesize($zipPath),
        ];
    }

    // =========================================================================
    // ZIP
    // =========================================================================

    private function createZipFromDirectory(string $sourceDir, string $zipPath, ?Project $project = null): void
    {
        // High-speed native ZIP creation on Windows using built-in tar.exe (bsdtar)
        if (PHP_OS_FAMILY === 'Windows' && function_exists('exec')) {
            $srcWin = str_replace('/', '\\', $sourceDir);
            $zipWin = str_replace('/', '\\', $zipPath);
            $cmd = "tar -a -c -f \"{$zipWin}\" -C \"{$srcWin}\" .";
            @exec($cmd, $out, $code);
            if ($code === 0 && file_exists($zipPath) && filesize($zipPath) > 1000) {
                return;
            }
        }

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Cannot create ZIP at {$zipPath}");
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($sourceDir) + 1);

            // Normalise on Windows
            $relativePath = str_replace('\\', '/', $relativePath);

            $zip->addFile($filePath, $relativePath);
        }

        $zip->close();
    }
}
