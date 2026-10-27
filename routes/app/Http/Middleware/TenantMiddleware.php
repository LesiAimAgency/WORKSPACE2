<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Lấy tenant từ domain hoặc subdomain
        $host = $request->getHost();

        // Tìm tenant theo domain
        $tenant = Tenant::where('domain', $host)
            ->orWhere('domain', 'like', "%{$host}%")
            ->active()
            ->first();

        if (! $tenant) {
            // Nếu không tìm thấy tenant, sử dụng tenant mặc định
            $tenant = Tenant::where('code', 'default')->first();

            if (! $tenant) {
                abort(404, 'Website không tồn tại');
            }
        }

        // Set tenant vào session
        session(['current_tenant_id' => $tenant->id]);
        session(['current_tenant' => $tenant]);

        // Set config cho app
        config(['app.current_tenant' => $tenant]);
        config(['app.default_tenant_id' => $tenant->id]);

        return $next($request);
    }
}
