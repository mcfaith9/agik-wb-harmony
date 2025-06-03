<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $fillable = ['task_id', 'user_id', 'message'];

    public function index(Task $task)
    {
        return $task->comments()->with('user')->latest()->get();
    }

    public function store(Request $request, Task $task)
    {
        $data = $request->validate([
            'message' => 'required|string',
        ]);

        $comment = $task->comments()->create([
            'message' => $data['message'],
            'user_id' => $request->user()->id,
        ]);

        return $comment->load('user');
    }
}
