<?php

namespace Feature\IndexTests;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexTaskControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    public function invoke_returns_ok_status_and_correct_json_structure_when_tasks_exist()
    {

        $user = User::factory()->create(['rol' => 'admin']);

        $token = JWTAuth::fromUser($user);

        $url = '/api/tasks';

        Task::factory()->count(20)->create();

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonCount(20);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'project_id',
                'title',
                'description',
                'status',
                'priority',
                'user_id',
                'created_at',
                'updated_at',
            ]
        ]);


    }

    /**
     * @test
     */
    public function invoke_returns_ok_status_and_empty_array_when_no_tasks_exist()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/tasks';

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonStructure([]);

    }
}
