<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SuperAdminLogHelper
{
    /**
     * Ghi log các hoạt động trong SuperAdmin
     *
     * @param  string  $action  Hành động (VD: 'Cập nhật Task', 'Tạo User')
     * @param  string|array  $changes  Chi tiết thay đổi
     * @return void
     */
    public static function logActivity($action, $changes = '')
    {
        $user = Auth::user();
        $userName = $user ? $user->name.' (ID: '.$user->id.')' : 'System/Unknown';

        if (is_array($changes)) {
            $changes = json_encode($changes, JSON_UNESCAPED_UNICODE);
        }

        $logMessage = sprintf(
            'Action: %s | User: %s | Changes: %s',
            $action,
            $userName,
            $changes
        );

        Log::channel('superadmin')->info($logMessage);
    }
}
