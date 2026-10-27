<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $unitPrice = $this->faker->numberBetween(10000, 500000);
        $totalPrice = $quantity * $unitPrice;

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_variation_id' => null,
            'product_name' => $this->faker->words(3, true),
            'product_sku' => strtoupper($this->faker->bothify('SKU-####-????')),
            'product_attributes' => null,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'total_price' => $totalPrice,
        ];
    }
}
