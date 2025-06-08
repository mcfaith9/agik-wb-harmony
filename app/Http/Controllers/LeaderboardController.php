<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaderboardRank;
use Illuminate\Http\JsonResponse;

class LeaderboardController extends Controller
{
    public function index(): JsonResponse
    {
        // Step 1: Get top 10 users with total points and latest badge
        $leaderboard = User::withSum('points', 'points')
            ->with(['badges' => function ($query) {
                $query->orderByDesc('user_badges.awarded_at')
                      ->select('badges.id', 'badges.name', 'badges.icon', 'user_badges.user_id');
            }])
            ->orderByDesc('points_sum_points')
            ->take(10)
            ->get()
            ->map(function ($user) {
                $user->latest_badge = $user->badges->first() ?? null;
                unset($user->badges);
                return $user;
            });

        // Step 2: Get previous ranks and batch upsert current ranks
        $previousRanks = LeaderboardRank::whereIn('user_id', $leaderboard->pluck('id'))->get()->keyBy('user_id');

        $rankData = [];
        foreach ($leaderboard as $index => $user) {
            $rank = $index + 1;
            $prevRank = $previousRanks[$user->id]->rank ?? null;
            $user->rank = $rank;
            $user->rank_change = is_null($prevRank) ? null : $prevRank - $rank;

            $rankData[] = [
                'user_id' => $user->id,
                'rank' => $rank,
                'updated_at' => now(),
                'created_at' => now(),
            ];
        }
        LeaderboardRank::upsert($rankData, ['user_id'], ['rank', 'updated_at']);

        // Step 3: Efficiently get top earners for today/week/month with joins
        $getTopEarner = fn ($start, $end) => User::select('users.id', 'first_name', 'last_name')
            ->join('user_points', 'users.id', '=', 'user_points.user_id')
            ->whereBetween('user_points.created_at', [$start, $end])
            ->groupBy('users.id', 'first_name', 'last_name')
            ->selectRaw('SUM(user_points.points) as total_points')
            ->orderByDesc('total_points')
            ->limit(1)
            ->first();

        $topTodayUser = $getTopEarner(now()->startOfDay(), now()->endOfDay());
        $topWeekUser = $getTopEarner(now()->startOfWeek(), now()->endOfWeek());
        $topMonthUser = $getTopEarner(now()->startOfMonth(), now()->endOfMonth());

        return response()->json([
            'leaderboard' => $leaderboard,
            'top_earners' => [
                'today' => $topTodayUser,
                'week' => $topWeekUser,
                'month' => $topMonthUser,
            ],
        ]);
    }
}
