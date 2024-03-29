<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {

        $permissions = Permission::all();

        return response()->json([
            'success' => true,
            'permissions' => $permissions,
        ]);
    }
}
