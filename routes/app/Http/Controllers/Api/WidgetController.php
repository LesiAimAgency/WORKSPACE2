<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use App\Services\WidgetRenderService;
use App\Services\WidgetSyncService;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function sync(Request $request, WidgetSyncService $syncService)
    {
        // Add basic authentication check here if needed
        $syncService->syncWidgets($request->all());

        return response()->json(['status' => 'success', 'message' => 'Widgets synced successfully']);
    }

    public function renderPartial($code, WidgetRenderService $service)
    {
        $widget = Widget::where('widget_code', $code)->where('is_active', true)->firstOrFail();

        // This is a placeholder for heavy logic. Real heavy logic would be based on widget type/data.
        $heavyData = [];

        // Return view (Ensure resources/views/widgets/partials folder exists)
        if (view()->exists('widgets.partials.'.$widget->widget_code)) {
            return view('widgets.partials.'.$widget->widget_code, [
                'widget' => $widget,
                'heavyData' => $heavyData,
            ])->render();
        }

        return "<div>Lazy loaded widget content for: {$widget->name}</div>";
    }
}
