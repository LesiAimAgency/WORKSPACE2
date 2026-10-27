<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CMSMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Cho phép superadmin, admin, cms hoặc user có level <= 1
        if (! isset($user->role) || ! in_array($user->role, ['superadmin', 'cms', 'admin'])) {
            if (! isset($user->level) || $user->level > 1) {
                abort(403, 'Bạn không có quyền truy cập CMS.');
            }
        }

        return $next($request);
    }
}
