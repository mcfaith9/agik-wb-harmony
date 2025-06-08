<?php

namespace App\Services;

use App\Models\User;
use App\Models\Badge;

class GamificationService
{
    /**
     * Award badges to user if they meet requirements
     */
    public function checkForNewBadges(User $user): void
    {
        $totalPoints = $user->points()->sum('points');
        $eligibleBadges = Badge::where('required_points', '<=', $totalPoints)->get();

        $currentBadgeIds = $user->badges()->pluck('badges.id')->toArray();

        $newBadges = $eligibleBadges->filter(fn($badge) => !in_array($badge->id, $currentBadgeIds));

        if ($newBadges->isNotEmpty()) {
            $attachData = $newBadges->mapWithKeys(fn($badge) => [
                $badge->id => ['awarded_at' => now()]
            ])->toArray();

            $user->badges()->attach($attachData);
        }
    }
}
