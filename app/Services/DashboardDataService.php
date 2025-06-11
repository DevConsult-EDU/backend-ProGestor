<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardDataService
{
    /**
     * Obtiene las 5 actividades (comentarios) más recientes.
     */
    public function getRecentActivities(): Collection
    {
        // La lógica del RecentActivitiesController
        return DB::table('comments')->latest()->take(5)->get();
    }

    /**
     * Obtiene los proyectos activos con sus contadores de tareas.
     */
    public function getActiveProjects(): Collection
    {
        // La lógica del UserActiveProjectsController
        $projects = DB::table('projects')->where('status', 'activo')->get();

        // Es más eficiente obtener todos los contadores en menos consultas
        $taskCounts = DB::table('tasks')
            ->select('project_id', DB::raw('count(*) as total'), DB::raw("sum(case when status = 'completado' then 1 else 0 end) as completed"))
            ->whereIn('project_id', $projects->pluck('id'))
            ->groupBy('project_id')
            ->get()
            ->keyBy('project_id');

        return $projects->map(function ($project) use ($taskCounts) {
            $project->task_count = $taskCounts[$project->id]->total ?? 0;
            $project->completed_task_count = $taskCounts[$project->id]->completed ?? 0;
            return $project;
        });
    }

    /**
     * Obtiene las tareas pendientes para el usuario autenticado.
     */
    public function getPendingTasks(): Collection
    {
        // La lógica del UserPendingTasksController
        $query = DB::table('tasks')->where('status', '!=', 'completado');

        if (Auth::user()->rol !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        return $query->get();
    }
}
