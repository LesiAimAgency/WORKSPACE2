<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'status' => 'todo',
            'position' => 0,
            'start_date' => now()->toDateString(),
            'deadline' => now()->addDays(fake()->numberBetween(1, 14))->toDateString(),
        ];
    }
}
