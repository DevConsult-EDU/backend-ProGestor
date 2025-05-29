<?php

namespace Feature\ListenerTests;

use App\Events\TaskStatusChangedEvent;
use App\Listeners\TaskStatusChangedNotificationListener;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Mockery;
use Tests\TestCase;

class TaskStatusChangedNotificationListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close(); // Limpiar mocks de Mockery
        parent::tearDown();
    }

    /**
     * @test
     * El listener crea una notificación cuando se maneja el evento TaskStatusChangedEvent.
     */
    public function it_creates_a_notification_when_handling_task_status_changed_event(): void
    {
        // 1. Arrange (Preparar)

        // Mockear la Tarea (Task)
        $mockTask = $this->createMock(Task::class);
        // El listener usa $task->user_id, $task->title, $task->status
        // El link está hardcodeado a '/auth/tasks', no usa $task->id para el link en sí.
        // Si el link fuera, por ejemplo, '/auth/tasks/' . $task->id, necesitaríamos mockear $task->id.
        $mockTask->user_id = 789; // ID del usuario a notificar
        $mockTask->title = 'Revisar nueva funcionalidad';
        $mockTask->status = 'En Revisión';


        // Crear una instancia del Evento con el mock de la Tarea
        // Asumimos que TaskStatusChangedEvent solo toma $task como argumento en su constructor.
        // Si TaskStatusChangedEvent necesitara más argumentos, deberías proporcionarlos o mockear el evento también.
        $event = new TaskStatusChangedEvent($mockTask); // Asume que el evento solo necesita la tarea

        // Mockear el método estático `create` del modelo Notification
        $notificationMock = Mockery::mock('alias:App\Models\Notification');

        // Datos esperados para la notificación, basados en la lógica del listener
        $expectedNotificationData = [
            'user_id' => $mockTask->user_id,
            'type' => 'Modificación',
            'title' => 'Cambio de estado en tarea',
            'content' => 'La tarea ' . $mockTask->title . ' ha cambiado a -' . $mockTask->status . '-',
            'link' => '/auth/tasks', // El listener usa un link estático
            'read' => false,
        ];

        // Establecer la expectativa: el método 'create' debe ser llamado una vez con los datos esperados.
        $notificationMock
            ->shouldReceive('create')
            ->once()
            ->with($expectedNotificationData)
            ->andReturn(new Notification($expectedNotificationData)); // Opcional: devolver una instancia


        // 2. Act (Actuar)
        $listener = new TaskStatusChangedNotificationListener();
        $listener->handle($event);

        // 3. Assert (Afirmar)
        // Mockery se encarga de las aserciones de `shouldReceive`.
        // Si `create` no se llama como se espera, el test fallará automáticamente.
        $this->assertTrue(true, "La expectativa de Notification::create() debería haberse cumplido.");
    }

    /**
     * @test
     * Verifica que el listener puede ser instanciado.
     */
    public function listener_can_be_instantiated(): void
    {
        $listener = new TaskStatusChangedNotificationListener();
        $this->assertInstanceOf(TaskStatusChangedNotificationListener::class, $listener);
    }
}
