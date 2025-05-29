<?php

namespace Feature\IndexTests;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexCommentControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    public function invoke_returns_ok_status_and_correct_json_structure_when_comments_exist()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $task = Task::factory()->create();

        $url = '/api/tasks/' . $task->id . '/comments';

        Comment::factory()->count(10)->create(['task_id' => $task->id]);

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonCount(10);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'task_id',
                'user_id',
                'comment',
            ]
        ]);


    }

    /**
     * @test
     */
    public function invoke_returns_ok_status_and_empty_array_when_no_comments_exist()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $task = Task::factory()->create();

        $url = '/api/tasks/' . $task->id . '/comments';

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonStructure([]);

    }

    /**
     * @test
     */
    public function shows_number_of_comments_in_list()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $task = Task::factory()->create();

        $url = '/api/tasks/' . $task->id . '/comments';

        $comment1 = Comment::factory()->create();
        $comment2 = Comment::factory()->count(3)->create();

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'task_id',
                'user_id',
                'comment'
            ]
        ]);

        $this->assertDatabaseCount('comments', 4);
    }
}
