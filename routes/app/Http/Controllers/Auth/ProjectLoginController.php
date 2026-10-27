<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectLoginController extends Controller
{
    /**
     * Get the guard for project authentication
     */
    protected function guard()
    {
        return Auth::guard('project');
    }

    public function showLoginForm(Request $request)
    {
        if (config('app.standalone_mode') || env('STANDALONE_MODE')) {
            return redirect()->route('login');
        }

        $project = $request->attributes->get('project');

        if (! $project) {
            abort(404, 'Dự án không tồn tại.');
        }

        // Check if already logged in via session
        if (session('project_user_id') && session('current_project') === $project->code) {
            return redirect('/'.$project->code.'/admin');
        }

        return view('auth.project-login', compact('project'));
    }

    public function login(Request $request)
    {
        if (config('app.standalone_mode') || env('STANDALONE_MODE')) {
            return app(LoginController::class)->login($request);
        }

        $project = $request->attributes->get('project');

        if (! $project) {
            abort(404, 'Dự án không tồn tại.');
        }

        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Project ID
        $projectTenantId = $project->tenant_id ?? $project->id;

        // Find user in database
        $user = User::on('mysql')
            ->withoutGlobalScope('tenant')
            ->where(function ($q) use ($credentials) {
                $q->where('username', $credentials['username'])
                    ->orWhere('email', $credentials['username']);
            })
            ->where(function ($q) {
                $q->whereIn('role', [
                    'cms',
                    'admin',
                    'dev',
                    'super_admin',
                    'superadmin',
                    'manager',
                    'web_admin',
                    'store_manager',
                    'multi_tenancy',
                    'multi_tenancy_control_center',
                ]);
            })
            ->get()
            ->first(function ($u) use ($project, $projectTenantId) {
                // Admin, Super admin, CMS or level <= 1 has full access
                if (in_array($u->role, ['super_admin', 'superadmin', 'admin', 'cms', 'dev', 'manager', 'web_admin', 'store_manager']) || (isset($u->level) && $u->level <= 1)) {
                    return true;
                }

                if (! empty($u->tenant_id) && ! empty($projectTenantId) && (int) $u->tenant_id === (int) $projectTenantId) {
                    return true;
                }

                $projectIds = is_array($u->project_ids) ? $u->project_ids : json_decode($u->project_ids ?? '[]', true);
                if (is_array($projectIds) && (in_array($project->id, $projectIds) || in_array($projectTenantId, $projectIds))) {
                    return true;
                }

                return false;
            });

        $passwordValid = false;
        if ($user && ! empty($user->password)) {
            try {
                $passwordValid = \Hash::check($credentials['password'], $user->password);
            } catch (\Throwable $e) {
                $passwordValid = password_verify($credentials['password'], $user->password);
            }
        }

        if ($user && $passwordValid) {
            // Auto rehash if using older/different algorithm to upgrade to modern bcrypt
            if (\Hash::needsRehash($user->password)) {
                $user->password = $credentials['password'];
                $user->save();
            }

            // 100% TENANT_ID BASED: store tenant context in session
            $tenantId = $user->tenant_id ?: ($project->tenant_id ?? $project->id);
            $request->session()->put('current_tenant_id', $tenantId);
            $request->session()->put('project_user_id', $user->id);
            $request->session()->put('project_user_username', $user->username);
            $request->session()->put('current_project', $project->code);
            $request->session()->put('current_project_id', $project->id);
            $request->session()->regenerate();
            $request->session()->put('current_tenant_id', $tenantId);
            $request->session()->put('project_user_id', $user->id);
            $request->session()->put('project_user_username', $user->username);
            $request->session()->put('current_project', $project->code);
            $request->session()->put('current_project_id', $project->id);

            \Log::info("Project login success: {$user->username} for project {$project->code} (tenant_id: {$tenantId})");

            return redirect()->intended('/'.$project->code.'/admin');
        }

        \Log::warning("Project login failed for username: {$credentials['username']} on project: {$project->code}");

        return back()->withErrors([
            'username' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        $project = $request->attributes->get('project');

        // Clear session-based auth
        $request->session()->forget(['project_user_id', 'project_user_username', 'current_project']);
        $request->session()->regenerateToken();

        return redirect()->route('project.login', ['projectCode' => $project ? $project->code : 'default']);
    }
}
