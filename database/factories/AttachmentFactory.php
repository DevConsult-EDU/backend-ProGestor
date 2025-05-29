<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
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
            'file_name' => $this->faker->word,
            'system_name' =>  $this->faker->word,
            'type_MIME' =>  $this->faker->word,
            'byte_size' => $this->faker->randomFloat(2, 10, 1024),
            'store_path' => $this->faker->text,
        ];
    }
}
