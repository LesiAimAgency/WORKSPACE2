<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectPasswordAudit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectPasswordAudit>
 */
class ProjectPasswordAuditFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(['viewed', 'generated', 'updated']),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'performed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
