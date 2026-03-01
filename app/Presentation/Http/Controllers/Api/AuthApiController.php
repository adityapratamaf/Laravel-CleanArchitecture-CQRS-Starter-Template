<?php

namespace App\Presentation\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Infrastructure\Persistence\Eloquent\Models\UserModel;

class AuthApiController
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
            'device_name' => ['nullable','string','max:100'],
        ]);

        $user = UserModel::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $deviceName = $data['device_name'] ?? 'api';

        $user->tokens()->delete();
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user || !$request->user()->currentAccessToken()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json(['user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}