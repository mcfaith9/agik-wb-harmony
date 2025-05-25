<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TaskList $tasklist)
    {
        $tasks = Task::with([
                    'tasklist.project', 
                    'users:id,first_name,last_name'
                ])->get();

        return response()->json($tasks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:none,low,medium,high',
            'privacy' => 'in:public,private',
            'tags' => 'nullable|array',
            'estimated_time' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'task_list_id' => 'required|exists:task_lists,id',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $validated['tags'] = $validated['tags'] ?? [];
        $validated['created_by'] = auth()->id();

        $userIds = $validated['user_ids'] ?? [];
        unset($validated['user_ids']); // remove user_ids from task creation payload

        $task = Task::create($validated);

        if (!empty($userIds)) {
            $task->users()->sync($userIds);
        }

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return response()->json($task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:none,low,medium,high',
            'privacy' => 'in:public,private',
            'tags' => 'nullable|array',
            'estimated_time' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($request->has('tags')) {
            $validated['tags'] = $request->tags;
        }

        $userIds = $validated['user_ids'] ?? null;
        unset($validated['user_ids']);

        $task->update($validated);

        if (!is_null($userIds)) {
            $task->users()->sync($userIds);
        }

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
