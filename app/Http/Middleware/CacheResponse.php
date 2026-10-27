<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheResponse
{
    public function handle(Request $request, Closure $next, int $minutes = 5)
    {
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        $key = 'response_'.md5($request->fullUrl());

        return Cache::remember($key, now()->addMinutes($minutes), function () use ($next, $request) {
            return $next($request);
        });
    }
}
