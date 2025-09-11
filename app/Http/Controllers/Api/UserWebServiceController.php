<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserWebServiceController extends Controller
{
    /**
     * User Login
     * REST API: POST /api/user/login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
                'data' => null
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'userId' => $user->id,
                'userName' => $user->name,
                'userEmail' => $user->email,
                'accessToken' => $token,
                'tokenType' => 'Bearer',
                'expiresIn' => 3600
            ]
        ]);
    }

    /**
     * User Registration
     * REST API: POST /api/user/register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => 'active'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => [
                'userId' => $user->id,
                'userName' => $user->name,
                'userEmail' => $user->email,
                'accessToken' => $token,
                'tokenType' => 'Bearer',
                'expiresIn' => 3600
            ]
        ], 201);
    }

    /**
     * Forgot Password
     * REST API: POST /api/user/forgot-password
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        
        // Generate reset token
        $resetToken = Str::random(60);
        $user->update(['remember_token' => $resetToken]);

        // In a real application, you would send an email here
        // For now, we'll return the token for testing purposes

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset token generated successfully',
            'data' => [
                'resetToken' => $resetToken,
                'expiresIn' => 3600, // 1 hour
                'instructions' => 'Use this token to reset your password'
            ]
        ]);
    }

    /**
     * Reset Password
     * REST API: POST /api/user/reset-password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $user = User::where('email', $request->email)
                   ->where('remember_token', $request->token)
                   ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid reset token or email',
                'data' => null
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'remember_token' => null
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully',
            'data' => [
                'userId' => $user->id,
                'userName' => $user->name,
                'userEmail' => $user->email
            ]
        ]);
    }

    /**
     * Get User Profile
     * REST API: GET /api/user/profile/{userId}
     */
    public function getProfile($userId)
    {
        $validator = Validator::make(['userId' => $userId], [
            'userId' => 'required|string|regex:/^[a-zA-Z0-9]+$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid userId format. Only alphabets and numbers allowed.',
                'data' => null
            ], 400);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'status' => 'I',
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User profile retrieved successfully',
            'data' => [
                'status' => $user->status === 'active' ? 'A' : 'I',
                'userName' => $user->name,
                'userEmail' => $user->email,
                'userDetails' => [
                    'HpNo' => $user->phone ?? 'N/A',
                    'HouseAdd' => $user->address ?? 'N/A',
                    'createdAt' => $user->created_at->toISOString(),
                    'lastLogin' => $user->last_login_at ?? 'N/A'
                ]
            ]
        ]);
    }

    /**
     * Update User Profile
     * REST API: PUT /api/user/profile/{userId}
     */
    public function updateProfile(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        $user->update($request->only(['name', 'phone', 'address']));

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => [
                'userId' => $user->id,
                'userName' => $user->name,
                'userEmail' => $user->email,
                'userDetails' => [
                    'HpNo' => $user->phone ?? 'N/A',
                    'HouseAdd' => $user->address ?? 'N/A'
                ]
            ]
        ]);
    }
}








