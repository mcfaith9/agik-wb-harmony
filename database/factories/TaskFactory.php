<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = \App\Models\Task::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(4),
            'description' => $this->faker->optional()->paragraph,
            'priority' => $this->faker->randomElement(['none', 'low', 'medium', 'high']),
            'privacy' => $this->faker->randomElement(['public', 'private']),
            'created_by' => 1,
        ];
    }
}
