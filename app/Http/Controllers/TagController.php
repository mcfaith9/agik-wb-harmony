<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        $tag = Tag::findOrFail($id);
        $tag->update($validated);

        return response()->json(['message' => 'Tag updated successfully.']);
    }
}
