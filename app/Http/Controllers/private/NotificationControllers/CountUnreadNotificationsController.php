<?php

namespace App\Http\Controllers\private\NotificationControllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class CountUnreadNotificationsController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $unreadCount = Notification::query()
            ->where('user_id', $user->id)
            ->where('read', false)
            ->count();

        return $unreadCount;
    }
}
