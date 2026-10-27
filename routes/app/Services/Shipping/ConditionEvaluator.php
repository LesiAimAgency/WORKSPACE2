<?php

namespace App\Services\Shipping;

use App\Models\ShippingRuleCondition;

class ConditionEvaluator
{
    /**
     * Evaluate a condition against the given context.
     */
    public function evaluate(ShippingRuleCondition $condition, ShippingEvaluationContext $context): bool
    {
        $attributeValue = $this->extractValueFromContext($condition->condition_type, $context);

        return $this->compare($attributeValue, $condition->operator, $condition->value_1, $condition->value_2);
    }

    /**
     * Get the corresponding value from the context based on condition type.
     *
     * @return mixed
     */
    protected function extractValueFromContext(string $type, ShippingEvaluationContext $context)
    {
        return match ($type) {
            'order_value' => $context->orderValue,
            'distance' => $context->distance,
            'weight' => $context->chargeableWeight,
            'zone' => $context->zoneId,
            'cod' => $context->isCod ? 1 : 0,
            default => null,
        };
    }

    /**
     * Compare values using the given operator.
     *
     * @param  mixed  $actual
     * @param  mixed  $target1
     * @param  mixed  $target2
     */
    protected function compare($actual, string $operator, $target1, $target2 = null): bool
    {
        if ($actual === null) {
            return false;
        }

        return match ($operator) {
            '=' => $actual == $target1,
            '!=' => $actual != $target1,
            '>' => $actual > $target1,
            '>=' => $actual >= $target1,
            '<' => $actual < $target1,
            '<=' => $actual <= $target1,
            'between' => $actual >= $target1 && $actual <= $target2,
            'in' => in_array($actual, is_string($target1) ? explode(',', $target1) : [$target1]), // For zones maybe
            default => false,
        };
    }
}
