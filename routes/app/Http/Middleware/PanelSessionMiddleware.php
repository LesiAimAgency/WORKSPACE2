<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class PanelSessionMiddleware
{
    public function handle(Request $request, Closure $next, $panel = 'web')
    {
        // Set session cookie name riêng cho từng panel
        $cookieName = 'laravel_session_'.$panel;
        Config::set('session.cookie', $cookieName);

        // Set guard mặc định cho panel
        Config::set('auth.defaults.guard', $panel);

        return $next($request);
    }
}
