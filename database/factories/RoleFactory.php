<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->slug(2),
            'display_name' => $this->faker->jobTitle(),
            'description' => $this->faker->sentence(),
            'is_default' => false,
            'level' => $this->faker->numberBetween(1, 10),
        ];
    }
}
