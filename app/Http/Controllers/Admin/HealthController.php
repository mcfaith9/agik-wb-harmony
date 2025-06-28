<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class HealthController extends Controller
{
    public function burnout()
    {
        $users = User::with('tasks')
            ->select('id', 'first_name', 'last_name', 'email')
            ->paginate(request('per_page', 10));

        $data = $users->getCollection()->map(function ($user) {
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

        return $this->paginateMappedData($data, $users);
    }

    public function delayedTasks()
    {
        $users = User::with('tasks')
            ->select('id', 'first_name', 'last_name', 'email')
            ->paginate(request('per_page', 10));

        $data = $users->getCollection()->map(function ($user) {
            $delayedTasks = $user->tasks->filter(
                fn($t) => $t->updated_at->lt(now()->subDays(7)) && $t->status !== 'completed'
            );

            return [
                'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
                'delayed_tasks' => $delayedTasks->count(),
                'delayed_task_titles' => $delayedTasks->pluck('name')->take(5)->values(),
                'delayed_task_avg_age' => round(
                    $delayedTasks->avg(fn($t) =>
                        $t->updated_at->isFuture() ? 0 : $t->updated_at->diffInDays(now())
                    ) ?? 0,
                    1
                ),
                'delayed_task_max_age' => $delayedTasks->max(fn($t) =>
                    $t->updated_at->isFuture() ? 0 : $t->updated_at->diffInDays(now())
                ) ?? 0,
                'delayed_task_distribution' => $delayedTasks->groupBy('status')->map->count(),
                'has_high_priority_delayed' => $delayedTasks->contains('priority', 'high'),
            ];
        });

        return $this->paginateMappedData($data, $users);
    }

    public function productivityDrop()
    {
        $now = now();
        $startLastWeek = $now->copy()->subDays(14);
        $endLastWeek = $now->copy()->subDays(7);

        $users = User::select('id', 'first_name', 'last_name', 'email')
            ->paginate(request('per_page', 10));

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

        return $this->paginateMappedData($data, $users);
    }

    public function overAssignment()
    {
        $users = User::with('tasks')
            ->select('id', 'first_name', 'last_name', 'email')
            ->paginate(request('per_page', 10));

        $data = $users->getCollection()->map(function ($user) {
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

        return $this->paginateMappedData($data, $users);
    }

    private function paginateMappedData($mapped, $originalPaginator)
    {
        return response()->json(new LengthAwarePaginator(
            $mapped,
            $originalPaginator->total(),
            $originalPaginator->perPage(),
            $originalPaginator->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        ));
    }
}
