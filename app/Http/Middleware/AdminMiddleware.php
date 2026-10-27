<?php

// MODIFIED: 2025-01-21

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $user = auth()->user();

        // Cho phép user có role 'admin' hoặc level <= 1 (SuperAdmin/Administrator)
        if (isset($user->role) && $user->role === 'admin') {
            return $next($request);
        }

        if (isset($user->level) && $user->level <= 1) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập khu vực cms.');
    }
}
