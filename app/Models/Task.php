<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_list_id',
        'name',
        'description',
        'priority',
        'privacy',
        'tags',
        'estimated_time',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function taskList()
    {
        return $this->belongsTo(TaskList::class, 'task_list_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
