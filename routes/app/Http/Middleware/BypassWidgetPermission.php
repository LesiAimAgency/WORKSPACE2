<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BypassWidgetPermission
{
    public function handle(Request $request, Closure $next)
    {
        // Only in local environment
        if (config('app.env') === 'local') {
            session(['widget_dev_access' => true]);
        }

        return $next($request);
    }
}
