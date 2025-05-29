<?php

namespace Feature\ListenerTests;

use App\Events\DayEndedEvent;
use App\Listeners\TimeEntryReminderNotificationListener;
use App\Models\Notification;
use Mockery;
use Tests\TestCase;

class TimeEntryReminderNotificationListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close(); // Importante para limpiar los mocks de Mockery
        parent::tearDown();
    }

    /**
     * @test
     * El listener crea una notificación de recordatorio cuando se maneja el evento DayEndedEvent.
     */
    public function it_creates_a_reminder_notification_when_handling_event(): void
    {
        // 1. Arrange (Preparar)

        // Crear una instancia del evento con un userId de prueba
        $testUserId = 789;
        // Para el evento DayEndedEvent, solo necesitamos el userId para el constructor.
        // Las propiedades $task y $timeEntry no son usadas por este listener.
        $event = new DayEndedEvent($testUserId);

        // Mockear el método estático `create` del modelo Notification
        $notificationMock = Mockery::mock('alias:App\Models\Notification');

        // Datos esperados para la notificación
        $expectedNotificationData = [
            'user_id' => $testUserId, // El userId viene directamente del evento
            'type' => 'Notificación',
            'title' => 'Recordatorio de registro de tiempo',
            'content' => 'No has registrado tiempo de trabajo hoy. Por favor, actualiza tus registros de tiempo.',
            'link' => '/auth/tasks',
            'read' => false,
        ];

        // Establecer la expectativa: el método 'create' debe ser llamado una vez con los datos esperados.
        $notificationMock
            ->shouldReceive('create')
            ->once()
            ->with($expectedNotificationData)
            ->andReturn(new Notification($expectedNotificationData)); // Opcional: devolver una instancia


        // 2. Act (Actuar)
        $listener = new TimeEntryReminderNotificationListener();
        $listener->handle($event);

        // 3. Assert (Afirmar)
        // Mockery se encarga de las aserciones de `shouldReceive`.
        $this->assertTrue(true, "La expectativa de Notification::create() debería haberse cumplido.");
    }

    /**
     * @test
     * Verifica que el listener puede ser instanciado.
     */
    public function listener_can_be_instantiated(): void
    {
        $listener = new TimeEntryReminderNotificationListener();
        $this->assertInstanceOf(TimeEntryReminderNotificationListener::class, $listener);
    }
}
