<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use App\OpenApi\RequestBodies\UserForgotPasswordRequestBody;
use App\OpenApi\RequestBodies\UserLoginRequestBody;
use App\OpenApi\RequestBodies\UserRegisterRequestBody;
use App\OpenApi\RequestBodies\UserResetPasswordRequestBody;
use App\OpenApi\RequestBodies\VerifyOtpRequestBody;
use App\OpenApi\Responses\EmptyResponse;
use App\OpenApi\Responses\ErrorMessageResponse;
use App\OpenApi\Responses\ErrorValidationResponse;
use App\OpenApi\Responses\GetCurrentUserResponse;
use App\OpenApi\Responses\UnauthenticatedResponse;
use App\OpenApi\Responses\UserLoginResponse;
use App\OpenApi\Responses\UserRegisterResponse;
use App\Rules\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenAPI\PathItem]
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
    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        // Check if user with phone_number exists and is verfied
        $isPhoneAlreadyRegisteredAndVerified = User::where('phone_number', $validated['phone_number'])->whereNotNull('phone_number_verified_at')->exists();

        if ($isPhoneAlreadyRegisteredAndVerified) {
            return abort(Response::HTTP_BAD_REQUEST, __('auth.phone_already_registered'));
        }

        $user = User::updateOrCreate(
            [
                'phone_number' => $validated['phone_number'],
            ],
            [
                'full_name' => $validated['full_name'],
                'phone_number' => $validated['phone_number'],
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
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('phone_number', $validated['phone_number'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'phone_number' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($validated['device_name'] ?? 'Unknown')->plainTextToken;

        return response()->json(['data' => ['token' => $token]]);
    }
}
