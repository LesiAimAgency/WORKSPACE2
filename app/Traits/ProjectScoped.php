<?php

namespace App\Traits;

use App\Models\Project;
use Illuminate\Support\Facades\Schema;

trait ProjectScoped
{
    /**
     * Cache table column existence to avoid repeated information_schema queries.
     *
     * @var array<string, bool>
     */
    protected static array $projectColumnCache = [];

    /**
     * Check if a table has a specific column with caching.
     */
    protected static function projectTableHasColumn(string $table, string $column): bool
    {
        $key = $table.'.'.$column;
        if (! isset(static::$projectColumnCache[$key])) {
            static::$projectColumnCache[$key] = Schema::hasColumn($table, $column);
        }

        return static::$projectColumnCache[$key];
    }

    /**
     * Boot the trait
     */
    protected static function bootProjectScoped(): void
    {
        static::addGlobalScope('project', function ($builder) {
            if (config('app.bypass_project_scope', false)) {
                return;
            }

            $table = $builder->getModel()->getTable();
            if ($table === 'users' || $table === 'projects') {
                return;
            }

            $project = request()->attributes->get('project');
            $projectId = $project?->id ?? session('current_project_id') ?? (app()->bound('current_project_id') ? app('current_project_id') : 1);

            $hasProjectColumn = static::projectTableHasColumn($table, 'project_id');
            if ($projectId && $hasProjectColumn) {
                $builder->where($table.'.project_id', $projectId);
            }
        });

        // Automatically set project_id when creating
        static::creating(function ($model) {
            $project = request()->attributes->get('project');
            $projectId = $project?->id ?? session('current_project_id') ?? (app()->bound('current_project_id') ? app('current_project_id') : 1);

            if ($projectId && empty($model->project_id) && static::projectTableHasColumn($model->getTable(), 'project_id')) {
                $model->project_id = $projectId;
            }
        });
    }

    /**
     * Get the project relationship
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
