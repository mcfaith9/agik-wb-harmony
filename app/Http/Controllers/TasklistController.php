<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Tasklist;
use Illuminate\Http\Request;

class TasklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        return response()->json($project->tasklists()->with('tasks')->get());
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
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'priority' => 'in:none,low,medium,high',
            'privacy' => 'in:public,private',
            'tags' => 'nullable|array',
        ]);

        $validated['created_by'] = auth()->id();

        $requestedBudget = $validated['budget'] ?? 0;
        $usedBudget = $project->tasklists()->sum('budget');
        $remaining = $project->budget - $usedBudget;

        if ($requestedBudget > $remaining) {
            return response()->json([
                'message' => 'Budget exceeds the remaining project budget'
            ], 422);
        }

        $tasklist = $project->tasklists()->create($validated);

        return response()->json($tasklist, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskList $tasklist)
    {
        return response()->json($tasklist);
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
    public function update(Request $request, TaskList $tasklist)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'priority' => 'in:none,low,medium,high',
            'privacy' => 'in:public,private',
            'tags' => 'nullable|array',
        ]);

        if ($request->has('budget')) {
            $usedBudget = $tasklist->project->tasklists()->where('id', '!=', $tasklist->id)->sum('budget');
            $remaining = $tasklist->project->budget - $usedBudget;

            if ($request->budget > $remaining) {
                return response()->json([
                    'message' => 'Updated budget exceeds the remaining project budget'
                ], 422);
            }
        }

        $tasklist->update($validated);

        return response()->json($tasklist);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskList $tasklist)
    {
        $tasklist->delete();
        return response()->json(['message' => 'TaskList deleted successfully']);
    }
}
