<?php

namespace Database\Factories;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductAttributeValue>
 */
class ProductAttributeValueFactory extends Factory
{
    protected $model = ProductAttributeValue::class;

    public function definition(): array
    {
        $value = $this->faker->unique()->word();

        return [
            'product_attribute_id' => ProductAttribute::factory(),
            'value' => $value,
            'slug' => Str::slug($value),
            'display_value' => ucfirst($value),
            'color_code' => null,
            'sort_order' => $this->faker->randomNumber(2),
        ];
    }

    public function withColor(): static
    {
        return $this->state(fn (array $attributes) => [
            'color_code' => $this->faker->hexColor(),
        ]);
    }
}
