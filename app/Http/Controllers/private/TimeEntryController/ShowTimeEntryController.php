<?php

namespace App\Http\Controllers\private\TimeEntryController;

use App\Http\Controllers\Controller;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class ShowTimeEntryController extends Controller
{
    public function __invoke(Request $request)
    {

        $timeEntry = TimeEntry::find($request->route("id"));

        return [
            "task_id" => $timeEntry['task_id'],
            "user_id" => $timeEntry['user_id'],
            "date" => $timeEntry['date'],
            "minutes" => $timeEntry['minutes'],
            "description" => $timeEntry['description'],
        ];
    }
}
