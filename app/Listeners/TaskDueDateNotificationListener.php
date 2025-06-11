<?php

namespace App\Listeners;

use App\Events\TaskDueDateApproachingEvent;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TaskDueDateNotificationListener
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
    public function handle(TaskDueDateApproachingEvent $event): void
    {
        $task = $event->task;

        $actualDate = new \DateTime();
        $dueDate = new \DateTime($task->due_date);

        $daysRemaining = $actualDate->diff($dueDate)->days;

        Notification::create([
            'user_id' => $task->user_id,
            'type' => 'Fecha límite',
            'title' => 'Tarea próxima a vencer',
            'content' => 'La tarea ' . $task->title . ' vence mañana',
            'link' => '/tasks/' . $task->id,
            'read' => false,
        ]);

    }
}
