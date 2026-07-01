<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Handle an API authentication attempt and issue a token.
     */
    public function authenticate(Request $request): JsonResponse
    {
        // 1. Validate incoming JSON payload
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Attempt authentication against the database
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 3. Generate a stateless Access Token (Requires Laravel Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            // 4. Return success payload along with bearer token
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful.',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'label' => $user->label
                ]
            ], 200);
        }

        // 5. Fail gracefully with a 422 Unprocessable Entity status code
        return response()->json([
            'message' => 'The provided credentials do not match our records.',
            'errors' => [
                'email' => ['The provided credentials do not match our records.']
            ]
        ], 422);
    }

    /**
     * Log the user out (Revoke the current API token).
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the exact token used to authenticate this API call
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'You have been logged out successfully.'
        ], 200);
    }
}
