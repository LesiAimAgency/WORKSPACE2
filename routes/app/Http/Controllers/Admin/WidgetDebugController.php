<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WidgetPermissionService;

class WidgetDebugController extends Controller
{
    public function permissions()
    {
        if (config('app.env') !== 'local') {
            abort(404);
        }

        $user = auth()->user();
        $permissionService = new WidgetPermissionService;

        $debugInfo = [
            'user' => $user ? [
                'id' => $user->id,
                'email' => $user->email,
                'level' => $user->level ?? 'not set',
                'role' => $user->role ?? 'not set',
                'all_attributes' => $user->getAttributes(),
            ] : null,
            'permissions' => [
                'can_manage_widgets' => $permissionService->canManageWidgets(),
                'can_toggle_widgets' => $permissionService->canToggleWidgets(),
                'permission_summary' => $permissionService->getPermissionSummary(),
            ],
            'environment' => [
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
                'session_widget_dev_access' => session('widget_dev_access', false),
            ],
        ];

        return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
    }

    public function grantAccess()
    {
        if (config('app.env') !== 'local') {
            abort(404);
        }

        session(['widget_dev_access' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Development access granted for this session',
            'redirect_url' => route('cms.widgets.index'),
        ]);
    }
}
