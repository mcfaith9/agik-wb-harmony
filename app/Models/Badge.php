<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = ['name', 'icon', 'description', 'required_points'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')->withPivot('awarded_at');
    }
}
