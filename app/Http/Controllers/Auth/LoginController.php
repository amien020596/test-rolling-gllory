<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{

    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }

    // protected function attemptLogin(Request $request)
    // {
    //     $token = $this->guard()->attempt($this->credentials($request));
    //     if (!$token) {
    //         return false;
    //     }
    //     $this->guard()->setToken($token);
    //     return true;
    // }
    // /**
    //  * Send the response after the user was authenticated.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // protected function sendLoginResponse(Request $request)
    // {
    //     $this->clearLoginAttempts($request);
    //     $token = (string) $this->guard()->getToken();
    //     $expiration = $this->guard()->getPayload()->get('exp');
    //     return response()->json([
    //         'token' => $token,
    //         'token_type' => 'bearer',
    //         'expires_in' => $expiration - time(),
    //     ]);
    // }
}
