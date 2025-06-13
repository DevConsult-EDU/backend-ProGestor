<?php

namespace App\Http\Controllers\private\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecentActivitiesController extends Controller
{
    public function __invoke(Request $request)
    {

        $comments = DB::table('comments')->take(8)->get();

        $Comentarios = [];
        foreach ($comments as $comment) {

            $Comentarios[] = [
                'id' => $comment->id,
                'task_id' => $comment->task_id,
                'user_id' => $comment->user_id,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at,
            ];
        }

        return response()->json($Comentarios);
    }
}
