<?php

namespace Feature\NotificationsTests;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class IndexUserNotificationControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private User $adminUser;
    private User $regularUser;
    private User $anotherUser;
    private string $url;


    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuarios
        $this->adminUser = User::factory()->create(['rol' => 'admin']);
        $this->regularUser = User::factory()->create(['rol' => 'user']); // o cualquier rol que no sea 'admin'
        $this->anotherUser = User::factory()->create(['rol' => 'user']);

        $this->url = 'api/auth/notifications/';

        // Crear algunas notificaciones de prueba
        // Notificación para regularUser (más reciente)
        DB::table('notifications')->insert([
            'id' => $this->faker->uuid(), // Asumiendo que usas UUIDs para 'id'
            'user_id' => $this->regularUser->id,
            'type' => 'Notificacion', // Ejemplo
            'title' => 'Nuevo comentario en tarea 1',
            'content' => 'Nuevo contenido de la notificacion para el usuario',
            'link' => '/tasks/1',
            'read' => false,
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ]);

        // Notificación para regularUser (más antigua)
        DB::table('notifications')->insert([
            'id' => $this->faker->uuid(), // Asumiendo que usas UUIDs para 'id'
            'user_id' => $this->regularUser->id,
            'type' => 'Notificacion', // Ejemplo
            'title' => 'Nuevo comentario en tarea 2',
            'content' => 'Nuevo contenido de la notificacion para el usuario',
            'link' => '/tasks/2',
            'read' => false,
            'created_at' => Date::now()->subDay(),
            'updated_at' => Date::now()->subDay(),
        ]);

        // Notificación para anotherUser
        DB::table('notifications')->insert([
            'id' => $this->faker->uuid(), // Asumiendo que usas UUIDs para 'id'
            'user_id' => $this->anotherUser->id,
            'type' => 'Notificacion', // Ejemplo
            'title' => 'Nuevo comentario en tarea 3',
            'content' => 'Nuevo contenido de la notificacion para el usuario',
            'link' => '/tasks/3',
            'read' => false,
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ]);
    }

    /** @test */
    public function admin_can_view_all_notifications_ordered_by_latest()
    {
        $token = JWTAuth::fromUser($this->adminUser);

        $response = $this->withToken($token)->getJson($this->url . $this->adminUser->id);

        $response->assertStatus(200);
        $response->assertJsonCount(3);

        $responseData = $response->json();

        // Verificar el orden (la más reciente primero)
        $this->assertEquals('Nuevo comentario en tarea 1', $responseData[0]['title']); // Notif. para regularUser
        $this->assertEquals('Nuevo comentario en tarea 3', $responseData[1]['title']);   // Notif. para anotherUser
        $this->assertEquals('Nuevo comentario en tarea 2', $responseData[2]['title']);         // Notif. para regularUser (más antigua)

        // Verificar la estructura de una notificación
        $response->assertJsonStructure([
            '*' => [
                'id',
                'user_id',
                'type',
                'title',
                'content',
                'link',
                'read',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    /** @test */
    public function non_admin_user_can_view_only_their_notifications_ordered_by_latest()
    {
        $token = JWTAuth::fromUser($this->regularUser);

        $response = $this->withToken($token)->getJson($this->url . $this->regularUser->id);

        $response->assertOk();
        $response->assertJsonCount(2);

        $responseData = $response->json();

        // Verificar el orden (la más reciente primero)
        $this->assertEquals('Nuevo comentario en tarea 1', $responseData[0]['title']);
        $this->assertEquals($this->regularUser->id, $responseData[0]['user_id']);
        $this->assertEquals('Nuevo comentario en tarea 2', $responseData[1]['title']);
        $this->assertEquals($this->regularUser->id, $responseData[1]['user_id']);

        // Asegurarse que no ve notificaciones de otros
        foreach ($responseData as $notification) {
            $this->assertEquals($this->regularUser->id, $notification['user_id']);
        }

        // Verificar la estructura de una notificación
        $response->assertJsonStructure([
            '*' => [
                'id',
                'user_id',
                'type',
                'title',
                'content',
                'link',
                'read',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    /** @test */
    public function user_with_no_notifications_sees_an_empty_array()
    {
        $userWithNoNotifications = User::factory()->create(['rol' => 'user']);

        $token = JWTAuth::fromUser($userWithNoNotifications);

        $response = $this->withToken($token)->getJson($this->url . $userWithNoNotifications->id);

        $response->assertOk();
        $response->assertJsonCount(0);
        $response->assertExactJson([]);
    }

    /** @test */
    public function notification_data_is_correctly_formatted()
    {
        // Tomamos la notificación más reciente de regularUser para verificar
        $notificationFromDb = DB::table('notifications')
            ->where('user_id', $this->regularUser->id)
            ->latest()
            ->first();

        $token = JWTAuth::fromUser($this->regularUser);

        $response = $this->withToken($token)->getJson($this->url . $this->regularUser->id);

        $response->assertOk();
        $expectedCreatedAt = Carbon::parse($notificationFromDb->created_at)->format('Y-m-d H:i:s');
        $expectedUpdatedAt = Carbon::parse($notificationFromDb->updated_at)->format('Y-m-d H:i:s');

        $response->assertJsonFragment([
            'id' => $notificationFromDb->id,
            'user_id' => $notificationFromDb->user_id,
            'type' => $notificationFromDb->type,
            'title' => $notificationFromDb->title,
            'content' => $notificationFromDb->content,
            'link' => $notificationFromDb->link,
            'read' => $notificationFromDb->read,
            'created_at' => $expectedCreatedAt,
            'updated_at' => $expectedUpdatedAt,
        ]);
    }
}
