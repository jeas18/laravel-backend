<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private const TOKEN_NAME = 'api';

    /**
     * Create a new user and issue an API token for it.
     *
     * @return array{user: User, token: string}
     */
    public function register(string $name, string $email, string $password): array
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        return ['user' => $user, 'token' => $this->issueToken($user)];
    }

    /**
     * Validate the credentials and issue an API token.
     *
     * @return array{user: User, token: string}
     *
     * @throws AuthenticationException
     */
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new AuthenticationException('Credenciales inválidas.');
        }

        return ['user' => $user, 'token' => $this->issueToken($user)];
    }

    /**
     * Revoke the token used in the current request.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    private function issueToken(User $user): string
    {
        return $user->createToken(self::TOKEN_NAME)->plainTextToken;
    }
}
