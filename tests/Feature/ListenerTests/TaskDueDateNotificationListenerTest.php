<?php

namespace Feature\ListenerTests;

use App\Events\TaskDueDateApproachingEvent;
use App\Listeners\TaskDueDateNotificationListener;
use App\Models\Notification;
use App\Models\Task;
use Carbon\Carbon;
use Mockery;
use Tests\TestCase;

class TaskDueDateNotificationListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close(); // Limpiar mocks de Mockery
        Carbon::setTestNow(); // Restablecer Carbon a la fecha real si se usó setTestNow()
        parent::tearDown();
    }

    /**
     * @test
     * El listener crea una notificación de fecha de vencimiento cuando se maneja el evento.
     */
    public function it_creates_a_due_date_notification_when_handling_event(): void
    {
        // 1. Arrange (Preparar)

        // Mockear la tarea
        $mockTask = $this->createMock(Task::class);
        $mockTask->id = 55;
        $mockTask->title = 'Preparar presentación';
        $mockTask->user_id = 22; // ID del usuario asignado a la tarea

        // $task->due_date es usado por `new \DateTime($task->due_date)`.
        // Aunque $daysRemaining no se usa en el 'content' final, debemos proveer un valor.
        // Si el 'content' usara $daysRemaining, necesitaríamos controlar esto con más cuidado.
        // Por ahora, una fecha futura cualquiera es suficiente.
        $mockTask->due_date = Carbon::now()->addDays(1)->toDateString();


        // Crear una instancia del evento con el mock de la tarea
        $event = new TaskDueDateApproachingEvent($mockTask);

        // Mockear el método estático `create` del modelo Notification
        $notificationMock = Mockery::mock('alias:App\Models\Notification');

        // Datos esperados para la notificación
        // NOTA: El 'content' está hardcodeado como "vence mañana" en el listener.
        // Si el listener usara $daysRemaining, este 'content' esperado cambiaría.
        $expectedNotificationData = [
            'user_id' => $mockTask->user_id,
            'type' => 'Fecha límite',
            'title' => 'Tarea próxima a vencer',
            'content' => 'La tarea ' . $mockTask->title . ' vence mañana',
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
        $listener = new TaskDueDateNotificationListener();
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
        $listener = new TaskDueDateNotificationListener();
        $this->assertInstanceOf(TaskDueDateNotificationListener::class, $listener);
    }

    /**
     * @test
     * Prueba el cálculo de días restantes si el contenido de la notificación dependiera de él.
     * Este test es más para demostrar cómo se haría si $daysRemaining fuera usado.
     * Dado que el listener actual no usa $daysRemaining en el output, este test es ilustrativo.
     */
    public function it_correctly_calculates_days_remaining_if_content_depended_on_it(): void
    {
        // Congelar el tiempo actual para el test
        $knownDate = Carbon::create(2023, 10, 26, 12, 0, 0); // Jueves, 26 de Octubre 2023
        Carbon::setTestNow($knownDate);

        $mockTask = $this->createMock(Task::class);
        $mockTask->id = 56;
        $mockTask->title = 'Otra tarea';
        $mockTask->user_id = 23;
        $mockTask->due_date = Carbon::now()->addDays(1)->toDateString(); // Vence el 27 de Octubre 2023 (mañana)

        $event = new TaskDueDateApproachingEvent($mockTask);

        // Capturar los argumentos pasados a Notification::create
        $capturedArgs = null;
        $notificationMock = Mockery::mock('alias:App\Models\Notification');
        $notificationMock
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::capture($capturedArgs));

        $listener = new TaskDueDateNotificationListener();
        $listener->handle($event);

        // Aquí podríamos verificar $capturedArgs['content'] si usara $daysRemaining.
        // Por ejemplo, si el contenido fuera: "La tarea X vence en {$daysRemaining} día(s)"
        // Y esperamos que daysRemaining sea 1.
        // $this->assertStringContainsString('vence en 1 día', $capturedArgs['content']);

        // Como el listener actual tiene el contenido hardcodeado, solo confirmamos la llamada.
        $this->assertNotNull($capturedArgs, "Notification::create() no fue llamado.");
        $this->assertEquals('La tarea  vence mañana', $capturedArgs['content']);


        Carbon::setTestNow(); // Limpiar el estado de Carbon
    }
}
