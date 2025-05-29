<?php

namespace App\Http\Controllers\private\TaskControllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class DeleteTaskController extends Controller
{

    public function __invoke(Request $request)
    {
        $task = Task::find($request->route("id"));

        $task->delete();
    }

}
