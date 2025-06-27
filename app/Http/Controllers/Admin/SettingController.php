<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return response()->json(
            Setting::all()->keyBy('key')->toArray()
        );
    }

    public function update(Request $request, $key)
    {
        $request->validate([
            'value' => 'required',
            'label' => 'nullable|string',
            'type' => 'nullable|in:boolean,number,string',
        ]);

        Setting::set(
            $key,
            $request->value,
            $request->label,
            $request->type ?? 'string'
        );

        return response()->json(['message' => 'Setting updated']);
    }
}
