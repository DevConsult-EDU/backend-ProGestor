<?php

namespace App\Http\Controllers\private\ProjectControllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class IndexProjectController extends Controller
{

    public function __invoke()
    {
        $projects = DB::table('projects')->get();

        $proyectos = [];
        foreach ($projects as $project) {
            $proyectos[] = [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'customer_id' => $project->customer_id,
                'status' => $project->status,
                'started_at' => $project->started_at,
                'finished_at' => $project->finished_at,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at
            ];
        }

        return response()->json($proyectos);
    }

}
