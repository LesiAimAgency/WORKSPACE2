<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    private $baseUrl = 'https://provinces.open-api.vn/api/v1';

    public function provinces()
    {
        try {
            $response = Http::get($this->baseUrl.'/', ['depth' => 2]);

            if (! $response->successful()) {
                return response()->json(['error' => 'API request failed'], 500);
            }

            $data = $response->json();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function districts($provinceCode)
    {
        try {
            $response = Http::get($this->baseUrl."/p/{$provinceCode}", ['depth' => 2]);

            if (! $response->successful()) {
                return response()->json(['error' => 'API request failed'], 500);
            }

            $data = $response->json();

            return response()->json($data['districts'] ?? []);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function wards($districtCode)
    {
        try {
            $response = Http::get($this->baseUrl."/d/{$districtCode}", ['depth' => 2]);

            if (! $response->successful()) {
                return response()->json(['error' => 'API request failed'], 500);
            }

            $data = $response->json();

            return response()->json($data['wards'] ?? []);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
