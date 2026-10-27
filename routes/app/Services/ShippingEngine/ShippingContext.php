<?php

namespace App\Services\ShippingEngine;

use App\Models\ShippingZoneLocation;

class ShippingContext
{
    protected $orderValue;

    protected $actualWeight;

    protected $volumetricWeight;

    protected $distance;

    protected $zoneIds;

    protected $codAmount;

    public function __construct(array $payload, $projectId = null)
    {
        $this->orderValue = $payload['order_value'] ?? 0;
        $this->actualWeight = $payload['actual_weight'] ?? 0;
        $divisor = $payload['volumetric_divisor'] ?? 5000;

        $length = $payload['length'] ?? 0;
        $width = $payload['width'] ?? 0;
        $height = $payload['height'] ?? 0;

        if ($divisor > 0) {
            $this->volumetricWeight = ($length * $width * $height) / $divisor;
        } else {
            $this->volumetricWeight = 0;
        }

        $this->distance = $payload['distance'] ?? 0;
        $this->codAmount = $payload['cod_amount'] ?? 0;
        $this->zoneIds = $this->resolveZones($payload['shipping_address'] ?? [], $projectId);
    }

    public function getChargeableWeight()
    {
        return max($this->actualWeight, $this->volumetricWeight);
    }

    public function getOrderValue()
    {
        return $this->orderValue;
    }

    public function getDistance()
    {
        return $this->distance;
    }

    public function getCodAmount()
    {
        return $this->codAmount;
    }

    public function getZoneIds()
    {
        return $this->zoneIds;
    }

    protected function resolveZones($address, $projectId)
    {
        if (empty($address) || ! $projectId) {
            return [];
        }

        // In a real app, query ShippingZoneLocation
        // Match by province_code, district_code, ward_code
        // Return array of matching zone_ids

        return [];
    }
}
