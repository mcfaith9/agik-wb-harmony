<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country',
        'city_state',
        'postal_code',
        'tax_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}