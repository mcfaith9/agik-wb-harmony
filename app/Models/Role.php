<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'label', 'attributes'];
    protected $casts = ['attributes' => 'array'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}