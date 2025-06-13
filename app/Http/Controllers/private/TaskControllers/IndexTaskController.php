<?php

namespace App\Http\Controllers\private\TaskControllers;

use App\Events\TaskDueDateApproachingEvent;
use App\Http\Controllers\Controller;
use App\Jobs\CheckTasksDueDateJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexTaskController extends Controller
{

    public function __invoke(Request $request)
    {

        $tasks = DB::table('tasks');

        if(auth()->user()->rol !== 'Admin'){
            $tasks = $tasks->where('user_id', auth()->user()->id);
        }

        $tasks = $tasks->get();

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
                'due_date' => $task->due_date,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ];
        }

        return response()->json($tareas);
    }

}
