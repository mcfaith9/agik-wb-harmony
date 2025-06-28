<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function burnout()
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')
            ->withCount([
                'tasks as task_count',
                'tasks as overdue' => fn($q) =>
                    $q->where('status', '!=', 'completed')->where('end_date', '<', now()),
                'tasks as stale_in_progress' => fn($q) =>
                    $q->where('status', 'in_progress')->where('tasks.updated_at', '<', now()->subDays(5)),
                'tasks as high_priority' => fn($q) =>
                    $q->where('priority', 'high'),
            ])
            ->paginate(request('per_page', 10));

        $mapped = $users->map(function ($user) {
            $burnoutScore = $user->overdue * 2 + $user->stale_in_progress * 1.5 + $user->high_priority * 1.2;

            return [
                'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
                'task_count' => $user->task_count,
                'overdue' => $user->overdue,
                'stale_in_progress' => $user->stale_in_progress,
                'high_priority' => $user->high_priority,
                'burnout_score' => round($burnoutScore, 2),
            ];
        });

        return $this->paginateMappedData($mapped, $users);
    }

    public function delayedTasks()
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')
            ->paginate(request('per_page', 10));

        $userIds = $users->pluck('id');

        $delayed = DB::table('task_user')
            ->join('tasks', 'task_user.task_id', '=', 'tasks.id')
            ->whereIn('task_user.user_id', $userIds)
            ->where('tasks.status', '!=', 'completed')
            ->where('tasks.updated_at', '<', now()->subDays(7))
            ->select('task_user.user_id', 'tasks.name', 'tasks.updated_at', 'tasks.status', 'tasks.priority')
            ->get()
            ->groupBy('user_id');

        $mapped = $users->map(function ($user) use ($delayed) {
            $tasks = $delayed[$user->id] ?? collect();

            $avgAge = round($tasks->avg(fn($t) => now()->diffInDays($t->updated_at)) ?? 0, 1);
            $maxAge = $tasks->max(fn($t) => now()->diffInDays($t->updated_at)) ?? 0;

            return [
                'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
                'delayed_tasks' => $tasks->count(),
                'delayed_task_titles' => $tasks->pluck('name')->take(5)->values(),
                'delayed_task_avg_age' => $avgAge,
                'delayed_task_max_age' => $maxAge,
                'delayed_task_distribution' => $tasks->groupBy('status')->map->count(),
                'has_high_priority_delayed' => $tasks->contains('priority', 'high'),
            ];
        });

        return $this->paginateMappedData($mapped, $users);
    }

    public function productivityDrop()
    {
        $now = now();
        $startLastWeek = $now->copy()->subDays(14);
        $endLastWeek = $now->copy()->subDays(7);

        $users = User::select('id', 'first_name', 'last_name', 'email')
            ->paginate(request('per_page', 10));

        $userIds = $users->pluck('id');

        $tasks = DB::table('task_user')
            ->join('tasks', 'tasks.id', '=', 'task_user.task_id')
            ->whereIn('task_user.user_id', $userIds)
            ->select(
                'task_user.user_id',
                'tasks.status',
                'tasks.priority',
                'tasks.created_at',
                'tasks.updated_at',
                'tasks.end_date'
            )
            ->get()
            ->groupBy('user_id');

        $mapped = $users->map(function ($user) use ($tasks, $now, $startLastWeek, $endLastWeek) {
            $userTasks = $tasks[$user->id] ?? collect();

            $completedThisWeek = $userTasks->where('status', 'completed')
                ->filter(fn($t) => between($t->updated_at, $now->copy()->subDays(7), $now))->count();

            $completedLastWeek = $userTasks->where('status', 'completed')
                ->filter(fn($t) => between($t->updated_at, $startLastWeek, $endLastWeek))->count();

            $changePercent = $completedLastWeek > 0
                ? round((($completedThisWeek - $completedLastWeek) / $completedLastWeek) * 100, 2)
                : null;

            $trendLabel = match (true) {
                is_null($changePercent) => 'No data from last week',
                $changePercent > 0 => "$changePercent% increase",
                $changePercent < 0 => abs($changePercent) . "% decrease",
                default => 'No change',
            };

            $recentTasks = $userTasks->filter(fn($t) => $t->created_at >= $startLastWeek);
            $completedTasks = $recentTasks->where('status', 'completed');

            $avgCompletion = round($completedTasks->avg(fn($t) =>
                \Carbon\Carbon::parse($t->created_at)->diffInDays(\Carbon\Carbon::parse($t->updated_at))
            ) ?? 0, 1);

            $completedOnTime = $completedTasks->filter(fn($t) =>
                $t->end_date && $t->updated_at <= $t->end_date
            )->count();

            $onTimeRate = $completedTasks->count() > 0
                ? round(($completedOnTime / $completedTasks->count()) * 100, 1) . '%'
                : 'N/A';

            return [
                'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
                'tasks_assigned' => $recentTasks->count(),
                'in_progress_tasks' => $recentTasks->where('status', 'in_progress')->count(),
                'completed_this_week' => $completedThisWeek,
                'completed_last_week' => $completedLastWeek,
                'productivity_trend' => $trendLabel,
                'avg_completion_time_days' => $avgCompletion,
                'high_priority_completed' => $completedTasks->where('priority', 'high')->count(),
                'completed_on_time_rate' => $onTimeRate,
            ];
        });

        return $this->paginateMappedData($mapped, $users);
    }

    public function overAssignment()
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')
            ->paginate(request('per_page', 10));

        $userIds = $users->pluck('id');

        $tasks = DB::table('task_user')
            ->join('tasks', 'tasks.id', '=', 'task_user.task_id')
            ->whereIn('task_user.user_id', $userIds)
            ->where('tasks.status', '!=', 'completed')
            ->select(
                'task_user.user_id',
                'tasks.status',
                'tasks.priority',
                'tasks.updated_at',
                'tasks.end_date'
            )
            ->get()
            ->groupBy('user_id');

        $mapped = $users->map(function ($user) use ($tasks) {
            $userTasks = $tasks[$user->id] ?? collect();

            $conflicts = $userTasks
                ->groupBy(fn($t) => optional($t->end_date)->toDateString())
                ->filter(fn($group) => $group->count() > 1)->count();

            $statuses = $userTasks->groupBy('status')->count();
            $contextSwitching = $statuses > 1;

            return [
                'user' => $user->only(['id', 'first_name', 'last_name', 'email']),
                'open_tasks' => $userTasks->count(),
                'high_priority_tasks' => $userTasks->where('priority', 'high')->count(),
                'conflicting_due_dates' => $conflicts,
                'context_switching_risk' => $contextSwitching,
                'avg_task_age_days' => round($userTasks->avg(fn($t) =>
                    now()->diffInDays(\Carbon\Carbon::parse($t->updated_at))
                ) ?? 0, 1),
                'tasks_due_soon' => $userTasks->filter(fn($t) =>
                    optional($t->end_date)->isBetween(now(), now()->addDays(7))
                )->count(),
            ];
        });

        return $this->paginateMappedData($mapped, $users);
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

if (!function_exists('between')) {
    function between($date, $start, $end)
    {
        return $date >= $start && $date <= $end;
    }
}
