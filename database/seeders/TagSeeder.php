<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'frontend', 'backend', 'api', 'ui-ux', 'bug', 'feature', 'documentation',
            'urgent', 'low-priority', 'research', 'testing', 'devops', 'performance',
            'security', 'refactor', 'design', 'qa', 'build', 'deployment', 'integration',
            'accessibility', 'support', 'analytics', 'internal', 'external', 'config',
            'new', 'in-progress', 'done', 'blocked'
        ];

        foreach ($tags as $tag) {
            DB::table('tags')->insert([
                'label' => ucwords(str_replace(['-', '_'], ' ', $tag)),
                'color' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
