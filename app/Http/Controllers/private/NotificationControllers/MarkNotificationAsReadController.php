<?php

namespace App\Http\Controllers\private\NotificationControllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MarkNotificationAsReadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {

        if (!auth()->user()) {
            return response()->json(['message' => 'No autorizado para marcar esta notificación.'], 403);
        }

        $notification = Notification::find($request->route('notificationId'));

        if (!$notification) {
            return response()->json(['message' => 'Notificación no encontrada.'], 404);
        }

        if ($notification->read) {
            return response()->json([
                'message' => 'La notificación ya estaba marcada como leída.',
                'notification' => $notification
            ]);
        }

        $notification->update(['read' => true]);

        return response()->json([
            'message' => 'Notificación marcada como leída exitosamente.',
            'notification' => $notification
        ]);
    }
}
