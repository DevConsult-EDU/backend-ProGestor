<?php

namespace App\Listeners;

use App\Events\TaskStatusChangedEvent;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TaskStatusChangedNotificationListener
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
    public function handle(TaskStatusChangedEvent $event): void
    {
        $task = $event->task;

        Notification::create([
            'user_id' => $task->user_id,
            'type' => 'Modificación',
            'title' => 'Cambio de estado en tarea',
            'content' => 'La tarea ' . $task->title . ' ha cambiado a -' . $task->status . '-',
            'link' => '/auth/tasks',
            'read' => false,
        ]);
    }
}
