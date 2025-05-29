<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeEntry>
 */
class TimeEntryFactory extends Factory
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
            'task_id' => Str::uuid()->toString(),
            'user_id' => Str::uuid()->toString(),
            'date' => $this->faker->date,
            'minutes' => $this->faker->numberBetween(30, 300),
            'description' => $this->faker->text,
        ];
    }
}
