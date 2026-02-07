<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request)
    {
        $result = $this->authService->register($request->validated());

        return $this->apiResponse(
            code: 201,
            message: 'User registered successfully',
            data: [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
                'token_type' => $result['token_type'],
            ]
        );
    }

    public function login(LoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if (!$result) {
            return $this->apiResponse(
                code: 401,
                message: 'Invalid credentials',
                errors: ['auth' => 'Email or password is incorrect']
            );
        }

        return $this->apiResponse(
            code: 200,
            message: 'Login successful',
            data: $result
        );
    }

    public function logout()
    {
        $this->authService->logout();

        return $this->apiResponse(
            code: 200,
            message: 'Successfully logged out'
        );
    }

    public function me()
    {
        $user = $this->authService->getCurrentUser();

        return $this->apiResponse(
            code: 200,
            message: 'User retrieved successfully',
            data: new UserResource($user)
        );
    }
}
