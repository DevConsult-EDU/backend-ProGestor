<?php

namespace Feature\IndexTests;

use App\Models\Attachment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexAttachmentControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    public function invoke_returns_ok_status_and_correct_json_structure_when_attachments_exist()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $task = Task::factory()->create();

        $url = '/api/tasks/' . $task->id . '/attachments';

        Attachment::factory()->count(10)->create(['task_id' => $task->id]);

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonCount(10);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'task_id',
                'user_id',
                'file_name',
                'system_name',
                'type_MIME',
                'byte_size',
                'store_path',
                'created_at',
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

        $url = '/api/tasks/' . $task->id . '/attachments';

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

        $attachment1 = Attachment::factory()->create();
        $attachment2 = Attachment::factory()->count(3)->create();

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'task_id',
                'user_id',
                'file_name',
                'system_name',
                'type_MIME',
                'byte_size',
                'store_path',
                'created_at',
            ]
        ]);

        $this->assertDatabaseCount('attachments', 4);
    }
}
