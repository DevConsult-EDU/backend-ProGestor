<?php

namespace Feature\IndexTests;

use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexTimeEntryControllerTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    public function invoke_returns_ok_status_and_correct_json_structure_when_time_entries_exist()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $task = Task::factory()->create();

        $url = '/api/tasks/' . $task->id . '/time-entries';

        TimeEntry::factory()->count(10)->create(['task_id' => $task->id]);

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonCount(10);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'task_id',
                'user_id',
                'date',
                'minutes',
                'description',
            ]
        ]);


    }

    /**
     * @test
     */
    public function invoke_returns_ok_status_and_empty_array_when_no_time_entries_exist()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $task = Task::factory()->create();

        $url = '/api/tasks/' . $task->id . '/time-entries';

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonStructure([]);

    }

    /**
     * @test
     */
    public function shows_number_of_time_entries_in_list()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $task = Task::factory()->create();

        $url = '/api/tasks/' . $task->id . '/time-entries';

        $timeEntry1 = TimeEntry::factory()->create();
        $timeEntry2 = TimeEntry::factory()->count(3)->create();

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'task_id',
                'user_id',
                'date',
                'minutes',
                'description',
            ]
        ]);

        $this->assertDatabaseCount('time_entries', 4);
    }
}
