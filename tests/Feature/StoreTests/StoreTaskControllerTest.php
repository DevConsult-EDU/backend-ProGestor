<?php

namespace Feature\StoreTests;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class StoreTaskControllerTest extends TestCase
{

    use RefreshDatabase; // Para resetear la BD con cada test
    use WithFaker; // Para generar datos falsos si es necesario

    /**
     * @test
     */
    public function task_can_be_created_successfully()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $taskData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => 'media',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id', 'project_id', 'title', 'description', 'status', 'priority', 'user_id'
        ]);

        $response->assertJson([
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id'],
        ]);

        $this->assertDatabaseHas('tasks', [
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_project_id_is_missing()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/tasks/createTask';

        $taskData = [
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => 'media',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['project_id' => 'The project id field is required.']);

        $this->assertDatabaseMissing('tasks', [
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id']
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_if_project_id_is_empty()
    {
        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $taskData = [
            'project_id' => '',
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => 'media',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['project_id' => 'The project id field is required.']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_title_is_missing()
    {

        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $taskData = [
            'project_id' => $project->id,
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => 'media',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['title' =>  'The title field is required']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $taskData['project_id'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_title_is_empty()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $taskData = [
            'project_id' => $project->id,
            'title' => '',
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => 'media',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['title' =>  'The title field is required']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_title_is_too_long()
    {
        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $taskData = [
            'project_id' => $project->id,
            'title' => str_repeat('a', 256),
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => 'media',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['title' =>  'The title field must not be greater than 255 characters.']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id']
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_if_title_already_exists()
    {

        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        Task::factory()->create(['title' => 'Tarea 1']);

        $taskData = [
            'project_id' => $project->id,
            'title' => 'Tarea 1',
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => 'media',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['title' =>  'The title has already been taken.']);

        $this->assertDatabaseCount('tasks', 1);

    }

    /**
     * @test
     */
    public function validation_fails_if_description_is_missing()
    {
        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $taskData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'status' => 'pendiente',
            'priority' => 'media',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['description' =>  'The description field is required.']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id']
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_if_description_is_empty()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $taskData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'status' =>  'pendiente',
            'priority' => 'media',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['description' =>  'The description field is required.']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_description_is_too_short()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $taskData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => str_repeat('a', 25),
            'status' =>  'pendiente',
            'priority' => 'media',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['description' =>  'The description field must be at least 30 characters.']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id']
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_if_priority_is_missing()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $taskData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' =>  'pendiente',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['priority' =>  'The priority field is required.']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'user_id' => $taskData['user_id']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_priority_is_empty()
    {
        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $taskData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' =>  'pendiente',
            'priority' => '',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['priority' =>  'The priority field is required.']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id']
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_if_user_id_is_missing()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $taskData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' =>  'pendiente',
            'priority' => 'media',
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['user_id' =>  'The user id field is required.']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_user_id_is_empty()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/tasks/createTask';

        $project = Project::factory()->create();

        $taskData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' =>  'pendiente',
            'priority' => 'media',
            'user_id' => '',
        ];

        $response = $this->withToken($token)->postJson($url, $taskData);

        $response->assertStatus(422);

        $response->assertInvalid(['user_id' =>  'The user id field is required.']);

        $this->assertDatabaseMissing('tasks', [
            'project_id' => $taskData['project_id'],
            'title' => $taskData['title'],
            'description' => $taskData['description'],
            'status' => $taskData['status'],
            'priority' => $taskData['priority'],
            'user_id' => $taskData['user_id']
        ]);

    }
}
