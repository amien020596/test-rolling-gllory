<?php

namespace App\Http\Controllers;

use App\Http\Resources\User as ResourcesUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
            $user->save();
            $user->assignRole('member');

            return new ResourcesUser($user);
        } catch (\Throwable $th) {
            Log::debug($th);
            return response()->json(['data' => [], 'message' => 'error, internal server error', 'status' => 'error']);
        }
    }
}
