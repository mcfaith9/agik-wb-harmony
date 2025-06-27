<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Models\Task;
use App\Models\UserPoint;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(TaskList $tasklist)
    {
        $tasks = Task::with([
                    'tasklist.project', 
                    'users:id,first_name,last_name'
                ])
                ->withCount('comments')
                ->get();

        return response()->json($tasks);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
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

        // Validate task budget against project budget
        $taskList = TaskList::with('project.tasklists.tasks')->findOrFail($validated['task_list_id']);
        $requestedTaskBudget = $validated['budget'] ?? 0;
        $usedBudget = $taskList->project->tasklists->flatMap->tasks->sum('budget');
        $remaining = $taskList->project->budget - $usedBudget;

        if ($requestedTaskBudget > $remaining) {
            return response()->json([
                'message' => 'Task budget exceeds remaining project budget'
            ], 422);
        }

        $userIds = $validated['user_ids'] ?? [];
        unset($validated['user_ids']);

        $task = Task::create($validated);

        $tagIds = collect($request->tags)->map(function ($tag) {
            if (isset($tag['id'])) {
                return $tag['id'];
            }

            $newTag = \App\Models\Tag::firstOrCreate([
                'label' => $tag['label']
            ], [
                'color' => $tag['color'] ?? '#ec4899',
                'created_by' => auth()->id(),
            ]);

            return $newTag->id;
        });
        $task->tags()->sync($tagIds);

        if (!empty($userIds)) {
            $task->users()->sync($userIds);
        }

        return response()->json([
            'message' => 'Task created successfully!',
            'task' => $task
        ], 201);
    }

    public function show(Task $task)
    {
        return response()->json($task);
    }

    public function edit(Task $task)
    {
        //
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'priority' => 'in:none,low,medium,high',
            'privacy' => 'in:public,private',
            'status' => 'in:todo,in_progress,completed',
            'tags' => 'nullable|array',
            'estimated_time' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($request->has('budget')) {
            $taskList = $task->tasklist()->with('project.tasklists.tasks')->first();

            if (!$taskList || !$taskList->project) {
                return response()->json([
                    'message' => 'Task is missing a valid task list or project association.'
                ], 500);
            }

            $usedBudget = $taskList->project->tasklists->flatMap->tasks->filter(function ($t) use ($task) {
                return $t->id !== $task->id;
            })->sum('budget');

            $remaining = $taskList->project->budget - $usedBudget;

            if ($request->budget > $remaining) {
                return response()->json([
                    'message' => 'Updated task budget exceeds the remaining project budget'
                ], 422);
            }
        }

        if ($request->has('tags')) {
            $validated['tags'] = $request->tags;
        }

        $userIds = $validated['user_ids'] ?? null;
        unset($validated['user_ids']);

        $task->update($validated);

        if (!is_null($userIds)) {
            $task->users()->sync($userIds);
        }

        if (($validated['status'] ?? null) === 'completed') {
            $gamify = new GamificationService();

            foreach ($task->users as $user) {
                UserPoint::create([
                    'user_id' => $user->id,
                    'action' => 'task_completed',
                    'points' => 10,
                ]);

                $gamify->checkForNewBadges($user);
            }
        }

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}