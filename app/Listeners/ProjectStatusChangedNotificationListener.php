<?php

namespace App\Listeners;

use App\Events\ProjectStatusChangedEvent;
use App\Models\Notification;

class ProjectStatusChangedNotificationListener
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
    public function handle(ProjectStatusChangedEvent $event): void
    {
        $project = $event->project;
        $user = $event->user;

        Notification::create([
            'user_id' => $user->id,
            'type' => 'Modificación',
            'title' => 'Actualización de proyecto',
            'content' => 'El proyecto ' . $project->name . ' ha cambiado a estado -' . $project->status . '-',
            'link' => '/auth/projects',
            'read' => false,
        ]);
    }
}
