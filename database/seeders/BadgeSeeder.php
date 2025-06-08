<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name' => 'First Task', 'icon' => '🎯', 'description' => 'You’ve taken the first step!', 'required_points' => 10],
            ['name' => 'Getting Productive', 'icon' => '💼', 'description' => 'Your efforts are paying off.', 'required_points' => 25],
            ['name' => 'Halfway There', 'icon' => '⏳', 'description' => 'Making solid progress.', 'required_points' => 50],
            ['name' => 'Task Champion', 'icon' => '🏆', 'description' => 'You’re on a winning streak!', 'required_points' => 100],
            ['name' => 'Overachiever', 'icon' => '🚀', 'description' => 'Taking things to the next level.', 'required_points' => 150],
            ['name' => 'Consistency is Key', 'icon' => '📅', 'description' => 'Consistency breeds success.', 'required_points' => 200],
            ['name' => 'Collaboration Star', 'icon' => '🤝', 'description' => 'Teamwork makes the dream work.', 'required_points' => 250],
            ['name' => 'Deadline Crusher', 'icon' => '⏰', 'description' => 'Always ahead of the clock.', 'required_points' => 300],
            ['name' => 'Project Hero', 'icon' => '🦸', 'description' => 'Stepping up when it counts.', 'required_points' => 350],
            ['name' => 'Legend of Productivity', 'icon' => '🌟', 'description' => 'Your work speaks volumes.', 'required_points' => 500],
            ['name' => 'Night Owl', 'icon' => '🌙', 'description' => 'Burning the midnight oil.', 'required_points' => 400],
            ['name' => 'Weekend Warrior', 'icon' => '🛠️', 'description' => 'Making weekends count.', 'required_points' => 320],
            ['name' => 'Focus Master', 'icon' => '🎧', 'description' => 'Laser-sharp concentration.', 'required_points' => 280],
            ['name' => 'Checklist Crusher', 'icon' => '✅', 'description' => 'Knocking it out, one by one.', 'required_points' => 270],
            ['name' => 'Bug Smasher', 'icon' => '🔧', 'description' => 'Fixing issues like a pro.', 'required_points' => 260],
            ['name' => 'Sprint Finisher', 'icon' => '🏁', 'description' => 'Crossing the finish line strong.', 'required_points' => 340],
            ['name' => 'Fast Starter', 'icon' => '⚡', 'description' => 'Quick off the mark.', 'required_points' => 230],
            ['name' => 'Organized Operator', 'icon' => '📂', 'description' => 'Everything in its place.', 'required_points' => 210],
            ['name' => 'Team MVP', 'icon' => '👑', 'description' => 'Leading your team with style.', 'required_points' => 370],
            ['name' => 'Elite Performer', 'icon' => '💎', 'description' => 'A true master of the craft.', 'required_points' => 750],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
