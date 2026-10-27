<?php

declare(strict_types=1);

namespace App\Services\Tenancy;

use App\Models\Project;

class ProjectContext
{
    private ?Project $project = null;

    private ?string $theme = null;

    private ?string $connection = null;

    public function setProject(Project $project): void
    {
        $this->project = $project;

        // Share to views globally
        view()->share('currentProject', $project);
        view()->share('currentTenant', null);

        // Bind to service container
        app()->instance('current_project', $project);
        app()->instance('current_project_id', $project->id);

        if (session()) {
            session(['current_project_id' => $project->id]);
            session(['current_project_code' => $project->code]);
        }
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getTenant(): ?object
    {
        return null;
    }

    public function getProjectId(): ?int
    {
        return $this->project?->id ?? 1;
    }

    public function getTenantId(): ?int
    {
        return null;
    }

    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
        view()->share('activeTheme', $theme);
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

    public function getConnection(): ?string
    {
        return $this->connection;
    }

    public function clear(): void
    {
        $this->project = null;
        $this->theme = null;
        $this->connection = null;
    }
}
