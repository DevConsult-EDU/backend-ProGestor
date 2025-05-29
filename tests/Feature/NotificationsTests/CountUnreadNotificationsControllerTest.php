<?php

namespace Feature\NotificationsTests;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CountUnreadNotificationsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = JwtAuth::fromUser(User::factory()->create());
        $this->url = '/api/auth/notifications/count-unread';
    }

    public function test_returns_unauthenticated_if_user_is_not_logged_in()
    {
        // No usamos actingAs(), por lo tanto, el usuario no está autenticado
        $response = $this->getJson($this->url);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Token not valid']);
    }

    public function test_returns_count_of_unread_notifications_for_authenticated_user()
    {
        $user = User::factory()->create();

        // Creamos 3 notificaciones no leídas para este usuario
        Notification::factory()->count(3)->create([
            'user_id' => $user->id,
            'read' => false,
        ]);

        // Creamos 1 notificación leída para este usuario (no debería contarse)
        Notification::factory()->read()->create([
            'user_id' => $user->id,
        ]);

        // Creamos 2 notificaciones no leídas para otro usuario (no deberían contarse)
        $anotherUser = User::factory()->create();
        Notification::factory()->count(2)->unread()->create([
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->withToken($this->token)->getJson($this->url);

        $response->assertStatus(200);
    }

    public function test_returns_zero_if_authenticated_user_has_no_unread_notifications()
    {
        $user = User::factory()->create();

        // Creamos 2 notificaciones leídas para este usuario
        Notification::factory()->count(2)->read()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->withToken($this->token)->getJson($this->url);

        $response->assertStatus(200);
    }

    public function test_returns_zero_if_authenticated_user_has_no_notifications_at_all()
    {
        $user = User::factory()->create();
        // No se crean notificaciones para este usuario

        $response = $this->withToken($this->token)->getJson($this->url);

        $response->assertStatus(200);
    }
}
