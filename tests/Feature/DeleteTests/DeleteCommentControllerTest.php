<?php

namespace Feature\DeleteTests;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteCommentControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     */
    public function test_comment_is_successfully_deleted(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/auth/comments/';

        $commentToDelete = Comment::factory()->create();


        $response = $this->withToken($token)->deleteJson($url . $commentToDelete->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('comments', [
            'id' => $commentToDelete->id,
        ]);

    }
}
