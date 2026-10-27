<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagicLoginController extends Controller
{
    /**
     * Route: GET /agency/magic-login/{user} (Bảo vệ bởi signed middleware)
     */
    public function login(Request $request, User $user)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Magic link is invalid or expired.');
        }

        // Bypass login
        Auth::login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Logged in via Agency Platform.');
    }
}
