<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCarrier;
use App\Models\ShippingZone;
use App\Services\ShippingEngine\ShippingEngineService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index($projectCode)
    {
        $project = request()->attributes->get('project');

        $carriers = ShippingCarrier::where('project_id', $project->id)
            ->with(['rateVersions' => function ($q) {
                $q->with(['rules.conditions']);
            }])
            ->get();

        $zones = ShippingZone::where('project_id', $project->id)->get();

        return view('cms.shipping.index', compact('carriers', 'zones', 'projectCode', 'project'));
    }

    public function calculator()
    {
        // View replaced by modal in index
        return redirect()->route('project.admin.shipping.index', request()->route('projectCode'));
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
