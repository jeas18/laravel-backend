<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        ['user' => $user, 'token' => $token] = $this->authService->register(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('password'),
        );

        return $this->tokenResponse($user, $token, Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        ['user' => $user, 'token' => $token] = $this->authService->login(
            $request->validated('email'),
            $request->validated('password'),
        );

        return $this->tokenResponse($user, $token, Response::HTTP_OK);
    }

    public function logout(Request $request): Response
    {
        $this->authService->logout($request->user());

        return response()->noContent();
    }

    private function tokenResponse(User $user, string $token, int $status): JsonResponse
    {
        return response()->json([
            'data' => [
                'user' => UserResource::make($user),
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
        ], $status);
    }
}
