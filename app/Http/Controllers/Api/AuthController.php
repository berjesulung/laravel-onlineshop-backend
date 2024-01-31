<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //validate the request
        $validated = $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users|max:100',
            'password' => 'required|min:8',
            'phone' => 'required',
            'roles' => 'required',
        ]);

        //password hashing
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ], 201);
    }

    //login
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'message' => ['Email not found'],
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['Password is wrong'],
            ], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    //logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        // $request->user()->currentAccessTokens()->delete();
        return response()->json([
            'message' => 'Logout success',
        ], 200);
    }
}
