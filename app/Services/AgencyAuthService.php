<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\URL;

class AgencyAuthService
{
    /**
     * Generate Temporary Signed URL
     */
    public function generateMagicLink(?string $targetEmail = null): string
    {
        // Get superadmin user or a specific user
        $admin = $targetEmail
            ? User::where('email', $targetEmail)->firstOrFail()
            : User::where('role', 'superadmin')->firstOrFail();

        return URL::temporarySignedRoute(
            'agency.magic_login',
            now()->addSeconds(60),
            ['user' => $admin->id]
        );
    }
}
