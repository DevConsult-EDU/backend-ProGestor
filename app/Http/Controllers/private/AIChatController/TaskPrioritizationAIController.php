<?php

namespace App\Http\Controllers\private\AIChatController;

use App\Http\Controllers\Controller;
use Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class TaskPrioritizationAIController extends Controller
{
    /**
     * Orquesta la obtención de tareas, la creación del prompt y la llamada a la API de Gemini.
     */
    public function __invoke(Request $request)
    {
        $tasksQuery = DB::table('tasks');

        if (auth()->user()->rol !== 'Admin') {
            $tasksQuery->where('user_id', auth()->user()->id);
        }

        $tasks = $tasksQuery->get();

        if ($tasks->isEmpty()) {
            return response()->json(['summary' => 'No hay tareas para generar un resumen.']);
        }


        $prompt = $this->createPrompt($tasks);


        $apiKey = getenv('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'La clave de API de Gemini no está configurada en el archivo .env'], 500);
        }

        $result = Gemini::client($apiKey)
            ->generativeModel(model: 'gemini-2.0-flash')->generateContent($prompt);

        $respuestaChat = $result->text();

        $patron = '/```markdown\s*\n(.*?)\n```/s';

        $contenidoExtraido = null;

        if (preg_match($patron, $respuestaChat, $coincidencias)) {
            $contenidoExtraido = trim($coincidencias[1]);
        }

        return response()->json(['summary' => $respuestaChat]);

    }

    /**
     * Crea un prompt detallado para Gemini a partir de una colección de tareas.
     *
     * @param  \Illuminate\Support\Collection $tasks La colección de tareas.
     * @return string El prompt listo para ser enviado a la API.
     */
    private function createPrompt(Collection $tasks): string
    {

        $taskDataForPrompt = $tasks->map(function ($task) {
            return " - Título: {$task->title}, Estado: {$task->status}, Prioridad: {$task->priority}, Fecha Vencimiento: {$task->due_date}";
        })->implode("\n");


        return <<<PROMPT
        Eres un asistente experto en gestión de proyectos. Analiza la siguiente lista de tareas y genera sugerencias sobre que tareas deberian ser priorizadas y porque.

        La sugerencia debe incluir:
        1. Un título que ponga Sugerencias en negrita.
        2. Una lista con las 3 tareas más urgentes y un breve razonamiento de porque son urgentes en cada una.
        3. No pongas consideraciones adicionales ni escribas las fechas.

        Aquí están los datos de las tareas:
        ---
        {$taskDataForPrompt}
        ---

        La estructura de la tarea debe ser asi:

        **tarea**: *razonamiento de porque es urgente*

        PROMPT;
    }
}
