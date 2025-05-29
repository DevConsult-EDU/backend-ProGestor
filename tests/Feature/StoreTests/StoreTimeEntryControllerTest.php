<?php

namespace Feature\StoreTests;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\JWT;

class StoreTimeEntryControllerTest extends TestCase
{
    use  RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    public function time_Entry_can_be_created()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/createTimeEntry';

        $task = Task::factory()->create();

        $newTimeEntry = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' =>  $this->faker->date,
            'minutes' => $this->faker->numberBetween(30, 300),
            'description' => $this->faker->text,
        ];

        $response = $this->withToken($token)->postJson($url, $newTimeEntry);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'task_id',
            'user_id',
            'date',
            'minutes',
            'description',
        ]);

        $response->assertJson([
            'task_id' => $newTimeEntry['task_id'],
            'user_id' => $newTimeEntry['user_id'],
            'date' => $newTimeEntry['date'],
            'minutes' => $newTimeEntry['minutes'],
            'description' => $newTimeEntry['description'],
        ]);

        $this->assertDatabaseHas('time_entries', $newTimeEntry);
    }

    /**
     * @test
     */
    public function validation_fails_if_date_is_missing()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/createTimeEntry';

        $task = Task::factory()->create();

        $newTimeEntry = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'minutes' => $this->faker->numberBetween(30, 300),
            'description' => $this->faker->text,
        ];

        $response = $this->withToken($token)->postJson($url, $newTimeEntry);

        $response->assertStatus(422);

        $response->assertInvalid(['date' => 'The date field is required.']);

        $this->assertDatabaseMissing('time_entries', $newTimeEntry);

    }

    /**
     * @test
     */
    public function validation_fails_if_date_is_empty()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/createTimeEntry';

        $task = Task::factory()->create();

        $newTimeEntry = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => '',
            'minutes' => $this->faker->numberBetween(30, 300),
            'description' => $this->faker->text,
        ];

        $response = $this->withToken($token)->postJson($url, $newTimeEntry);

        $response->assertStatus(422);

        $response->assertInvalid(['date' => 'The date field is required.']);

        $this->assertDatabaseMissing('time_entries', $newTimeEntry);

    }

    /**
     * @test
     */
    public function validation_fails_if_minutes_is_missing()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/createTimeEntry';

        $task = Task::factory()->create();

        $newTimeEntry = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'description' => $this->faker->text,
        ];

        $response = $this->withToken($token)->postJson($url, $newTimeEntry);

        $response->assertStatus(422);

        $response->assertInvalid(['minutes' => 'The minutes field is required.']);

        $this->assertDatabaseMissing('time_entries', $newTimeEntry);

    }

    /**
     * @test
     */
    public function validation_fails_if_minutes_is_empty()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/createTimeEntry';

        $task = Task::factory()->create();

        $newTimeEntry = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' => '',
            'description' => $this->faker->text,
        ];

        $response = $this->withToken($token)->postJson($url, $newTimeEntry);

        $response->assertStatus(422);

        $response->assertInvalid(['minutes' => 'The minutes field is required.']);

        $this->assertDatabaseMissing('time_entries', $newTimeEntry);

    }

    /**
     * @test
     */
    public function validation_fails_if_description_is_missing()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/createTimeEntry';

        $task = Task::factory()->create();

        $newTimeEntry = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' => $this->faker->numberBetween(30, 300),
        ];

        $response = $this->withToken($token)->postJson($url, $newTimeEntry);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field is required.']);

        $this->assertDatabaseMissing('time_entries', $newTimeEntry);
    }

    /**
     * @test
     */
    public function validation_fails_if_description_is_empty()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/createTimeEntry';

        $task = Task::factory()->create();

        $newTimeEntry = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' => $this->faker->numberBetween(30, 300),
            'description' => '',
        ];

        $response = $this->withToken($token)->postJson($url, $newTimeEntry);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field is required.']);

        $this->assertDatabaseMissing('time_entries', $newTimeEntry);
    }

    /**
     * @test
     */
    public function validation_fails_if_description_is_too_long()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/createTimeEntry';

        $task = Task::factory()->create();

        $newTimeEntry = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' => $this->faker->numberBetween(30, 300),
            'description' => str_repeat('a', 256),
        ];

        $response = $this->withToken($token)->postJson($url, $newTimeEntry);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field must not be greater than 255 characters.']);

        $this->assertDatabaseMissing('time_entries', $newTimeEntry);
    }

    /**
     * @test
     */
    public function validation_fails_if_description_is_too_short()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/time-entries/createTimeEntry';

        $task = Task::factory()->create();

        $newTimeEntry = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'date' => $this->faker->date,
            'minutes' => $this->faker->numberBetween(30, 300),
            'description' => str_repeat('a', 19),
        ];

        $response = $this->withToken($token)->postJson($url, $newTimeEntry);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field must be at least 20 characters.']);

        $this->assertDatabaseMissing('time_entries', $newTimeEntry);
    }
}
