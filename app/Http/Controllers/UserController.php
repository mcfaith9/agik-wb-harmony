<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::select('id', 'first_name', 'last_name')->get());
    }

    public function assignees()
    {
        $users = User::select('id', 'first_name', 'last_name')->get()->map(function ($user) {
            $user->name = $user->first_name . ' ' . $user->last_name;
            $user->avatar = 'https://ui-avatars.com/api/?name=' . urlencode($user->name);
            return $user;
        });

        return response()->json($users);
    }
}
