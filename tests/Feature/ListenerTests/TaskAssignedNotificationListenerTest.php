<?php

namespace Feature\ListenerTests;

use App\Events\TaskAssignedEvent;
use App\Listeners\TaskAssignedNotificationListener;
use App\Models\Notification;
use App\Models\Project;
use App\Models\Task;
use Mockery;
use Tests\TestCase;

class TaskAssignedNotificationListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close(); // Limpiar mocks de Mockery
        parent::tearDown();
    }

    /**
     * @test
     * El listener crea una notificación cuando se maneja el evento TaskAssignedEvent.
     */
    public function it_creates_a_notification_when_handling_event(): void
    {
        // 1. Arrange (Preparar)

        // Mockear la tarea
        $mockTask = $this->createMock(Task::class);
        $mockTask->title = 'Desarrollar módulo de autenticación';
        $mockTask->user_id = 15; // ID del usuario al que se asignó la tarea

        // Mockear el proyecto
        $mockProject = $this->createMock(Project::class);
        $mockProject->name = 'Plataforma E-commerce V2';

        // Crear una instancia del evento con los mocks
        $event = new TaskAssignedEvent($mockTask, $mockProject);

        // Mockear el método estático `create` del modelo Notification
        $notificationMock = Mockery::mock('alias:App\Models\Notification');

        // Datos esperados para la notificación
        $expectedNotificationData = [
            'user_id' => $mockTask->user_id, // El ID del usuario asignado a la tarea
            'type' => 'Asignación',
            'title' => 'Nueva tarea asignada',
            'content' => 'Se te ha asignado la tarea ' . $mockTask->title .' en el proyecto ' . $mockProject->name,
            'link' => '/auth/tasks',
            'read' => false,
        ];

        // Establecer la expectativa
        $notificationMock
            ->shouldReceive('create')
            ->once()
            ->with($expectedNotificationData)
            ->andReturn(new Notification($expectedNotificationData)); // Opcional


        // 2. Act (Actuar)
        $listener = new TaskAssignedNotificationListener();
        $listener->handle($event);

        // 3. Assert (Afirmar)
        // Mockery maneja las aserciones.
        $this->assertTrue(true, "La expectativa de Notification::create() debería haberse cumplido.");
    }

    /**
     * @test
     * Verifica que el listener puede ser instanciado.
     */
    public function listener_can_be_instantiated(): void
    {
        $listener = new TaskAssignedNotificationListener();
        $this->assertInstanceOf(TaskAssignedNotificationListener::class, $listener);
    }
}
