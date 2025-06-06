<?php

namespace App\Http\Controllers\private\NotificationControllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarkAllNotificationsAsReadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $updatedCount = Notification::where('user_id', $user->id)
            ->where('read', false)
            ->update(['read' => true]);

        if ($updatedCount > 0) {
            return response()->json([
                'message' => "Se marcaron {$updatedCount} notificaciones como leídas.",
                'updated_count' => $updatedCount
            ]);
        }

        return response()->json([
            'message' => 'No había notificaciones sin leer para marcar.',
            'updated_count' => 0
        ]);
    }
}
