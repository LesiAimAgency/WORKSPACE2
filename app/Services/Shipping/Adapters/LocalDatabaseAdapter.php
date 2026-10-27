<?php

namespace App\Services\Shipping\Adapters;

use App\Models\Project;
use App\Services\Shipping\ShippingEngine;
use App\Services\Shipping\ShippingEvaluationContext;

class LocalDatabaseAdapter implements ShippingCarrierInterface
{
    protected ShippingEngine $engine;

    public function __construct(ShippingEngine $engine)
    {
        $this->engine = $engine;
    }

    public function calculateRate(Project $project, ShippingEvaluationContext $context): array
    {
        return $this->engine->calculate($project, $context);
    }

    public function createShipment(array $shipmentData): array
    {
        // Local adapter does not create API shipments, just returns success
        return [
            'success' => true,
            'message' => 'Shipment saved locally.',
            'tracking_number' => 'LOC-'.uniqid(),
        ];
    }

    public function trackShipment(string $trackingNumber): array
    {
        return [
            'success' => true,
            'status' => 'Pending',
        ];
    }
}
