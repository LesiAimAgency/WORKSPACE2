<?php

namespace App\Services;

use App\Models\Widget;
use Illuminate\Support\Facades\Cache;

class WidgetSyncService
{
    public function syncWidgets(array $widgetsData): void
    {
        foreach ($widgetsData as $data) {
            Widget::updateOrCreate(
                ['widget_code' => $data['widget_code']],
                [
                    'name' => $data['name'],
                    'settings' => $data['settings'] ?? [],
                    'rules' => $data['rules'] ?? [],
                    'data' => $data['data'] ?? [],
                    'area' => $data['position'] ?? 'sidebar', // Map position to area
                    'sort_order' => $data['order'] ?? 0, // Map order to sort_order
                    'is_active' => $data['is_active'] ?? true,
                    'is_lazy_loaded' => $data['is_lazy_loaded'] ?? false,
                    'tenant_id' => $data['tenant_id'] ?? null,
                ]
            );
        }

        // Xóa cache để frontend lấy data mới nhất
        Cache::forget('widgets_position_sidebar');
        // If using redis cache tags: Cache::tags(['widgets'])->flush();
    }
}
