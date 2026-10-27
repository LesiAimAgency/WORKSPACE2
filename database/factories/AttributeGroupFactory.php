<?php

namespace Database\Factories;

use App\Models\AttributeGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<AttributeGroup>
 */
class AttributeGroupFactory extends Factory
{
    protected $model = AttributeGroup::class;

    public function definition(): array
    {
        $name = $this->faker->word();

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'sort_order' => $this->faker->randomNumber(2),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
