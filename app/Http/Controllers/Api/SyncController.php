<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    /**
     * Resolve the current project for this standalone satellite.
     * In standalone mode, there is always exactly one project.
     */
    private function resolveProject(): ?Project
    {
        return Project::first();
    }

    /**
     * Nhận danh sách widgets từ SuperAdmin và cập nhật nội bộ
     */
    public function syncWidgets(Request $request)
    {
        $payload = $request->input('data', []);

        if (empty($payload)) {
            return response()->json(['success' => false, 'message' => 'No data provided'], 400);
        }

        $project = $this->resolveProject();

        try {
            DB::beginTransaction();

            foreach ($payload as $widgetData) {
                // Ensure project_id and tenant_id are scoped to this project
                if ($project) {
                    $widgetData['project_id'] = $project->id;
                    $widgetData['tenant_id'] = $project->id;
                }

                DB::table('widgets')->updateOrInsert(
                    ['id' => $widgetData['id']],
                    $widgetData
                );
            }

            DB::commit();

            // Clear widget cache (use Cache::forget instead of Cache::tags for file driver compatibility)
            Cache::forget('widgets_all');
            Cache::forget('widgets_project_'.($project?->id ?? 'default'));

            Log::info('Widgets synced successfully. Count: '.count($payload));

            return response()->json(['success' => true, 'message' => 'Widgets synced successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync widgets failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Nhận danh sách settings từ SuperAdmin và cập nhật nội bộ
     */
    public function syncSettings(Request $request)
    {
        $payload = $request->input('data', []);

        if (empty($payload)) {
            return response()->json(['success' => false, 'message' => 'No data provided'], 400);
        }

        $project = $this->resolveProject();

        try {
            DB::beginTransaction();

            foreach ($payload as $settingData) {
                // Ensure project_id and tenant_id are scoped to this project
                if ($project) {
                    $settingData['project_id'] = $project->id;
                    $settingData['tenant_id'] = $project->id;
                }

                DB::table('settings')->updateOrInsert(
                    ['key' => $settingData['key']],
                    $settingData
                );
            }

            DB::commit();

            // Clear settings cache
            Cache::forget('global_settings');
            Cache::forget('settings_project_'.($project?->id ?? 'default'));

            Log::info('Settings synced successfully. Count: '.count($payload));

            return response()->json(['success' => true, 'message' => 'Settings synced successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync settings failed: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
