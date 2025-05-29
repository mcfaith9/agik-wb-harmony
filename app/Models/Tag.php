<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'label',
        'color',
        'created_by',
    ];

    public function tasks() {
        return $this->morphedByMany(Task::class, 'taggable');
    }

    public function taskLists() {
        return $this->morphedByMany(TaskList::class, 'taggable');
    }

    public function projects() {
        return $this->morphedByMany(Project::class, 'taggable');
    }
}
