<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
    // List all API tokens for the authenticated user
    public function tokens(Request $request)
    {
        $tokens = $request->user()->tokens->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'description' => $token->description,
                'plain_token' => $token->plain_token,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
            ];
        });
        return response()->json(['tokens' => $tokens]);
    }

    // Create a new API token with a custom name
    public function createToken(Request $request)
    {
        if (!$request->user()->is_partner) {
            return response()->json(['message' => 'Restricted: Partner account required'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
        
        $token = $request->user()->createToken($request->name);
        $accessToken = $token->accessToken;
        
        // Save the plain token for dashboard visibility
        $accessToken->plain_token = $token->plainTextToken;
        
        // Save description if provided
        if ($request->filled('description')) {
            $accessToken->description = $request->description;
        }
        
        $accessToken->save();
        
        return response()->json([
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
        ], 201);
    }

    // Revoke (delete) a specific API token by ID
    public function revokeToken(Request $request, $tokenId)
    {
        $token = $request->user()->tokens()->findOrFail($tokenId);
        $token->delete();
        return response()->json(['message' => 'Token revoked successfully.']);
    }
}
