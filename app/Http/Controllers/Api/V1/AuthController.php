<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $tokenName = $validated['device_name'] ?? 'api-token';
        $token = $user->createToken($tokenName)->plainTextToken;

        $roles = $user->getRoleNames();
        $primaryRole = $roles->first() ?? $user->role;

        return response()->json([
            'message' => 'Login berhasil.',
            'token_type' => 'Bearer',
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $primaryRole,
                'roles' => $roles,
            ],
        ], 200);
    }

    #[Middleware('auth:sanctum')]
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $roles = $user->getRoleNames();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $roles->first() ?? $user->role,
                'roles' => $roles,
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ], 200);
    }

    #[Middleware('auth:sanctum')]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout berhasil. Bearer token telah dicabut.',
        ], 200);
    }
}