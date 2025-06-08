<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name' => 'First Task', 'description' => 'You’ve taken the first step!', 'required_points' => 10],
            ['name' => 'Getting Productive', 'description' => 'Your efforts are paying off.', 'required_points' => 25],
            ['name' => 'Halfway There', 'description' => 'Making solid progress.', 'required_points' => 50],
            ['name' => 'Task Champion', 'description' => 'You’re on a winning streak!', 'required_points' => 100],
            ['name' => 'Overachiever', 'description' => 'Taking things to the next level.', 'required_points' => 150],
            ['name' => 'Consistency is Key', 'description' => 'Consistency breeds success.', 'required_points' => 200],
            ['name' => 'Collaboration Star', 'description' => 'Teamwork makes the dream work.', 'required_points' => 250],
            ['name' => 'Deadline Crusher', 'description' => 'Always ahead of the clock.', 'required_points' => 300],
            ['name' => 'Project Hero', 'description' => 'Stepping up when it counts.', 'required_points' => 350],
            ['name' => 'Legend of Productivity', 'description' => 'Your work speaks volumes.', 'required_points' => 500],
            ['name' => 'Night Owl', 'description' => 'Burning the midnight oil.', 'required_points' => 400],
            ['name' => 'Weekend Warrior', 'description' => 'Making weekends count.', 'required_points' => 320],
            ['name' => 'Focus Master', 'description' => 'Laser-sharp concentration.', 'required_points' => 280],
            ['name' => 'Checklist Crusher', 'description' => 'Knocking it out, one by one.', 'required_points' => 270],
            ['name' => 'Bug Smasher', 'description' => 'Fixing issues like a pro.', 'required_points' => 260],
            ['name' => 'Sprint Finisher', 'description' => 'Crossing the finish line strong.', 'required_points' => 340],
            ['name' => 'Fast Starter', 'description' => 'Quick off the mark.', 'required_points' => 230],
            ['name' => 'Organized Operator', 'description' => 'Everything in its place.', 'required_points' => 210],
            ['name' => 'Team MVP', 'description' => 'Leading your team with style.', 'required_points' => 370],
            ['name' => 'Elite Performer', 'description' => 'A true master of the craft.', 'required_points' => 750],
        ];

        foreach ($badges as $index => $badge) {
            $badge['icon'] = 'Icon' . ($index + 1) . '.png';
            Badge::create($badge);
        }
    }
}
