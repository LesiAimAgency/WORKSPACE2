<?php

namespace App\Services;

use App\Models\Widget;
use Illuminate\Support\Facades\Cache;

class WidgetRenderService
{
    /**
     * Lấy danh sách Widgets theo vị trí (đã được cache cực nhanh)
     */
    public function getWidgetsForPosition(string $position)
    {
        $cacheKey = "widgets_position_{$position}";

        if (config('app.debug', false) || env('WIDGET_CACHE', false) === false) {
            return Widget::where('area', $position)
                ->where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get();
        }

        // Sử dụng Cache::rememberForever. Nếu có Redis, chuyển sang Cache::tags(['widgets'])
        return Cache::rememberForever($cacheKey, function () use ($position) {
            return Widget::where('area', $position) // using 'area' to map to position in existing schema
                ->where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get()
                ->map(function ($widget) {
                    // Pre-process dữ liệu nếu cần
                    return $widget;
                });
        });
    }
}
