<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('task_lists', function (Blueprint $table) {
            $table->decimal('budget', 12, 2)->default(0)->after('description');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->decimal('budget', 12, 2)->default(0)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_lists', function (Blueprint $table) {
            $table->dropColumn('budget');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('budget');
        });
    }
};
