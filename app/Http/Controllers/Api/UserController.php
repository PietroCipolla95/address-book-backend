<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {

        /*  fetch all users */
        $users = User::all()->where('deleted', 0);

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

        // validation
        $request->validate([
            'name' => 'nullable|max:255',
            'type' => 'required',
            'password' => 'required'
        ]);

        // create new user
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
        // show single user
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
        // validation
        $request->validate([
            'name' => 'nullable|max:255',
            'type' => 'required',
            'password' => 'nullable'
        ]);

        // update user
        $user->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'User updated'
        ]);
    }

    public function destroy(User $user)
    {
        $user->update(['deleted' => !$user->deleted]);

        // delete user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!'
        ]);
    }

    public function loginUser(Request $request)
    {

        // validations
        $validateUser = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required'
        ]);


        // if validation fails
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 422);
        }

        // if no record in database is found
        if (!Auth::attempt($request->only(['name', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // find logging user
        $user = User::where('name', $request->name)->first();

        return response()->json([
            'status' => true,
            'message' => 'User logged successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'type' => $user->type,
            ],
            'token' => $user->createToken('api token')->plainTextToken
        ], 200);
    }
}
