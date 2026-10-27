<?php

namespace App\Services\Shipping\Adapters;

use App\Models\Project;
use App\Services\Shipping\ShippingEvaluationContext;

interface ShippingCarrierInterface
{
    /**
     * Calculate shipping rate for the given context.
     */
    public function calculateRate(Project $project, ShippingEvaluationContext $context): array;

    /**
     * Create a shipment with the carrier via API.
     */
    public function createShipment(array $shipmentData): array;

    /**
     * Track a shipment.
     */
    public function trackShipment(string $trackingNumber): array;
}
