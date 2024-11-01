<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function create(Request $request)
    {
        // $request->validate(['name' => 'required|string|max:255']);
        $group = Group::create($request->only('name'));

        return response()->json($group);
    }

    public function index()
    {
        return Group::all();
    }
}
