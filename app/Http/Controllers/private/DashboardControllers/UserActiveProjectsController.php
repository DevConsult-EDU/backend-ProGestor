<?php

namespace App\Http\Controllers\private\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserActiveProjectsController extends Controller
{
    public function __invoke()
    {
        $projects = DB::table('projects')->whereNotIn('status', ['Completado', 'Pospuesto'])->take(5)->get();

        $proyectos = [];
        foreach ($projects as $project) {

            $taskCount = DB::table('tasks')->where('project_id', $project->id)->count();
            $completedTask = DB::table('tasks')->where('project_id', $project->id)->where('status', 'Hecho')->count();

                $proyectos[] = [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'customer_id' => $project->customer_id,
                    'status' => $project->status,
                    'started_at' => $project->started_at,
                    'finished_at' => $project->finished_at,
                    'created_at' => $project->created_at,
                    'updated_at' => $project->updated_at,
                    'task_count' => $taskCount,
                    'completed_task_count' => $completedTask,
                ];

        }

        return response()->json($proyectos);
    }
}
