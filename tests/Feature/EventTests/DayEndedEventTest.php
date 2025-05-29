<?php

namespace Feature\EventTests;

use App\Events\DayEndedEvent;
use Illuminate\Broadcasting\PrivateChannel;
use Tests\TestCase;

class DayEndedEventTest extends TestCase
{
    /**
     * @test
     * El evento se puede instanciar y la propiedad userId se asigna correctamente.
     */
    public function event_can_be_instantiated_and_user_id_is_set_correctly(): void
    {
        // 1. Arrange (Preparar)
        $expectedUserId = 123;

        // 2. Act (Actuar)
        $event = new DayEndedEvent($expectedUserId);

        // 3. Assert (Afirmar)
        $this->assertInstanceOf(DayEndedEvent::class, $event);
        $this->assertEquals($expectedUserId, $event->userId, "La propiedad 'userId' no se asignó correctamente.");

        // Verificar que las otras propiedades públicas existen pero son null por defecto (ya que no se inicializan)
        $this->assertNull($event->task, "La propiedad 'task' debería ser null por defecto.");
        $this->assertNull($event->timeEntry, "La propiedad 'timeEntry' debería ser null por defecto.");
    }

    /**
     * @test
     * El método broadcastOn devuelve los canales correctos.
     */
    public function broadcast_on_returns_correct_channels(): void
    {
        // 1. Arrange
        $testUserId = 456;
        $event = new DayEndedEvent($testUserId); // El constructor necesita un userId

        // 2. Act
        $channels = $event->broadcastOn();

        // 3. Assert
        $this->assertIsArray($channels, "broadcastOn() debería devolver un array.");
        $this->assertCount(1, $channels, "broadcastOn() debería devolver un array con un canal.");
        $this->assertInstanceOf(PrivateChannel::class, $channels[0], "El canal debería ser una instancia de PrivateChannel.");
        $this->assertEquals('private-channel-name', $channels[0]->name, "El nombre del canal privado no es el esperado.");
    }
}
