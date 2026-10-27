<?php

declare(strict_types=1);

namespace App\Core\Theme;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class ThemeManifest
{
    protected array $data = [];

    protected string $filePath = '';

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        if (File::exists($filePath)) {
            $content = File::get($filePath);
            $this->data = json_decode($content, true) ?: [];
        }
    }

    public function getPath(): string
    {
        return $this->filePath;
    }

    public static function load(string $path): ?self
    {
        if (! File::exists($path)) {
            return null;
        }

        return new self($path);
    }

    public function isValid(): bool
    {
        return ! empty($this->getName()) && ! empty($this->getVersion());
    }

    public function getName(): string
    {
        return (string) Arr::get($this->data, 'name', '');
    }

    public function getTitle(): string
    {
        return (string) Arr::get($this->data, 'title', $this->getName());
    }

    public function getVersion(): string
    {
        return (string) Arr::get($this->data, 'version', '1.0.0');
    }

    public function getDescription(): string
    {
        return (string) Arr::get($this->data, 'description', '');
    }

    public function getAuthor(): string
    {
        return (string) Arr::get($this->data, 'author', 'VGT Team');
    }

    public function getFeatures(): array
    {
        return (array) Arr::get($this->data, 'features', []);
    }

    public function getDatabaseTables(): array
    {
        return (array) Arr::get($this->data, 'database.tables', []);
    }

    public function getSeeders(): array
    {
        return (array) Arr::get($this->data, 'database.seeders', []);
    }

    public function getWidgets(): array
    {
        return (array) Arr::get($this->data, 'widgets', []);
    }

    public function getMenus(): array
    {
        return (array) Arr::get($this->data, 'menus', []);
    }

    public function getDefaultSettings(): array
    {
        return (array) Arr::get($this->data, 'default_settings', []);
    }

    public function getRoutesFile(): ?string
    {
        return Arr::get($this->data, 'routes_file');
    }

    public function getViewPath(): string
    {
        return (string) Arr::get($this->data, 'view_path', 'resources/views/themes/'.$this->getName());
    }

    public function getAssetPath(): string
    {
        return (string) Arr::get($this->data, 'asset_path', 'public/themes/'.$this->getName());
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
