<?php

namespace App\Http\Controllers\private\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserPendingTasksController extends Controller
{
    public function __invoke(Request $request)
    {

        $tasks = DB::table('tasks');

        if(auth()->user()->rol !== 'Admin'){
            $tasks = $tasks->where('user_id', auth()->user()->id);
        }

        $tasks = $tasks->whereNotIn('status', ['Hecho'])->take(5)->get();

        $tareas = [];

        foreach ($tasks as $task) {
            $tareas[] = [
                'id' => $task->id,
                'project_id' => $task->project_id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'user_id' => $task->user_id,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ];
        }

        return response()->json($tareas);
    }
}
