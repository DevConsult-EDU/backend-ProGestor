<?php

namespace Feature\IndexTests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexUserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    /**
     * @test
     */
    public function invoke_returns_ok_status_and_correct_json_structure_when_users_exist()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/users';

        User::factory()->count(20)->create();

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonCount(21);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'email',
                'rol',
                'created_at',
            ]
        ]);


    }

    /**
     * @test
     */
    public function invoke_returns_ok_status_and_empty_array_when_no_users_exist()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/users';

        $response = $this->withToken($token)->getJson($url);

        $response->assertStatus(200);

        $response->assertJsonStructure([]);

    }

}
