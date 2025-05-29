<?php

namespace App\Listeners;

use App\Events\CommentCreatedEvent;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CommentCreatedNotificationListener
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
    public function handle(CommentCreatedEvent $event): void
    {
        $user = $event->user;
        $task = $event->task;
        $comment = $event->comment;

        Notification::create([
            'user_id' => $task->user_id,
            'type' => 'Comentario',
            'title' => 'Nuevo comentario en tu tarea',
            'content' => $user->name . ' ha comentado en la tarea ' . $task->title . ': ' . $comment->comment,
            'link' => '/auth/tasks/' . $task->id,
            'read' => false,
        ]);
    }
}
