<?php

namespace Database\Factories;

use App\Models\ProjectProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProjectProduct>
 */
class ProjectProductFactory extends Factory
{
    protected $model = ProjectProduct::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => 'SKU-'.$this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->paragraphs(3, true),
            'short_description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'stock_status' => $this->faker->randomElement(['in_stock', 'out_of_stock']),
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'product_type' => 'simple',
            'language_id' => 1,
            'is_featured' => $this->faker->boolean(20),
            'is_favorite' => $this->faker->boolean(10),
            'is_bestseller' => $this->faker->boolean(15),
            'has_price' => true,
            'manage_stock' => $this->faker->boolean(50),
            'noindex' => false,
            'views' => $this->faker->numberBetween(0, 1000),
            'rating_average' => $this->faker->randomFloat(2, 0, 5),
            'rating_count' => $this->faker->numberBetween(0, 100),
        ];
    }

    public function variable(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => 'variable',
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }
}
