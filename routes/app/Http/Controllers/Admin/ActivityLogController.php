<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'project']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model
        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by project (if multi-tenant)
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $logs = $query->latest()->paginate(25);

        // Get filter options
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $actions = ActivityLog::distinct()->pluck('action')->filter()->sort();
        $models = ActivityLog::distinct()->pluck('model')->filter()->sort();

        return view('cms.activity-logs.index', compact('logs', 'users', 'actions', 'models'));
    }

    /**
     * Display the specified activity log.
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['user', 'project']);

        return view('cms.activity-logs.show', compact('activityLog'));
    }

    /**
     * Get activity logs for a specific user.
     */
    public function userLogs(Request $request, User $user)
    {
        $query = $user->activityLogs()->with(['project']);

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->latest()->paginate(20);

        return view('cms.activity-logs.user-logs', compact('logs', 'user'));
    }

    /**
     * Export activity logs to CSV.
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with(['user', 'project']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $logs = $query->latest()->get();

        $filename = 'activity_logs_'.now()->format('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'ID',
                'User',
                'Email',
                'Action',
                'Model',
                'Model ID',
                'Description',
                'IP Address',
                'User Agent',
                'Project',
                'Created At',
            ]);

            // CSV data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'N/A',
                    $log->user?->email ?? 'N/A',
                    $log->action,
                    $log->model ?? 'N/A',
                    $log->model_id ?? 'N/A',
                    $log->description,
                    $log->ip_address ?? 'N/A',
                    $log->user_agent ?? 'N/A',
                    $log->project?->name ?? 'N/A',
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clear old activity logs.
     */
    public function clearOld(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $cutoffDate = now()->subDays($request->days);
        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        // Log this action
        if (! app()->environment('testing')) {
            ActivityLog::log(
                'activity_logs_cleared',
                "Cleared {$deletedCount} activity logs older than {$request->days} days",
                null,
                'ActivityLog',
                null
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} old activity logs.",
            'deleted_count' => $deletedCount,
        ]);
    }

    /**
     * Get activity statistics.
     */
    public function statistics(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);

        $stats = [
            'total_logs' => ActivityLog::where('created_at', '>=', $startDate)->count(),
            'unique_users' => ActivityLog::where('created_at', '>=', $startDate)
                ->distinct('user_id')
                ->count('user_id'),
            'top_actions' => ActivityLog::where('created_at', '>=', $startDate)
                ->selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
            'top_users' => ActivityLog::where('created_at', '>=', $startDate)
                ->with('user:id,name,email')
                ->selectRaw('user_id, COUNT(*) as count')
                ->groupBy('user_id')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
            'daily_activity' => ActivityLog::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return response()->json($stats);
    }
}
