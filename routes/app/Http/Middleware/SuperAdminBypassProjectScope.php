<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SuperAdminBypassProjectScope
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure we're using main database for superadmin
        $this->ensureMainDatabase();

        // Nếu là SuperAdmin, bypass project scope
        $user = Auth::guard('web')->user();
        if ($user && ($user->level <= 1 || $user->user_level === 'superadmin')) {
            // Tắt global scope cho project_id
            Config::set('app.bypass_project_scope', true);
        }

        return $next($request);
    }

    /**
     * Ensure we're connected to main database
     */
    private function ensureMainDatabase(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $currentConnection = Config::get('database.default');

        if ($currentConnection !== 'mysql') {
            DB::setDefaultConnection('mysql');
            Config::set('database.default', 'mysql');
            DB::purge('project');
        }
    }
}
