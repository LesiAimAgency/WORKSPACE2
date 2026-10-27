<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductVariation>
 */
class ProductVariationFactory extends Factory
{
    protected $model = ProductVariation::class;

    public function definition(): array
    {
        $price = $this->faker->numberBetween(10000, 500000);

        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper(Str::random(12)),
            'price' => $price,
            'sale_price' => $this->faker->boolean(40) ? $price * 0.85 : null,
            'stock_quantity' => $this->faker->numberBetween(0, 500),
            'attributes' => [],
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withAttributes(array $attributes = []): static
    {
        return $this->state(fn (array $state) => [
            'attributes' => $attributes ?: ['1' => '1', '2' => '2'],
        ]);
    }
}
