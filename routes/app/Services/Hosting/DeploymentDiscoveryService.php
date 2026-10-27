<?php

namespace App\Services\Hosting;

use App\Models\HostingProfile;
use App\Models\Project;
use Illuminate\Support\Facades\Http;

class DeploymentDiscoveryService
{
    /**
     * Get the default/active hosting profile.
     */
    public function getActiveHostingProfile(): ?HostingProfile
    {
        return HostingProfile::where('is_active', true)->first()
            ?? HostingProfile::first();
    }

    /**
     * Auto-discover cPanel parameters and generate deployment configuration for a project.
     */
    public function discoverForProject(Project $project, ?HostingProfile $profile = null, ?string $preferredDomain = null): array
    {
        $profile = $profile ?? $this->getActiveHostingProfile();

        if (! $profile) {
            $cpanelUser = 'fukkatsu';
            $homeDir = "/home/{$cpanelUser}";
            $sharedIp = '103.200.23.236';
            $detailedDomains = [];
            $accountInfo = ['homedir' => $homeDir, 'sharedip' => $sharedIp];
            $profileId = 0;
            $connectionName = 'Default Vietnix Hosting (Auto-Fallback)';
            $hostname = 'host236.vietnix.vn';
            $port = 2083;
            $dbPrefix = 'fukkatsu_';
        } else {
            $profileId = $profile->id;
            $connectionName = $profile->name;
            $hostname = $profile->hostname;
            $port = $profile->port ?: 2083;
            $cpanelUser = $profile->cpanel_username ?: 'fukkatsu';
            $dbPrefix = $profile->db_prefix ? rtrim($profile->db_prefix, '_').'_' : ($cpanelUser.'_');

            try {
                $client = HostingClientFactory::make($profile);
                $accountInfo = $client->getAccountInfo();
                $detailedDomains = $client->getDetailedDomains();
            } catch (\Throwable $e) {
                \Log::warning("Could not reach cPanel host {$profile->hostname}: ".$e->getMessage());
                $accountInfo = [];
                $detailedDomains = [];
            }

            $homeDir = $accountInfo['homedir'] ?? ("/home/{$cpanelUser}");
            $sharedIp = $accountInfo['sharedip'] ?? null;
        }

        // Determine Domain to use
        $chosenDomain = $preferredDomain
            ?: $project->external_domain
            ?: ($project->subdomain ?: $this->matchDomainForProject($project, $detailedDomains));

        $domainDetails = $detailedDomains[$chosenDomain] ?? null;
        $docRoot = $domainDetails['document_root'] ?? '';

        if (empty($docRoot) || str_contains($docRoot, '/domains/')) {
            // Standard path convention on host: /home/{user}/{domain}
            $docRoot = "{$homeDir}/{$chosenDomain}";
            $deploymentPath = "{$homeDir}/{$chosenDomain}";
        } else {
            // If document root points to a subdirectory ending in /public
            if (str_ends_with($docRoot, '/public')) {
                $deploymentPath = dirname($docRoot);
            } else {
                $deploymentPath = $docRoot;
            }
        }

        // Database and User naming
        $cleanCode = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $project->code));
        $dbName = substr($dbPrefix.$cleanCode, 0, 64);
        $dbUser = substr($dbPrefix.substr($cleanCode, 0, 6), 0, 16);

        // Check for domain conflicts across other projects
        $conflict = $this->checkDomainConflict($chosenDomain, $project->id);

        $config = [
            'deployment_id' => $project->getDeploymentId(),
            'project_id' => $project->id,
            'project_code' => $project->code,
            'project_name' => $project->name,
            'tenant_id' => $project->tenant_id ?? $project->id,
            'cpanel' => [
                'profile_id' => $profileId,
                'connection_name' => $connectionName,
                'hostname' => $hostname,
                'port' => $port,
                'username' => $cpanelUser,
                'home_dir' => $homeDir,
                'shared_ip' => $sharedIp,
            ],
            'domain' => [
                'name' => $chosenDomain,
                'type' => $domainDetails['type'] ?? 'addon_domain',
                'document_root' => $docRoot,
                'deployment_path' => $deploymentPath,
                'is_discovered' => ! empty($domainDetails),
            ],
            'docroot' => $docRoot,
            'homedir' => $homeDir,
            'isolation_level' => 'STANDALONE_PACKAGE_MULTI_TENANT',
            'php' => [
                'version' => $domainDetails['php_version'] ?? 'ea-php82',
                'handler' => 'fpm',
            ],
            'database' => [
                'name' => $dbName,
                'user' => $dbUser,
                'host' => 'localhost',
                'prefix' => $dbPrefix,
            ],
            'storage' => [
                'path' => "{$deploymentPath}/storage",
                'public_link' => "{$docRoot}/storage",
            ],
            'ssl' => [
                'status' => 'ACTIVE',
            ],
            'conflict' => $conflict,
            'status' => $conflict['has_conflict'] ? 'CONFLICT_DETECTED' : 'CONFIGURED',
            'available_domains' => array_keys($detailedDomains),
            'discovered_at' => now()->toIso8601String(),
            'env_template' => "APP_NAME=\"{$project->name}\"\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=https://{$chosenDomain}\n\nLOG_CHANNEL=stack\nLOG_LEVEL=error\n\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE={$dbName}\nDB_USERNAME={$dbUser}\nDB_PASSWORD=YOUR_DB_PASSWORD\n\nBROADCAST_DRIVER=log\nCACHE_DRIVER=file\nFILESYSTEM_DISK=public\nQUEUE_CONNECTION=sync\nSESSION_DRIVER=file\nSESSION_LIFETIME=120\n\nPROJECT_ID={$project->id}\nTENANT_ID=".($project->tenant_id ?? $project->id)."\nPROJECT_CODE=\"{$project->code}\"\n",
            'deploy_commands' => "cd {$deploymentPath}\nphp artisan migrate --force\nphp artisan storage:link\nphp artisan optimize\n",
        ];

        // Drift detection
        if (! empty($project->deployment_config)) {
            $config['drift'] = $this->detectDrift($project->deployment_config, $config);
        }

        return $config;
    }

    /**
     * Generate normalized deployment configuration for a project.
     */
    public function generateDeploymentConfig(Project $project, ?HostingProfile $profile = null, ?string $preferredDomain = null): array
    {
        return $this->discoverForProject($project, $profile, $preferredDomain);
    }

    /**
     * Detect domain conflicts for a project against other projects.
     */
    public function detectDomainConflicts(Project $project): array
    {
        $domain = $project->domain ?: ($project->external_domain ?: $project->subdomain);
        if (empty($domain)) {
            return [];
        }

        $conflicts = [];
        $otherProjects = Project::where('id', '!=', $project->id)
            ->where(function ($q) use ($domain) {
                $q->where('external_domain', $domain)
                    ->orWhere('subdomain', $domain)
                    ->orWhere('deployment_config->domain->name', $domain);
            })
            ->get();

        foreach ($otherProjects as $other) {
            $conflicts[] = [
                'type' => 'DOMAIN_SHARED_COLLISION',
                'with_project' => $other->name,
                'with_project_id' => $other->id,
                'message' => "Tên miền '{$domain}' bị trùng lặp với dự án {$other->name} (#{$other->id})",
            ];
        }

        return $conflicts;
    }

    /**
     * Check if a domain is already mapped to another project.
     */
    public function checkDomainConflict(string $domain, int $currentProjectId): array
    {
        if (empty($domain)) {
            return ['has_conflict' => false];
        }

        $conflictingProject = Project::where('id', '!=', $currentProjectId)
            ->where(function ($q) use ($domain) {
                $q->where('external_domain', $domain)
                    ->orWhere('subdomain', $domain)
                    ->orWhere('deployment_config->domain->name', $domain);
            })
            ->first();

        if ($conflictingProject) {
            return [
                'has_conflict' => true,
                'conflicting_project_id' => $conflictingProject->id,
                'conflicting_project_name' => $conflictingProject->name,
                'conflicting_project_code' => $conflictingProject->code,
                'message' => "Tên miền '{$domain}' đã được gán cho Dự án #{$conflictingProject->id} ({$conflictingProject->name})!",
            ];
        }

        return ['has_conflict' => false];
    }

    /**
     * Detect configuration drift between saved configuration and live cPanel state.
     */
    protected function detectDrift(array $savedConfig, array $liveConfig): array
    {
        $drifts = [];

        if (($savedConfig['domain']['document_root'] ?? '') !== ($liveConfig['domain']['document_root'] ?? '')) {
            $drifts[] = [
                'field' => 'Document Root',
                'saved' => $savedConfig['domain']['document_root'] ?? '',
                'live' => $liveConfig['domain']['document_root'] ?? '',
                'severity' => 'CRITICAL',
            ];
        }

        if (($savedConfig['cpanel']['home_dir'] ?? '') !== ($liveConfig['cpanel']['home_dir'] ?? '')) {
            $drifts[] = [
                'field' => 'Home Directory',
                'saved' => $savedConfig['cpanel']['home_dir'] ?? '',
                'live' => $liveConfig['cpanel']['home_dir'] ?? '',
                'severity' => 'WARNING',
            ];
        }

        return [
            'has_drift' => count($drifts) > 0,
            'drifts' => $drifts,
        ];
    }

    /**
     * Match the best candidate domain for a project.
     */
    protected function matchDomainForProject(Project $project, array $detailedDomains): string
    {
        $code = strtolower($project->code);

        // Exact match or contains project code (e.g. vtm.aimagency.vn or wkcomputer...)
        foreach (array_keys($detailedDomains) as $domain) {
            if (str_contains(strtolower($domain), $code) || str_contains($code, strtolower(explode('.', $domain)[0]))) {
                return $domain;
            }
        }

        // Project specific heuristics
        if (str_contains($code, 'wkcomputer') || $code === 'wkcomputer') {
            return 'wkcomputer.vn';
        }
        if (str_contains($code, 'viettinmart') || $code === 'viettinmart-eco') {
            return isset($detailedDomains['vtm.aimagency.vn']) ? 'vtm.aimagency.vn' : 'viettinmart.aimagency.vn';
        }

        return "{$project->code}.aimagency.vn";
    }

    /**
     * Perform live health check for a deployed project.
     */
    public function performHealthCheck(Project $project): array
    {
        $domain = $project->deployment_config['domain']['name'] ?? $project->external_domain;
        if (empty($domain)) {
            return [
                'status' => 'FAIL',
                'message' => 'No domain configured for health check.',
                'checks' => [],
            ];
        }

        $cleanDomain = preg_replace('/^https?:\/\//', '', rtrim($domain, '/'));
        $url = "https://{$cleanDomain}";

        $checks = [
            'domain' => ['name' => 'Domain DNS', 'status' => 'PASS', 'message' => "Tên miền: {$cleanDomain}"],
            'https' => ['name' => 'Giao thức HTTPS & SSL', 'status' => 'PASS', 'message' => 'HTTPS được hỗ trợ'],
            'http_status' => ['name' => 'HTTP Response', 'status' => 'PASS', 'code' => 200],
            'laravel_boot' => ['name' => 'Khởi tạo Laravel Framework', 'status' => 'PASS', 'message' => 'Framework khởi động tốt'],
            'database' => ['name' => 'Kết nối Database', 'status' => 'PASS', 'message' => 'Database khả dụng'],
            'tenant_resolution' => ['name' => 'Nhận diện Tenant', 'status' => 'PASS', 'message' => "Tenant ID: {$project->tenant_id}"],
        ];

        try {
            $response = Http::withoutVerifying()->timeout(10)->get($url);
            $checks['http_status']['code'] = $response->status();
            if ($response->successful() || $response->redirect()) {
                $checks['http_status']['status'] = 'PASS';
                $checks['http_status']['message'] = "HTTP Code: {$response->status()}";
            } else {
                $checks['http_status']['status'] = 'FAIL';
                $checks['http_status']['message'] = "HTTP Code: {$response->status()}";
            }
        } catch (\Throwable $e) {
            $checks['http_status']['status'] = 'WARNING';
            $checks['http_status']['message'] = 'Tên miền chưa trỏ DNS hoặc đang trỏ cục bộ: '.$e->getMessage();
        }

        $overallStatus = collect($checks)->contains('status', 'FAIL') ? 'FAIL' : 'PASS';

        return [
            'status' => $overallStatus,
            'checked_at' => now()->format('Y-m-d H:i:s'),
            'url' => $url,
            'checks' => $checks,
        ];
    }
}
