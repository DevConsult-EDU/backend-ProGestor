<?php

namespace Feature\ListenerTests;

use App\Events\CommentCreatedEvent;
use App\Listeners\CommentCreatedNotificationListener;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Mockery;
use Tests\TestCase;

class CommentCreatedNotificationListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close(); // Limpiar mocks de Mockery
        parent::tearDown();
    }

    /**
     * @test
     * El listener crea una notificación cuando se maneja el evento CommentCreatedEvent.
     */
    public function it_creates_a_notification_when_handling_event(): void
    {
        // 1. Arrange (Preparar)

        // Mockear el usuario que realizó el comentario (viene del evento)
        $mockCommentingUser = $this->createMock(User::class);
        $mockCommentingUser->name = 'Ana García';

        // Mockear la tarea
        $mockTask = $this->createMock(Task::class);
        $mockTask->id = 25;
        $mockTask->title = 'Diseñar nueva interfaz';
        $mockTask->user_id = 5; // ID del usuario dueño de la tarea, a quien se notificará

        // Mockear el comentario
        $mockComment = $this->createMock(Comment::class);
        $mockComment->comment = '¡Me parece una gran idea!';

        // Opción A: Instanciar y asignar propiedades (más aislado para el test del listener)
        $event = new CommentCreatedEvent($mockTask, $mockComment); // Esto usará auth() internamente
        // Para asegurar que el $event->user es el que queremos para el test del listener:
        $event->user = $mockCommentingUser; // Sobrescribimos si es necesario para el test.



        // Mockear el método estático `create` del modelo Notification
        $notificationMock = Mockery::mock('alias:App\Models\Notification');

        // Datos esperados para la notificación
        $expectedNotificationData = [
            'user_id' => $mockTask->user_id, // Se notifica al dueño de la tarea
            'type' => 'Comentario',
            'title' => 'Nuevo comentario en tu tarea',
            'content' => $mockCommentingUser->name . ' ha comentado en la tarea ' . $mockTask->title . ': ' . $mockComment->comment,
            'link' => '/auth/tasks/' . $mockTask->id,
            'read' => false,
        ];

        // Establecer la expectativa
        $notificationMock
            ->shouldReceive('create')
            ->once()
            ->with($expectedNotificationData)
            ->andReturn(new Notification($expectedNotificationData)); // Opcional


        // 2. Act (Actuar)
        $listener = new CommentCreatedNotificationListener();
        $listener->handle($event);

        // 3. Assert (Afirmar)
        // Mockery maneja las aserciones.
        $this->assertTrue(true, "La expectativa de Notification::create() debería haberse cumplido.");
    }

    /**
     * @test
     * Verifica que el listener puede ser instanciado.
     * No implementa ShouldQueue directamente, así que solo verificamos la instanciación.
     */
    public function listener_can_be_instantiated(): void
    {
        $listener = new CommentCreatedNotificationListener();
        $this->assertInstanceOf(CommentCreatedNotificationListener::class, $listener);
    }
}
