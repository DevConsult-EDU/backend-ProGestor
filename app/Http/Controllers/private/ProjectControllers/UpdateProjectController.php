<?php

namespace App\Http\Controllers\private\ProjectControllers;

use App\Events\ProjectStatusChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UpdateProjectController extends Controller
{

    public function __invoke(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string|max:255',
            'customer_id' => 'string|exists:App\Models\Customer,id',
            'status' => 'string|max:255',
            "started_at" => "date",
            "finished_at" => "nullable|date",
            "created_at" => "nullable|date",
            "updated_at" => "nullable|date",
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $project = Project::find($request->route("id"));

        $datos = [
            'id' => Str::uuid()->toString(),
            'name' => $request->input("name"),
            'description' => $request->input("description"),
            'customer_id' => $request->input("customer_id"),
            'status' => $request->input("status"),
            'started_at' => $request->input("started_at"),
            'finished_at' => $request->input("finished_at"),
            'created_at' => $request->input("created_at"),
            'updated_at' => $request->input("updated_at"),
        ];

        $successfullUpdate = $project->update($datos);

        if($successfullUpdate && $project->wasChanged('status'))
        {
            event(new ProjectStatusChangedEvent($project, $user));
        }

        return $project;
    }

}
