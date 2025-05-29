<?php

namespace Feature\ListenerTests;

use App\Events\UserMentionedEvent;
use App\Listeners\TaskStatusChangedNotificationListener;
use App\Listeners\UserMentionedNotificationListener;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class UserMentionedNotificationListenerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     * Testea que el listener crea una notificación por cada usuario mencionado.
     */
    public function it_creates_notifications_for_mentioned_users(): void
    {
        // Arrange: Preparamos los datos de entrada y los mocks
        $commentText = 'Este es el texto del comentario';
        $commentUserName = 'Usuario Comentador';

        // Mock del usuario que hizo el comentario (necesario para el contenido de la notificación)
        $mockCommentUser = User::factory()->create(['name' => $commentUserName]);

        $task = Task::factory()->create(['user_id' => $mockCommentUser->id]);

        // Mock del comentario
        $mockComment = Comment::factory()->create(['comment' => $commentText, 'task_id' => $task->id, 'user_id' => $mockCommentUser->id]);

        // Mocks de los usuarios mencionados
        $mockMentionedUser1 = User::factory()->create();

        $mockMentionedUser2 = User::factory()->create();

        // Colección de usuarios mencionados
        $mentionedUsers = new Collection([$mockMentionedUser1, $mockMentionedUser2]);

        // Creamos una instancia del evento con los mocks
        $event = new UserMentionedEvent($mockComment, $mentionedUsers);

        // Esperamos que Notification::create sea llamado DOS veces (una por cada usuario mencionado)
        // y verificamos los argumentos de cada llamada.
        // Datos esperados para la notificación
        $expectedNotificationData = [
            'user_id' => $mockMentionedUser1->id,
            'type' => 'Notificación',
            'title' => 'Te han mencionado en un comentario',
            'content' => $mockCommentUser->name . ' te ha mencionado en un comentario: ' . $commentText,
            'link' => '/auth/tasks/' . $task->id,
            'read' => false,
        ];

        $expectedNotificationData2 = [
            'user_id' => $mockMentionedUser2->id,
            'type' => 'Notificación',
            'title' => 'Te han mencionado en un comentario',
            'content' => $mockCommentUser->name . ' te ha mencionado en un comentario: ' . $commentText,
            'link' => '/auth/tasks/' . $task->id,
            'read' => false,
        ];

        // Act: Instanciamos el listener y llamamos al método handle
        $listener = new UserMentionedNotificationListener();
        $listener->handle($event);

        $this->assertDatabaseHas('notifications', $expectedNotificationData);
        $this->assertDatabaseHas('notifications', $expectedNotificationData2);

        // Assert: Mockery verifica automáticamente si las expectativas ('shouldReceive') se cumplieron
        // al finalizar el test (gracias a tearDown en Laravel TestCase).
        // Si las llamadas a Notification::create no ocurrieron como se esperaba, el test fallará aquí.
        $this->assertTrue(true);
    }

    /**
     * @test
     * Testea que el listener no crea notificaciones si no hay usuarios mencionados.
     */
    public function it_does_not_create_notifications_if_no_users_mentioned(): void
    {
        // Arrange: Preparamos los datos de entrada y los mocks
        $mockComment = Mockery::mock(Comment::class); // El comentario no importa mucho aquí
        $mentionedUsers = new Collection([]); // Colección vacía

        // Creamos una instancia del evento con los mocks
        $event = new UserMentionedEvent($mockComment, $mentionedUsers);

        // Mockeamos la clase Notification
        $notificationMock = Mockery::mock('overload:' . Notification::class);

        // Esperamos que Notification::create *nunca* sea llamado
        $notificationMock->shouldNotReceive('create');


        // Act: Instanciamos el listener y llamamos al método handle
        $listener = new UserMentionedNotificationListener();
        $listener->handle($event);

        // Assert: Mockery verifica automáticamente si las expectativas ('shouldNotReceive') se cumplieron.
        $this->assertTrue(true); // Asersión simple para asegurar que el test llega hasta aquí
    }
}
