<?php

namespace App\Http\Controllers\private\AIChatController;

use App\Http\Controllers\Controller;
use Exception;
use Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class DescriptionGeneratorAIController extends Controller
{

    public function generateDescription(Request $request)
    {
        $name = $request->input('name');
        $type = $request->input('type');
        $prompt = $this->createPrompt($name, $type);

        try {

            $yourApiKey = getenv('GEMINI_API_KEY');

            $result = Gemini::client($yourApiKey)
                ->generativeModel(model: 'gemini-2.0-flash')
                ->generateContent($prompt);

            return response()->json(['descriptionAI' => trim($result->text())]);

        } catch (Throwable $e) {
            Log::error('Gemini Service Error: ' . $e->getMessage());
            return "No se pudo generar la descripción para '{$name}'. Por favor, añádela manualmente.";
        }
    }

    /**
     * Crea un prompt específico para la IA según el tipo de contenido.
     */
    private function createPrompt(string $title, string $type): string
    {
        if ($type === 'task') {
            return "Eres un asistente de gestión de proyectos. Genera una descripción técnica
            y concisa para una tarea de desarrollo de software con el siguiente título: \"{$title}\".

             La descripción debe estar en español, ser profesional, detallar el objetivo principal y
             tener una longitud maxima de 10 palabras.";
        }

        return "Eres un asistente de gestión de proyectos. Genera una descripción profesional para
        un proyecto de software con el siguiente nombre: \"{$title}\".
        La descripción debe estar en español, explicar su objetivo general, su alcance y el valor que aportará.
        La descripción no debe de tener título.
        La longitud mínima debe ser de 20 palabras.";
    }
}
