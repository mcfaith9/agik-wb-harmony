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
        'priority',
        'privacy',
        'tags',
        'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function tasklists()
    {
        return $this->hasMany(TaskList::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
