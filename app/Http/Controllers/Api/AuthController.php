<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //validate fields
        $request->validate([
            'email' => 'required|string|unique:users',
            'password' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number' => 'required|string',
            'password' => 'required|string|min:8',
        ]);
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            return response()->json(
                [
                    'message' => 'Email already exists',
                ],
                409
            );
        }

        $user = User::create($request->all());

        if ($user->save()) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(
                [
                    'message' => 'User created successfully',
                    'user' => $user,
                    'token' => $token,
                ],
                201
            );
        }

        return response()->json(
            [
                'message' => 'Error while creating user',
            ],
            500
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        //check email and password

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(
                [
                    'message' => 'User not found.',
                ],
                404
            );
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    'message' => 'Incorrect password. Please try again.',
                ],
                401
            );
        }

        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json(
                [
                    'message' => 'Unauthorized',
                ],
                401
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(
            [
                'message' => 'User logged in successfully',
                'user' => $user,
                'token' => $token,
            ],
            200
        );
    }

    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return response()->json(
            [
                'message' => 'User logged out successfully',
            ],
            200
        );
    }
}
