<?php

namespace Feature\EventTests;

use App\Events\CommentCreatedEvent;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Tests\TestCase;

class CommentCreatedEventTest extends TestCase
{
    /**
     * @test
     * El evento se puede instanciar y las propiedades se asignan correctamente.
     */
    public function event_can_be_instantiated_and_properties_are_set_correctly(): void
    {
        // 1. Arrange (Preparar)
        // Mockear los modelos
        $mockTask = $this->createMock(Task::class);
        $mockComment = $this->createMock(Comment::class);
        $mockUser = $this->createMock(User::class); // Este será el usuario autenticado

        // Simular un usuario autenticado
        $this->actingAs($mockUser);

        // 2. Act (Actuar)
        $event = new CommentCreatedEvent($mockTask, $mockComment);

        // 3. Assert (Afirmar)
        $this->assertInstanceOf(CommentCreatedEvent::class, $event);
        $this->assertSame($mockTask, $event->task, "La propiedad 'task' no se asignó correctamente.");
        $this->assertSame($mockComment, $event->comment, "La propiedad 'comment' no se asignó correctamente.");
        $this->assertSame($mockUser, $event->user, "La propiedad 'user' (obtenida de auth()->user()) no se asignó correctamente.");
    }

    /**
     * @test
     * El método broadcastOn devuelve los canales correctos.
     */
    public function broadcast_on_returns_correct_channels(): void
    {
        // 1. Arrange
        $mockTask = $this->createMock(Task::class);
        $mockComment = $this->createMock(Comment::class);
        $mockUser = $this->createMock(User::class);

        $this->actingAs($mockUser); // Necesario para que el constructor no falle

        $event = new CommentCreatedEvent($mockTask, $mockComment);

        // 2. Act
        $channels = $event->broadcastOn();

        // 3. Assert
        $this->assertIsArray($channels, "broadcastOn() debería devolver un array.");
        $this->assertCount(1, $channels, "broadcastOn() debería devolver un array con un canal.");
        $this->assertInstanceOf(PrivateChannel::class, $channels[0], "El canal debería ser una instancia de PrivateChannel.");
        $this->assertEquals('private-channel-name', $channels[0]->name, "El nombre del canal privado no es el esperado.");
    }

}
