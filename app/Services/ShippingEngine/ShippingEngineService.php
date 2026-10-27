<?php

namespace App\Services\ShippingEngine;

use App\Models\ShippingCarrier;

class ShippingEngineService
{
    protected $projectId;

    protected $context;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    public function calculate(array $payload)
    {
        // 1. Build Context (Weight, Distance, Order Value, etc.)
        $this->context = new ShippingContext($payload, $this->projectId);

        // 2. Resolve Carrier
        $carrierCode = $payload['carrier_code'] ?? 'default';
        $carrier = ShippingCarrier::where('project_id', $this->projectId)
            ->where('code', $carrierCode)
            ->where('status', true)
            ->first();

        if (! $carrier) {
            // Check if there is any default carrier
            $carrier = ShippingCarrier::where('project_id', $this->projectId)->where('status', true)->first();
            if (! $carrier) {
                // FALLBACK: If completely empty, return a default simulated response
                return [
                    'success' => true,
                    'carrier' => 'Tự giao hàng (Default)',
                    'carrier_code' => 'local',
                    'rate_version' => 'Auto Fallback',
                    'chargeable_weight' => $this->context->getChargeableWeight(),
                    'distance' => $this->context->getDistance(),
                    'matched_rules' => [['id' => 0, 'name' => 'Phí mặc định hệ thống', 'action' => 'override', 'fee' => 30000]],
                    'base_fee' => 30000,
                    'surcharge' => 0,
                    'discount' => 0,
                    'final_fee' => 30000,
                ];
            }
        }

        // 3. Resolve Active Version
        $version = $carrier->activeRateVersion;
        if (! $version) {
            return [
                'success' => true,
                'carrier' => $carrier->name,
                'carrier_code' => $carrier->code,
                'rate_version' => 'No Version',
                'chargeable_weight' => $this->context->getChargeableWeight(),
                'distance' => $this->context->getDistance(),
                'matched_rules' => [['id' => 0, 'name' => 'Chưa cấu hình bảng giá', 'action' => 'override', 'fee' => 0]],
                'base_fee' => 0,
                'surcharge' => 0,
                'discount' => 0,
                'final_fee' => 0,
            ];
        }

        // 4. Load Rules
        $rules = $version->rules()->with('conditions')->where('status', true)->orderBy('priority', 'desc')->get();

        // 5. Evaluate Rules
        $evaluator = new RuleEvaluator($this->context);

        $baseFee = null;
        $surcharges = 0;
        $matchedRules = [];
        $isFreeShip = false;

        foreach ($rules as $rule) {
            // Evaluator returns true if all conditions match
            if ($evaluator->match($rule)) {
                $matchedRules[] = [
                    'id' => $rule->id,
                    'name' => $rule->name,
                    'action' => $rule->action_type,
                    'fee' => $rule->fee,
                ];

                if ($rule->action_type === 'free') {
                    $isFreeShip = true;
                    if ($baseFee === null) {
                        $baseFee = 0;
                    } // base fee becomes 0 if not set yet
                } elseif ($rule->action_type === 'override') {
                    // Highest priority override rule wins
                    if ($baseFee === null && ! $isFreeShip) {
                        $baseFee = $rule->fee;
                    }
                } elseif ($rule->action_type === 'add') {
                    $surcharges += $rule->fee;
                } elseif ($rule->action_type === 'subtract') {
                    $surcharges -= $rule->fee;
                }
            }
        }

        if ($baseFee === null && ! $isFreeShip) {
            // Default fallback if no override rules matched at all
            $baseFee = 50000;
        }

        $finalFee = $isFreeShip ? $surcharges : ($baseFee + $surcharges);
        if ($finalFee < 0) {
            $finalFee = 0;
        }

        return [
            'success' => true,
            'carrier' => $carrier->name,
            'carrier_code' => $carrier->code,
            'rate_version' => $version->version_name,
            'chargeable_weight' => $this->context->getChargeableWeight(),
            'distance' => $this->context->getDistance(),
            'matched_rules' => $matchedRules,
            'base_fee' => $baseFee,
            'surcharge' => $surcharges,
            'discount' => $isFreeShip ? $baseFee : 0,
            'final_fee' => $finalFee,
        ];
    }

    protected function errorResponse($message)
    {
        return [
            'success' => false,
            'error' => $message,
        ];
    }
}
