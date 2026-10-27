<?php

namespace App\Services\Hosting;

use App\Models\HostingProfile;
use App\Services\Hosting\Contracts\HostingClientInterface;
use Illuminate\Support\Facades\Http;

class CpanelHostingClient implements HostingClientInterface
{
    protected HostingProfile $profile;

    public function setProfile(HostingProfile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Call cPanel UAPI
     */
    protected function callUapi(string $module, string $function, array $params = [], string $method = 'GET')
    {
        $host = preg_replace('/^https?:\/\//', '', $this->profile->hostname);
        $host = preg_replace('/:\d+.*$/', '', $host);
        $host = rtrim($host, '/');
        $port = $this->profile->port ?: 2083;

        $url = "https://{$host}:{$port}/execute/{$module}/{$function}";

        $request = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'cpanel '.trim($this->profile->cpanel_username).':'.trim($this->profile->api_token),
            ])
            ->timeout(60);

        $response = strtoupper($method) === 'POST'
            ? $request->asForm()->post($url, $params)
            : $request->get($url, $params);

        if ($response->failed()) {
            throw new \Exception("cPanel UAPI Error ({$module}::{$function}): HTTP {$response->status()} - {$response->body()}");
        }

        $rawBody = $response->body();
        // Remove UTF-8 BOM if present
        $rawBody = preg_replace('/^'.pack('H*', 'EFBBBF').'/', '', $rawBody);

        $responseData = json_decode($rawBody, true);

        if (! is_array($responseData)) {
            $rawSample = substr($rawBody, 0, 500);
            throw new \Exception('cPanel UAPI Error: Invalid JSON response format from server. JSON Error: '.json_last_error_msg().'. Raw response: '.$rawSample);
        }

        // Some versions/endpoints of UAPI wrap the response in 'result', others return it directly at the root.
        $result = $responseData['result'] ?? $responseData;

        // Check for API-level errors
        if (isset($result['status']) && $result['status'] === 0) {
            $errors = $result['errors'] ?? ['Unknown API error'];
            $errorMsg = is_array($errors) ? implode(', ', $errors) : $errors;
            throw new \Exception("cPanel UAPI Error ({$module}::{$function}): ".$errorMsg);
        }

        return $result['data'] ?? true;
    }

    public function testConnection(): array
    {
        try {
            // Retrieve domain information to verify connection and provide meaningful data
            $data = $this->callUapi('DomainInfo', 'list_domains');

            $domains = [];
            if (isset($data['main_domain'])) {
                $domains[] = $data['main_domain'];
            }
            if (isset($data['addon_domains'])) {
                $domains = array_merge($domains, $data['addon_domains']);
            }

            return [
                'status' => 'success',
                'domains' => $domains,
                'message' => 'Connected successfully to cPanel.',
            ];
        } catch (\Exception $e) {
            throw new \Exception('Connection test failed: '.$e->getMessage());
        }
    }

    public function createDatabase(string $dbName): bool
    {
        $this->callUapi('Mysql', 'create_database', [
            'name' => $dbName,
        ]);

        return true;
    }

    public function createDatabaseUser(string $dbUser, string $password): bool
    {
        $this->callUapi('Mysql', 'create_user', [
            'name' => $dbUser,
            'password' => $password,
        ]);

        return true;
    }

    public function setDatabaseUserPassword(string $dbUser, string $password): bool
    {
        $this->callUapi('Mysql', 'set_password', [
            'user' => $dbUser,
            'password' => $password,
        ]);

        return true;
    }

    public function grantPrivileges(string $dbName, string $dbUser): bool
    {
        $this->callUapi('Mysql', 'set_privileges_on_database', [
            'database' => $dbName,
            'user' => $dbUser,
            'privileges' => 'ALL PRIVILEGES',
        ]);

        return true;
    }

    public function uploadFile(string $localPath, string $remoteDir, string $remoteFileName): bool
    {
        $host = preg_replace('/^https?:\/\//', '', $this->profile->hostname);
        $host = rtrim($host, '/');
        $port = $this->profile->port ?: 2083;
        $url = "https://{$host}:{$port}/execute/Fileman/upload_files";

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'cpanel '.trim($this->profile->cpanel_username).':'.trim($this->profile->api_token),
            ])
            ->timeout(1200) // Upload large archives can take time
            ->attach(
                'file-1', fopen($localPath, 'r'), $remoteFileName
            )
            ->post($url, [
                'dir' => $remoteDir,
                'overwrite' => 1,
            ]);

        if ($response->failed()) {
            throw new \Exception("cPanel File Upload Error: HTTP {$response->status()} - {$response->body()}");
        }

        $responseData = $response->json();

        if (! is_array($responseData)) {
            $rawBody = substr($response->body(), 0, 500);
            throw new \Exception('cPanel File Upload Error: Invalid response format from server. Raw response: '.$rawBody);
        }

        $result = $responseData['result'] ?? $responseData;

        if (isset($result['status']) && $result['status'] === 0) {
            $errors = $result['errors'] ?? ['Unknown upload error'];
            $errorMsg = is_array($errors) ? implode(', ', $errors) : $errors;
            throw new \Exception('cPanel File Upload Error: '.$errorMsg);
        }

        return true;
    }

    public function extractZip(string $remoteFilePath, string $remoteExtractDir): bool
    {
        $cpanelUser = trim($this->profile->cpanel_username);
        $homePrefix = "/home/{$cpanelUser}/";

        if (! str_starts_with($remoteFilePath, '/home/')) {
            $remoteFilePath = $homePrefix.ltrim($remoteFilePath, '/');
        }

        if (! str_starts_with($remoteExtractDir, '/home/')) {
            $remoteExtractDir = $homePrefix.ltrim($remoteExtractDir, '/');
        }

        $host = preg_replace('/^https?:\/\//', '', $this->profile->hostname);
        $host = rtrim($host, '/');
        $port = $this->profile->port ?: 2083;
        $url = "https://{$host}:{$port}/json-api/cpanel";

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'cpanel '.trim($this->profile->cpanel_username).':'.trim($this->profile->api_token),
            ])
            ->timeout(600)
            ->get($url, [
                'cpanel_jsonapi_apiversion' => '2',
                'cpanel_jsonapi_module' => 'Fileman',
                'cpanel_jsonapi_func' => 'fileop',
                'op' => 'extract',
                'sourcefiles' => $remoteFilePath,
                'destfiles' => $remoteExtractDir,
                'doubledecode' => '1',
            ]);

        if ($response->failed()) {
            throw new \Exception("cPanel API2 Extract Error: HTTP {$response->status()} - {$response->body()}");
        }

        $data = $response->json();

        if (isset($data['cpanelresult']['error']) && $data['cpanelresult']['error'] !== '') {
            throw new \Exception('cPanel API2 Extract Error: '.$data['cpanelresult']['error']);
        }

        // Also check if there's an error message inside the data array
        if (isset($data['cpanelresult']['data'][0]['result']) && $data['cpanelresult']['data'][0]['result'] === 0) {
            $errorMsg = $data['cpanelresult']['data'][0]['output'] ?? ($data['cpanelresult']['data'][0]['statusmsg'] ?? 'Unknown extract error');
            throw new \Exception('cPanel API2 Extract Error: '.$errorMsg);
        }

        return true;
    }

    public function createDirectory(string $remoteDir): bool
    {
        $this->callUapi('Fileman', 'mkdir', [
            'dir' => $remoteDir,
        ], 'POST');

        return true;
    }

    public function saveFileContent(string $remoteFilePath, string $content): bool
    {
        $dir = dirname($remoteFilePath);
        $file = basename($remoteFilePath);

        $this->callUapi('Fileman', 'save_file_content', [
            'dir' => $dir,
            'file' => $file,
            'content' => $content,
        ], 'POST');

        return true;
    }

    public function createDomain(string $domain, string $documentRoot): bool
    {
        $domain = trim($domain);
        $documentRoot = trim($documentRoot);

        // Normalize document root so it is relative to homedir and does NOT share main public_html
        $cpanelUser = trim($this->profile->cpanel_username);
        $homePrefix = "/home/{$cpanelUser}/";
        if (str_starts_with($documentRoot, $homePrefix)) {
            $dir = substr($documentRoot, strlen($homePrefix));
        } else {
            $dir = ltrim($documentRoot, '/');
        }

        // Standard convention on host: directory named directly as domain (e.g. wkcomputer.aimagency.vn)
        if (empty($dir) || $dir === 'public_html' || str_starts_with($dir, 'domains/')) {
            $dir = $domain;
        }

        $parts = explode('.', $domain);

        // If it's something like demo1.aimagency.vn
        if (count($parts) >= 3 && strlen($parts[count($parts) - 2]) > 3) {
            $sub = array_shift($parts);
            $rootdomain = implode('.', $parts);

            try {
                $this->callUapi('SubDomain', 'addsubdomain', [
                    'domain' => $sub,
                    'rootdomain' => $rootdomain,
                    'dir' => $dir,
                ], 'POST');

                return true;
            } catch (\Exception $e) {
                // If already exists on cPanel, update docroot to ensure it matches
                try {
                    $this->callUapi('SubDomain', 'changedocroot', [
                        'domain' => $domain,
                        'docroot' => $dir,
                    ], 'POST');

                    return true;
                } catch (\Exception $ignored) {
                    // Fallback to addon domain below
                }
            }
        }

        // Addon Domain via cPanel API 2 (AddonDomain::addaddondomain)
        $subdomain = str_replace('.', '_', $domain);
        $host = preg_replace('/^https?:\/\//', '', $this->profile->hostname);
        $host = rtrim($host, '/');
        $port = $this->profile->port ?: 2083;
        $url = "https://{$host}:{$port}/json-api/cpanel";

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Authorization' => 'cpanel '.trim($this->profile->cpanel_username).':'.trim($this->profile->api_token),
            ])
            ->timeout(60)
            ->get($url, [
                'cpanel_jsonapi_apiversion' => '2',
                'cpanel_jsonapi_module' => 'AddonDomain',
                'cpanel_jsonapi_func' => 'addaddondomain',
                'dir' => $dir,
                'newdomain' => $domain,
                'subdomain' => $subdomain,
            ]);

        $data = $response->json();
        if (isset($data['cpanelresult']['error']) && $data['cpanelresult']['error'] !== '') {
            $err = $data['cpanelresult']['error'];
            if (! str_contains($err, 'already exists') && ! str_contains($err, 'owned by')) {
                throw new \Exception('cPanel API2 AddonDomain Error: '.$err);
            }
        }

        return true;
    }

    public function getServerIp(): string
    {
        $data = $this->callUapi('Variables', 'get_user_information');

        if (! empty($data['ip'])) {
            return $data['ip'];
        }

        if (! empty($data['sharedip'])) {
            return $data['sharedip'];
        }

        return '103.200.23.236';
    }

    /**
     * Retrieve cPanel account information (homedir, user, sharedip, etc.)
     */
    public function getAccountInfo(): array
    {
        try {
            $data = $this->callUapi('Variables', 'get_user_information');

            return [
                'user' => $data['user'] ?? $this->profile->cpanel_username,
                'homedir' => $data['homedir'] ?? ('/home/'.$this->profile->cpanel_username),
                'sharedip' => $data['sharedip'] ?? null,
                'status' => 'success',
            ];
        } catch (\Throwable $e) {
            return [
                'user' => $this->profile->cpanel_username,
                'homedir' => '/home/'.$this->profile->cpanel_username,
                'sharedip' => null,
                'status' => 'fallback',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve full details of all domains on this cPanel account with document roots
     */
    public function getDetailedDomains(): array
    {
        try {
            $data = $this->callUapi('DomainInfo', 'domains_data', ['format' => 'hash']);

            $domains = [];

            // Main domain
            if (! empty($data['main_domain'])) {
                $main = $data['main_domain'];
                $domains[$main['domain']] = [
                    'domain' => $main['domain'],
                    'type' => 'main_domain',
                    'document_root' => $main['documentroot'] ?? '',
                    'homedir' => $main['homedir'] ?? '',
                    'ip' => $main['ip'] ?? '',
                    'php_version' => $main['phpversion'] ?? null,
                ];
            }

            // Addon domains
            if (! empty($data['addon_domains']) && is_array($data['addon_domains'])) {
                foreach ($data['addon_domains'] as $addon) {
                    if (is_array($addon) && ! empty($addon['domain'])) {
                        $domains[$addon['domain']] = [
                            'domain' => $addon['domain'],
                            'type' => 'addon_domain',
                            'document_root' => $addon['documentroot'] ?? '',
                            'homedir' => $addon['homedir'] ?? '',
                            'ip' => $addon['ip'] ?? '',
                            'php_version' => $addon['phpversion'] ?? null,
                        ];
                    }
                }
            }

            // Subdomains
            if (! empty($data['sub_domains']) && is_array($data['sub_domains'])) {
                foreach ($data['sub_domains'] as $sub) {
                    if (is_array($sub) && ! empty($sub['domain'])) {
                        $domains[$sub['domain']] = [
                            'domain' => $sub['domain'],
                            'type' => 'sub_domain',
                            'document_root' => $sub['documentroot'] ?? '',
                            'homedir' => $sub['homedir'] ?? '',
                            'ip' => $sub['ip'] ?? '',
                            'php_version' => $sub['phpversion'] ?? null,
                        ];
                    }
                }
            }

            return $domains;
        } catch (\Throwable $e) {
            \Log::warning('cPanel getDetailedDomains failed: '.$e->getMessage());

            return [];
        }
    }
}
