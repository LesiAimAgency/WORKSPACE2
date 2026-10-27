<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RemoteProjectService
{
    private function getToken($projectCode)
    {
        $project = \DB::table('projects')
            ->where('code', $projectCode)
            ->first();

        return $project->api_token ?? null;
    }

    public function callRemote($projectUrl, $projectCode, $action, $data = [])
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-Bridge-Token' => $this->getToken($projectCode),
                    'Accept' => 'application/json',
                ])
                ->post($projectUrl.'/api/bridge', array_merge([
                    'project_code' => $projectCode,
                    'action' => $action,
                ], $data));

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'HTTP '.$response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('Remote project call failed: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getRemoteConfig($projectUrl, $projectCode)
    {
        return $this->callRemote($projectUrl, $projectCode, 'get_config');
    }

    public function updateRemoteConfig($projectUrl, $projectCode, $settings)
    {
        return $this->callRemote($projectUrl, $projectCode, 'update_config', [
            'settings' => $settings,
        ]);
    }

    public function getRemoteHistory($projectUrl, $projectCode)
    {
        return $this->callRemote($projectUrl, $projectCode, 'get_history');
    }

    public function syncRemoteData($projectUrl, $projectCode, $data)
    {
        return $this->callRemote($projectUrl, $projectCode, 'sync_data', [
            'data' => $data,
        ]);
    }

    public function getRemoteStats($projectUrl, $projectCode)
    {
        return $this->callRemote($projectUrl, $projectCode, 'get_stats');
    }
}
