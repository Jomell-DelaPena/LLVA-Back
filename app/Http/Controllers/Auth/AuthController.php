<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        $credentials = $request->only('email','password');

        if (!Auth::attempt($credentials)) {

            return response()->json([
                'message' => 'Invalid credentials'
            ],401);

        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);

    }

    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);

    }

    public function me(Request $request)
    {

        return response()->json($request->user());

    }

    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json($user);

    }

}