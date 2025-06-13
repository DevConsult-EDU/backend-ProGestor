<?php

namespace App\Http\Controllers\private\AIChatController;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskDistributionAnalysisController extends Controller
{
    /**
     * Orquesta la obtención de tareas, la creación del prompt y la llamada a la API de Gemini.
     */
    public function __invoke(Request $request)
    {
        $apiKey = getenv('GEMINI_API_KEY');

        $userId = $request->user()->id;

        $tasks = Task::where('user_id', $userId)
            ->where('status', '!=', 'Hecho')
            ->get(['id', 'title', 'description', 'status', 'priority', 'project_id', 'created_at']);

        $prompt = "Analiza estas tareas y genera categorías inteligentes para agruparlas:
                   " . json_encode($tasks) . "

                   Responde ÚNICAMENTE con este JSON:
                   {
                     \"chart_data\": {
                       \"labels\": [\"array de nombres de categorías\"],
                       \"data\": [array de números],
                       \"colors\": [\"array de colores hex\"]
                     },
                     \"insight\": \"Un insight clave sobre la distribución\",
                     \"focus_recommendation\": \"Dónde debe enfocarse el usuario hoy\"
                   }";

        $response = Gemini::client($apiKey)
            ->generativeModel(model: 'gemini-2.0-flash')->generateContent($prompt);

        $rawText = $response->text();

        $jsonString = trim(str_replace(['```json', '```'], '', $rawText));

        $decodedData = json_decode($jsonString, true);


        return response()->json($decodedData);

    }


}
