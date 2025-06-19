<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Team::with([
            'users' => function ($query) {
                $query->select('users.id', 'first_name', 'last_name', 'email');
            },
            'users.tasks' => function ($query) {
                $query->select('tasks.id', 'name');
            }
        ])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:teams,name',
        ]);

        try {
            Team::create(['name' => $request->name]);
            return response()->json(['message' => 'Team created.'], 201);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json(['message' => 'Team already exists.'], 422);
            }

            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function addUser(Request $request, Team $team)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $team->users()->syncWithoutDetaching([$request->user_id]);

        return response()->json(['message' => 'User added successfully.']);
    }

    public function removeUser(Request $request, Team $team)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $team->users()->detach($request->user_id);

        return response()->json(['message' => 'User removed from team']);
    }
}
