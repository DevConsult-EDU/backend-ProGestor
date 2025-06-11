<?php

namespace App\Http\Controllers\private\NotificationControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexUserNotificationController extends Controller
{
    public function __invoke(Request $request)
    {

        $limit = $request->input('limit', 20);

        $notificationsQuery = DB::table('notifications')
            ->where('user_id', auth()->user()->id)
            ->latest();

        if ($request->has('types') && is_array($request->input('types'))) {
            $filterTypes = $request->input('types');

            if (!empty($filterTypes)) {
                $notificationsQuery->whereIn('type', $filterTypes);
            }
        }

        $paginatedNotifications = $notificationsQuery->paginate($limit);

        $transformedNotifications = [];
        foreach ($paginatedNotifications as $notification) {
            $transformedNotifications[] = [
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

        return response()->json($transformedNotifications);
    }
}
