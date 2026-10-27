<?php

namespace App\Services\Hosting\Contracts;

use App\Models\HostingProfile;

interface HostingClientInterface
{
    /**
     * Set the hosting profile to be used for API requests.
     */
    public function setProfile(HostingProfile $profile): self;

    /**
     * Test connection and credentials.
     *
     * @return array Array with connection details or result data.
     */
    public function testConnection(): array;

    /**
     * Create a MySQL database.
     *
     * @param  string  $dbName  Full database name (including prefix)
     */
    public function createDatabase(string $dbName): bool;

    /**
     * Create a MySQL user.
     *
     * @param  string  $dbUser  Full database user (including prefix)
     * @param  string  $password  User password
     */
    public function createDatabaseUser(string $dbUser, string $password): bool;

    /**
     * Grant all privileges to a user on a database.
     *
     * @param  string  $dbName  Full database name
     * @param  string  $dbUser  Full database user
     */
    public function grantPrivileges(string $dbName, string $dbUser): bool;

    /**
     * Upload a file to the server.
     *
     * @param  string  $localPath  Path to local file
     * @param  string  $remoteDir  Remote directory (relative to home)
     * @param  string  $remoteFileName  Name of the file on the server
     */
    public function uploadFile(string $localPath, string $remoteDir, string $remoteFileName): bool;

    /**
     * Extract a ZIP archive on the server.
     *
     * @param  string  $remoteFilePath  Path to the ZIP file (relative to home)
     * @param  string  $remoteExtractDir  Directory to extract to (relative to home)
     */
    public function extractZip(string $remoteFilePath, string $remoteExtractDir): bool;

    /**
     * Create a directory on the server.
     *
     * @param  string  $remoteDir  Directory to create (relative to home)
     */
    public function createDirectory(string $remoteDir): bool;

    /**
     * Save content to a file on the server.
     *
     * @param  string  $remoteFilePath  Path to the file (relative to home)
     * @param  string  $content  Content to write
     */
    public function saveFileContent(string $remoteFilePath, string $content): bool;

    /**
     * Create an addon domain or subdomain.
     *
     * @param  string  $domain  The domain name to create
     * @param  string  $documentRoot  The document root for the domain
     */
    public function createDomain(string $domain, string $documentRoot): bool;

    /**
     * Get the server's shared IP address.
     */
    public function getServerIp(): string;
}
