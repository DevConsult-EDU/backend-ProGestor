<?php

namespace App\Http\Controllers\private\TimeEntryController;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreTimeEntryController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {

        request()->validate([
            'date' => 'required|date',
            'minutes' => 'required|integer|min:1',
            'description' => 'required|string|min:20|max:255',
            'created_at' => 'nullable|date|date_format:d-m-Y',
            'updated_at' => 'nullable|date|date_format:d-m-Y',
        ]);

        $taskId = $request->input('task_id');
        $task = Task::find($taskId);
        if(is_null($task)){
            throw new \InvalidArgumentException("Task not found: " . $taskId);
        }
        $user = auth()->user();

        $datos = [
            'id' => Str::uuid()->toString(),
            'task_id' => $taskId,
            'user_id' => $user->id,
            'date' => $request->input("date"),
            'minutes' => $request->input("minutes"),
            'description' => $request->input("description"),
            'created_at' => $request->input("created_at"),
            'updated_at' => $request->input("updated_at"),
        ];

        $timeEntry = new TimeEntry($datos);

        $timeEntry->save();

        return response()->json([
            'id' => $timeEntry->id,
            'task_id' => $timeEntry->task_id,
            'user_id' => $timeEntry->user_id,
            'date' => $timeEntry->date,
            'minutes' => $timeEntry->minutes,
            'description' => $timeEntry->description,
            'created_at' => $timeEntry->created_at,
            'updated_at' => $timeEntry->updated_at
        ]);
    }
}
