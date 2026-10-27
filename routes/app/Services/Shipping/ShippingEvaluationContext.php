<?php

namespace App\Services\Shipping;

class ShippingEvaluationContext
{
    public float $orderValue;

    public float $actualWeight;

    public float $volumetricWeight;

    public float $chargeableWeight;

    public float $distance;

    public ?int $zoneId;

    public bool $isCod;

    public function __construct(
        float $orderValue = 0,
        float $actualWeight = 0,
        float $volumetricWeight = 0,
        float $distance = 0,
        ?int $zoneId = null,
        bool $isCod = false
    ) {
        $this->orderValue = $orderValue;
        $this->actualWeight = $actualWeight;
        $this->volumetricWeight = $volumetricWeight;
        // Chargeable weight is the maximum of actual and volumetric
        $this->chargeableWeight = max($actualWeight, $volumetricWeight);
        $this->distance = $distance;
        $this->zoneId = $zoneId;
        $this->isCod = $isCod;
    }
}
