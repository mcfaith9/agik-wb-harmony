<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function userAddress(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'country' => 'required|string|max:255',
            'city_state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'tax_id' => 'nullable|string|max:50',
        ]);

        $user->info()->updateOrCreate([], $validated);

        return response()->json(['message' => 'Address updated.']);
    }

    public function show()
    {
        $user = Auth::user();
        $info = $user->info;

        if (!$info) {
            return response()->json(['message' => 'No user info found.'], 404);
        }

        return response()->json([
            'country'     => $info->country,
            'city_state'  => $info->city_state,
            'postal_code' => $info->postal_code,
            'tax_id'      => $info->tax_id,
        ]);
    }
}
