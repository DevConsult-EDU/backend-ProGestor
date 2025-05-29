<?php

namespace App\Http\Controllers\private\CommentControllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class DeleteCommentController extends Controller
{
    public function __invoke(Request $request)
    {
        $comment = Comment::find($request->route("id"));

        $comment->delete();
    }
}
