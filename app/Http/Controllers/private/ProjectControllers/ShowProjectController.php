<?php

namespace App\Http\Controllers\private\ProjectControllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ShowProjectController extends Controller
{

    public function __invoke(Request $request)
    {

        $project = Project::find($request->route("id"));

        return [
            "name" => $project['name'],
            "description" => $project['description'],
            "customer_id" => $project['customer_id'],
            "status" => $project['status'],
            "started_at" => $project['started_at'],
            "finished_at" => $project['finished_at'],
            "created_at" => $project['created_at'],
            "updated_at" => $project['updated_at'],
        ];
    }

}
