<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {

        $users = User::all();

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|max:255',
            'type' => 'required',
            'password' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'type' => $request->type,
            'password' => $request->password
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created!'
        ]);
    }

    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function edit()
    {
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'nullable|max:255',
            'type' => 'required',
            'password' => 'required'
        ]);

        $user->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'User updated'
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'messge' => 'User deleted successfully!'
        ]);
    }
}
