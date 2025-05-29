<?php

namespace Feature\StoreTests;

use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class StoreProjectControllerTest extends TestCase
{

    use RefreshDatabase; // Para resetear la BD con cada test
    use WithFaker; // Para generar datos falsos si es necesario

    /**
     * @test
     */
    public function project_can_be_created_successfully(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/projects/createProject';

        $customer = Customer::factory()->create();

        $projectData = [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'customer_id' => $customer->id,
            'status' => 'pendiente',
            'started_at' => '2025-12-01',
            'finished_at' => '2025-12-21',
        ];

        $response = $this->withToken($token)->postJson($url, $projectData);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id', 'name', 'description', 'customer_id', 'status', 'started_at', 'finished_at'
        ]);

        $response->assertJson([
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'customer_id' => $projectData['customer_id'],
            'status' => $projectData['status'],
            'started_at' => $projectData['started_at'],
            'finished_at' => $projectData['finished_at']
        ]);

        $this->assertDatabaseHas('projects', [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'customer_id' => $projectData['customer_id'],
            'status' => $projectData['status'],
            'started_at' => $projectData['started_at'],
            'finished_at' => $projectData['finished_at']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_name_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/projects/createProject';

        $customer = Customer::factory()->create();

        $projectData = [
            'description' => $this->faker->sentence,
            'customer_id' => $customer->id,
            'status' => 'pendiente',
            'started_at' => '2025-12-01',
            'finished_at' => '2025-12-21',
        ];

        $response = $this->withToken($token)->postJson($url, $projectData);

        $response->assertStatus(422);

        $response->assertInvalid(['name' => 'The name field is required.']);

        $this->assertDatabaseMissing('projects', [
            'description' => $projectData['description'],
            'customer_id' => $projectData['customer_id'],
            'status' => $projectData['status'],
            'started_at' => $projectData['started_at'],
            'finished_at' => $projectData['finished_at']
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_if_name_is_empty(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/projects/createProject';

        $customer = Customer::factory()->create();

        $projectData = [
            'name' => '',
            'description' => $this->faker->sentence,
            'customer_id' => $customer->id,
            'status' => 'pendiente',
            'started_at' => '2025-12-01',
            'finished_at' => '2025-12-21',
        ];

        $response = $this->withToken($token)->postJson($url, $projectData);

        $response->assertStatus(422);

        $response->assertInvalid(['name' => 'The name field is required.']);

        $this->assertDatabaseMissing('projects', [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'customer_id' => $projectData['customer_id'],
            'status' => $projectData['status'],
            'started_at' => $projectData['started_at'],
            'finished_at' => $projectData['finished_at']
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_if_name_is_too_long(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/projects/createProject';

        $customer = Customer::factory()->create();

        $projectData = [
            'name' => str_repeat('a', 256),
            'description' => $this->faker->sentence,
            'customer_id' => $customer->id,
            'status' => 'pendiente',
            'started_at' => '2025-12-01',
            'finished_at' => '2025-12-21',
        ];

        $response = $this->withToken($token)->postJson($url, $projectData);

        $response->assertStatus(422);

        $response->assertInvalid(['name' => 'The name field must not be greater than 255 characters.']);

        $this->assertDatabaseMissing('projects', [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'customer_id' => $projectData['customer_id'],
            'status' => $projectData['status'],
            'started_at' => $projectData['started_at'],
            'finished_at' => $projectData['finished_at']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_description_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/projects/createProject';

        $customer = Customer::factory()->create();

        $projectData = [
            'name' => $this->faker->name,
            'customer_id' => $customer->id,
            'status' => 'pendiente',
            'started_at' => '2025-12-01',
            'finished_at' => '2025-12-21',
        ];

        $response = $this->withToken($token)->postJson($url, $projectData);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field is required.']);

        $this->assertDatabaseMissing('projects', [
            'name' => $projectData['name'],
            'customer_id' => $projectData['customer_id'],
            'status' => $projectData['status'],
            'started_at' => $projectData['started_at'],
            'finished_at' => $projectData['finished_at']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_description_is_empty()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/projects/createProject';

        $customer = Customer::factory()->create();

        $projectData = [
            'name' => $this->faker->name,
            'description' => '',
            'customer_id' => $customer->id,
            'status' => 'pendiente',
            'started_at' => '2025-12-01',
            'finished_at' => '2025-12-21',
        ];

        $response = $this->withToken($token)->postJson($url, $projectData);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field is required.']);

        $this->assertDatabaseMissing('projects', [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'customer_id' => $projectData['customer_id'],
            'status' => $projectData['status'],
            'started_at' => $projectData['started_at'],
            'finished_at' => $projectData['finished_at']
        ]);


    }

    /**
     * @test
     */
    public function validation_fails_if_description_is_too_short()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/projects/createProject';

        $customer = Customer::factory()->create();

        $projectData = [
            'name' => $this->faker->name,
            'description' => str_repeat('a', 20),
            'customer_id' => $customer->id,
            'status' => 'pendiente',
            'started_at' => '2025-12-01',
            'finished_at' => '2025-12-21',
        ];

        $response = $this->withToken($token)->postJson($url, $projectData);

        $response->assertStatus(422);

        $response->assertInvalid(['description' => 'The description field must be at least 30 characters.']);

        $this->assertDatabaseMissing('projects', [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'customer_id' => $projectData['customer_id'],
            'status' => $projectData['status'],
            'started_at' => $projectData['started_at'],
            'finished_at' => $projectData['finished_at']
        ]);

    }

    /**
     * @test
     */
    public function validation_fails_if_customer_id_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/projects/createProject';

        $projectData = [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'status' => 'pendiente',
            'started_at' => '2025-12-01',
            'finished_at' => '2025-12-21',
        ];

        $response = $this->withToken($token)->postJson($url, $projectData);

        $response->assertStatus(422);

        $response->assertInvalid(['customer_id' => 'The customer id field is required.']);

        $this->assertDatabaseMissing('projects', [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'status' => $projectData['status'],
            'started_at' => $projectData['started_at'],
            'finished_at' => $projectData['finished_at']
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_if_customer_id_is_empty(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/projects/createProject';

        $projectData = [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'customer_id' => '',
            'status' => 'pendiente',
            'started_at' => '2025-12-01',
            'finished_at' => '2025-12-21',
        ];

        $response = $this->withToken($token)->postJson($url, $projectData);

        $response->assertStatus(422);

        $response->assertInvalid(['customer_id' => 'The customer id field is required.']);

        $this->assertDatabaseMissing('projects', [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'customer_id' => $projectData['customer_id'],
            'status' => $projectData['status'],
            'started_at' => $projectData['started_at'],
            'finished_at' => $projectData['finished_at']
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_if_started_at_is_missing(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/projects/createProject';

        $customer = Customer::factory()->create();

        $projectData = [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'customer_id' => $customer->id,
            'status' =>  'pendiente',
            'finished_at' => '2025-12-01',
        ];

        $response = $this->withToken($token)->postJson($url, $projectData);

        $response->assertStatus(422);

        $response->assertInvalid(['started_at' => 'The started at field is required.']);

        $this->assertDatabaseMissing('projects', [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'customer_id' => $projectData['customer_id'],
            'status' => $projectData['status'],
            'finished_at' => $projectData['finished_at']
        ]);
    }

    /**
     * @test
     */
    public function validation_fails_if_started_at_is_empty(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/projects/createProject';

        $customer = Customer::factory()->create();

        $projectData = [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'customer_id' => $customer->id,
            'status' =>  'pendiente',
            'started_at' => '',
            'finished_at' => '2025-12-01',
        ];

        $response = $this->withToken($token)->postJson($url, $projectData);

        $response->assertStatus(422);

        $response->assertInvalid(['started_at' => 'The started at field is required.']);

        $this->assertDatabaseMissing('projects', [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'customer_id' => $projectData['customer_id'],
            'status' => $projectData['status'],
            'started_at' => $projectData['started_at'],
            'finished_at' => $projectData['finished_at']
        ]);
    }

}
