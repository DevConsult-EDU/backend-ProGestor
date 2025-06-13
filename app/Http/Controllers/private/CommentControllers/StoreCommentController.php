<?php

namespace App\Http\Controllers\private\CommentControllers;

use App\Events\CommentCreatedEvent;
use App\Events\UserMentionedEvent;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreCommentController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {

        request()->validate([
            'comment' => 'required|string|min:20|max:255',
            'created_at' => 'nullable|date|date_format:d-m-Y',
            'updated_at' => 'nullable|date|date_format:d-m-Y',
        ]);

        $taskId = $request->input('task_id');
        $task = Task::find($taskId);

        if(is_null($task)){
            throw new \InvalidArgumentException("Task not found: " . $taskId);
        }
        $user = auth()->user();

        $datos = [
            'id' => Str::uuid()->toString(),
            'task_id' => $taskId,
            'user_id' => $user->id,
            'comment' => $request->input('comment'),
            'created_at' => $request->input("created_at"),
        ];

        $comment = new Comment($datos);

        $comment->save();

        event(new CommentCreatedEvent($task, $comment));

        $mentionedUsernames = $this->extractUsernamesFromMentions($comment->comment);

        if (!empty($mentionedUsernames)) {

            $mentionedUsers = User::whereIn('name', $mentionedUsernames)->get();

            if (count($mentionedUsers) > 0) {
                event(new UserMentionedEvent($comment, $mentionedUsers));
            }
        }

        return response()->json([
            'id' => $comment->id,
            'task_id' => $comment->task_id,
            'user_id' => $comment->user_id,
            'comment' => $comment->comment,
            'created_at' => $comment->created_at,
        ]);
    }

    private function extractUsernamesFromMentions(string $text): array
    {
        preg_match_all('/@([\w.-]+)/', $text, $matches);

        if (!empty($matches[1])) {
            return array_unique($matches[1]);
        }
        return [];
    }
}
