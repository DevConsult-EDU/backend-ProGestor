<?php

namespace App\Http\Controllers\private\AttachmentControllers;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadAttachmentController extends Controller
{
    public function __invoke(Request $request, $id)
    {
        try {

            $attachment = Attachment::findOrFail($id);

            $filePath = $attachment->store_path;

            if (!Storage::exists($filePath)) {
                Log::error("Archivo no encontrado en almacenamiento: {$filePath}");
                return response()->json([
                    'message' => 'El archivo solicitado no existe'
                ], 404);
            }

            Log::info("Usuario " . Auth::id() . " descargó el archivo " . $attachment->file_name);

            return Storage::download(
                $filePath,
                $attachment->file_name,
                [
                    'Content-Type' => $attachment->type_MIME,
                    'Content-Disposition' => 'attachment; filename="' . $attachment->file_name . '"'
                ]
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("Attachment no encontrado: {$id}");
            return response()->json([
                'message' => 'Archivo adjunto no encontrado'
            ], 404);
        } catch (\Exception $e) {
            Log::error("Error al descargar archivo: " . $e->getMessage());
            return response()->json([
                'message' => 'Error al procesar la descarga del archivo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
