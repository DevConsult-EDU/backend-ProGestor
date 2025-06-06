<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UserMentionedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Comment $comment;
    public Collection $mentionedUsers;

    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment, Collection $mentionedUsers)
    {
        $this->comment = $comment;
        $this->mentionedUsers = $mentionedUsers;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
