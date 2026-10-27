<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCarrier;
use App\Models\ShippingZone;
use App\Services\ShippingEngine\ShippingEngineService;
use Illuminate\Http\Request;

class ShippingEngineController extends Controller
{
    public function index($projectCode)
    {
        $project = request()->attributes->get('project');

        // Ensure default local carrier exists to prevent errors
        $localCarrier = ShippingCarrier::firstOrCreate(
            ['project_id' => $project->id, 'code' => 'local'],
            ['name' => 'Tự giao hàng', 'type' => 'local', 'status' => true]
        );

        if ($localCarrier->wasRecentlyCreated || ! $localCarrier->rateVersions()->exists()) {
            $version = $localCarrier->rateVersions()->create([
                'version_name' => 'Bảng giá mặc định',
                'is_active' => true,
            ]);
            $version->rules()->create([
                'name' => 'Phí giao hàng tiêu chuẩn',
                'priority' => 0,
                'action_type' => 'override',
                'fee' => 30000,
                'status' => true,
            ]);
        }

        $carriers = ShippingCarrier::where('project_id', $project->id)
            ->with(['rateVersions' => function ($q) {
                $q->with(['rules.conditions']);
            }])
            ->get();

        $zones = ShippingZone::where('project_id', $project->id)->get();

        return view('cms.shipping.index', compact('carriers', 'zones', 'projectCode', 'project'));
    }

    public function calculate(Request $request, $projectCode)
    {
        $project = request()->attributes->get('project');
        $service = new ShippingEngineService($project->id);

        $payload = $request->validate([
            'order_value' => 'required|numeric',
            'actual_weight' => 'required|numeric',
            'distance' => 'required|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'cod_amount' => 'nullable|numeric',
            'shipping_address' => 'nullable|array',
            'carrier_code' => 'nullable|string',
        ]);

        $result = $service->calculate($payload);

        return response()->json($result);
    }
}
