<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'tasklist_id',
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

    public function tasklist()
    {
        return $this->belongsTo(TaskList::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
