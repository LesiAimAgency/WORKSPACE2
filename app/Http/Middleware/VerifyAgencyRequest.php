<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAgencyRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra Secret Key
        $agencySecret = config('services.agency.secret');
        $providedSecret = $request->header('X-Agency-Secret') ?? $request->bearerToken();

        if (! hash_equals((string) $agencySecret, (string) $providedSecret)) {
            return response()->json(['message' => 'Unauthorized Agency'], Response::HTTP_UNAUTHORIZED);
        }

        // 2. Kiểm tra IP Allowlist
        $allowedIps = explode(',', config('services.agency.allowed_ips', ''));
        $clientIp = $request->ip();

        if (! app()->environment('local') && ! in_array($clientIp, $allowedIps)) {
            return response()->json(['message' => 'IP not allowed'], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
