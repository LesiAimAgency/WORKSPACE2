<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProjectApiController extends Controller
{
    /**
     * Authenticate request using Bearer project token and match with project_uuid.
     */
    protected function authenticateToken(Request $request): ?ProjectToken
    {
        $bearer = $request->bearerToken();
        if (! $bearer) {
            $bearer = $request->header('X-Project-Token');
        }

        if (! $bearer) {
            return null;
        }

        $token = ProjectToken::findValidToken($bearer);
        if (! $token) {
            return null;
        }

        $uuid = $request->input('project_uuid') ?: $request->header('X-Project-UUID');
        if ($uuid && $token->project && $token->project->uuid !== $uuid) {
            Log::warning("[ProjectAPI] UUID mismatch: Token project {$token->project->uuid} vs requested {$uuid}");

            return null;
        }

        return $token;
    }

    /**
     * POST /api/v1/projects/register
     *
     * Called by Website Installer at step 17 to establish link with VGT Core.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_uuid' => 'required|string',
            'domain' => 'nullable|string',
            'app_url' => 'nullable|string',
            'project_key' => 'nullable|string',
            'installation_id' => 'nullable|string',
            'theme' => 'nullable|string',
            'theme_version' => 'nullable|string',
            'cms_version' => 'nullable|string',
            'php_version' => 'nullable|string',
            'installed_at' => 'nullable|string',
        ]);

        $uuid = $validated['project_uuid'];
        $domain = $validated['domain'] ?? parse_url($validated['app_url'] ?? '', PHP_URL_HOST) ?? 'unknown.local';
        $theme = $validated['theme'] ?? 'default';

        // 1. Find existing project or register a new one in VGT Core
        $project = Project::where('uuid', $uuid)->first();
        if (! $project && ! empty($validated['project_key'])) {
            $project = Project::where('project_key', $validated['project_key'])->first();
        }

        if (! $project) {
            // Check if domain matches any pending project
            $project = Project::where('subdomain', $domain)
                ->orWhere('external_domain', $domain)
                ->orWhere('code', Str::slug($domain, '_'))
                ->first();
        }

        if ($project) {
            // Update project with installation metadata
            $project->update([
                'uuid' => $uuid,
                'project_key' => $validated['project_key'] ?? $project->project_key ?: 'prj_'.Str::slug($domain, '_'),
                'installation_id' => $validated['installation_id'] ?? $project->installation_id,
                'external_domain' => $domain,
                'remote_url' => $validated['app_url'] ?? $project->remote_url,
                'theme_version' => $validated['theme_version'] ?? '1.0.0',
                'cms_version' => $validated['cms_version'] ?? '2.0.0',
                'connection_status' => 'online',
                'last_heartbeat_at' => now(),
                'remote_ip' => $request->ip(),
                'features' => array_merge($project->features ?? [], ['theme' => $theme]),
            ]);
        } else {
            // Create a newly registered project record
            $code = Str::slug($domain, '_');
            if (Project::where('code', $code)->exists()) {
                $code .= '_'.substr(md5($uuid), 0, 4);
            }

            $project = Project::create([
                'uuid' => $uuid,
                'name' => 'Website '.$domain,
                'code' => $code,
                'subdomain' => $domain,
                'external_domain' => $domain,
                'remote_url' => $validated['app_url'] ?? "https://{$domain}",
                'project_key' => $validated['project_key'] ?? 'prj_'.$code,
                'installation_id' => $validated['installation_id'] ?? 'inst_'.md5($uuid),
                'status' => 'active',
                'theme_version' => $validated['theme_version'] ?? '1.0.0',
                'cms_version' => $validated['cms_version'] ?? '2.0.0',
                'connection_status' => 'online',
                'last_heartbeat_at' => now(),
                'remote_ip' => $request->ip(),
                'features' => ['theme' => $theme],
            ]);
        }

        // 2. Issue a unique Project Token
        $tokenData = $project->issueToken('installer_registration');

        // 3. Log registration telemetry
        DB::table('remote_telemetry_logs')->insert([
            'project_id' => $project->id,
            'event_type' => 'website_registered',
            'ip_address' => $request->ip(),
            'payload' => json_encode($validated),
            'status_code' => 201,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Website registered successfully with VGT Core.',
            'project_uuid' => $project->uuid,
            'project_token' => $tokenData['token'],
            'heartbeat_interval_seconds' => 300,
            'registered_at' => now()->toIso8601String(),
            'core_version' => '2.0.0',
        ], 201);
    }

    /**
     * POST /api/v1/projects/heartbeat
     *
     * Periodic telemetry signal sent by Website Application.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $token = $this->authenticateToken($request);
        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or revoked project token.',
            ], 401);
        }

        $project = $token->project;
        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'Project associated with this token not found.',
            ], 404);
        }

        // Update token last used
        $token->update(['last_used_at' => now()]);

        // Update project heartbeat & metrics
        $metrics = $request->input('metrics', []);
        $project->update([
            'last_heartbeat_at' => now(),
            'connection_status' => 'online',
            'remote_ip' => $request->ip(),
            'cms_version' => $request->input('cms_version', $project->cms_version),
            'theme_version' => $request->input('theme_version', $project->theme_version),
            'health_metrics' => is_array($metrics) ? $metrics : json_decode((string) $metrics, true),
        ]);

        return response()->json([
            'success' => true,
            'status' => 'acknowledged',
            'project_uuid' => $project->uuid,
            'connection_status' => 'online',
            'core_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * GET /api/v1/projects/status
     *
     * Check status of a project.
     */
    public function status(Request $request): JsonResponse
    {
        $token = $this->authenticateToken($request);
        if (! $token) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $project = $token->project;

        return response()->json([
            'success' => true,
            'project_uuid' => $project->uuid,
            'project_name' => $project->name,
            'domain' => $project->external_domain ?: $project->subdomain,
            'status' => $project->status,
            'connection_status' => $project->connection_status,
            'cms_version' => $project->cms_version,
            'theme_version' => $project->theme_version,
            'last_heartbeat_at' => $project->last_heartbeat_at?->toIso8601String(),
            'is_online' => $project->isOnline(),
        ]);
    }

    /**
     * POST /api/v1/projects/report
     *
     * Receive error or diagnostic reports from Website Application.
     */
    public function report(Request $request): JsonResponse
    {
        $token = $this->authenticateToken($request);
        if (! $token) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $project = $token->project;
        $event = $request->input('event_type', 'incident_report');
        $payload = $request->input('data', $request->all());

        DB::table('remote_telemetry_logs')->insert([
            'project_id' => $project->id,
            'event_type' => $event,
            'ip_address' => $request->ip(),
            'payload' => json_encode($payload),
            'status_code' => (int) $request->input('status_code', 200),
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report logged successfully.',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * POST /api/v1/projects/token/rotate
     *
     * Rotate current project token.
     */
    public function rotateToken(Request $request): JsonResponse
    {
        $token = $this->authenticateToken($request);
        if (! $token) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $project = $token->project;

        // Revoke old token
        $token->revoke();

        // Issue new token
        $newToken = $project->issueToken('rotated_token');

        // Log rotation
        DB::table('remote_telemetry_logs')->insert([
            'project_id' => $project->id,
            'event_type' => 'token_rotated',
            'ip_address' => $request->ip(),
            'payload' => json_encode(['old_token_prefix' => $token->token_prefix]),
            'status_code' => 200,
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token rotated successfully. Please update your website environment.',
            'new_project_token' => $newToken['token'],
            'rotated_at' => now()->toIso8601String(),
        ]);
    }
}
