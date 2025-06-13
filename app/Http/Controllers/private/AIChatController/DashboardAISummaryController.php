<?php

namespace App\Http\Controllers\private\AIChatController;

use App\Http\Controllers\Controller;
use App\Services\DashboardDataService;
use Exception;
use Gemini;
use Illuminate\Http\JsonResponse;

class DashboardAISummaryController extends Controller
{
    /**
     * Este controlador usa el DashboardDataService para obtener datos,
     * los formatea para la IA y pide un resumen a Gemini.
     */
    public function __invoke(DashboardDataService $dashboardService): JsonResponse
    {
        try {

            $contextData = [
                'actividad_reciente_comentarios' => $dashboardService->getRecentActivities(),
                'proyectos_activos' => $dashboardService->getActiveProjects(),
                'mis_tareas_pendientes' => $dashboardService->getPendingTasks(),
            ];


            $jsonContext = json_encode($contextData, JSON_PRETTY_PRINT);


            $prompt = $this->createPrompt($jsonContext);


            $yourApiKey = getenv('GEMINI_API_KEY');
            if (!$yourApiKey) {
                throw new Exception('API key for Gemini not configured.');
            }

            $result = Gemini::client($yourApiKey)
                ->generativeModel(model: 'gemini-2.0-flash')->generateContent($prompt);

            $respuestaChat = $result->text();

            $patron = '/```markdown\s*\n(.*?)\n```/s';

            $contenidoExtraido = null;

            if (preg_match($patron, $respuestaChat, $coincidencias)) {
                $contenidoExtraido = trim($coincidencias[1]);
            }


            return response()->json(['summary' => $respuestaChat]);

        } catch (Exception $e) {
            report($e);
            return response()->json([
                'error' => 'No se pudo generar el resumen del dashboard.',

                'message' => $e->getMessage()
            ], 503);
        }
    }

    /**
     * Construye un prompt detallado para obtener un resumen de alta calidad.
     */
    private function createPrompt(string $jsonContext): string
    {
        return <<<PROMPT
        Eres un analista de proyectos experto y tu función es analizar los datos de un dashboard de gestión de proyectos y generar un resumen ejecutivo conciso en español.
        No des recomendaciones ni opiniones. Los username deben estar en negrita.

        A continuación te proporciono los datos del dashboard actual en formato JSON:

        ```json
        {$jsonContext}
        ```

        Basándote en estos datos, por favor, genera un resumen estético que incluya:
        1. **Inicio:** Llevará un titulo en negrita que ponga "Resumen de Hoy". Un resumen breve de las actividades recientes en formato de lista.
        2. **Puntos importantes:** Dirá el número de tareas y proyectos activos.
        3. **Tareas urgentes:** Mostrará un listado de las 5 tareas más urgentes y una breve explicación de porque lo son.
        4.  **Tono:** El resumen debe ser profesional, claro y directo.
        5. **Estilo:** Utiliza Markdown para formatear tu respuesta con títulos y listas.
        PROMPT;
    }
}
