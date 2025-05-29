<?php

namespace Feature\StoreTests;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class StoreCommentControllerTest extends TestCase
{
    use  RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    public function comment_can_be_created()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/comments/createComment';

        $task = Task::factory()->create();

        $newComment = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'comment' => $this->faker->text,
        ];

        $response = $this->withToken($token)->postJson($url, $newComment);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'task_id',
            'user_id',
            'comment'
        ]);

        $response->assertJson([
            'task_id' => $newComment['task_id'],
            'user_id' => $newComment['user_id'],
            'comment' => $newComment['comment'],
        ]);

        $this->assertDatabaseHas('comments', $newComment);
    }

    /**
     * @test
     */
    public function validation_fails_if_comment_is_missing()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/comments/createComment';

        $task = Task::factory()->create();

        $newComment = [
            'task_id' => $task->id,
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $newComment);

        $response->assertStatus(422);

        $response->assertInvalid(['comment' => 'The comment field is required.']);

        $this->assertDatabaseMissing('comments', $newComment);

    }

    /**
     * @test
     */
    public function validation_fails_if_comment_is_empty()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/comments/createComment';

        $task = Task::factory()->create();

        $newComment = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'comment' => ''
        ];

        $response = $this->withToken($token)->postJson($url, $newComment);

        $response->assertStatus(422);

        $response->assertInvalid(['comment' => 'The comment field is required.']);

        $this->assertDatabaseMissing('comments', $newComment);

    }

    /**
     * @test
     */
    public function validation_fails_if_comment_is_too_long()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/comments/createComment';

        $task = Task::factory()->create();

        $newComment = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'comment' => str_repeat('a', 256),
        ];

        $response = $this->withToken($token)->postJson($url, $newComment);

        $response->assertStatus(422);

        $response->assertInvalid(['comment' => 'The comment field must not be greater than 255 characters.']);

        $this->assertDatabaseMissing('comments', $newComment);
    }

    /**
     * @test
     */
    public function validation_fails_if_comment_is_too_short()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/comments/createComment';

        $task = Task::factory()->create();

        $newComment = [
            'task_id' => $task->id,
            'user_id' => $user->id,
            'comment' => str_repeat('a', 19),
        ];

        $response = $this->withToken($token)->postJson($url, $newComment);

        $response->assertStatus(422);

        $response->assertInvalid(['comment' => 'The comment field must be at least 20 characters.']);

        $this->assertDatabaseMissing('comments', $newComment);
    }
}
