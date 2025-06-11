<?php

namespace App\Listeners;

use App\Events\DayEndedEvent;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TimeEntryReminderNotificationListener
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
    public function handle(DayEndedEvent $event): void
    {
        Notification::create([
            'user_id' => $event->userId,
            'type' => 'Notificación',
            'title' => 'Recordatorio de registro de tiempo',
            'content' => 'No has registrado tiempo de trabajo hoy. Por favor, actualiza tus registros de tiempo.',
            'link' => '/tasks',
            'read' => false,
        ]);
    }
}
