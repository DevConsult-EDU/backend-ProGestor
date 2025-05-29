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
        $user = $request->user(); // Obtiene el usuario autenticado

        // Si por alguna razón no hay usuario autenticado (aunque el middleware debería prevenirlo)
        if (!$user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $updatedCount = Notification::where('user_id', $request->route('id'))
            ->where('read', false) // Solo las no leídas
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
