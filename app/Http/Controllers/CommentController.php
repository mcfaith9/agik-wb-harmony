<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Task;
use App\Models\TaskList;

class CommentController extends Controller
{
    protected $commentables = [
        'task' => Task::class,
        'tasklist' => TaskList::class,
    ];

    protected function resolveCommentable(string $type, int $id)
    {
        if (!isset($this->commentables[$type])) {
            abort(404, 'Invalid commentable type.');
        }

        $modelClass = $this->commentables[$type];
        return $modelClass::findOrFail($id);
    }

    public function index(string $type, int $id)
    {
        $commentable = $this->resolveCommentable($type, $id);
        return $commentable->comments()->with('user')->latest()->get();
    }

    public function store(Request $request, string $type, int $id)
    {
        $commentable = $this->resolveCommentable($type, $id);

        $data = $request->validate([
            'message' => 'required|string',
        ]);

        $comment = $commentable->comments()->create([
            'message' => $data['message'],
            'user_id' => $request->user()->id,
        ]);

        return $comment->load('user');
    }
}

