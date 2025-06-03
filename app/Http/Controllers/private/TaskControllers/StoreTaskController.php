<?php

namespace App\Http\Controllers\private\TaskControllers;

use App\Events\TaskAssignedEvent;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreTaskController extends Controller
{

    public function __invoke(Request $request, User $user): JsonResponse
    {

        $validatedData = request()->validate([
            'project_id' => 'required|string|exists:App\Models\Project,id',
            'title' => 'required|string|max:255|unique:tasks',
            'description' => 'required|string|min:30',
            'status' => 'string|max:255',
            'priority' => 'required|string|max:255',
            'user_id' => 'required|string|exists:App\Models\User,id',
            'due_date' => 'nullable|date',
            "created_at" => "nullable|date",
            "updated_at" => "nullable|date",
        ]);

        $project = Project::findOrFail($validatedData['project_id']);

        $datos = [
            'id' => Str::uuid()->toString(),
            'project_id' => $request->input("project_id"),
            'title' => $request->input("title"),
            'description' => $request->input("description"),
            'status' => "pendiente",
            'priority' => $request->input("priority"),
            'user_id' => $request->input("user_id"),
            'due_date' => $request->input("due_date"),
            'created_at' => $request->input("created_at"),
            'updated_at' => $request->input("updated_at"),
        ];

        $task = new Task($datos);

        $task->save();

        event(new TaskAssignedEvent($task, $project));

        return response()->json([
            'id' => $task->id,
            'project_id' => $task->project_id,
            'title' => $task->title,
            'description' => $task->description,
            'status' => $task->status,
            'priority' => $task->priority,
            'user_id' => $task->user_id,
            'due_date' => $task->due_date,
            'created_at' => $task->created_at,
            'updated_at' => $task->updated_at
        ]);
    }

}
