<?php

namespace App\Services;

use App\Models\Project;
use Carbon\Carbon;

class ProjectHealthService
{
    public function getStatus(Project $project): array
    {
        $tasks = $project->tasklists->flatMap->tasks;

        $totalTasks = $tasks->count();
        $completed = $tasks->where('status', 'completed')->count();
        $incomplete = $tasks->where('status', '!=', 'completed')->count();
        $overdue = $tasks->where('end_date', '<', now())
                         ->where('status', '!=', 'completed')
                         ->count();

        $progress = $totalTasks > 0 ? $completed / $totalTasks : 1;
        $overdueRatio = $incomplete > 0 ? $overdue / $incomplete : 0;

        $assignedBudget = $tasks->sum('budget');
        $budgetUsage = $project->budget > 0
            ? $assignedBudget / $project->budget
            : 0;

        $teamLoad = $project->taskUsers()
            ->withCount('activeTasks')
            ->get()
            ->avg('active_tasks_count');

        $score = 100;

        if ($progress < 0.5) $score -= 20;
        if ($overdueRatio > 0.3) $score -= 25;
        if ($budgetUsage > 1.0) $score -= 30;
        if ($teamLoad > 8) $score -= 15;

        $status = match (true) {
            $score >= 80 => 'green',
            $score >= 50 => 'yellow',
            default => 'red',
        };

        return [
            'status' => $status,
            'score' => $score,
            'metrics' => [
                'progress' => round($progress * 100),
                'overdue' => round($overdueRatio * 100),
                'budget_usage' => round($budgetUsage * 100),
                'team_load' => round($teamLoad, 1),
            ]
        ];
    }
}
