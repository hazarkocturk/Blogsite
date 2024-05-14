<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function register(Request $request)
    {
        Log::info('Register endpoint hit');

        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'userType' => 'required|string|in:user,admin'
        ]);

        Log::info('Validation passed', $validatedData);

        try {
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'userType' => $request->userType // Save userType directly
            ]);

            $user->save();

            Log::info('User saved successfully', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'User created successfully!'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error saving user', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Error creating user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        Log::info('Login endpoint hit');

        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        Log::info('Validation passed', $validatedData);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;

            Log::info('User logged in successfully', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'Successfully logged in!',
                'token' => $token
            ], 200);
        }

        Log::warning('Invalid email or password');

        return response()->json([
            'message' => 'Invalid email or password'
        ], 401);
    }

    public function logout(Request $request)
    {
        Log::info('Logout endpoint hit');

        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();
            Log::info('User logged out successfully', ['user_id' => $user->id]);

            return response()->json([
                'message' => 'Successfully logged out!'
            ], 200);
        }

        Log::warning('User was not authenticated');

        return response()->json([
            'message' => 'User was not authenticated'
        ], 401);
    }
}
