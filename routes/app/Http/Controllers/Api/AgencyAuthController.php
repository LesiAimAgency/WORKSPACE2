<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AgencyAuthService;
use Illuminate\Http\JsonResponse;

class AgencyAuthController extends Controller
{
    public function __construct(private AgencyAuthService $authService) {}

    /**
     * Endpoint: POST /api/agency/auth (Bảo vệ bởi VerifyAgencyRequest)
     */
    public function requestMagicLink(): JsonResponse
    {
        $magicLink = $this->authService->generateMagicLink();

        return response()->json([
            'status' => 'success',
            'magic_link' => $magicLink,
        ]);
    }
}
