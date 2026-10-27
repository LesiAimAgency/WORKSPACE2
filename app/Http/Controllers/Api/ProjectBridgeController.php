<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectBridgeController extends Controller
{
    public function handle(Request $request)
    {
        $token = $request->header('X-Bridge-Token');
        $projectCode = $request->input('project_code');

        if (! $this->validateToken($token, $projectCode)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $action = $request->input('action');

        return match ($action) {
            'get_config' => $this->getConfig($request),
            'update_config' => $this->updateConfig($request),
            'get_history' => $this->getHistory($request),
            'sync_data' => $this->syncData($request),
            'get_stats' => $this->getStats($request),
            default => response()->json(['error' => 'Invalid action'], 400)
        };
    }

    private function validateToken($token, $projectCode)
    {
        $project = \DB::connection('mysql')
            ->table('projects')
            ->where('code', $projectCode)
            ->first();

        if (! $project || ! $project->api_token) {
            return false;
        }

        return hash_equals($project->api_token, $token);
    }

    private function getConfig(Request $request)
    {
        $modules = DB::table('settings')
            ->where('group', 'modules')
            ->get();

        return response()->json([
            'success' => true,
            'modules' => $modules,
        ]);
    }

    private function updateConfig(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['payload' => json_encode($value), 'updated_at' => now()]
            );
        }

        return response()->json(['success' => true]);
    }

    private function getHistory(Request $request)
    {
        $logs = DB::table('activity_logs')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'logs' => $logs,
        ]);
    }

    private function syncData(Request $request)
    {
        $data = $request->input('data', []);

        foreach ($data as $table => $rows) {
            if (in_array($table, ['settings', 'menus', 'widgets', 'posts'])) {
                DB::table($table)->truncate();
                DB::table($table)->insert($rows);
            }
        }

        return response()->json(['success' => true]);
    }

    private function getStats(Request $request)
    {
        $stats = [
            'users' => DB::table('users')->count(),
            'products' => DB::table('products_enhanced')->count(),
            'orders' => DB::table('orders')->count(),
            'posts' => DB::table('posts')->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }
}
