<?php

namespace App\Services\Hosting;

use App\Models\HostingProfile;
use App\Services\Hosting\Contracts\HostingClientInterface;
use Illuminate\Support\Facades\Http;

class DirectAdminHostingClient implements HostingClientInterface
{
    protected $profile;

    protected $baseUrl;

    protected $username;

    protected $apiToken;

    public function setProfile(HostingProfile $profile): self
    {
        $this->profile = $profile;
        // DirectAdmin typically uses port 2222
        $port = $profile->port ?: 2222;
        $host = preg_replace('/^https?:\/\//', '', $profile->hostname);
        $host = rtrim($host, '/');
        $this->baseUrl = "https://{$host}:{$port}";
        $this->username = $profile->cpanel_username; // We reuse this field for DA username
        $this->apiToken = $profile->api_token;

        return $this;
    }

    protected function client()
    {
        return Http::withBasicAuth($this->username, $this->apiToken)
            ->withoutVerifying()
            ->timeout(60);
    }

    public function testConnection(): array
    {
        // CMD_API_SHOW_USER_DOMAINS
        $response = $this->client()->get("{$this->baseUrl}/CMD_API_SHOW_USER_DOMAINS");

        if (! $response->successful() || strpos($response->body(), 'error=1') !== false) {
            throw new \Exception('Failed to connect to DirectAdmin: '.$response->body());
        }

        // Parse the url-encoded body to get domains
        parse_str($response->body(), $data);
        $domains = array_keys($data);

        return [
            'status' => 'success',
            'domains' => $domains,
            'message' => 'Connected successfully to DirectAdmin.',
        ];
    }

    public function createDatabase(string $dbName): bool
    {
        throw new \Exception('DirectAdmin createDatabase not fully implemented yet.');
    }

    public function createDatabaseUser(string $dbUser, string $password): bool
    {
        throw new \Exception('DirectAdmin createDatabaseUser not fully implemented yet.');
    }

    public function grantPrivileges(string $dbName, string $dbUser): bool
    {
        throw new \Exception('DirectAdmin grantPrivileges not fully implemented yet.');
    }

    public function uploadFile(string $localPath, string $remoteDir, string $remoteFileName): bool
    {
        throw new \Exception('DirectAdmin File Upload via API is not fully supported yet. Please use FTP/SFTP.');
    }

    public function extractZip(string $remoteFilePath, string $remoteExtractDir): bool
    {
        throw new \Exception('DirectAdmin Zip Extraction via API is not fully supported yet.');
    }

    public function createDirectory(string $remoteDir): bool
    {
        throw new \Exception('DirectAdmin Directory Creation via API is not fully supported yet.');
    }

    public function saveFileContent(string $remoteFilePath, string $content): bool
    {
        throw new \Exception('DirectAdmin saveFileContent via API is not fully supported yet.');
    }

    public function createDomain(string $domain, string $documentRoot): bool
    {
        throw new \Exception('DirectAdmin createDomain via API is not fully supported yet.');
    }

    public function getServerIp(): string
    {
        throw new \Exception('DirectAdmin getServerIp via API is not fully supported yet.');
    }
}
