<?php

namespace Feature\UpdateTests;

use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateTimeEntryControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetTimeEntry = TimeEntry::factory()->create();
    }

    /**
     * @test
     * Prueba que una tarea puede ser actualizada exitosamente con datos válidos.
     */
    public function it_can_update_a_task_successfully(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/' . $this->targetTimeEntry->id . '/updateTimeEntry';

        $newData = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' =>  $this->faker->numberBetween(30, 300),
            'description' => $this->faker->text,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('time_entries', [
            'id' => $this->targetTimeEntry->id,
            'task_id' => $newData['task_id'],
            'user_id' => $newData['user_id'],
            'date' => $newData['date'],
            'minutes' => $newData['minutes'],
            'description' => $newData['description'],
        ]);

    }

    /**
     * @test
     * Prueba la validación de la regla 'max' para el título.
     */
    public function it_returns_validation_error_if_date_is_empty(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/' . $this->targetTimeEntry->id . '/updateTimeEntry';

        $newData = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => '',
            'minutes' =>  $this->faker->numberBetween(30, 300),
            'description' => $this->faker->text,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['date' => 'The date field is required.']);

        $this->assertDatabaseMissing('time_entries', [
            'id' => $this->targetTimeEntry->id,
            'date' => $newData['date']
        ]);

    }

    /**
     * @test
     */
    public function validation_succeds_if_date_is_unchanged(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/' . $this->targetTimeEntry->id . '/updateTimeEntry';

        $originalDate = $this->targetTimeEntry->date;

        $newData = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $originalDate,
            'minutes' =>  $this->faker->numberBetween(30, 300),
            'description' => $this->faker->text,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('time_entries', $newData);

    }

    /**
     * @test
     * Prueba la validación de la regla 'max' para el título.
     */
    public function it_returns_validation_error_if_minutes_is_empty(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/' . $this->targetTimeEntry->id . '/updateTimeEntry';

        $newData = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' =>  '',
            'description' => $this->faker->text,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['minutes' => 'The minutes field is required.']);

        $this->assertDatabaseMissing('time_entries', [
            'id' => $this->targetTimeEntry->id,
            'minutes' => $newData['minutes']
        ]);

    }

    /**
     * @test
     */
    public function validation_succeds_if_minutes_is_unchanged(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/' . $this->targetTimeEntry->id . '/updateTimeEntry';

        $originalMinutes = $this->targetTimeEntry->minutes;

        $newData = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' =>  $originalMinutes,
            'description' => $this->faker->text,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('time_entries', $newData);

    }

    /**
     * @test
     * Prueba la validación de la regla 'max' para el título.
     */
    public function it_returns_validation_error_if_description_is_empty(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/' . $this->targetTimeEntry->id . '/updateTimeEntry';

        $newData = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' =>  $this->faker->numberBetween(30, 300),
            'description' => '',
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field is required.']);

        $this->assertDatabaseMissing('time_entries', [
            'id' => $this->targetTimeEntry->id,
            'description' => $newData['description']
        ]);

    }

    /**
     * @test
     */
    public function validation_succeds_if_description_is_unchanged(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/' . $this->targetTimeEntry->id . '/updateTimeEntry';

        $originalDescription = $this->targetTimeEntry->description;

        $newData = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' =>  $this->faker->numberBetween(30, 300),
            'description' => $originalDescription,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('time_entries', $newData);

    }

    /**
     * @test
     * Prueba la validación de la regla 'max' para el título.
     */
    public function it_returns_validation_error_if_description_is_too_short(): void
    {

        $url = '/api/time-entries/' . $this->targetTimeEntry->id . '/updateTimeEntry';

        $user = User::factory()->create();

        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);

        $newData = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' =>  $this->faker->numberBetween(30, 300),
            'description' => str_repeat('a', 39),
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field must be at least 40 characters.']);

        $this->assertDatabaseMissing('tasks', [
            'id' => $this->targetTimeEntry->id,
            'description' => $newData['description']
        ]);
    }

    /**
     * @test
     * Prueba la validación de la regla 'max' para el título.
     */
    public function it_returns_validation_error_if_description_is_too_long(): void
    {

        $url = '/api/time-entries/' . $this->targetTimeEntry->id . '/updateTimeEntry';

        $user = User::factory()->create();

        $task = Task::factory()->create();

        $token = JWTAuth::fromUser($user);

        $newData = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' =>  $this->faker->numberBetween(30, 300),
            'description' => str_repeat('a', 256),
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field must not be greater than 255 characters.']);

        $this->assertDatabaseMissing('tasks', [
            'id' => $this->targetTimeEntry->id,
            'description' => $newData['description']
        ]);
    }


}
