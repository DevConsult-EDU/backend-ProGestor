<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'project_id' => Str::uuid()->toString(),
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => 'baja',
            'user_id' => Str::uuid()->toString(),
            'due_date' => $this->faker->date,
        ];
    }
}
