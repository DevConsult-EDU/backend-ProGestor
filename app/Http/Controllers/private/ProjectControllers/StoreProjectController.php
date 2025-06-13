<?php

namespace App\Http\Controllers\private\ProjectControllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreProjectController extends Controller
{

    public function __invoke(Request $request): JsonResponse
    {

        request()->validate([
            'name' => 'required|string|max:40',
            'description' => 'required|string|min:20',
            'customer_id' => 'required|string|exists:App\Models\Customer,id',
            'status' => 'string|max:255',
            "started_at" => "required|date",
            "finished_at" => "nullable|date",
            "created_at" => "nullable|date",
            "updated_at" => "nullable|date",
        ]);

        $datos = [
            'id' => Str::uuid()->toString(),
            'name' => $request->input("name"),
            'description' => $request->input("description"),
            'customer_id' => $request->input("customer_id"),
            'status' => "pendiente",
            'started_at' => $request->input("started_at"),
            'finished_at' => $request->input("finished_at"),
            'created_at' => $request->input("created_at"),
            'updated_at' => $request->input("updated_at"),
        ];

        $project = new Project($datos);

        $project->save();

        return response()->json([
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'customer_id' => $project->customer_id,
            'status' => $project->status,
            'started_at' => $project->started_at,
            'finished_at' => $project->finished_at,
            'created_at' => $project->created_at,
            'updated_at' => $project->updated_at
        ]);
    }

}
