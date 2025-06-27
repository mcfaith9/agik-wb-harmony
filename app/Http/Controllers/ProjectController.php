<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Setting;
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

            $project->append(['total_expenses', 'remaining_budget', 'is_over_budget']);
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
            'budget' => 'nullable|numeric|min:0',
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
        $project->load('tasklists.tasks');
        $project->append(['total_expenses', 'remaining_budget', 'is_over_budget']);

        return response()->json($project);
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
            'budget' => 'nullable|numeric|min:0',
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

    public function projectBudgetOverview()
    {
        $projects = Project::with('tasklists.tasks')->get();

        $data = $projects->map(function ($project) {
            $used = $project->tasklists->flatMap->tasks->sum('budget');
            $remaining = $project->budget - $used;

            return [
                'id' => $project->id,
                'name' => $project->name,
                'total_budget' => $project->budget,
                'used_budget' => $used,
                'remaining_budget' => $remaining,
            ];
        });

        return response()->json($data);
    }

    public function budgetSummary()
    {
        $projects = Project::with('tasklists.tasks')->get();

        $totalAllocated = $projects->sum(fn($project) => $project->total_allocated);
        $totalExpenses = $projects->sum(fn($project) => $project->total_expenses);

        // Load global budget from settings
        $globalBudget = Setting::get('global_total_budget', 0); // defaults to 0 if not found

        // Ensure it's numeric
        $globalBudget = floatval($globalBudget);

        // Calculate remaining global budget
        $remainingBudget = $globalBudget - $totalAllocated;

        return response()->json([
            'global_budget' => $globalBudget,
            'allocated_budget' => $totalAllocated,
            'total_expenses' => $totalExpenses,
            'remaining_budget' => $remainingBudget,
            'is_overall_over_budget' => $remainingBudget < 0,
            'budget_usage_percent' => $globalBudget > 0
                ? round(($totalAllocated / $globalBudget) * 100, 2)
                : 0,
        ]);
    }
}
