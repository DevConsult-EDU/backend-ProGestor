<?php

namespace App\Jobs;

use App\Events\TaskDueDateApproachingEvent; // Asegúrate de que el namespace del evento sea correcto
use Carbon\Carbon; // Usar Carbon para fechas
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Para registrar errores o advertencias

class CheckTasksDueDateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // No se necesitan argumentos para este job en particular,
        // ya que obtiene todas las tareas internamente.
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Usar cursor() para eficiencia de memoria con muchas tareas
        foreach (DB::table('tasks')->orderBy('id')->cursor() as $task) {
            if (empty($task->due_date)) {
                // Opcional: registrar tareas sin fecha de vencimiento
                // Log::warning("Task ID {$task->id} has no due_date. Skipping.");
                continue;
            }

            try {
                // Convertir due_date a un objeto Carbon, considerando solo la fecha (ignorando la hora)
                $dueDate = Carbon::parse($task->due_date)->startOfDay();
            } catch (\Exception $e) {
                Log::error("Invalid date format for task ID {$task->id}: {$task->due_date}. Error: " . $e->getMessage());
                continue; // Saltar esta tarea si la fecha no es válida
            }

            $actualDateStartOfDay = Carbon::now()->startOfDay();

            // Verificar si la fecha de vencimiento es hoy o mañana
            // 1. La fecha de vencimiento debe ser hoy o en el futuro (gte: greater than or equal)
            // 2. La diferencia en días entre la fecha de vencimiento y hoy debe ser 0 (hoy) o 1 (mañana)
            if ($dueDate->gte($actualDateStartOfDay) && $dueDate->diffInDays($actualDateStartOfDay) <= 1) {
                // $task es un objeto stdClass porque viene de DB::table().
                // Si TaskDueDateApproachingEvent espera un modelo Eloquent (App\Models\Task),
                // necesitarías obtener el modelo, por ejemplo:
                // $taskModel = \App\Models\Task::find($task->id);
                // if ($taskModel) {
                //     event(new TaskDueDateApproachingEvent($taskModel));
                // }
                // Por ahora, asumimos que el evento puede manejar stdClass o solo necesita el ID.
                event(new TaskDueDateApproachingEvent($task));
            }
        }
    }
}
