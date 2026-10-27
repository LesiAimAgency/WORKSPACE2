<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Theme\ThemeManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VgtHeartbeatService
{
    /**
     * Send heartbeat telemetry to VGT Core.
     * Guaranteed non-blocking and fault-tolerant: NEVER throws exceptions.
     */
    public function sendHeartbeat(array $customMetrics = []): array
    {
        if (config('vgt.offline_mode')) {
            return [
                'success' => true,
                'status' => 'offline_mode_active',
                'message' => 'Website is configured in full offline mode. Heartbeat skipped.',
            ];
        }

        $coreUrl = rtrim(config('vgt.core_url') ?: '', '/');
        $uuid = config('vgt.project_uuid');
        $token = config('vgt.project_token');

        if (! $coreUrl || ! $uuid || ! $token) {
            return [
                'success' => false,
                'status' => 'unconfigured',
                'message' => 'VGT Core credentials not configured in environment.',
            ];
        }

        $themeManager = app(ThemeManager::class);
        $activeTheme = $themeManager->getActiveTheme();
        $manifest = $themeManager->getManifest($activeTheme);
        $themeVersion = $manifest?->getVersion() ?? '1.0.0';

        // Check local DB health
        $dbStatus = 'ok';
        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $dbStatus = 'error: '.$e->getMessage();
        }

        $payload = [
            'project_uuid' => $uuid,
            'theme' => $activeTheme,
            'theme_version' => $themeVersion,
            'cms_version' => config('vgt.cms_version', '2.0.0'),
            'domain' => request()->getHost(),
            'status' => $dbStatus === 'ok' ? 'healthy' : 'degraded',
            'metrics' => array_merge([
                'db_connection' => $dbStatus,
                'storage_writable' => is_writable(storage_path()),
                'php_version' => PHP_VERSION,
            ], $customMetrics),
            'timestamp' => time(),
        ];

        $timeout = config('vgt.heartbeat_timeout', 3);

        try {
            $response = Http::timeout($timeout)
                ->withToken($token)
                ->withHeaders([
                    'X-Project-UUID' => $uuid,
                    'Accept' => 'application/json',
                ])
                ->post("{$coreUrl}/api/v1/projects/heartbeat", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => 'acknowledged',
                    'response' => $response->json(),
                ];
            }

            Log::warning("[VGT Heartbeat] Core rejected heartbeat: HTTP {$response->status()} - {$response->body()}");

            return [
                'success' => false,
                'status' => 'rejected',
                'http_status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            // Core is offline or unreachable - Graceful Degradation
            Log::channel('single')->info("[VGT Heartbeat] Core unreachable (Graceful Fallback): {$e->getMessage()}");

            return [
                'success' => false,
                'status' => 'core_offline',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send error or incident report to VGT Core.
     */
    public function sendReport(string $eventType, array $payload = [], int $statusCode = 200): array
    {
        $coreUrl = rtrim(config('vgt.core_url') ?: '', '/');
        $token = config('vgt.project_token');
        $uuid = config('vgt.project_uuid');

        if (! $coreUrl || ! $token || ! $uuid || config('vgt.offline_mode')) {
            return ['success' => false, 'message' => 'Reporting disabled or unconfigured.'];
        }

        try {
            $response = Http::timeout(3)
                ->withToken($token)
                ->withHeaders(['X-Project-UUID' => $uuid])
                ->post("{$coreUrl}/api/v1/projects/report", [
                    'event_type' => $eventType,
                    'data' => $payload,
                    'status_code' => $statusCode,
                ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
