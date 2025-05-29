<?php

namespace App\Listeners;

use App\Events\UserMentionedEvent;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserMentionedNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserMentionedEvent $event): void
    {
        $comment = $event->comment;
        $mentionedUsers = $event->mentionedUsers;

        foreach ($mentionedUsers as $mentionedUser) {
            Notification::create([
                'user_id' => $mentionedUser->id,
                'type' => 'Notificación',
                'title' => 'Te han mencionado en un comentario',
                'content' => $comment->user->name . ' te ha mencionado en un comentario: ' . $comment->comment,
                'link' => '/auth/tasks/' . $comment->task_id,
                'read' => false,
            ]);
        }
    }
}
