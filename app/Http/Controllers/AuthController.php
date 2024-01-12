<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Get the current user
     * 
     * Gets the current logged in user based on authorization headers
     */
    public function index(Request $request)
    {
        return response()->json(['data' => ['user' => $request->user()]]);
    }

    /**
     * Register a new user account
     * 
     * Registers a new user account from given data
     */
    public function register(UserRegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create(
            [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]
        );

        $token = $user->createToken($validated['device_name'] ?? 'Unknown')->plainTextToken;

        return response()->json(['data' => ['token' => $token]]);
    }

    /**
     * Login a user
     * 
     * Logs in user with provided credentials
     */
    public function login(UserLoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('username', $validated['username'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($validated['device_name'] ?? 'Unknown', ['*'])->plainTextToken;

        return response()->json(['data' => ['token' => $token]]);
    }
}
