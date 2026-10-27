<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get reviews configuration for client
     */
    public function getConfig(): JsonResponse
    {
        $reviewsSetting = setting('reviews', []);
        $reviews = is_array($reviewsSetting) ? $reviewsSetting : json_decode($reviewsSetting, true) ?? [];

        // Return only client-needed configuration
        $clientConfig = [
            'enabled' => $reviews['enabled'] ?? true,
            'require_login' => $reviews['require_login'] ?? true,
            'require_purchase' => $reviews['require_purchase'] ?? false,
            'auto_approve' => $reviews['auto_approve'] ?? false,
            'allow_images' => $reviews['allow_images'] ?? true,
            'max_images' => $reviews['max_images'] ?? 5,
            'min_rating' => $reviews['min_rating'] ?? 1,
            'show_verified' => $reviews['show_verified'] ?? true,
            'reward_points' => $reviews['reward_points'] ?? 0,
            'enable_product' => $reviews['enable_product'] ?? true,
            'enable_post' => $reviews['enable_post'] ?? false,
            'align' => $reviews['align'] ?? 'left',
            'display_order' => $reviews['display_order'] ?? 10,
            'template' => $reviews['template'] ?? 'template1',
            'fake_type' => $reviews['fake_type'] ?? 'preset',
            'enable_fake' => $reviews['enable_fake'] ?? false,
            'default_sort' => $reviews['default_sort'] ?? 'newest',
            'per_page' => $reviews['per_page'] ?? 10,
            'allow_helpful' => $reviews['allow_helpful'] ?? true,
        ];

        return response()->json([
            'success' => true,
            'data' => $clientConfig,
        ]);
    }

    /**
     * Store a new review
     */
    public function store(Request $request): JsonResponse
    {
        // Implementation for storing reviews
        // This would be implemented based on your review model structure

        return response()->json([
            'success' => true,
            'message' => 'Review stored successfully',
        ]);
    }
}
