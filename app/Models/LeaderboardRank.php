<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderboardRank extends Model
{
    protected $fillable = [
       'user_id',
       'points',
       'rank',
   ];
}
