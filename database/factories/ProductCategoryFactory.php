<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'image' => null,
            'parent_id' => null,
            'level' => 0,
            'path' => Str::slug($name),
            'sort_order' => $this->faker->randomNumber(2),
            'is_active' => true,
            'meta_title' => ucfirst($name),
            'meta_description' => $this->faker->sentence(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withParent(ProductCategory $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'level' => $parent->level + 1,
            'path' => $parent->path.'/'.$attributes['slug'],
        ]);
    }
}
