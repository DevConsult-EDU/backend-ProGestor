<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
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
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'customer_id' => Str::uuid()->toString(),
            'status' => $this->faker->randomElement(['pendiente', 'en progreso', 'completado', 'cancelado']),
            'started_at' => $this->faker->dateTimeThisYear($max = 'now', $timezone = null),
            'finished_at' => $this->faker->dateTimeThisYear($max = 'now', $timezone = null),
        ];
    }
}
