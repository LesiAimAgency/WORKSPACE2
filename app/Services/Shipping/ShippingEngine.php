<?php

namespace App\Services\Shipping;

use App\Models\Project;
use App\Models\ShippingCarrier;
use App\Models\ShippingRule;
use Illuminate\Support\Collection;

class ShippingEngine
{
    protected ConditionEvaluator $evaluator;

    public function __construct(ConditionEvaluator $evaluator)
    {
        $this->evaluator = $evaluator;
    }

    /**
     * Calculate the final shipping fee based on the context and project configuration.
     */
    public function calculate(Project $project, ShippingEvaluationContext $context): array
    {
        // 1. Get the local shipping carrier for the project
        // Note: For multi-carrier support, you could iterate or take a specific carrier_id in context.
        // For now, we get the active local carrier.
        $carrier = $project->shippingCarriers()
            ->where('type', 'local')
            ->where('status', true)
            ->first();

        if (! $carrier) {
            return $this->errorResult('No active shipping carrier found for this project.');
        }

        // 2. Get the active rate version
        $rateVersion = $carrier->activeRateVersion;
        if (! $rateVersion) {
            return $this->errorResult('No active rate version found for this carrier.');
        }

        // 3. Get all active rules for this version
        $rules = $rateVersion->rules()->where('status', true)->get();

        if ($rules->isEmpty()) {
            return $this->errorResult('No shipping rules found in the active version.');
        }

        // 4. Evaluate rules
        $matchedRules = collect();
        foreach ($rules as $rule) {
            if ($this->ruleMatches($rule, $context)) {
                $matchedRules->push($rule);
            }
        }

        if ($matchedRules->isEmpty()) {
            return $this->errorResult('No matching rules for the given context.');
        }

        // 5. Calculate Final Fee
        return $this->applyRules($matchedRules, $carrier, $rateVersion, $context);
    }

    /**
     * Check if all conditions (AND) of a rule match the context.
     * (Assuming simpler OR logic could be handled by multiple rules)
     */
    protected function ruleMatches(ShippingRule $rule, ShippingEvaluationContext $context): bool
    {
        $conditions = $rule->conditions;

        if ($conditions->isEmpty()) {
            return true; // No conditions means it always matches
        }

        foreach ($conditions as $condition) {
            if (! $this->evaluator->evaluate($condition, $context)) {
                return false; // ANY false means the rule fails
            }
        }

        return true;
    }

    /**
     * Apply matched rules to calculate the base fee and surcharges.
     */
    protected function applyRules(Collection $matchedRules, ShippingCarrier $carrier, $rateVersion, ShippingEvaluationContext $context): array
    {
        // Split rules by type
        $baseRules = $matchedRules->where('is_surcharge', false)->sortByDesc('priority');
        $surchargeRules = $matchedRules->where('is_surcharge', true)->sortByDesc('priority');

        $baseFee = 0;
        $activeBaseRule = null;
        $discount = 0;

        // Apply Free Ship first (if priority is high, though we sort by priority)
        // Wait, free_ship action can be in base rules.
        $freeShipRule = $baseRules->where('action_type', 'free')->first();

        if ($freeShipRule) {
            $baseFee = 0;
            $activeBaseRule = $freeShipRule;
        } else {
            // Apply highest priority base rule
            $activeBaseRule = $baseRules->first();
            if ($activeBaseRule) {
                if ($activeBaseRule->action_type === 'override') {
                    $baseFee = $activeBaseRule->fee;
                } elseif ($activeBaseRule->action_type === 'add') {
                    // fallback if incorrectly configured
                    $baseFee = $activeBaseRule->fee;
                }
            }
        }

        // Apply Surcharges
        $totalSurcharge = 0;
        foreach ($surchargeRules as $rule) {
            if ($rule->action_type === 'add') {
                $totalSurcharge += $rule->fee;
            } elseif ($rule->action_type === 'subtract') {
                $totalSurcharge -= $rule->fee;
            } elseif ($rule->action_type === 'add_percent') {
                // If fee is percentage of orderValue
                $totalSurcharge += ($context->orderValue * ($rule->fee / 100));
            }
        }

        $finalFee = max(0, $baseFee + $totalSurcharge - $discount);

        return [
            'success' => true,
            'carrier' => $carrier->code ?? 'local',
            'rate_version' => $rateVersion->version_name,
            'chargeable_weight' => $context->chargeableWeight,
            'distance' => $context->distance,
            'zone' => $context->zoneId,
            'matched_rules' => $matchedRules->map(fn ($r) => ['id' => $r->id, 'name' => $r->name])->toArray(),
            'base_fee' => $baseFee,
            'surcharge' => $totalSurcharge,
            'discount' => $discount,
            'final_fee' => $finalFee,
        ];
    }

    protected function errorResult(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'final_fee' => 0,
        ];
    }
}
