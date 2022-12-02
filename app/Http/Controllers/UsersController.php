<?php

namespace App\Http\Controllers;



use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Http\Resources\Users;
use App\Http\Resources\UsersCollection;


class UsersController extends Controller
{
    public function register(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
            ]);

            $name = $request->input('name');
            $email = $request->input('email');
            $password = $request->input('password');

            if (User::where('name', $name)->where('email', $email)->first()) {
                return response()->json(['data' => [], 'message' => 'user, already created', 'status' => 'failed']);
            }

            if ($validator->fails()) {
                return response()->json(['data' => [], 'message' => $validator->errors(), 'status' => 'error']);
            }

            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = $password;
            $user->assignRole(Role::findByName('member', 'api'));
            $user->save();

            $credentials = request(['email', 'password']);

            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
            ]);

            $name = $request->input('name');
            $email = $request->input('email');
            $password = Hash::make($request->input('password'));

            if (User::where('name', $name)->where('email', $email)->first()) {
                return response()->json(['data' => [], 'message' => 'user, already created', 'status' => 'failed']);
            }

            if ($validator->fails()) {
                return response()->json(['data' => [], 'message' => $validator->errors(), 'status' => 'error']);
            }

            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = $password;
            $user->assignRole(Role::findByName('member', 'api'));
            $user->save();
            return new Users($user);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function show($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['data' => [], 'message' => 'user is not exist in this system', 'status' => 'failed']);
            }

            return new Users($user);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function delete($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['data' => [], 'message' => 'failed, data user not found', 'status' => 'error']);
            }

            if (!$user->hasRole('admin')) {
                if (!User::destroy($id)) {
                    return response()->json(['data' => [], 'message' => 'failed, failed to delete data user', 'status' => 'error']);
                }
            } else {
                return response()->json(['data' => [], 'message' => 'failed, you can not delete data user administrator', 'status' => 'error']);
            }

            return response()->json(['data' => [], 'message' => 'succes, success delete data user', 'status' => 'success']);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function get_all_users()
    {
        try {
            $data = User::paginate($limit ?? config('amount_data_page'));
            return new UsersCollection($data);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function update_patch(Request $request, $id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['data' => [], 'message' => 'failed, data user not found', 'status' => 'error']);
            }
            $user->fill($request->all());
            if ($user->isClean()) {
                return response()->json(['data' => [], 'message' => 'at least one value must change', 'status' => 'error']);
            }
            $user->save();
            return new Users($user);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }

    public function update_put(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['data' => [], 'message' => $validator->errors(), 'status' => 'error',]);
            }

            $user = User::find($id);
            if (!$user) {
                return response()->json(['data' => [], 'message' => 'failed, data user not found', 'status' => 'error']);
            }
            $user->fill($request->all());
            $user->save();
            return new Users($user);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }
}
