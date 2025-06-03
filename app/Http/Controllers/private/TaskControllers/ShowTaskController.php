<?php

namespace App\Http\Controllers\private\TaskControllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class ShowTaskController extends Controller
{

    public function __invoke(Request $request)
    {

        $project = Task::find($request->route("id"));

        $totalTime = TimeEntry::where('task_id', $project['id'])->sum('minutes');

        return [
            "totalTime" => $totalTime,
            "project_id" => $project['project_id'],
            "title" => $project['title'],
            "description" => $project['description'],
            "status" => $project['status'],
            "priority" => $project['priority'],
            "user_id" => $project['user_id'],
            "due_date" => $project['due_date'],
            "created_at" => $project['created_at'],
            "updated_at" => $project['updated_at'],
        ];
    }

}
