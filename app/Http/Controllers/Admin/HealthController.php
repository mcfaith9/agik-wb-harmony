<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use \App\Models\Task;
use Illuminate\Support\Carbon;

class HealthController extends Controller
{
    public function burnout()
    {
        $users = User::with('tasks')->get();

        $data = $users->map(function ($user) {
            $overdue = $user->tasks->where('due_date', '<', now())->where('status', '!=', 'completed')->count();
            $stale = $user->tasks->where('status', 'in_progress')->filter(
                fn($t) => $t->updated_at->lt(now()->subDays(5))
            )->count();
            $highPriority = $user->tasks->where('priority', 'high')->count();
            $taskCount = $user->tasks->count();

            $burnoutScore = $overdue * 2 + $stale * 1.5 + $highPriority * 1.2;

            return [
                'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
                'task_count' => $taskCount,
                'overdue' => $overdue,
                'stale_in_progress' => $stale,
                'high_priority' => $highPriority,
                'burnout_score' => round($burnoutScore, 2),
            ];
        });

        return response()->json($data->sortByDesc('burnout_score')->values());
    }

    public function stalledTasks()
    {
        $users = User::with('tasks')->get();

        $data = $users->map(function ($user) {
            $stalled = $user->tasks->filter(
                fn($t) => $t->updated_at->lt(now()->subDays(7)) && $t->status !== 'completed'
            )->count();

            return [
                'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
                'stalled_tasks' => $stalled,
            ];
        });

        return response()->json($data->sortByDesc('stalled_tasks')->values());
    }

    public function productivityDrop()
    {
        $now = now();
        $startLastWeek = $now->copy()->subDays(14);
        $endLastWeek = $now->copy()->subDays(7);

        $users = User::get();

        $data = $users->map(function ($user) use ($now, $startLastWeek, $endLastWeek) {
            $completedThisWeek = $user->tasks()
                ->where('tasks.status', 'completed')
                ->whereBetween('tasks.updated_at', [$now->copy()->subDays(7), $now])
                ->count();

            $completedLastWeek = \App\Models\Task::join('task_user', 'tasks.id', '=', 'task_user.task_id')
                ->where('task_user.user_id', $user->id)
                ->where('tasks.status', 'completed')
                ->whereBetween('tasks.updated_at', [$startLastWeek, $endLastWeek])
                ->count();

            $change = $completedLastWeek > 0
                ? round((($completedThisWeek - $completedLastWeek) / $completedLastWeek) * 100, 2)
                : null;

            return [
                'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
                'completed_this_week' => $completedThisWeek,
                'completed_last_week' => $completedLastWeek,
                'productivity_change_percent' => $change,
            ];
        });

        return response()->json($data->sortByDesc('productivity_change_percent')->values());
    }

    public function overAssignment()
    {
        $users = User::with('tasks')->get();

        $data = $users->map(function ($user) {
            $openTasks = $user->tasks->where('status', '!=', 'completed');
            $sameDueDates = $openTasks->groupBy('due_date')->filter(
                fn($group) => $group->count() > 1
            )->count();

            return [
                'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
                'open_tasks' => $openTasks->count(),
                'conflicting_due_dates' => $sameDueDates,
            ];
        });

        return response()->json($data->sortByDesc('open_tasks')->values());
    }
}
