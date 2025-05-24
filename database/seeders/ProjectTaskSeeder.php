<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\TaskList;
use App\Models\Task;
use App\Models\User;

class ProjectTaskSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        // Total tasks limit
        $maxTasks = 10;
        $tasksCreated = 0;

        // Create up to 3 projects
        Project::factory(rand(1, 3))->create([
            'created_by' => $user->id,
        ])->each(function ($project) use (&$tasksCreated, $maxTasks, $user) {

            // Each project gets between 0 to 5 tasklists
            $tasklistsCount = rand(0, 5);

            TaskList::factory($tasklistsCount)->create([
                'project_id' => $project->id,
                'created_by' => $user->id,
            ])->each(function ($tasklist) use (&$tasksCreated, $maxTasks, $user) {

                // Random tasks for this tasklist, but respect maxTasks global limit
                $remainingTasks = $maxTasks - $tasksCreated;
                if ($remainingTasks <= 0) {
                    return;
                }

                // Create 1 to min(4, remainingTasks) tasks per tasklist
                $tasksCount = rand(1, min(4, $remainingTasks));

                Task::factory($tasksCount)->create([
                    'task_list_id' => $tasklist->id,
                    'created_by' => $user->id,
                ]);

                $tasksCreated += $tasksCount;
            });
        });
    }
}
