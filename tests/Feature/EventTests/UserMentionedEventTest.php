<?php

namespace Feature\EventTests;

use App\Events\UserMentionedEvent;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class UserMentionedEventTest extends TestCase
{
    /**
     * @test
     * Testea que el constructor del evento asigna correctamente las propiedades.
     */
    public function event_constructor_sets_properties_correctly(): void
    {
        // Arrange: Preparamos los datos de entrada
        $mockComment = Mockery::mock(Comment::class);
        $user1 = Mockery::mock(User::class);
        $user2 = Mockery::mock(User::class);
        $mentionedUsers = new Collection([$user1, $user2]);

        // Act: Creamos una instancia del evento
        $event = new UserMentionedEvent($mockComment, $mentionedUsers);

        // Assert: Verificamos que las propiedades se hayan asignado
        $this->assertInstanceOf(Comment::class, $event->comment, 'La propiedad comment no es una instancia de Comment.');
        $this->assertSame($mockComment, $event->comment, 'La propiedad comment no es la instancia esperada.');

        $this->assertInstanceOf(Collection::class, $event->mentionedUsers, 'La propiedad mentionedUsers no es una instancia de Collection.');
        $this->assertSame($mentionedUsers, $event->mentionedUsers, 'La propiedad mentionedUsers no es la colección esperada.');
        $this->assertCount(2, $event->mentionedUsers, 'La colección mentionedUsers no tiene la cantidad esperada de usuarios.');
    }

    /**
     * @test
     * Testea que el método broadcastOn devuelve el canal privado correcto.
     */
    public function broadcast_on_returns_correct_private_channel(): void
    {
        // Arrange: Preparamos los datos de entrada
        $mockComment = Mockery::mock(Comment::class);
        $mentionedUsers = new Collection([Mockery::mock(User::class)]); // Solo necesitamos una colección válida
        $event = new UserMentionedEvent($mockComment, $mentionedUsers);

        // Act: Obtenemos los canales de difusión
        $channels = $event->broadcastOn();

        // Assert: Verificamos los canales
        $this->assertIsArray($channels, 'broadcastOn debería devolver un array.');
        $this->assertCount(1, $channels, 'broadcastOn debería devolver un array con un solo canal.');

        $channel = $channels[0];
        $this->assertInstanceOf(PrivateChannel::class, $channel, 'El canal debería ser una instancia de PrivateChannel.');
        $this->assertEquals('private-channel-name', $channel->name, 'El nombre del PrivateChannel no es el esperado.');
    }

    /**
     * @test
     * Testea que el evento implementa ShouldBroadcast.
     */
    public function event_implements_should_broadcast(): void
    {
        // Arrange
        $mockComment = Mockery::mock(Comment::class);
        $mentionedUsers = new Collection();
        $event = new UserMentionedEvent($mockComment, $mentionedUsers);

        // Assert
        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, $event);
    }
}
