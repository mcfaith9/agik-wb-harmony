<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskList>
 */
class TaskListFactory extends Factory
{
    protected $model = \App\Models\TaskList::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(2),
            'description' => $this->faker->optional()->paragraph,
            'priority' => $this->faker->randomElement(['none', 'low', 'medium', 'high']),
            'privacy' => $this->faker->randomElement(['public', 'private']),
            'tags' => json_encode([$this->faker->word, $this->faker->word]),
            'created_by' => 1,
        ];
    }
}
