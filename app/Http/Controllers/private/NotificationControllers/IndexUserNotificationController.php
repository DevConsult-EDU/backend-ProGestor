<?php

namespace App\Http\Controllers\private\NotificationControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexUserNotificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $notifications = DB::table('notifications');

        if(auth()->user()->rol !== 'admin'){
            $notifications = $notifications->where('user_id', auth()->user()->id);
        }

        $notifications = $notifications->latest()->get();

        $Notificaciones = [];
        foreach ($notifications as $notification) {
            $Notificaciones[] = [

                'id' => $notification->id,
                'user_id' => $notification->user_id,
                'type' => $notification->type,
                'title' => $notification->title,
                'content' => $notification->content,
                'link' => $notification->link,
                'read' => $notification->read,
                'created_at' =>  $notification->created_at,
                'updated_at' => $notification->updated_at
            ];
        }

        return response()->json($Notificaciones);
    }
}
