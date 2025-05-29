<?php

namespace Feature\UpdateTests;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateTaskControllerTest extends TestCase
{

    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->targetTask = Task::factory()->create();

        $this->updateUrl = '/api/tasks/updateTask/';
    }

    /**
     * @test
     * Prueba que una tarea puede ser actualizada exitosamente con datos válidos.
     */
    public function it_can_update_a_task_successfully(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = $this->updateUrl . $this->targetTask->id;

        $project = Project::factory()->create();

        $newData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => 'alta',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $this->targetTask->id,
            'project_id' => $newData['project_id'],
            'title' => $newData['title'],
            'description' => $newData['description'],
            'status' => $newData['status'],
            'priority' => $newData['priority'],
            'user_id' => $newData['user_id'],
        ]);

    }

    /**
     * @test
     * Prueba la validación de la regla 'max' para el título.
     */
    public function it_returns_validation_error_if_title_is_too_long(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = $this->updateUrl . $this->targetTask->id;

        $project = Project::factory()->create();

        $newData = [
            'project_id' => $project->id,
            'title' => str_repeat('a', 56),
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => 'baja',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['title' => 'The title field must not be greater than 50 characters.']);

        $this->assertDatabaseMissing('tasks', [
            'id' => $this->targetTask->id,
            'title' => $newData['title']
        ]);

    }

    /**
     * @test
     */
    public function validation_succeds_if_title_is_unchanged(): void
    {
        $url = $this->updateUrl . $this->targetTask->id;

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $originalTitle = $this->targetTask->title;

        $newData = [
            'project_id' => $project->id,
            'title' => $originalTitle,
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => 'alta',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', $newData);

    }

    /**
     * @test
     * Prueba la validación de la regla 'max' para el título.
     */
    public function it_returns_validation_error_if_description_is_too_short(): void
    {

        $url = $this->updateUrl . $this->targetTask->id;

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $newData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => str_repeat('a', 26),
            'status' => 'pendiente',
            'priority' => 'baja',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field must be at least 30 characters.']);

        $this->assertDatabaseMissing('tasks', [
            'id' => $this->targetTask->id,
            'description' => $newData['description']
        ]);

        // 1. Arrange: Crear una tarea existente
        // 2. Act: Realizar la petición PUT/PATCH con un título que excede los 255 caracteres
        // 3. Assert: Verificar la respuesta (status 400, error específico para title)
    }

    /**
     * @test
     */
    public function it_returns_validation_error_if_description_is_too_long(): void
    {

        $url = $this->updateUrl . $this->targetTask->id;

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $newData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => str_repeat('a', 256),
            'status' => 'pendiente',
            'priority' => 'baja',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field must not be greater than 255 characters.']);

        $this->assertDatabaseMissing('tasks', [
            'id' => $this->targetTask->id,
            'description' => $newData['description']
        ]);

        // 1. Arrange: Crear una tarea existente
        // 2. Act: Realizar la petición PUT/PATCH con un título que excede los 255 caracteres
        // 3. Assert: Verificar la respuesta (status 400, error específico para title)
    }

    /**
     * @test
     */
    public function validation_succeds_if_description_is_unchanged(): void
    {

        $url = $this->updateUrl . $this->targetTask->id;

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $originalDescription = $this->targetTask->description;

        $newData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => $originalDescription,
            'status' => 'pendiente',
            'priority' => 'alta',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', $newData);

    }

    /**
     * @test
     */
    public function validation_succeds_if_status_is_unchanged(): void
    {

        $url = $this->updateUrl . $this->targetTask->id;

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $originalStatus = $this->targetTask->status;

        $newData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' => $originalStatus,
            'priority' => 'alta',
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', $newData);

    }

    /**
     * @test
     */
    public function validation_succeds_if_priority_is_unchanged(): void
    {

        $url = $this->updateUrl . $this->targetTask->id;

        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $originalPriority = $this->targetTask->priority;

        $newData = [
            'project_id' => $project->id,
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'status' => 'pendiente',
            'priority' => $originalPriority,
            'user_id' => $user->id,
        ];

        $response = $this->withToken($token)->putJson($url, $newData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', $newData);

    }
}
