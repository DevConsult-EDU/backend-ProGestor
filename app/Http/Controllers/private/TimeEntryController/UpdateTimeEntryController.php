<?php

namespace App\Http\Controllers\private\TimeEntryController;

use App\Http\Controllers\Controller;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class UpdateTimeEntryController extends Controller
{
    public function __invoke(Request $request)
    {

        $request->validate([
            'date' => 'required|date',
            'minutes' => 'required|integer|min:1',
            'description' => 'required|string|min:40|max:255',
            'created_at' => 'nullable|date|date_format:d-m-Y',
            'updated_at' => 'nullable|date|date_format:d-m-Y',
        ]);

        $timeEntry = TimeEntry::find($request->route("id"));

        if(is_null($timeEntry)){
            throw new \InvalidArgumentException('El registro no existe');
        }

        $datos = [
            'task_id' => $request->input("task_id"),
            'user_id' => $request->input("user_id"),
            'date' => $request->input("date"),
            'minutes' => $request->input("minutes"),
            'description' => $request->input("description"),
            'created_at' => $request->input("created_at"),
            'updated_at' => $request->input("updated_at"),
        ];

        $timeEntry->update($datos);

        return $timeEntry;
    }
}
