<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        return Role::paginate($perPage);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles',
            'label' => 'nullable|string',
            'attributes' => 'nullable|array'
        ]);

        $role = Role::create($validated);
        return $role;
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'label' => 'nullable|string',
            'attributes' => 'nullable|array'
        ]);

        $role->update($validated);
        return $role;
    }

    public function assign(Request $request, User $user)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $user->roles()->syncWithoutDetaching([$request->role_id]);
        return response()->json(['message' => 'Role assigned']);
    }

    public function unassign(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->roles()->detach($request->role_id);

        return response()->json(['message' => 'Role unassigned successfully.']);
    }

    public function userRoles(User $user)
    {
        return $user->roles;
    }

    public function destroy(Role $role)
    {
        // Optional: Detach from all users before deleting to avoid constraint violations
        $role->users()->detach();

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully.']);
    }
}
