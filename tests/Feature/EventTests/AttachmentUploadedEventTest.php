<?php

namespace Feature\EventTests;

use App\Events\AttachmentUploadedEvent;
use App\Models\Attachment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Tests\TestCase;

class AttachmentUploadedEventTest extends TestCase
{
    /**
     * @test
     * El evento se puede instanciar y las propiedades se asignan correctamente.
     */
    public function event_can_be_instantiated_and_properties_are_set_correctly(): void
    {
        // 1. Arrange (Preparar)
        // Mockear los modelos para no depender de la base de datos
        // Si tus modelos son simples POPOs o usas factories que no persisten,
        // podrías instanciarlos directamente. Pero mockear es más seguro para tests unitarios.
        $mockAttachment = $this->createMock(Attachment::class);
        $mockTask = $this->createMock(Task::class);
        $mockUser = $this->createMock(User::class);

        // 2. Act (Actuar)
        $event = new AttachmentUploadedEvent($mockAttachment, $mockTask, $mockUser);

        // 3. Assert (Afirmar)
        $this->assertInstanceOf(AttachmentUploadedEvent::class, $event);
        $this->assertSame($mockAttachment, $event->attachment, "La propiedad 'attachment' no se asignó correctamente.");
        $this->assertSame($mockTask, $event->task, "La propiedad 'task' no se asignó correctamente.");
        $this->assertSame($mockUser, $event->user, "La propiedad 'user' no se asignó correctamente.");
    }

    /**
     * @test
     * El método broadcastOn devuelve los canales correctos.
     */
    public function broadcast_on_returns_correct_channels(): void
    {
        // 1. Arrange
        $mockAttachment = $this->createMock(Attachment::class);
        $mockTask = $this->createMock(Task::class);
        $mockUser = $this->createMock(User::class);

        $event = new AttachmentUploadedEvent($mockAttachment, $mockTask, $mockUser);

        // 2. Act
        $channels = $event->broadcastOn();

        // 3. Assert
        $this->assertIsArray($channels, "broadcastOn() debería devolver un array.");
        $this->assertCount(1, $channels, "broadcastOn() debería devolver un array con un canal.");
        $this->assertInstanceOf(PrivateChannel::class, $channels[0], "El canal debería ser una instancia de PrivateChannel.");
        $this->assertEquals('private-channel-name', $channels[0]->name, "El nombre del canal privado no es el esperado.");
    }
}
