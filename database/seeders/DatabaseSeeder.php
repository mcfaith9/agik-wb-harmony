<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\TaskList;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $user = User::first() ?? User::factory()->create();

        // Create 1 project named "Internal"
        $project = Project::factory()->create([
            'name' => 'Internal',
            'description' => 'An internal project focused on improving team workflows, documentation, and development tooling.',
            'created_by' => $user->id,
        ]);

        // Create 1 task list under the Internal project
        TaskList::factory()->create([
            'name' => 'Setup & Planning',
            'description' => 'Tasks related to initial setup, process planning, and internal resource organization.',
            'project_id' => $project->id,
            'created_by' => $user->id,
        ]);
    }
}
