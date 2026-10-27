<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        $price = $this->faker->numberBetween(10000, 500000);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'short_description' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'sku' => strtoupper(Str::random(10)),
            'price' => $price,
            'sale_price' => $this->faker->boolean(30) ? $price * 0.8 : null,
            'has_price' => true,
            'stock_quantity' => $this->faker->numberBetween(0, 1000),
            'manage_stock' => true,
            'stock_status' => $this->faker->randomElement(['in_stock', 'out_of_stock']),
            'featured_image' => null,
            'gallery' => null,
            'weight' => $this->faker->randomFloat(2, 0.1, 100),
            'dimensions' => $this->faker->bothify('###x###x###'),
            'product_category_id' => ProductCategory::factory(),
            'brand_id' => Brand::factory(),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'is_featured' => $this->faker->boolean(20),
            'badges' => null,
            'meta_title' => $name,
            'meta_description' => $this->faker->sentence(),
            'settings' => null,
            'views' => $this->faker->numberBetween(0, 10000),
            'rating_average' => $this->faker->randomFloat(1, 0, 5),
            'rating_count' => $this->faker->numberBetween(0, 1000),
            'product_type' => 'simple',
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }

    public function variable(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => 'variable',
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
            'stock_status' => 'out_of_stock',
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
