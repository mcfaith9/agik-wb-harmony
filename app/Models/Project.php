<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'budget',
        'priority',
        'privacy',
        'health_status',
        'health_metrics',
        'tags',
        'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'health_metrics' => 'array',
    ];

    public function tasklists()
    {
        return $this->hasMany(TaskList::class);
    }

    public function taskUsers()
    {
        return User::whereHas('tasks.tasklist.project', function ($query) {
            $query->where('projects.id', $this->id);
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getTotalExpensesAttribute()
    {
        return $this->expenses()->sum('amount');
    }

    public function getRemainingBudgetAttribute()
    {
        return $this->budget - $this->total_expenses;
    }

    public function getIsOverBudgetAttribute()
    {
        return $this->total_expenses > $this->budget;
    }

    public function getTotalAllocatedAttribute()
    {
        return $this->tasklists->sum(function ($tasklist) {
            return $tasklist->tasks->sum('budget');
        });
    }
}
