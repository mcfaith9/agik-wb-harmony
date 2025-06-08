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

        foreach ($eligibleBadges as $badge) {
            if (!$user->badges->contains($badge->id)) {
                $user->badges()->attach($badge->id, ['awarded_at' => now()]);
            }
        }
    }
}
