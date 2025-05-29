<?php

namespace App\Http\Controllers\private\TimeEntryController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexTimeEntryController extends Controller
{
    public function __invoke(Request $request)
    {

        $time_entries = DB::table('time_entries')->where('task_id', $request->route('id'))->get();

        $HorasEchadas = [];
        foreach ($time_entries as $time_entry) {
            $HorasEchadas[] = [
                'id' => $time_entry->id,
                'task_id' => $time_entry->task_id,
                'user_id' => $time_entry->user_id,
                'date' => $time_entry->date,
                'minutes' => $time_entry->minutes,
                'description' => $time_entry->description
            ];
        }

        return response()->json($HorasEchadas);
    }
}
