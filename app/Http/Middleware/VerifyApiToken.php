<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('X-API-TOKEN');

        // Token được cấu hình trong .env của satellite website (SYNC_API_TOKEN)
        $expectedToken = config('app.sync_api_token');

        if (! $token || ! $expectedToken || $token !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or invalid API token',
            ], 401);
        }

        return $next($request);
    }
}
