<?php

namespace App\Listeners;

use App\Events\AttachmentUploadedEvent;
use App\Models\Notification;

class AttachmentUploadedNotificationListener
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
    public function handle(AttachmentUploadedEvent $event): void
    {
        $attachment = $event->attachment;
        $task = $event->task;
        $user = $event->user;

        Notification::create([
            'user_id' => $attachment->user_id,
            'type' => 'Notificación',
            'title' => 'Nuevo archivo adjunto',
            'content' => $user->name . ' ha subido el archivo ' . $attachment->file_name . ' a la tarea ' . $task->title,
            'link' => '/auth/tasks/' . $task->id,
            'read' => false,
        ]);
    }
}
