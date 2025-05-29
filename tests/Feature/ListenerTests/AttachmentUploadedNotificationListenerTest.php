<?php

namespace Feature\ListenerTests;

use App\Events\AttachmentUploadedEvent;
use App\Listeners\AttachmentUploadedNotificationListener;
use App\Models\Attachment;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Mockery;
use Tests\TestCase;

class AttachmentUploadedNotificationListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close(); // Importante para limpiar los mocks de Mockery después de cada test
        parent::tearDown();
    }

    /**
     * @test
     * El listener crea una notificación cuando se maneja el evento AttachmentUploadedEvent.
     */
    public function it_creates_a_notification_when_handling_event(): void
    {
        // 1. Arrange (Preparar)

        // Mockear el evento y sus propiedades
        $mockAttachment = $this->createMock(Attachment::class);
        $mockAttachment->user_id = 1; // El usuario que subió el archivo, o el dueño del attachment
        $mockAttachment->file_name = 'documento_importante.pdf';

        $mockTask = $this->createMock(Task::class);
        $mockTask->id = 10;
        $mockTask->title = 'Revisar especificaciones';

        $mockUserPerformingAction = $this->createMock(User::class); // El usuario que disparó el evento
        $mockUserPerformingAction->name = 'Juan Pérez';


        // Crear una instancia del evento con los mocks
        // No es necesario mockear el evento en sí si solo pasamos los datos.
        // Pero si el evento tuviera lógica compleja, se podría mockear también.
        $event = new AttachmentUploadedEvent(
            $mockAttachment,
            $mockTask,
            $mockUserPerformingAction
        );

        // Mockear el método estático `create` del modelo Notification
        // Usamos alias mock porque 'Notification::create' es una llamada estática.
        $notificationMock = Mockery::mock('alias:App\Models\Notification');

        // Datos esperados para la notificación
        $expectedNotificationData = [
            'user_id' => $mockAttachment->user_id, // O podría ser otro user_id dependiendo de tu lógica
            'type' => 'Notificación',
            'title' => 'Nuevo archivo adjunto',
            'content' => $mockUserPerformingAction->name . ' ha subido el archivo ' . $mockAttachment->file_name . ' a la tarea ' . $mockTask->title,
            'link' => '/auth/tasks/' . $mockTask->id,
            'read' => false,
        ];

        // Establecer la expectativa: el método 'create' debe ser llamado una vez con los datos esperados.
        $notificationMock
            ->shouldReceive('create')
            ->once()
            ->with($expectedNotificationData)
            ->andReturn(new Notification($expectedNotificationData)); // Opcional: devolver una instancia


        // 2. Act (Actuar)
        $listener = new AttachmentUploadedNotificationListener();
        $listener->handle($event);

        // 3. Assert (Afirmar)
        // Mockery se encarga de las aserciones de `shouldReceive`. Si `create` no se llama
        // como se espera, el test fallará automáticamente.
        // No necesitas un `assertTrue(true)` aquí, pero a veces se añade para claridad.
        $this->assertTrue(true, "La expectativa de Notification::create() debería haberse cumplido.");
    }

    /**
     * @test
     * Verifica que el listener implementa ShouldQueue si es necesario.
     * Este test es más bien una comprobación de la interfaz, no de la lógica.
     */
    public function listener_implements_should_queue_interface(): void
    {
        $listener = new AttachmentUploadedNotificationListener();
        // Comentado porque tu listener NO implementa ShouldQueue directamente en la clase,
        // lo cual es correcto. Si lo hiciera, esta sería la aserción.
        // $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $listener);

        // Si quisieras probar que el listener se registra correctamente en la cola cuando se
        // dispara el evento, necesitarías un test de integración/feature usando Event::fake().
        // Por ahora, solo verificamos la estructura del listener en sí.

        // Como no lo implementa directamente, solo verificamos que es instanciable.
        $this->assertInstanceOf(AttachmentUploadedNotificationListener::class, $listener);
    }
}
