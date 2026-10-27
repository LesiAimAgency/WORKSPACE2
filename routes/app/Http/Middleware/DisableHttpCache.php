<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableHttpCache
{
    /**
     * Tắt browser HTTP cache khi ở môi trường debug/phát triển
     * Giúp mọi thay đổi CSS, JS, Blade template cập nhật ngay lập tức khi F5
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (config('app.debug', false) || env('APP_ENV') === 'local') {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
