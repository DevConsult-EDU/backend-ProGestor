<?php

namespace Feature\DeleteTests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteUserControllerTest extends TestCase
{

    use RefreshDatabase;
    /**
     * @test
     */
    public function user_is_successfully_deleted(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/auth/users/';

        $userToDelete = User::factory()->create();


        $response = $this->withToken($token)->deleteJson($url . $userToDelete->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $userToDelete->id,
        ]);

    }
}
