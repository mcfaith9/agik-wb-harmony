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
            'priority' => 'in:none,low,medium,high',
            'privacy' => 'in:public,private',
            'tags' => 'nullable|array',
        ]);

        $validated['created_by'] = Auth::id();

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
            'priority' => 'in:none,low,medium,high',
            'privacy' => 'in:public,private',
            'tags' => 'nullable|array',
        ]);

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
