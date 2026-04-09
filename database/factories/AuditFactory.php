<?php

namespace Database\Factories;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditFactory extends Factory
{
    protected $model = Audit::class;

    public function definition(): array
    {
        return [
            'audit_code' => 'AUD-' . $this->faker->unique()->numberBetween(1000, 9999),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph,
            'audit_type' => $this->faker->randomElement(['internal', 'it', 'compliance', 'performance']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'status' => 'draft',
            'planned_start_date' => now(),
            'planned_end_date' => now()->addMonth(),
            'created_by' => User::factory(),
        ];
    }
}
