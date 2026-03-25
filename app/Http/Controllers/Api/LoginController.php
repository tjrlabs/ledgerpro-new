<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'error' => false,
            'message' => 'Login successful',
            'data' => [
                'token' => $token
            ]
        ]);
    }

    public function logout() {
        Auth::user()->currentAccessToken()->delete();
        return response()->json(['error' => false, 'message' => 'Logged out successfully', 'data' => null], 200);
    }

    public function getUserProfile(Request $request) {
        $user = $request->user();
        return response()->json(['error' => false, 'message' => 'User profile fetched successfully', 'data' => $user], 200);
    }
}
