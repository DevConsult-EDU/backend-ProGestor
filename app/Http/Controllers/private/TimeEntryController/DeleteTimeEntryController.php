<?php

namespace App\Http\Controllers\private\TimeEntryController;

use App\Http\Controllers\Controller;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class DeleteTimeEntryController extends Controller
{
    public function __invoke(Request $request)
    {
        $timeEntry = TimeEntry::find($request->route("id"));

        $timeEntry->delete();
    }
}
