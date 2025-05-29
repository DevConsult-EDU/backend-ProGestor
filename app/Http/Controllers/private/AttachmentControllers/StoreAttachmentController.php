<?php

namespace App\Http\Controllers\private\AttachmentControllers;

use App\Events\AttachmentUploadedEvent;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Task; // Asegúrate de importar el modelo Task si necesitas validar la existencia
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Para generar nombres únicos si no usas el store() directo
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response; // Para los códigos de estado HTTP

class StoreAttachmentController extends Controller
{
    /**
     * Almacena un nuevo archivo adjunto subido.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {

        $taskId = $request->route('id');
        $task = Task::findOrFail($taskId);
        $uploadedFiles = $request->file('file');
        $user = $request->user();
        $fileNames = $request->input('file_name');

        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado.'], Response::HTTP_UNAUTHORIZED);
        }

        try {

            foreach ($fileNames as $index => $fileName) {

                $uploadedFile = $uploadedFiles[$index];

                if (!$uploadedFile->isValid()) {
                    return response()->json(['message' => 'El archivo subido no es válido.'], Response::HTTP_BAD_REQUEST);
                }

                $originalFileName = $uploadedFile->getClientOriginalName();
                $mimeType = $uploadedFile->getMimeType();
                $size = $uploadedFile->getSize();


                $storagePath = $uploadedFile->store('attachments', 'local');

                if (!$storagePath) {
                    throw new \Exception("No se pudo almacenar el archivo.");
                }


                $systemFileName = basename($storagePath);


                $attachment = Attachment::create([
                    'task_id'     => $taskId,
                    'user_id'     => $user->id,
                    'file_name'   => $fileName,
                    'system_name' => $systemFileName,
                    'type_MIME'   => $mimeType,
                    'byte_size'   => $size,
                    'store_path'  => $storagePath,

                ]);

            }

            $user = auth()->user();

            event(new AttachmentUploadedEvent($attachment, $task, $user));

            return response()->json($attachment, Response::HTTP_CREATED);

        } catch (\Exception $e) {

            if (isset($storagePath) && Storage::disk('local')->exists($storagePath)) {
                Storage::disk('local')->delete($storagePath);
            }

            \Log::error("Error al almacenar adjunto: " . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error interno al intentar guardar el archivo.' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
