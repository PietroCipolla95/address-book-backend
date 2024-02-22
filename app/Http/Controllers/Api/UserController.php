<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {

        $users = User::where('deleted', 0)->with('permissions')->get()->toArray();

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        // validation
        $valData = $request->validate([
            'name' => 'nullable|max:255',
            'type' => 'required',
            'password' => 'required',
            'permissions' => 'required|array',
        ]);

        $permissionIds = [];
        foreach ($request->permissions as $permission) {
            $permissionIds[] = Permission::where('name', $permission)->value('id');
        }




        // create new user
        $newUser = User::create($valData);
        $newUser->permissions()->attach($permissionIds);

        return response()->json([
            'success' => true,
            'message' => 'User created!',
            'permissions' => $newUser
        ]);
    }

    public function show(User $user)
    {

        $permissions = [];
        $user = User::with('permissions')->find($user)->first()->toArray();
        /* $user->with('permissions')->find($user); */

        Log::debug($user);
        Log::debug($user);
        Log::debug(var_export($user, true));

        foreach ($user['permissions'] as $permission) {
            $permissions[] = $permission['name'];
        }

        unset($user['permissions'], $user['created_at'], $user['deleted_at'], $user['deleted']);
        // show single user
        return response()->json([
            'success' => true,
            'user' => $user,
            'permissions' => $permissions
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
        $user->permissions()->detach();

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
            'user' => $user,
            'token' => $user->createToken('api token')->plainTextToken
        ], 200);
    }
}
