<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(100000, 5000000);
        $taxAmount = $subtotal * 0.1;
        $shippingAmount = $this->faker->numberBetween(10000, 500000);

        return [
            'order_number' => 'ORD-'.strtoupper(Str::random(10)),
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipped', 'delivered']),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $this->faker->randomElement([0, $subtotal * 0.1]),
            'total_amount' => $subtotal + $taxAmount + $shippingAmount,
            'currency' => 'VND',
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->safeEmail(),
            'customer_phone' => $this->faker->phoneNumber(),
            'billing_address' => [
                'address' => $this->faker->address(),
                'city' => $this->faker->city(),
                'state' => $this->faker->city(),
                'postal_code' => $this->faker->postcode(),
                'country' => 'Vietnam',
            ],
            'shipping_address' => [
                'address' => $this->faker->address(),
                'city' => $this->faker->city(),
                'state' => $this->faker->city(),
                'postal_code' => $this->faker->postcode(),
                'country' => 'Vietnam',
            ],
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'cash']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid']),
            'paid_at' => $this->faker->boolean(60) ? $this->faker->dateTimeThisMonth() : null,
            'customer_notes' => null,
            'internal_notes' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
        ]);
    }
}
