<?php

namespace App\Services\ShippingEngine;

use App\Models\ShippingRule;
use App\Models\ShippingRuleCondition;

class RuleEvaluator
{
    protected $context;

    public function __construct(ShippingContext $context)
    {
        $this->context = $context;
    }

    public function match(ShippingRule $rule)
    {
        if ($rule->conditions->isEmpty()) {
            return false;
        }

        foreach ($rule->conditions as $condition) {
            if (! $this->evaluateCondition($condition)) {
                return false; // AND logic implicitly
            }
        }

        return true;
    }

    protected function evaluateCondition(ShippingRuleCondition $condition)
    {
        $actualValue = $this->getActualValue($condition->condition_type);

        if ($condition->condition_type === 'zone') {
            if ($condition->operator === 'in' || $condition->operator === '=') {
                return in_array($condition->zone_id, $this->context->getZoneIds());
            }
            if ($condition->operator === '!=') {
                return ! in_array($condition->zone_id, $this->context->getZoneIds());
            }

            return false;
        }

        switch ($condition->operator) {
            case '>=': return $actualValue >= $condition->value_1;
            case '<=': return $actualValue <= $condition->value_1;
            case '=': return $actualValue == $condition->value_1;
            case '>': return $actualValue > $condition->value_1;
            case '<': return $actualValue < $condition->value_1;
            case 'between': return $actualValue >= $condition->value_1 && $actualValue <= $condition->value_2;
            case 'in': return in_array($actualValue, explode(',', $condition->value_1));
            default: return false;
        }
    }

    protected function getActualValue($type)
    {
        switch ($type) {
            case 'order_value': return $this->context->getOrderValue();
            case 'distance': return $this->context->getDistance();
            case 'weight': return $this->context->getChargeableWeight();
            case 'cod': return $this->context->getCodAmount();
            default: return 0;
        }
    }
}
