<?php

namespace Feature\ListenerTests;

use App\Events\ProjectStatusChangedEvent;
use App\Listeners\ProjectStatusChangedNotificationListener;
use App\Models\Notification;
use App\Models\Project;
use App\Models\User;
use Mockery;
use Tests\TestCase;

class ProjectStatusChangedNotificationListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close(); // Limpiar mocks de Mockery
        parent::tearDown();
    }

    /**
     * @test
     * El listener crea una notificación cuando se maneja el evento ProjectStatusChangedEvent.
     */
    public function it_creates_a_notification_when_handling_event(): void
    {
        // 1. Arrange (Preparar)

        // Mockear el proyecto
        $mockProject = $this->createMock(Project::class);
        $mockProject->name = 'Lanzamiento Alpha';
        $mockProject->status = 'En Progreso'; // El estado actual del proyecto

        // Mockear el usuario a quien se notificará (o el usuario que realizó la acción,
        // dependiendo de la lógica deseada para 'user_id' en la notificación)
        // En tu listener, usas $event->user->id, lo que implica que el usuario del evento
        // es a quien se le asigna la notificación.
        $mockUser = $this->createMock(User::class);
        $mockUser->id = 99; // ID del usuario

        // Crear una instancia del evento con los mocks
        $event = new ProjectStatusChangedEvent($mockProject, $mockUser);

        // Mockear el método estático `create` del modelo Notification
        $notificationMock = Mockery::mock('alias:App\Models\Notification');

        // Datos esperados para la notificación
        $expectedNotificationData = [
            'user_id' => $mockUser->id,
            'type' => 'Modificación',
            'title' => 'Actualización de proyecto',
            'content' => 'El proyecto ' . $mockProject->name . ' ha cambiado a estado -' . $mockProject->status . '-',
            'link' => '/auth/projects',
            'read' => false,
        ];

        // Establecer la expectativa
        $notificationMock
            ->shouldReceive('create')
            ->once()
            ->with($expectedNotificationData)
            ->andReturn(new Notification($expectedNotificationData)); // Opcional


        // 2. Act (Actuar)
        $listener = new ProjectStatusChangedNotificationListener();
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
        $listener = new ProjectStatusChangedNotificationListener();
        $this->assertInstanceOf(ProjectStatusChangedNotificationListener::class, $listener);
    }
}
