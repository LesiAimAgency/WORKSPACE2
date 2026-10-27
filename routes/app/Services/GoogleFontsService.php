<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GoogleFontsService
{
    /**
     * Google Fonts API endpoint.
     */
    protected string $apiUrl = 'https://www.googleapis.com/webfonts/v1/webfonts';

    /**
     * Cache key for the font list.
     */
    protected string $cacheKey = 'google_fonts_list';

    /**
     * Cache duration in minutes (24 hours).
     */
    protected int $cacheMinutes = 1440;

    /**
     * Retrieve the list of fonts from Google Fonts API.
     */
    public function getFonts(): ?array
    {
        return Cache::remember($this->cacheKey, $this->cacheMinutes, function () {
            $key = env('GOOGLE_FONTS_API_KEY');
            if (! $key) {
                return null;
            }

            $response = Http::get($this->apiUrl, ['key' => $key]);
            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            return $data['items'] ?? null; // Each item contains family, variants, etc.
        });
    }
}
