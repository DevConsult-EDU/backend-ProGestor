<?php

namespace Feature\DeleteTests;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteAttachmentControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     */
    public function test_comment_is_successfully_deleted(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/auth/attachments/';

        $attachmentToDelete = Attachment::factory()->create();


        $response = $this->withToken($token)->deleteJson($url . $attachmentToDelete->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('comments', [
            'id' => $attachmentToDelete->id,
        ]);

    }
}
