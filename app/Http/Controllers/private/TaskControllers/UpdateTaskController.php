<?php

namespace App\Http\Controllers\private\TaskControllers;

use App\Events\TaskStatusChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UpdateTaskController extends Controller
{

    public function __invoke(Request $request)
    {

        $request->validate([
            'project_id' => 'required|string|exists:App\Models\Project,id',
            'title' => 'required|string|max:50',
            'description' => 'required|string|min:30|max:255',
            'status' => 'required|string',
            'priority' => 'required|string',
            'user_id' => 'required|string|exists:App\Models\User,id',
            'due_date' => 'nullable|date',
            'created_at' => 'nullable|date',
            'updated_at' => 'nullable|date',
        ]);

        $task = Task::find($request->route("id"));

        $datos = [
            'project_id' => $request->input("project_id"),
            'title' => $request->input("title"),
            'description' => $request->input("description"),
            'status' => $request->input("status"),
            'priority' => $request->input("priority"),
            'user_id' => $request->input("user_id"),
            'due_date' => $request->input("due_date"),
            'created_at' => $request->input("created_at"),
            'updated_at' => $request->input("updated_at"),
        ];

        $successfullUpdate = $task->update($datos);

        if($successfullUpdate && $task->wasChanged('status')){
            event(new TaskStatusChangedEvent($task));
        }

        return $task;
    }

}
