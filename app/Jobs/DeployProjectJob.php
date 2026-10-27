<?php

namespace App\Jobs;

use App\Models\HostingProfile;
use App\Models\Project;
use App\Services\Hosting\DeploymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeployProjectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes

    protected $project;

    protected $profile;

    protected $userId;

    public function __construct(Project $project, HostingProfile $profile, int $userId)
    {
        $this->project = $project;
        $this->profile = $profile;
        $this->userId = $userId;
    }

    public function handle(DeploymentService $deploymentService): void
    {
        try {
            $deploymentService->deploy($this->project, $this->profile, $this->userId);
        } catch (\Exception $e) {
            \Log::error("DeployProjectJob failed for Project {$this->project->id}: ".$e->getMessage());
            // Optionally, handle failure logic (already handled partially in service)
        }
    }
}
