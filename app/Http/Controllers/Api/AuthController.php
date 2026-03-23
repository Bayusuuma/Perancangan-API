<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
                'data' => null,
                'status_code' => 422
            ], 422);
        }

        $credentials = $request->only('username', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah!',
                'data' => null,
                'status_code' => 401
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Logged in.',
            'token' => $token
        ], 200);
    }
}