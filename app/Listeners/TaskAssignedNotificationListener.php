<?php

namespace App\Listeners;

use App\Events\TaskAssignedEvent;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TaskAssignedNotificationListener
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
    public function handle(TaskAssignedEvent $event): void
    {
        $task = $event->task;
        $project = $event->project;

        Notification::create([
            'user_id' => $task->user_id,
            'type' => 'Asignación',
            'title' => 'Nueva tarea asignada',
            'content' => 'Se te ha asignado la tarea ' . $task->title .' en el proyecto ' . $project->name,
            'link' => '/auth/tasks',
            'read' => false,
        ]);
    }
}
