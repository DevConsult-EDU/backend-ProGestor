<?php

namespace Feature\NotificationsTests;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class MarkAllNotificationsAsReadControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

    }

    /** @test */
    public function it_marks_all_unread_notifications_as_read_for_a_user()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/auth/notifications/' . $user->id . '/mark-all-readed';

        Notification::factory()->count(3)->create([
            'user_id' => $user->id,
            'read' => false,
        ]);
        Notification::factory()->count(2)->create([
            'user_id' => $user->id,
            'read' => true,
        ]);

        $response = $this->withToken($token)->putJson($url);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Se marcaron 3 notificaciones como leídas.',
            'updated_count' => 3,
        ]);

        $this->assertEquals(0, Notification::where('user_id', $user->id)->where('read', false)->count());
        $this->assertEquals(5, Notification::where('user_id', $user->id)->where('read', true)->count());

    }

    /** @test */
    public function it_returns_message_if_no_unread_notifications_to_mark()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/auth/notifications/' . $user->id . '/mark-all-readed';

        // Arrange: Creamos solo notificaciones ya leídas para targetUser
        Notification::factory()->count(2)->create([
            'user_id' => $user->id,
            'read' => true,
        ]);
        // O el usuario no tiene notificaciones

        // Act
        $response = $this->withToken($token)->putJson($url);

        // Assert
        $response->assertOk();
        $response->assertJson([
            'message' => 'No había notificaciones sin leer para marcar.',
            'updated_count' => 0,
        ]);
    }

    /** @test */
    public function it_returns_message_if_target_user_has_no_notifications_at_all()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $url = '/api/auth/notifications/' . $user->id . '/mark-all-readed';

        // Act
        $response = $this->withToken($token)->putJson($url);

        // Assert
        $response->assertOk();
        $response->assertJson([
            'message' => 'No había notificaciones sin leer para marcar.',
            'updated_count' => 0,
        ]);
    }

    /** @test */
    public function it_returns_unauthenticated_if_no_user_is_logged_in()
    {

        $user = User::factory()->create();

        $url = '/api/auth/notifications/' . $user->id . '/mark-all-readed';

        $response = $this->putJson($url);

        // Assert
        $response->assertStatus(401); // Esperamos un 401
        // Si quieres ser más específico sobre el mensaje del middleware:
        // $response->assertJson(['message' => 'Unauthenticated.']);
    }

}
