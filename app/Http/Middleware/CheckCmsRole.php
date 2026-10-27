<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCmsRole
{
    /**
     * Handle an incoming request for CMS admin access.
     * Standalone project has only 1 admin/cms account.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = session('project_user_id') ?? Auth::id();

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = Auth::user() ?? User::find($userId);

        if (! $user) {
            session()->forget(['project_user_id', 'project_user_username', 'current_project']);
            Auth::logout();

            return redirect()->route('login');
        }

        view()->share('authUser', $user);
        $request->attributes->set('auth_user', $user);
        if (! Auth::check()) {
            Auth::setUser($user);
        }

        // Cho phép các role quản trị: cms, admin, superadmin
        if (isset($user->role) && in_array(strtolower($user->role), ['cms', 'admin', 'superadmin'])) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập CMS này (Chỉ tài khoản vai trò CMS/Admin mới được phép).');
    }
}
