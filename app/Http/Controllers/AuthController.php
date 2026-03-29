<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/register",
        summary: "Register a new user",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User registered successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User registered successfully"),
                        new OA\Property(property: "user", type: "object")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function register(\App\Http\Requests\RegisterRequest $request)
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
            'user' => $user,
        ], 201);
    }

    #[OA\Post(
        path: "/login",
        summary: "Login user and get token",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "password123"),
                    new OA\Property(property: "device_name", type: "string", example: "my-iphone")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "token", type: "string", example: "1|abcdef123456")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Invalid credentials")
        ]
    )]
    public function login(\App\Http\Requests\LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $tokenName = $request->device_name ?? 'auth_token';

        return response()->json([
            'access_token' => $user->createToken($tokenName)->plainTextToken,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    #[OA\Post(
        path: "/logout",
        summary: "Logout user (revoke current token)",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(response: 200, description: "Logged out successfully"),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    #[OA\Get(
        path: "/me",
        summary: "Get authenticated user details",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200, 
                description: "User details",
                content: new OA\JsonContent(type: "object")
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function me(Request $request)
    {
        return $request->user();
    }

    #[OA\Get(
        path: "/tokens",
        summary: "List all active tokens for the user",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of tokens",
                content: new OA\JsonContent(type: "array", items: new OA\Items(type: "object"))
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function tokens(Request $request)
    {
        return response()->json(['tokens' => $request->user()->tokens]);
    }

    public function createToken(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $token = $request->user()->createToken($request->name)->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    #[OA\Delete(
        path: "/tokens/{id}",
        summary: "Revoke a specific token",
        security: [["bearerAuth" => []]],
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Token revoked successfully"),
            new OA\Response(response: 404, description: "Token not found")
        ]
    )]
    public function revokeToken(Request $request, $id)
    {
        $token = $request->user()->tokens()->findOrFail($id);
        $token->delete();

        return response()->json(['message' => 'Token revoked successfully.']);
    }
}
