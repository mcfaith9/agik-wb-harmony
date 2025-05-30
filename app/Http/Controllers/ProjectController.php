<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::with('tasklists.tasks')->get();

        $projects->each(function ($project) {
            $project->tasklists->each(function ($tasklist) {
                $tasklist->task_counts = [
                    'todo' => $tasklist->tasks->where('status', 'todo')->count(),
                    'in_progress' => $tasklist->tasks->where('status', 'in_progress')->count(),
                    'completed' => $tasklist->tasks->where('status', 'completed')->count(),
                ];
            });
        });

        return response()->json($projects);
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
        ]);

        $validated['created_by'] = auth()->id();

        $project = Project::create($validated);

        return response()->json($project, 201);      
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return response()->json($project->load('tasklists.tasks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $project->update($request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'in:none,low,medium,high',
            'privacy' => 'in:public,private',
            'tags' => 'nullable|array',
        ]));

        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(['message' => 'Project deleted']);
    }
}
