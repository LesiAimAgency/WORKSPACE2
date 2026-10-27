<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushWidgetSyncToRemote implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $project;

    protected $type;

    protected $payload;

    /**
     * Create a new job instance.
     */
    public function __construct($project, $type, $payload)
    {
        $this->project = $project;
        $this->type = $type; // 'widgets' or 'settings'
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (! $this->project->sync_enabled || empty($this->project->external_domain) || empty($this->project->api_token)) {
            return;
        }

        $url = rtrim($this->project->external_domain, '/').'/api/sync/'.$this->type;

        try {
            $response = Http::withHeaders([
                'X-API-TOKEN' => $this->project->api_token,
                'Accept' => 'application/json',
            ])->timeout(30)->post($url, [
                'data' => $this->payload,
            ]);

            if ($response->successful()) {
                Log::info("Successfully synced {$this->type} to {$url}");
            } else {
                Log::error("Failed to sync {$this->type} to {$url}. Status: ".$response->status().' Response: '.$response->body());
            }
        } catch (\Exception $e) {
            Log::error("Exception syncing {$this->type} to {$url}: ".$e->getMessage());
        }
    }
}
