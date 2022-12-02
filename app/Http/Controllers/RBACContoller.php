<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RBACContoller extends Controller
{
    public function create_role(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:roles',
            ]);

            $name = $request->input('name');

            if ($validator->fails()) {
                return response()->json(['data' => [], 'message' => $validator->errors(), 'status' => 'error']);
            }

            $role = Role::create(['name' => $name]);
            Log::debug($role);

            return response()->json(['data' => [], 'message' => 'success, create new role', 'status' => 'success']);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function create_permission(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:roles',
            ]);

            $name = $request->input('name');

            if ($validator->fails()) {
                return response()->json(['data' => [], 'message' => $validator->errors(), 'status' => 'error']);
            }

            Permission::create(['name' => $name]);

            return response()->json(['data' => [], 'message' => 'success, create new permission', 'status' => 'success']);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function assign_permission_to_role(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role' => 'required',
                'permission' => 'required',
            ]);

            $role = $request->input('role');
            $permissions = $request->input('permission');

            if ($validator->fails()) {
                return response()->json(['data' => [], 'message' => $validator->errors(), 'status' => 'error']);
            }

            $role = Role::findByName('member');
            foreach ($permissions as $permission) {
                $role->givePermissionTo($permission);
            }

            return response()->json(['data' => [], 'message' => 'success, Assign permissions to Roles', 'status' => 'success']);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }
}
