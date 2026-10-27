<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Project',
            'code' => strtoupper(fake()->unique()->lexify('??-####')),
            'status' => 'active',
        ];
    }
}
