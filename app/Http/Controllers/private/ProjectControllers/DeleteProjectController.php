<?php

namespace App\Http\Controllers\private\ProjectControllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class DeleteProjectController extends Controller
{

    public function __invoke(Request $request)
    {
        $project = Project::find($request->route("id"));

        $project->delete();
    }

}
