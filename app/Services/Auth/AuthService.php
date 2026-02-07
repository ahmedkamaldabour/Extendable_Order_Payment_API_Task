<?php

namespace App\Services\Auth;

use App\Models\User;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $token = auth()->login($user);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
        ];
    }

    public function login(array $credentials): ?array
    {
        if (!$token = auth()->attempt($credentials)) {
            return null;
        }

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ];
    }

    public function logout(): void
    {
        auth()->logout();
    }

    public function getCurrentUser(): User
    {
        return auth()->user();
    }
}
