<?php

namespace Feature\NotificationsTests;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class MarkNotificationAsReadControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_marks_an_unread_notification_as_read()
    {

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $notification = Notification::factory()->create([
            'user_id' => $user->id,
            'read' => false,
        ]);

        $url = '/api/auth/notifications/' . $notification->id . '/mark-as-readed';

        $response = $this->withToken($token)->putJson($url);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Notificación marcada como leída exitosamente.',
            'notification' => [
                'id' => $notification->id,
                'read' => true,
            ]
        ]);

        $notification->refresh();
        $this->assertTrue($notification->read);

    }

    /** @test */
    public function it_returns_success_if_notification_is_already_read()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $notification = Notification::factory()->create([
            'user_id' => $user->id,
            'read' => true,
        ]);

        $url = '/api/auth/notifications/' . $notification->id . '/mark-as-readed';

        // Act
        $response = $this->withToken($token)->putJson($url);

        // Assert
        $response->assertOk();
        $response->assertJson([
            'message' => 'La notificación ya estaba marcada como leída.',
            'notification' => [
                'id' => $notification->id,
                'read' => true,
            ]
        ]);
    }
}
